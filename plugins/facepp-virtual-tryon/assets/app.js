(function () {
	'use strict';

	var config = window.faceppTryonConfig || {};

	function distance(a, b) {
		var dx = b.x - a.x;
		var dy = b.y - a.y;
		return Math.sqrt(dx * dx + dy * dy);
	}

	function clamp(value, min, max) {
		return Math.min(max, Math.max(min, value));
	}

	function initApp(root) {
		var openBtn = root.querySelector('.facepp-tryon-open');
		var modal = root.querySelector('.facepp-tryon-modal');
		var photo = root.querySelector('.facepp-tryon-photo');
		var frame = root.querySelector('.facepp-tryon-frame');
		var empty = root.querySelector('.facepp-tryon-empty');
		var fileInput = root.querySelector('.facepp-tryon-file');
		var status = root.querySelector('.facepp-tryon-status');
		var autoBtn = root.querySelector('.facepp-tryon-auto');
		var resetBtn = root.querySelector('.facepp-tryon-reset');
		var framesBox = root.querySelector('.facepp-tryon-frames');
		var sliders = root.querySelectorAll('[data-control]');

		var state = {
			photoLoaded: false,
			frameLoaded: false,
			photoSrc: '',
			drag: null,
			selectedFrame: null,
			auto: { x: 0, y: 0, rotate: 0, width: 0 },
			manual: { x: 0, y: 0, rotate: 0, scale: 1 },
		};

		function setStatus(message) {
			status.textContent = message || '';
		}

		function syncSliders() {
			sliders.forEach(function (slider) {
				var key = slider.getAttribute('data-control');
				slider.value = state.manual[key];
			});
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
			var m = getMetrics();
			var show = state.photoLoaded && state.frameLoaded && m.width > 0 && m.height > 0;
			empty.style.display = state.photoLoaded ? 'none' : 'flex';
			if (!show) {
				frame.classList.remove('is-ready');
				return;
			}

			var width = state.auto.width * state.manual.scale;
			var x = m.left + state.auto.x + state.manual.x;
			var y = m.top + state.auto.y + state.manual.y;
			var rotate = state.auto.rotate + state.manual.rotate;

			frame.style.width = width + 'px';
			frame.style.left = x + 'px';
			frame.style.top = y + 'px';
			frame.style.transform = 'rotate(' + rotate + 'deg)';
			frame.classList.add('is-ready');
		}

		function autoCenter() {
			if (!state.photoLoaded || !state.frameLoaded) {
				return;
			}
			var m = getMetrics();
			state.auto.width = m.width * 0.56;
			state.auto.x = (m.width - state.auto.width) / 2;
			state.auto.y = m.height * 0.34;
			state.auto.rotate = 0;
			state.manual = { x: 0, y: 0, rotate: 0, scale: 1 };
			syncSliders();
			render();
		}

		function applyEyes(leftEye, rightEye) {
			var m = getMetrics();
			var eyeDistance = distance(leftEye, rightEye);
			var widthFactor = state.selectedFrame && state.selectedFrame.widthFactor ? state.selectedFrame.widthFactor : 2.15;
			var yOffset = state.selectedFrame && state.selectedFrame.yOffset ? state.selectedFrame.yOffset : 0.02;
			var width = eyeDistance * widthFactor;
			var height = width * ((frame.naturalHeight || 1) / (frame.naturalWidth || 1));
			var cx = (leftEye.x + rightEye.x) / 2;
			var cy = (leftEye.y + rightEye.y) / 2;
			var rotate = Math.atan2(rightEye.y - leftEye.y, rightEye.x - leftEye.x) * (180 / Math.PI);

			state.auto.width = width;
			state.auto.x = cx - width / 2;
			state.auto.y = cy - height / 2 + eyeDistance * yOffset;
			state.auto.rotate = rotate;
			state.manual = { x: 0, y: 0, rotate: 0, scale: 1 };
			syncSliders();
			render();
		}

		function buildFrameButtons() {
			framesBox.innerHTML = '';
			(config.frames || []).forEach(function (item, idx) {
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'facepp-tryon-frame-item';
				btn.innerHTML = '<img alt="" src="' + item.url + '"><span>' + item.name + '</span>';
				btn.addEventListener('click', function () {
					state.selectedFrame = item;
					framesBox.querySelectorAll('.facepp-tryon-frame-item').forEach(function (x) {
						x.classList.remove('is-active');
					});
					btn.classList.add('is-active');
					frame.onload = function () {
						state.frameLoaded = true;
						autoCenter();
					};
					frame.src = item.url;
					setStatus(config.i18n.frameSelected);
				});
				framesBox.appendChild(btn);
				if (idx === 0) {
					btn.click();
				}
			});
		}

		async function detectFace() {
			var formData = new window.FormData();
			formData.append('action', 'facepp_tryon_detect');
			formData.append('nonce', config.ajaxNonce);
			formData.append('image', photo.src);

			var res = await window.fetch(config.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});
			var json = await res.json();
			if (!res.ok || !json || !json.success || !json.data) {
				var err = new Error((json && json.data && json.data.message) || 'Detect failed');
				err.code = json && json.data && json.data.code ? json.data.code : 'unknown';
				throw err;
			}
			return json.data;
		}

		async function runAutoAlign() {
			if (!state.photoLoaded) {
				setStatus(config.i18n.noPhoto);
				return;
			}
			if (!state.frameLoaded || !state.selectedFrame) {
				setStatus(config.i18n.noFrame);
				return;
			}
			try {
				setStatus(config.i18n.detecting);
				var data = await detectFace();
				var m = getMetrics();
				var nw = photo.naturalWidth || m.width;
				var nh = photo.naturalHeight || m.height;

				applyEyes(
					{ x: (data.left_eye.x / nw) * m.width, y: (data.left_eye.y / nh) * m.height },
					{ x: (data.right_eye.x / nw) * m.width, y: (data.right_eye.y / nh) * m.height }
				);
				setStatus(config.i18n.aligned);
			} catch (e) {
				if (e && e.code === 'missing_credentials') {
					setStatus(config.i18n.missingCreds);
				} else if (e && (e.code === 'no_face' || e.code === 'no_eye_landmark')) {
					setStatus(config.i18n.noFace);
				} else {
					setStatus(config.i18n.detectFailed);
				}
			}
		}

		function resetManual() {
			state.manual = { x: 0, y: 0, rotate: 0, scale: 1 };
			syncSliders();
			render();
			setStatus(config.i18n.manualReset);
		}

		function beginDrag(event) {
			if (!state.frameLoaded || !state.photoLoaded) {
				return;
			}
			var p = event.touches ? event.touches[0] : event;
			state.drag = {
				x: p.clientX,
				y: p.clientY,
				startX: state.manual.x,
				startY: state.manual.y,
			};
			frame.classList.add('is-dragging');
			event.preventDefault();
		}

		function moveDrag(event) {
			if (!state.drag) {
				return;
			}
			var p = event.touches ? event.touches[0] : event;
			state.manual.x = clamp(state.drag.startX + (p.clientX - state.drag.x), -260, 260);
			state.manual.y = clamp(state.drag.startY + (p.clientY - state.drag.y), -260, 260);
			syncSliders();
			render();
			if (event.preventDefault) {
				event.preventDefault();
			}
		}

		function endDrag() {
			state.drag = null;
			frame.classList.remove('is-dragging');
		}

		openBtn.addEventListener('click', function () {
			modal.hidden = false;
			document.body.style.overflow = 'hidden';
			render();
		});

		modal.addEventListener('click', function (event) {
			if (event.target.getAttribute('data-close') === '1') {
				modal.hidden = true;
				document.body.style.overflow = '';
			}
		});

		fileInput.addEventListener('change', function (event) {
			var file = event.target.files && event.target.files[0];
			if (!file) {
				return;
			}
			state.photoSrc = URL.createObjectURL(file);
			photo.onload = function () {
				state.photoLoaded = true;
				autoCenter();
				setStatus(config.i18n.photoReady);
			};
			photo.src = state.photoSrc;
		});

		autoBtn.addEventListener('click', runAutoAlign);
		resetBtn.addEventListener('click', resetManual);

		sliders.forEach(function (slider) {
			slider.addEventListener('input', function () {
				var key = slider.getAttribute('data-control');
				state.manual[key] = parseFloat(slider.value);
				render();
			});
		});

		frame.addEventListener('mousedown', beginDrag);
		frame.addEventListener('touchstart', beginDrag, { passive: false });
		window.addEventListener('mousemove', moveDrag);
		window.addEventListener('touchmove', moveDrag, { passive: false });
		window.addEventListener('mouseup', endDrag);
		window.addEventListener('touchend', endDrag);
		window.addEventListener('resize', render);

		buildFrameButtons();
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.facepp-tryon-app').forEach(initApp);
	});
})();

