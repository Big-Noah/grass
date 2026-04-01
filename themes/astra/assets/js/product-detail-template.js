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

	function syncThumbLayout(root) {
		var thumbList = root.querySelector('[data-gallery-thumb-list]');

		if (!thumbList) {
			return;
		}

		var thumbCount = thumbList.querySelectorAll('[data-gallery-thumb]').length;
		thumbList.classList.toggle('is-single-thumb', thumbCount <= 1);
		thumbList.classList.toggle('is-multi-thumb', thumbCount > 1);
	}

	function updateGalleryCount(root, activeIndex, total) {
		root.querySelectorAll('[data-gallery-count]').forEach(function (node) {
			node.textContent = String(activeIndex) + ' / ' + String(total || 1);
		});
	}

	function bindGalleryThumbs(root) {
		var main = root.querySelector('[data-gallery-main]');
		var thumbList = root.querySelector('[data-gallery-thumb-list]');

		if (!main || !thumbList) {
			return;
		}

		syncThumbLayout(root);

		thumbList.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				main.src = thumb.getAttribute('data-gallery-thumb');
				var thumbs = thumbList.querySelectorAll('[data-gallery-thumb]');
				thumbs.forEach(function (entry) {
					entry.classList.remove('is-active');
				});
				thumb.classList.add('is-active');
				updateGalleryCount(root, Array.prototype.indexOf.call(thumbs, thumb) + 1, thumbs.length);
			});
		});

		updateGalleryCount(root, 1, thumbList.querySelectorAll('[data-gallery-thumb]').length);
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

		updateGalleryCount(root, 1, images && images.length ? images.length : 1);
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
		var likeCountMobile = root.querySelector('[data-product-like-count-mobile]');
		var color = root.querySelector('[data-product-color]');
		var colorMobile = root.querySelector('[data-product-color-mobile]');
		var priceMobile = root.querySelector('[data-product-price-mobile]');
		var shortNameMobile = root.querySelector('[data-product-short-name-mobile]');
		var subNameMobile = root.querySelector('[data-product-sub-name-mobile]');

		if (shortName) {
			shortName.textContent = variant.shortName || '';
		}

		if (shortNameMobile) {
			shortNameMobile.textContent = variant.shortName || '';
		}

		if (subName) {
			subName.textContent = variant.subName || '';
		}

		if (subNameMobile) {
			subNameMobile.textContent = variant.subName || '';
		}

		if (price) {
			price.innerHTML = variant.priceHtml || '';
		}

		if (priceMobile) {
			priceMobile.innerHTML = variant.priceHtml || '';
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

		if (likeCountMobile) {
			likeCountMobile.textContent = variant.likeCount || '';
		}

		if (color) {
			color.textContent = variant.colorName || '';
		}

		if (colorMobile) {
			colorMobile.textContent = variant.colorName || '';
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

	function initShare(root) {
		root.querySelectorAll('[data-product-share]').forEach(function (button) {
			button.addEventListener('click', function () {
				var titleNode = root.querySelector('[data-product-sub-name-mobile]') || root.querySelector('[data-product-sub-name]');
				var shareData = {
					title: document.title,
					text: titleNode ? titleNode.textContent : document.title,
					url: window.location.href
				};

				if (navigator.share) {
					navigator.share(shareData).catch(function () {});
					return;
				}

				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(window.location.href).then(function () {
						button.classList.add('is-copied');
						window.setTimeout(function () {
							button.classList.remove('is-copied');
						}, 1600);
					}).catch(function () {});
				}
			});
		});
	}

	function initProductDetail(root) {
		var data = parseData(root);

		if (!data || !data.variants) {
		bindGalleryThumbs(root);
		initMeasurements(root);
		initSizeGuide(root);
		initShare(root);
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
		initShare(root);

		if (data.defaultVariant) {
			applyVariant(root, data.defaultVariant, data);
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.muukal-product-detail-template').forEach(initProductDetail);
	});
})();
