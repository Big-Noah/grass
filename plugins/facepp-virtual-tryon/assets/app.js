(function () {
	'use strict';

	var globals = window.faceppTryonGlobals || {};

	function distance(a, b) {
		var dx = b.x - a.x;
		var dy = b.y - a.y;
		return Math.sqrt(dx * dx + dy * dy);
	}

	function clamp(value, min, max) {
		return Math.min(max, Math.max(min, value));
	}

	function sanitizeKey(value) {
		return String(value || '')
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '');
	}

	function parseConfig(root) {
		var node = root.querySelector('.facepp-tryon-instance-config');

		if (!node) {
			return {};
		}

		try {
			return JSON.parse(node.textContent || '{}');
		} catch (error) {
			return {};
		}
	}

	function initApp(root) {
		var config = parseConfig(root);
		var openBtn = root.querySelector('.facepp-tryon-open');
		var modal = root.querySelector('.facepp-tryon-modal');
		var stage = root.querySelector('.facepp-tryon-stage');
		var photo = root.querySelector('.facepp-tryon-photo');
		var frame = root.querySelector('.facepp-tryon-frame');
		var empty = root.querySelector('.facepp-tryon-empty');
		var fileInput = root.querySelector('.facepp-tryon-file');
		var status = root.querySelector('.facepp-tryon-status');
		var loadingText = root.querySelector('.facepp-tryon-stage-loading-text');
		var framesBox = root.querySelector('.facepp-tryon-frames');
		var modelsBox = root.querySelector('.facepp-tryon-models');
		var controls = root.querySelectorAll('[data-tryon-action]');
		var leftEye = root.querySelector('.facepp-tryon-eye-left');
		var rightEye = root.querySelector('.facepp-tryon-eye-right');

		if (!openBtn || !modal || !photo || !frame || !framesBox || !modelsBox) {
			return;
		}

		var fixedHost = findFixedContainingBlock(root);

		function findFixedContainingBlock(node) {
			var current = node ? node.parentElement : null;

			while (current && current !== document.body) {
				var styles = window.getComputedStyle(current);
				var containValue = styles.contain || '';

				if (
					styles.transform !== 'none' ||
					styles.perspective !== 'none' ||
					styles.filter !== 'none' ||
					styles.backdropFilter !== 'none' ||
					containValue.indexOf('paint') !== -1
				) {
					return current;
				}

				current = current.parentElement;
			}

			return null;
		}

		function syncModalViewport() {
			var hostRect = fixedHost ? fixedHost.getBoundingClientRect() : { left: 0, top: 0 };

			modal.style.left = String(-hostRect.left) + 'px';
			modal.style.top = String(-hostRect.top) + 'px';
			modal.style.right = 'auto';
			modal.style.bottom = 'auto';
			modal.style.width = String(window.innerWidth) + 'px';
			modal.style.height = String(window.innerHeight) + 'px';
		}

		var state = {
			productId: String(config.productId || root.getAttribute('data-product-id') || ''),
			photoLoaded: false,
			frameLoaded: false,
			photoDataUrl: '',
			photoSourceUrl: '',
			selectedFrame: null,
			selectedModel: null,
			preferredFrameKey: sanitizeKey(config.defaultFrame || ''),
			drag: null,
			pendingDetect: false,
			pendingPreset: null,
			pendingDetectedEyes: null,
			detectedEyes: null,
			loadToken: 0,
			alignToken: 0,
			auto: { x: 0, y: 0, rotate: 0, width: 0 },
			manual: { x: 0, y: 0, rotate: 0, scale: 1 }
		};
		var bodyOverflowBeforeOpen = null;

		function setStatus(message) {
			if (status) {
				status.textContent = message || '';
			}
		}

		function setBusy(isBusy, message) {
			if (stage) {
				stage.classList.toggle('is-busy', !!isBusy);
			}

			if (loadingText && message) {
				loadingText.textContent = message;
			}
		}

		function updateEyeMarker(marker, point) {
			if (!marker || !point) {
				if (marker) {
					marker.style.display = 'none';
				}
				return;
			}

			marker.style.left = point.x - 32.5 + 'px';
			marker.style.top = point.y - 32.5 + 'px';
			marker.style.display = 'none';
		}

		function clearEyeMarkers() {
			updateEyeMarker(leftEye, null);
			updateEyeMarker(rightEye, null);
		}

		function getMetrics() {
			return {
				width: photo.clientWidth || 0,
				height: photo.clientHeight || 0,
				left: photo.offsetLeft || 0,
				top: photo.offsetTop || 0
			};
		}

		function render() {
			var metrics = getMetrics();
			var showFrame = state.photoLoaded && state.frameLoaded && metrics.width > 0 && metrics.height > 0;

			empty.style.display = state.photoLoaded ? 'none' : 'flex';

			if (!showFrame) {
				frame.classList.remove('is-ready');
				return;
			}

			var width = state.auto.width * state.manual.scale;
			var x = metrics.left + state.auto.x + state.manual.x;
			var y = metrics.top + state.auto.y + state.manual.y;
			var rotate = state.auto.rotate + state.manual.rotate;

			frame.style.width = width + 'px';
			frame.style.left = x + 'px';
			frame.style.top = y + 'px';
			frame.style.transform = 'rotate(' + rotate + 'deg)';
			frame.classList.add('is-ready');
		}

		function resetManual() {
			state.manual = { x: 0, y: 0, rotate: 0, scale: 1 };
		}

		function getFrameFitScale() {
			var fitScale = state.selectedFrame && typeof state.selectedFrame.fitScale === 'number' ? state.selectedFrame.fitScale : 0.78;

			if (!isFinite(fitScale) || fitScale <= 0) {
				return 0.78;
			}

			return fitScale;
		}

		function autoCenter() {
			if (!state.photoLoaded || !state.frameLoaded) {
				return;
			}

			var metrics = getMetrics();

			state.auto.width = metrics.width * 0.56;
			state.auto.x = (metrics.width - state.auto.width) / 2;
			state.auto.y = metrics.height * 0.34;
			state.auto.rotate = 0;
			resetManual();
			render();
		}

		function applyPreset(preset) {
			if (!state.photoLoaded || !state.frameLoaded || !preset) {
				return;
			}

			var metrics = getMetrics();
			var baseWidth = Number(config.stageWidth || 350);
			var baseHeight = Number(config.stageHeight || 410);
			var widthScale = baseWidth ? metrics.width / baseWidth : 1;
			var heightScale = baseHeight ? metrics.height / baseHeight : 1;

			state.auto.width = Number(preset.frameWidth || 172) * widthScale;
			state.auto.x = Number(preset.frameLeft || 85) * widthScale;
			state.auto.y = Number(preset.frameTop || 121) * heightScale;
			state.auto.rotate = Number(preset.frameRotate || 0);
			resetManual();
			render();
		}

		function applyEyes(leftPoint, rightPoint) {
			var eyeDistance = distance(leftPoint, rightPoint);
			var widthFactor = state.selectedFrame && state.selectedFrame.widthFactor ? state.selectedFrame.widthFactor : 2.15;
			var yOffset = state.selectedFrame && state.selectedFrame.yOffset ? state.selectedFrame.yOffset : 0.02;
			var fitScale = getFrameFitScale();
			var centerX = (leftPoint.x + rightPoint.x) / 2;
			var centerY = (leftPoint.y + rightPoint.y) / 2;
			var rotate = Math.atan2(rightPoint.y - leftPoint.y, rightPoint.x - leftPoint.x) * (180 / Math.PI);
			var naturalWidth = frame.naturalWidth || 1;
			var naturalHeight = frame.naturalHeight || 1;
			var leftEyeX = state.selectedFrame && typeof state.selectedFrame.leftEyeX === 'number' ? state.selectedFrame.leftEyeX : NaN;
			var leftEyeY = state.selectedFrame && typeof state.selectedFrame.leftEyeY === 'number' ? state.selectedFrame.leftEyeY : NaN;
			var rightEyeX = state.selectedFrame && typeof state.selectedFrame.rightEyeX === 'number' ? state.selectedFrame.rightEyeX : NaN;
			var rightEyeY = state.selectedFrame && typeof state.selectedFrame.rightEyeY === 'number' ? state.selectedFrame.rightEyeY : NaN;
			var useAnchors =
				isFinite(leftEyeX) &&
				isFinite(leftEyeY) &&
				isFinite(rightEyeX) &&
				isFinite(rightEyeY) &&
				rightEyeX > leftEyeX &&
				rightEyeX - leftEyeX > 0.05;

			if (useAnchors) {
				var anchorSpanX = (rightEyeX - leftEyeX) * naturalWidth;
				var scale = anchorSpanX > 0 ? eyeDistance / anchorSpanX : 0;

				scale *= fitScale;

				var width = naturalWidth * scale;
				var height = naturalHeight * scale;
				var anchorCenterX = ((leftEyeX + rightEyeX) / 2) * width;
				var anchorCenterY = ((leftEyeY + rightEyeY) / 2) * height;

				state.auto.width = width;
				state.auto.x = centerX - anchorCenterX;
				state.auto.y = centerY - anchorCenterY;
				state.auto.rotate = rotate;
				resetManual();
				updateEyeMarker(leftEye, leftPoint);
				updateEyeMarker(rightEye, rightPoint);
				render();
				return;
			}

			var fallbackWidth = eyeDistance * widthFactor * fitScale;
			var fallbackHeight = fallbackWidth * (naturalHeight / naturalWidth);

			state.auto.width = fallbackWidth;
			state.auto.x = centerX - fallbackWidth / 2;
			state.auto.y = centerY - fallbackHeight / 2 + eyeDistance * yOffset;
			state.auto.rotate = rotate;
			resetManual();
			updateEyeMarker(leftEye, leftPoint);
			updateEyeMarker(rightEye, rightPoint);
			render();
		}

		function applyStoredEyes() {
			var metrics = getMetrics();
			var naturalWidth = photo.naturalWidth || metrics.width || 1;
			var naturalHeight = photo.naturalHeight || metrics.height || 1;

			if (!state.detectedEyes || !state.detectedEyes.left_eye || !state.detectedEyes.right_eye || !metrics.width || !metrics.height) {
				return false;
			}

			applyEyes(
				{
					x: (state.detectedEyes.left_eye.x / naturalWidth) * metrics.width,
					y: (state.detectedEyes.left_eye.y / naturalHeight) * metrics.height
				},
				{
					x: (state.detectedEyes.right_eye.x / naturalWidth) * metrics.width,
					y: (state.detectedEyes.right_eye.y / naturalHeight) * metrics.height
				}
			);

			return true;
		}

		function buildFrameButtons() {
			var frames = Array.isArray(config.frames) ? config.frames : [];

			framesBox.innerHTML = '';

			frames.forEach(function (item, index) {
				var key = sanitizeKey(item.key || item.name || String(index + 1));
				var button = document.createElement('button');

				button.type = 'button';
				button.className = 'cv-btn facepp-tryon-frame-item';
				button.setAttribute('data-frame-key', key);
				button.innerHTML = '<img alt="' + (item.name || 'Frame') + '" src="' + (item.thumbnail || item.url) + '">';
				button.addEventListener('click', function () {
					var firstLoad = !state.frameLoaded;

					state.selectedFrame = item;
					state.preferredFrameKey = key;
					framesBox.querySelectorAll('.facepp-tryon-frame-item').forEach(function (entry) {
						entry.classList.remove('is-active');
					});
					button.classList.add('is-active');

					frame.onload = function () {
						state.frameLoaded = true;

						if (applyStoredEyes()) {
							setBusy(false);
							setStatus((globals.i18n || {}).aligned);
							return;
						}

						if (state.photoLoaded && state.pendingDetect) {
							runAutoAlign({ fallbackPreset: state.pendingPreset });
							return;
						}

						if (state.selectedModel && state.selectedModel.preset) {
							applyPreset(state.selectedModel.preset);
							setBusy(false);
							return;
						}

						if (state.photoLoaded || firstLoad) {
							autoCenter();
							setBusy(false);
							return;
						}

						render();
					};
					frame.src = item.url;
					if (frame.complete) {
						frame.onload();
					}
					setStatus(/\.png(?:$|\?)/i.test(item.url || '') ? (globals.i18n || {}).frameSelected : ((globals.i18n || {}).badFrameFormat || (globals.i18n || {}).frameSelected));
				});

				framesBox.appendChild(button);
			});

			var defaultButton = framesBox.querySelector('[data-frame-key="' + state.preferredFrameKey + '"]') || framesBox.querySelector('.facepp-tryon-frame-item');

			if (defaultButton) {
				defaultButton.click();
			}
		}

		function setActiveModelButton(button) {
			modelsBox.querySelectorAll('.facepp-tryon-model-item').forEach(function (entry) {
				entry.classList.remove('is-active');
			});
			if (button) {
				button.classList.add('is-active');
			}
		}

		function loadPhoto(src, dataUrl, readyMessage, preset, shouldDetect, detectedEyes) {
			var loadToken = ++state.loadToken;

			state.alignToken += 1;
			state.photoDataUrl = dataUrl || '';
			state.photoSourceUrl = typeof src === 'string' && src.indexOf('data:image/') !== 0 ? src : '';
			state.pendingPreset = preset || null;
			state.pendingDetect = !!shouldDetect;
			state.pendingDetectedEyes = detectedEyes || null;
			state.detectedEyes = null;
			state.photoLoaded = false;
			clearEyeMarkers();
			resetManual();

			photo.onload = function () {
				if (loadToken !== state.loadToken) {
					return;
				}

				state.photoLoaded = true;

				if (state.pendingDetectedEyes && state.frameLoaded) {
					state.detectedEyes = state.pendingDetectedEyes;
					state.pendingDetect = false;

					if (applyStoredEyes()) {
						setBusy(false);
						setStatus((globals.i18n || {}).aligned);
						return;
					}
				}

				if (state.pendingDetect && state.frameLoaded) {
					runAutoAlign({ fallbackPreset: state.pendingPreset });
					return;
				}

				if (state.pendingPreset && state.frameLoaded) {
					applyPreset(state.pendingPreset);
					setBusy(false);
				} else if (state.frameLoaded) {
					autoCenter();
					setBusy(false);
				}

				setStatus(readyMessage || (globals.i18n || {}).photoReady);
			};

			photo.onerror = function () {
				if (loadToken !== state.loadToken) {
					return;
				}

				setBusy(false);
				setStatus((globals.i18n || {}).detectFailed);
			};

			photo.crossOrigin = typeof src === 'string' && /^https?:\/\//i.test(src) ? 'anonymous' : '';
			photo.src = src;
			if (photo.complete) {
				photo.onload();
			}
		}

		function buildModelButtons() {
			var models = Array.isArray(config.testModels) ? config.testModels : [];

			modelsBox.innerHTML = '';

			models.forEach(function (item) {
				var button = document.createElement('button');

				button.type = 'button';
				button.className = 'try_sys_pic facepp-tryon-model-item';
				button.innerHTML = '<img alt="' + (item.name || 'Model') + '" src="' + item.url + '">';
				button.addEventListener('click', function () {
					var storedEyes = item.detectedEyes || null;
					state.selectedModel = item;
					setActiveModelButton(button);
					setBusy(true, (globals.i18n || {}).loadingModel || (globals.i18n || {}).detecting);
					loadPhoto(item.url, '', (globals.i18n || {}).modelReady || (globals.i18n || {}).photoReady, item.preset || null, !storedEyes, storedEyes);
				});
				modelsBox.appendChild(button);
			});

			var firstModel = modelsBox.querySelector('.facepp-tryon-model-item');

			if (firstModel) {
				setActiveModelButton(firstModel);
			}
		}

		function getPhotoPayload() {
			if (state.photoDataUrl && state.photoDataUrl.indexOf('data:image/') === 0) {
				return state.photoDataUrl;
			}

			if (!state.photoLoaded) {
				return '';
			}

			var width = photo.naturalWidth || photo.clientWidth || 0;
			var height = photo.naturalHeight || photo.clientHeight || 0;

			if (!width || !height) {
				return '';
			}

			var canvas = document.createElement('canvas');
			var context = canvas.getContext('2d');

			canvas.width = width;
			canvas.height = height;

			try {
				context.drawImage(photo, 0, 0, width, height);
				return canvas.toDataURL('image/jpeg', 0.92);
			} catch (error) {
				return '';
			}
		}

		async function detectFace() {
			var imagePayload = getPhotoPayload();

			if (!imagePayload && !state.photoSourceUrl) {
				var encodeError = new Error('Image encode failed');
				encodeError.code = 'encode_failed';
				throw encodeError;
			}

			var formData = new window.FormData();

			formData.append('action', 'facepp_tryon_detect');
			formData.append('nonce', globals.ajaxNonce);

			if (imagePayload) {
				formData.append('image', imagePayload);
			} else if (state.photoSourceUrl) {
				formData.append('image_url', state.photoSourceUrl);
			}

			var response = await window.fetch(globals.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});
			var json = await response.json();

			if (!response.ok || !json || !json.success || !json.data) {
				var error = new Error((json && json.data && json.data.message) || 'Detect failed');
				error.code = json && json.data && json.data.code ? json.data.code : 'unknown';
				throw error;
			}

			return json.data;
		}

		async function runAutoAlign(options) {
			var settings = options || {};
			var alignToken = ++state.alignToken;

			if (!state.photoLoaded) {
				setBusy(false);
				setStatus((globals.i18n || {}).noPhoto);
				return;
			}

			if (!state.frameLoaded || !state.selectedFrame) {
				setBusy(false);
				setStatus((globals.i18n || {}).noFrame);
				return;
			}

			try {
				setBusy(true, (globals.i18n || {}).detecting);
				setStatus((globals.i18n || {}).detecting);
				var data = await detectFace();

				if (alignToken !== state.alignToken) {
					return;
				}

				var metrics = getMetrics();
				var naturalWidth = photo.naturalWidth || metrics.width;
				var naturalHeight = photo.naturalHeight || metrics.height;

				state.detectedEyes = {
					left_eye: data.left_eye,
					right_eye: data.right_eye
				};
				state.pendingDetect = false;

				applyEyes(
					{ x: (data.left_eye.x / naturalWidth) * metrics.width, y: (data.left_eye.y / naturalHeight) * metrics.height },
					{ x: (data.right_eye.x / naturalWidth) * metrics.width, y: (data.right_eye.y / naturalHeight) * metrics.height }
				);
				setBusy(false);
				setStatus((globals.i18n || {}).aligned);
			} catch (error) {
				if (alignToken !== state.alignToken) {
					return;
				}

				state.pendingDetect = false;

				if (settings.fallbackPreset) {
					applyPreset(settings.fallbackPreset);
					setBusy(false);
					setStatus((globals.i18n || {}).fallbackAligned || (globals.i18n || {}).detectFailed);
					return;
				}

				setBusy(false);

				if (error && error.code === 'missing_credentials') {
					setStatus((globals.i18n || {}).missingCreds);
				} else if (error && (error.code === 'no_face' || error.code === 'no_eye_landmark')) {
					setStatus((globals.i18n || {}).noFace);
				} else if (error && error.code === 'encode_failed') {
					setStatus((globals.i18n || {}).encodeFailed || (globals.i18n || {}).detectFailed);
				} else if (error && error.message) {
					setStatus(error.message);
				} else {
					setStatus((globals.i18n || {}).detectFailed);
				}
			}
		}

		function handleControl(action) {
			switch (action) {
				case 'size_b':
					state.manual.scale = clamp(state.manual.scale + 0.02, 0.4, 3);
					break;
				case 'size_s':
					state.manual.scale = clamp(state.manual.scale - 0.02, 0.4, 3);
					break;
				case 'rotate_l':
					state.manual.rotate = clamp(state.manual.rotate - 1, -45, 45);
					break;
				case 'rotate_r':
					state.manual.rotate = clamp(state.manual.rotate + 1, -45, 45);
					break;
				case 'move_t':
					state.manual.y = clamp(state.manual.y - 3, -260, 260);
					break;
				case 'move_r':
					state.manual.x = clamp(state.manual.x + 3, -260, 260);
					break;
				case 'move_b':
					state.manual.y = clamp(state.manual.y + 3, -260, 260);
					break;
				case 'move_l':
					state.manual.x = clamp(state.manual.x - 3, -260, 260);
					break;
				default:
					return;
			}

			render();
		}

		function beginDrag(event) {
			if (!state.photoLoaded || !state.frameLoaded) {
				return;
			}

			var point = event.touches ? event.touches[0] : event;

			state.drag = {
				x: point.clientX,
				y: point.clientY,
				startX: state.manual.x,
				startY: state.manual.y
			};

			frame.classList.add('is-dragging');
			event.preventDefault();
		}

		function moveDrag(event) {
			if (!state.drag) {
				return;
			}

			var point = event.touches ? event.touches[0] : event;

			state.manual.x = clamp(state.drag.startX + (point.clientX - state.drag.x), -260, 260);
			state.manual.y = clamp(state.drag.startY + (point.clientY - state.drag.y), -260, 260);
			render();

			if (event.preventDefault) {
				event.preventDefault();
			}
		}

		function endDrag() {
			state.drag = null;
			frame.classList.remove('is-dragging');
		}

		function closeModal() {
			modal.hidden = true;
			if (bodyOverflowBeforeOpen !== null) {
				document.body.style.overflow = bodyOverflowBeforeOpen;
				bodyOverflowBeforeOpen = null;
			}
			setBusy(false);
		}

		function selectFrameByKey(key) {
			var button = framesBox.querySelector('[data-frame-key="' + sanitizeKey(key) + '"]');

			if (button) {
				button.click();
			}
		}

		openBtn.addEventListener('click', function () {
			syncModalViewport();
			modal.hidden = false;
			if (bodyOverflowBeforeOpen === null) {
				bodyOverflowBeforeOpen = document.body.style.overflow || '';
			}
			modal.scrollTop = 0;

			if (!state.photoLoaded) {
				var activeModel = modelsBox.querySelector('.facepp-tryon-model-item.is-active') || modelsBox.querySelector('.facepp-tryon-model-item');

				if (activeModel) {
					activeModel.click();
				}
			}

			window.requestAnimationFrame(syncModalViewport);
			render();
		});

		modal.addEventListener('click', function (event) {
			if (event.target.getAttribute('data-close') === '1') {
				closeModal();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !modal.hidden) {
				closeModal();
			}
		});

		window.addEventListener('resize', function () {
			if (!modal.hidden) {
				syncModalViewport();
				render();
			}
		});

		fileInput.addEventListener('change', function (event) {
			var file = event.target.files && event.target.files[0];

			if (!file) {
				return;
			}

			var reader = new window.FileReader();

			modelsBox.querySelectorAll('.facepp-tryon-model-item').forEach(function (entry) {
				entry.classList.remove('is-active');
			});
			setBusy(true, (globals.i18n || {}).uploading || (globals.i18n || {}).detecting);

			reader.onload = function (loadEvent) {
				var dataUrl = loadEvent && loadEvent.target ? loadEvent.target.result : '';

				if (typeof dataUrl !== 'string' || dataUrl.indexOf('data:image/') !== 0) {
					setBusy(false);
					setStatus((globals.i18n || {}).detectFailed);
					return;
				}

				state.selectedModel = null;
				loadPhoto(dataUrl, dataUrl, (globals.i18n || {}).photoReady, null, true);
			};

			reader.readAsDataURL(file);
		});

		if (fileInput) {
			fileInput.addEventListener('click', function () {
				fileInput.value = '';
			});
		}

		controls.forEach(function (button) {
			button.addEventListener('click', function () {
				handleControl(button.getAttribute('data-tryon-action'));
			});
		});

		frame.addEventListener('mousedown', beginDrag);
		frame.addEventListener('touchstart', beginDrag, { passive: false });
		window.addEventListener('mousemove', moveDrag);
		window.addEventListener('touchmove', moveDrag, { passive: false });
		window.addEventListener('mouseup', endDrag);
		window.addEventListener('touchend', endDrag);
		window.addEventListener('resize', function () {
			if (applyStoredEyes()) {
				return;
			}

			render();
		});

		document.addEventListener('muukal:productColorChanged', function (event) {
			var detail = event.detail || {};

			if (state.productId && detail.productId && String(detail.productId) !== state.productId) {
				return;
			}

			if (detail.tryonKey) {
				state.preferredFrameKey = sanitizeKey(detail.tryonKey);
				selectFrameByKey(state.preferredFrameKey);
			}
		});

		buildFrameButtons();
		buildModelButtons();
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.facepp-tryon-app').forEach(initApp);
	});
})();
