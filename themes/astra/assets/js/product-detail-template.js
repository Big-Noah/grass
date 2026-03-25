(function () {
	'use strict';

	function initGallery(root) {
		var main = root.querySelector('[data-gallery-main]');
		if (!main) {
			return;
		}

		root.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				main.src = thumb.getAttribute('data-gallery-thumb');
				root.querySelectorAll('[data-gallery-thumb]').forEach(function (item) {
					item.classList.remove('is-active');
				});
				thumb.classList.add('is-active');
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.muukal-product-detail-template [data-gallery]').forEach(initGallery);
	});
})();
