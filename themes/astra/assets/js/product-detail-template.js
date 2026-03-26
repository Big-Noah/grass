(function () {
	'use strict';

	function parseData(root) {
		var node = root.querySelector('.muukal-product-detail-data');

		if (!node) {
			return null;
		}

		try {
			return JSON.parse(node.textContent || '{}');
		} catch (error) {
			return null;
		}
	}

	function bindGalleryThumbs(root) {
		var main = root.querySelector('[data-gallery-main]');
		var thumbList = root.querySelector('[data-gallery-thumb-list]');

		if (!main || !thumbList) {
			return;
		}

		thumbList.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				main.src = thumb.getAttribute('data-gallery-thumb');
				thumbList.querySelectorAll('[data-gallery-thumb]').forEach(function (entry) {
					entry.classList.remove('is-active');
				});
				thumb.classList.add('is-active');
			});
		});
	}

	function renderThumbs(root, images, altText) {
		var thumbList = root.querySelector('[data-gallery-thumb-list]');

		if (!thumbList) {
			return;
		}

		thumbList.innerHTML = '';

		(images || []).forEach(function (image, index) {
			var button = document.createElement('button');
			button.type = 'button';
			button.className = 'lit_gpic col-xl-2' + (index === 0 ? ' is-active' : '');
			button.setAttribute('data-gallery-thumb', image);
			button.innerHTML = '<img src="' + image + '" alt="' + (altText || '') + '">';
			thumbList.appendChild(button);
		});

		bindGalleryThumbs(root);

		var main = root.querySelector('[data-gallery-main]');
		if (main && images && images.length) {
			main.src = images[0];
		}
	}

	function dispatchVariant(root, variant, data) {
		if (!variant) {
			return;
		}

		document.dispatchEvent(
			new window.CustomEvent('muukal:productColorChanged', {
				detail: {
					productId: data.productId,
					colorId: variant.colorId,
					colorLabel: variant.colorName,
					colorSlug: variant.key,
					tryonKey: variant.tryonKey,
					price: variant.framePriceRaw,
					imageUrl: variant.frameImage
				}
			})
		);
	}

	function applyVariant(root, key, data) {
		var variants = data && data.variants ? data.variants : {};
		var variant = variants[key];

		if (!variant) {
			return;
		}

		root.querySelectorAll('[data-variant-key]').forEach(function (button) {
			var active = button.getAttribute('data-variant-key') === key;
			button.classList.toggle('choose-color', active);
			button.classList.toggle('is-active', active);
		});

		var shortName = root.querySelector('[data-product-short-name]');
		var subName = root.querySelector('[data-product-sub-name]');
		var price = root.querySelector('[data-product-price]');
		var regularPrice = root.querySelector('[data-product-regular-price]');
		var discount = root.querySelector('[data-product-discount]');
		var likeCount = root.querySelector('[data-product-like-count]');
		var color = root.querySelector('[data-product-color]');

		if (shortName) {
			shortName.textContent = variant.shortName || '';
		}

		if (subName) {
			subName.textContent = variant.subName || '';
		}

		if (price) {
			price.innerHTML = variant.priceHtml || '';
		}

		if (regularPrice) {
			regularPrice.innerHTML = variant.regularPriceHtml || '';
			regularPrice.style.display = variant.regularPriceHtml ? '' : 'none';
		}

		if (discount) {
			discount.textContent = (variant.discount || 0) + '% OFF';
			discount.style.display = variant.discount ? '' : 'none';
		}

		if (likeCount) {
			likeCount.textContent = variant.likeCount || '';
		}

		if (color) {
			color.textContent = variant.colorName || '';
		}

		renderThumbs(root, variant.gallery || [], variant.subName || variant.shortName || '');
		dispatchVariant(root, variant, data);
	}

	function initMeasurements(root) {
		var toggle = root.querySelector('[data-measurement-toggle]');
		var showingInches = false;

		if (!toggle) {
			return;
		}

		toggle.addEventListener('click', function () {
			showingInches = !showingInches;
			root.querySelectorAll('.unit-mm').forEach(function (entry) {
				entry.style.display = showingInches ? 'none' : '';
			});
			root.querySelectorAll('.unit-inch').forEach(function (entry) {
				entry.style.display = showingInches ? '' : 'none';
			});
			toggle.textContent = showingInches ? 'Show in millimeters' : 'Show in inches';
		});
	}

	function initSizeGuide(root) {
		var modal = root.querySelector('.muukal-product-detail-modal');

		if (!modal) {
			return;
		}

		root.querySelectorAll('.muukal-size-guide-trigger').forEach(function (trigger) {
			trigger.addEventListener('click', function () {
				modal.hidden = false;
				document.body.style.overflow = 'hidden';
			});
		});

		modal.querySelectorAll('[data-detail-close]').forEach(function (closer) {
			closer.addEventListener('click', function () {
				modal.hidden = true;
				document.body.style.overflow = '';
			});
		});
	}

	function initProductDetail(root) {
		var data = parseData(root);

		if (!data || !data.variants) {
			bindGalleryThumbs(root);
			return;
		}

		root.querySelectorAll('[data-variant-key]').forEach(function (button) {
			button.addEventListener('click', function () {
				applyVariant(root, button.getAttribute('data-variant-key'), data);
			});
		});

		bindGalleryThumbs(root);
		initMeasurements(root);
		initSizeGuide(root);

		if (data.defaultVariant) {
			applyVariant(root, data.defaultVariant, data);
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.muukal-product-detail-template').forEach(initProductDetail);
	});
})();
