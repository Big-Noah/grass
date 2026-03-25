(function () {
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
			primary.setAttribute('src', mainSrc);
			primary.setAttribute('data-original', mainSrc);
			primary.setAttribute('data-default-src', mainSrc);
		}

		if (secondary && secondarySrc) {
			secondary.setAttribute('src', secondarySrc);
			secondary.setAttribute('data-original', secondarySrc);
			secondary.setAttribute('data-default-src', secondarySrc);
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
})();
