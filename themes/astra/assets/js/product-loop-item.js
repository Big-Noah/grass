(function () {
	var smartFitCache = new Map();

	function averageCornerColor(data, width, height) {
		var samples = [
			0,
			width - 1,
			(height - 1) * width,
			height * width - 1
		];
		var red = 0;
		var green = 0;
		var blue = 0;
		var alpha = 0;

		samples.forEach(function (index) {
			var offset = index * 4;
			red += data[offset];
			green += data[offset + 1];
			blue += data[offset + 2];
			alpha += data[offset + 3];
		});

		return {
			r: red / samples.length,
			g: green / samples.length,
			b: blue / samples.length,
			a: alpha / samples.length
		};
	}

	function colorDistance(pixel, background) {
		var dr = pixel.r - background.r;
		var dg = pixel.g - background.g;
		var db = pixel.b - background.b;
		return Math.sqrt((dr * dr) + (dg * dg) + (db * db));
	}

	function buildSmartFitImage(src) {
		if (!src || src.indexOf('data:') === 0) {
			return Promise.resolve(src);
		}

		if (smartFitCache.has(src)) {
			return smartFitCache.get(src);
		}

		var promise = new Promise(function (resolve) {
			var image = new Image();

			image.crossOrigin = 'anonymous';
			image.decoding = 'async';

			image.onload = function () {
				var canvas = document.createElement('canvas');
				var context;
				var imageData;
				var background;
				var minX = image.naturalWidth;
				var minY = image.naturalHeight;
				var maxX = -1;
				var maxY = -1;
				var x;
				var y;

				canvas.width = image.naturalWidth;
				canvas.height = image.naturalHeight;
				context = canvas.getContext('2d', { willReadFrequently: true });

				if (!context) {
					resolve(src);
					return;
				}

				context.drawImage(image, 0, 0);
				imageData = context.getImageData(0, 0, canvas.width, canvas.height);
				background = averageCornerColor(imageData.data, canvas.width, canvas.height);

				for (y = 0; y < canvas.height; y += 1) {
					for (x = 0; x < canvas.width; x += 1) {
						var offset = ((y * canvas.width) + x) * 4;
						var alpha = imageData.data[offset + 3];
						var pixel = {
							r: imageData.data[offset],
							g: imageData.data[offset + 1],
							b: imageData.data[offset + 2]
						};

						if (alpha < 24 || colorDistance(pixel, background) < 18) {
							continue;
						}

						if (x < minX) {
							minX = x;
						}
						if (y < minY) {
							minY = y;
						}
						if (x > maxX) {
							maxX = x;
						}
						if (y > maxY) {
							maxY = y;
						}
					}
				}

				if (maxX <= minX || maxY <= minY) {
					resolve(src);
					return;
				}

				var boundsWidth = maxX - minX + 1;
				var boundsHeight = maxY - minY + 1;
				var paddingX = Math.max(6, Math.round(boundsWidth * 0.04));
				var paddingY = Math.max(6, Math.round(boundsHeight * 0.08));
				var cropX = Math.max(0, minX - paddingX);
				var cropY = Math.max(0, minY - paddingY);
				var cropWidth = Math.min(canvas.width - cropX, boundsWidth + (paddingX * 2));
				var cropHeight = Math.min(canvas.height - cropY, boundsHeight + (paddingY * 2));
				var cropCanvas = document.createElement('canvas');
				var cropContext;

				cropCanvas.width = cropWidth;
				cropCanvas.height = cropHeight;
				cropContext = cropCanvas.getContext('2d');

				if (!cropContext) {
					resolve(src);
					return;
				}

				cropContext.drawImage(
					canvas,
					cropX,
					cropY,
					cropWidth,
					cropHeight,
					0,
					0,
					cropWidth,
					cropHeight
				);

				resolve(cropCanvas.toDataURL('image/png'));
			};

			image.onerror = function () {
				resolve(src);
			};

			image.src = src;
		});

		smartFitCache.set(src, promise);
		return promise;
	}

	function applySmartFit(image) {
		if (!image) {
			return;
		}

		var source = image.getAttribute('data-fit-source') || image.getAttribute('data-default-src') || image.getAttribute('data-original') || image.getAttribute('src') || '';

		if (!source) {
			return;
		}

		image.setAttribute('data-fit-source', source);

		buildSmartFitImage(source).then(function (processedSrc) {
			if (!processedSrc || image.getAttribute('data-fit-source') !== source) {
				return;
			}

			image.setAttribute('src', processedSrc);
			image.setAttribute('data-original', processedSrc);
		});
	}

	function applySmartFitToCard(card) {
		if (!card) {
			return;
		}

		card.querySelectorAll('.muukal-product-image').forEach(function (image) {
			applySmartFit(image);
		});
	}

	function updateCard(card, button) {
		var links = card.querySelectorAll('.muukal-product-link');
		var primary = card.querySelector('.muukal-product-image-primary');
		var secondary = card.querySelector('.muukal-product-image-secondary');
		var price = card.querySelector('.g-price');
		var originalPrice = card.querySelector('.o_price');
		var badge = card.querySelector('.buyout-p');
		var badgeText = badge ? badge.querySelector('.sale-text') : null;
		var buttons = card.querySelectorAll('.muukal-swatch-button');
		var mainSrc = button.getAttribute('data-main') || '';
		var secondarySrc = button.getAttribute('data-secondary') || mainSrc;
		var linkHref = button.getAttribute('data-link') || '';
		var priceHtml = button.getAttribute('data-price-html') || '';
		var originalPriceHtml = button.getAttribute('data-original-price-html') || '';
		var discount = parseInt(button.getAttribute('data-discount') || '0', 10);

		buttons.forEach(function (item) {
			item.classList.remove('is-active', 'choose-color');
		});

		button.classList.add('is-active', 'choose-color');

		if (linkHref) {
			links.forEach(function (link) {
				link.setAttribute('href', linkHref);
			});
		}

		if (primary && mainSrc) {
			primary.setAttribute('data-fit-source', mainSrc);
			primary.setAttribute('src', mainSrc);
			primary.setAttribute('data-original', mainSrc);
			primary.setAttribute('data-default-src', mainSrc);
			applySmartFit(primary);
		}

		if (secondary && secondarySrc) {
			secondary.setAttribute('data-fit-source', secondarySrc);
			secondary.setAttribute('src', secondarySrc);
			secondary.setAttribute('data-original', secondarySrc);
			secondary.setAttribute('data-default-src', secondarySrc);
			applySmartFit(secondary);
		}

		if (price) {
			price.innerHTML = priceHtml;
		}

		if (originalPrice) {
			if (discount > 0 && originalPriceHtml) {
				originalPrice.innerHTML = originalPriceHtml;
				originalPrice.style.display = '';
			} else {
				originalPrice.style.display = 'none';
			}
		}

		if (badge && badgeText) {
			if (discount > 0) {
				badge.style.display = '';
				badgeText.textContent = discount + '% OFF';
			} else {
				badge.style.display = 'none';
			}
		}
	}

	document.addEventListener('click', function (event) {
		var button = event.target.closest('.muukal-swatch-button');
		if (!button) {
			return;
		}

		var card = button.closest('.muukal-product-card');
		if (!card) {
			return;
		}

		event.preventDefault();
		updateCard(card, button);
	});

	document.querySelectorAll('.muukal-product-card').forEach(function (card) {
		applySmartFitToCard(card);
	});
})();
