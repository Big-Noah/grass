(function () {
	'use strict';

	var config = window.muukalTryOnConfig || {};

	function distance(a, b) {
		var dx = b.x - a.x;
		var dy = b.y - a.y;
		return Math.sqrt(dx * dx + dy * dy);
	}

	function clamp(value, min, max) {
		return Math.min(max, Math.max(min, value));
	}

	function initApp(root) {
		var openButton = root.querySelector('.muukal-try-on-open');
		var modal = root.querySelector('.muukal-try-on-modal');
		var stage = root.querySelector('.muukal-try-on-stage');
		var cropper = root.querySelector('.muukal-try-on-cropper');
		var cropFrame = root.querySelector('.muukal-try-on-crop-frame');
		var cropImage = root.querySelector('.muukal-try-on-crop-image');
		var baseImage = root.querySelector('.muukal-try-on-base');
		var overlay = root.querySelector('.muukal-try-on-overlay');
		var eyeLayer = root.querySelector('.muukal-try-on-eye-layer');
		var eyeMarkers = root.querySelectorAll('.muukal-try-on-eye-marker');
		var status = root.querySelector('.muukal-try-on-status');
		var fileInput = root.querySelector('.muukal-try-on-file');
		var applyCropButton = root.querySelector('.muukal-try-on-apply-crop');
		var autoAlignButton = root.querySelector('.muukal-try-on-auto-align');
		var manualEyesButton = root.querySelector('.muukal-try-on-manual-eyes');
		var resetButton = root.querySelector('.muukal-try-on-reset');
		var manualSliders = root.querySelectorAll('[data-control]');
		var cropSliders = root.querySelectorAll('[data-crop-control]');
		var resizeObserver = window.ResizeObserver ? new ResizeObserver(function () {
			renderCropper();
			renderOverlay();
		}) : null;

		var state = {
			baseLoaded: false,
			overlayLoaded: false,
			uploadSrc: '',
			auto: { x: 0, y: 0, rotate: 0, widthPx: 0 },
			manual: { manualX: 0, manualY: 0, manualScale: 1, manualRotate: 0 },
			crop: { zoom: 1.35, x: 0, y: 0 },
			cropDragging: null,
			drag: null,
			eyeDrag: null,
			manualEyesEnabled: false,
			eyes: {
				left: { x: 0, y: 0 },
				right: { x: 0, y: 0 }
			}
		};

		if (resizeObserver) {
			resizeObserver.observe(stage);
		} else {
			window.addEventListener('resize', function () {
				renderCropper();
				renderOverlay();
			});
		}

		function setStatus(message) {
			status.textContent = message || '';
		}

		function getStageMetrics() {
			return {
				width: baseImage.clientWidth || 0,
				height: baseImage.clientHeight || 0,
				left: baseImage.offsetLeft || 0,
				top: baseImage.offsetTop || 0
			};
		}

		function syncManualSliders() {
			manualSliders.forEach(function (slider) {
				var key = slider.getAttribute('data-control');
				if (state.manual[key] !== undefined) {
					slider.value = state.manual[key];
				}
			});
		}

		function syncCropSliders() {
			cropSliders.forEach(function (slider) {
				var key = slider.getAttribute('data-crop-control');
				if (state.crop[key] !== undefined) {
					slider.value = state.crop[key];
				}
			});
		}

		function resetManual() {
			state.manual = { manualX: 0, manualY: 0, manualScale: 1, manualRotate: 0 };
			syncManualSliders();
			renderOverlay();
		}

		function resetCrop() {
			state.crop = { zoom: 1.35, x: 0, y: 0 };
			syncCropSliders();
			renderCropper();
		}

		function setDefaultEyes() {
			var metrics = getStageMetrics();
			state.eyes.left = {
				x: metrics.width * 0.39,
				y: metrics.height * 0.4
			};
			state.eyes.right = {
				x: metrics.width * 0.61,
				y: metrics.height * 0.4
			};
			positionEyeMarkers();
		}

		function positionEyeMarkers() {
			var metrics = getStageMetrics();
			var ready = state.baseLoaded && metrics.width > 0 && metrics.height > 0;

			if (!ready) {
				eyeLayer.hidden = true;
				eyeLayer.classList.remove('is-ready');
				return;
			}

			eyeMarkers.forEach(function (marker) {
				var key = marker.getAttribute('data-eye');
				var point = state.eyes[key];
				marker.style.left = (metrics.left + point.x) + 'px';
				marker.style.top = (metrics.top + point.y) + 'px';
			});

			eyeLayer.hidden = !state.manualEyesEnabled;
			eyeLayer.classList.toggle('is-ready', state.manualEyesEnabled);
		}

		function renderCropper() {
			if (!state.uploadSrc || !cropImage.naturalWidth || !cropImage.naturalHeight) {
				cropper.hidden = true;
				return;
			}

			var frameWidth = cropFrame.clientWidth;
			var frameHeight = cropFrame.clientHeight;
			var coverScale = Math.max(frameWidth / cropImage.naturalWidth, frameHeight / cropImage.naturalHeight);
			var actualScale = coverScale * state.crop.zoom;

			cropImage.style.width = (cropImage.naturalWidth * actualScale) + 'px';
			cropImage.style.height = (cropImage.naturalHeight * actualScale) + 'px';
			cropImage.style.transform = 'translate(-50%, -50%) translate(' + state.crop.x + 'px, ' + state.crop.y + 'px)';
			cropper.hidden = false;
		}

		function renderOverlay() {
			var metrics = getStageMetrics();
			var hasBase = state.baseLoaded && metrics.width > 0 && metrics.height > 0;
			stage.classList.toggle('has-image', hasBase);

			if (!hasBase || !state.overlayLoaded) {
				overlay.classList.remove('is-ready');
				positionEyeMarkers();
				return;
			}

			var widthPx = state.auto.widthPx * state.manual.manualScale;
			var x = metrics.left + state.auto.x + state.manual.manualX;
			var y = metrics.top + state.auto.y + state.manual.manualY;
			var rotate = state.auto.rotate + state.manual.manualRotate;

			overlay.style.width = widthPx + 'px';
			overlay.style.left = x + 'px';
			overlay.style.top = y + 'px';
			overlay.style.transform = 'rotate(' + rotate + 'deg)';
			overlay.classList.add('is-ready');
			positionEyeMarkers();
		}

		function applyEyes(leftEye, rightEye, statusMessage) {
			var eyeDistance = distance(leftEye, rightEye);
			var widthPx = eyeDistance * (config.autoWidthFactor || 2.15);
			var heightPx = widthPx * ((overlay.naturalHeight || 1) / (overlay.naturalWidth || 1));
			var centerX = (leftEye.x + rightEye.x) / 2;
			var centerY = (leftEye.y + rightEye.y) / 2;
			var rotation = Math.atan2(rightEye.y - leftEye.y, rightEye.x - leftEye.x) * (180 / Math.PI);
			var offsetY = eyeDistance * (config.autoYOffset || 0.02);

			state.auto.widthPx = widthPx;
			state.auto.x = centerX - widthPx / 2;
			state.auto.y = centerY - heightPx / 2 + offsetY;
			state.auto.rotate = rotation;
			state.eyes.left = { x: leftEye.x, y: leftEye.y };
			state.eyes.right = { x: rightEye.x, y: rightEye.y };
			resetManual();
			renderOverlay();

			if (statusMessage) {
				setStatus(statusMessage);
			}
		}

		function autoCenterOverlay() {
			if (!state.baseLoaded || !state.overlayLoaded) {
				return;
			}

			var metrics = getStageMetrics();
			state.auto.widthPx = Math.min(metrics.width * 0.58, overlay.naturalWidth || metrics.width * 0.58);
			state.auto.x = (metrics.width - state.auto.widthPx) / 2;
			state.auto.y = metrics.height * 0.34;
			state.auto.rotate = 0;
			setDefaultEyes();
			renderOverlay();
		}

		function setBaseImage(src, statusMessage, keepCropperOpen) {
			if (!src) {
				return;
			}

			state.baseLoaded = false;
			state.manualEyesEnabled = false;
			baseImage.classList.remove('is-ready');
			stage.classList.remove('has-image');
			baseImage.onload = function () {
				state.baseLoaded = true;
				baseImage.classList.add('is-ready');
				if (!keepCropperOpen) {
					cropper.hidden = true;
				}
				autoCenterOverlay();
				setStatus(config.overlayImage ? statusMessage : config.i18n.overlayMissing);
			};
			baseImage.src = src;
		}

		function loadOverlay() {
			if (!config.overlayImage) {
				setStatus(config.i18n.overlayMissing);
				return;
			}

			overlay.onload = function () {
				state.overlayLoaded = true;
				autoCenterOverlay();
			};
			overlay.src = config.overlayImage;
		}

		async function detectFaceWithApi() {
			if (!config.ajaxUrl || !config.ajaxNonce) {
				throw new Error('missing ajax config');
			}

			var formData = new window.FormData();
			formData.append('action', 'muukal_try_on_detect_face');
			formData.append('nonce', config.ajaxNonce);
			formData.append('image', baseImage.src);

			var response = await window.fetch(config.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});

			var result = await response.json();

			if (!response.ok || !result || !result.success || !result.data) {
				var message = result && result.data && result.data.message ? result.data.message : 'Face detection failed';
				var code = result && result.data && result.data.code ? result.data.code : 'unknown';
				var error = new Error(message);
				error.code = code;
				throw error;
			}

			return result.data;
		}

		async function runAutoAlign() {
			if (!state.baseLoaded) {
				setStatus(state.uploadSrc ? config.i18n.cropPhoto : config.i18n.cropNeeded);
				return;
			}

			if (!state.overlayLoaded) {
				setStatus(config.i18n.overlayMissing);
				return;
			}

			try {
				setStatus(config.i18n.detecting);
				var result = await detectFaceWithApi();
				var metrics = getStageMetrics();
				var naturalWidth = baseImage.naturalWidth || metrics.width;
				var naturalHeight = baseImage.naturalHeight || metrics.height;
				applyEyes(
					{
						x: (result.left_eye.x / naturalWidth) * metrics.width,
						y: (result.left_eye.y / naturalHeight) * metrics.height
					},
					{
						x: (result.right_eye.x / naturalWidth) * metrics.width,
						y: (result.right_eye.y / naturalHeight) * metrics.height
					},
					config.i18n.ready
				);
			} catch (error) {
				console.error(error);
				state.manualEyesEnabled = true;
				positionEyeMarkers();
				setStatus(error && error.code === 'missing_credentials' ? config.i18n.faceppMissing : (error && error.code === 'no_face' ? config.i18n.noFace : config.i18n.alignFailed));
			}
		}

		function buildCropDataUrl() {
			if (!state.uploadSrc || !cropImage.naturalWidth || !cropImage.naturalHeight) {
				return '';
			}

			var frameWidth = cropFrame.clientWidth;
			var frameHeight = cropFrame.clientHeight;
			var coverScale = Math.max(frameWidth / cropImage.naturalWidth, frameHeight / cropImage.naturalHeight);
			var actualScale = coverScale * state.crop.zoom;
			var drawWidth = cropImage.naturalWidth * actualScale;
			var drawHeight = cropImage.naturalHeight * actualScale;
			var centerX = frameWidth / 2 + state.crop.x;
			var centerY = frameHeight / 2 + state.crop.y;
			var drawX = centerX - drawWidth / 2;
			var drawY = centerY - drawHeight / 2;
			var canvas = document.createElement('canvas');
			var ctx = canvas.getContext('2d');

			canvas.width = 700;
			canvas.height = Math.round(700 * 41 / 35);
			ctx.fillStyle = '#f5ede4';
			ctx.fillRect(0, 0, canvas.width, canvas.height);
			ctx.drawImage(
				cropImage,
				0,
				0,
				cropImage.naturalWidth,
				cropImage.naturalHeight,
				(drawX / frameWidth) * canvas.width,
				(drawY / frameHeight) * canvas.height,
				(drawWidth / frameWidth) * canvas.width,
				(drawHeight / frameHeight) * canvas.height
			);

			return canvas.toDataURL('image/jpeg', 0.92);
		}

		function updateCropPreview(statusMessage, keepCropperOpen) {
			var dataUrl = buildCropDataUrl();
			if (!dataUrl) {
				setStatus(config.i18n.cropNeeded);
				return;
			}

			setBaseImage(dataUrl, statusMessage, keepCropperOpen);
		}

		function applyCrop() {
			updateCropPreview(config.i18n.cropApplied, false);
		}

		function beginDrag(event) {
			if (!state.overlayLoaded || !state.baseLoaded) {
				return;
			}

			var point = event.touches ? event.touches[0] : event;
			state.drag = {
				x: point.clientX,
				y: point.clientY,
				startX: state.manual.manualX,
				startY: state.manual.manualY
			};
			overlay.classList.add('is-dragging');
			event.preventDefault();
		}

		function moveDrag(event) {
			if (!state.drag) {
				return;
			}

			var point = event.touches ? event.touches[0] : event;
			state.manual.manualX = clamp(state.drag.startX + (point.clientX - state.drag.x), -220, 220);
			state.manual.manualY = clamp(state.drag.startY + (point.clientY - state.drag.y), -220, 220);
			syncManualSliders();
			renderOverlay();
		}

		function endDrag() {
			state.drag = null;
			overlay.classList.remove('is-dragging');
		}

		function beginEyeDrag(event) {
			if (!state.baseLoaded) {
				return;
			}

			var marker = event.currentTarget;
			var point = event.touches ? event.touches[0] : event;
			state.eyeDrag = {
				key: marker.getAttribute('data-eye'),
				marker: marker
			};
			marker.classList.add('is-dragging');
			moveEyeDrag(point);
			event.preventDefault();
		}

		function moveEyeDrag(event) {
			if (!state.eyeDrag || !state.baseLoaded) {
				return;
			}

			var point = event.touches ? event.touches[0] : event;
			var metrics = getStageMetrics();
			var stageBox = stage.getBoundingClientRect();
			var x = clamp(point.clientX - stageBox.left - metrics.left, 0, metrics.width);
			var y = clamp(point.clientY - stageBox.top - metrics.top, 0, metrics.height);

			state.eyes[state.eyeDrag.key] = { x: x, y: y };
			applyEyes(state.eyes.left, state.eyes.right, config.i18n.manualApplied);

			if (event.preventDefault) {
				event.preventDefault();
			}
		}

		function endEyeDrag() {
			if (!state.eyeDrag) {
				return;
			}

			state.eyeDrag.marker.classList.remove('is-dragging');
			state.eyeDrag = null;
		}

		function toggleManualEyes() {
			if (!state.baseLoaded) {
				setStatus(state.uploadSrc ? config.i18n.cropPhoto : config.i18n.cropNeeded);
				return;
			}

			state.manualEyesEnabled = !state.manualEyesEnabled;
			positionEyeMarkers();
			setStatus(state.manualEyesEnabled ? config.i18n.manualEyes : config.i18n.cropApplied);
		}

		function beginCropDrag(event) {
			if (cropper.hidden) {
				return;
			}

			state.cropDragging = {
				x: event.clientX,
				y: event.clientY,
				startX: state.crop.x,
				startY: state.crop.y
			};
			event.preventDefault();
		}

		function moveCropDrag(event) {
			if (!state.cropDragging) {
				return;
			}

			state.crop.x = clamp(state.cropDragging.startX + (event.clientX - state.cropDragging.x), -260, 260);
			state.crop.y = clamp(state.cropDragging.startY + (event.clientY - state.cropDragging.y), -300, 300);
			syncCropSliders();
			renderCropper();
			updateCropPreview(config.i18n.cropPhoto, true);
		}

		function endCropDrag() {
			state.cropDragging = null;
		}

		openButton.addEventListener('click', function () {
			modal.hidden = false;
			document.body.style.overflow = 'hidden';
		});

		modal.addEventListener('click', function (event) {
			if (event.target.hasAttribute('data-close-modal')) {
				modal.hidden = true;
				document.body.style.overflow = '';
			}
		});

		fileInput.addEventListener('change', function (event) {
			var file = event.target.files && event.target.files[0];
			if (!file) {
				return;
			}

			state.uploadSrc = URL.createObjectURL(file);
			state.baseLoaded = false;
			state.manualEyesEnabled = false;
			baseImage.removeAttribute('src');
			baseImage.classList.remove('is-ready');
			stage.classList.remove('has-image');
			overlay.classList.remove('is-ready');
			cropImage.onload = function () {
				resetCrop();
				renderCropper();
				updateCropPreview(config.i18n.cropPhoto, true);
			};
			cropImage.src = state.uploadSrc;
		});

		applyCropButton.addEventListener('click', applyCrop);
		autoAlignButton.addEventListener('click', runAutoAlign);
		manualEyesButton.addEventListener('click', toggleManualEyes);
		resetButton.addEventListener('click', resetManual);

		root.querySelectorAll('.muukal-try-on-model').forEach(function (button) {
			button.addEventListener('click', function () {
				state.uploadSrc = '';
				cropper.hidden = true;
				setBaseImage(button.getAttribute('data-model-image'), config.i18n.modelSelected + ' ' + button.getAttribute('data-model-label'), false);
			});
		});

		manualSliders.forEach(function (slider) {
			slider.addEventListener('input', function () {
				var key = slider.getAttribute('data-control');
				state.manual[key] = parseFloat(slider.value);
				renderOverlay();
			});
		});

		cropSliders.forEach(function (slider) {
			slider.addEventListener('input', function () {
				var key = slider.getAttribute('data-crop-control');
				state.crop[key] = parseFloat(slider.value);
				renderCropper();
				updateCropPreview(config.i18n.cropPhoto, true);
			});
		});

		overlay.addEventListener('mousedown', beginDrag);
		overlay.addEventListener('touchstart', beginDrag, { passive: false });
		eyeMarkers.forEach(function (marker) {
			marker.addEventListener('mousedown', beginEyeDrag);
			marker.addEventListener('touchstart', beginEyeDrag, { passive: false });
		});
		cropFrame.addEventListener('mousedown', beginCropDrag);

		window.addEventListener('mousemove', moveDrag);
		window.addEventListener('touchmove', moveDrag, { passive: false });
		window.addEventListener('mouseup', endDrag);
		window.addEventListener('touchend', endDrag);
		window.addEventListener('mousemove', moveEyeDrag);
		window.addEventListener('touchmove', moveEyeDrag, { passive: false });
		window.addEventListener('mouseup', endEyeDrag);
		window.addEventListener('touchend', endEyeDrag);
		window.addEventListener('mousemove', moveCropDrag);
		window.addEventListener('mouseup', endCropDrag);

		loadOverlay();

		if (config.models && config.models[0] && config.models[0].image) {
			cropper.hidden = true;
			setBaseImage(config.models[0].image, config.i18n.modelSelected, false);
		} else if (config.overlayImage) {
			setStatus(config.i18n.cropNeeded);
		} else {
			setStatus(config.i18n.overlayMissing);
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.muukal-try-on-app').forEach(initApp);
	});
})();
