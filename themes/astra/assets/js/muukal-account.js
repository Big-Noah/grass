document.addEventListener('DOMContentLoaded', function () {
	var body = document.body;

	if (!body || !body.classList.contains('woocommerce-account')) {
		return;
	}

	if (body.classList.contains('logged-in')) {
		var navigationList = document.querySelector('.woocommerce-MyAccount-navigation ul');
		var content = document.querySelector('.woocommerce-MyAccount-content');
		var activeLink = document.querySelector('.woocommerce-MyAccount-navigation li.is-active a');

		if (navigationList) {
			var items = Array.from(navigationList.children);
			var groups = [
				{
					title: 'My Order',
					match: function (item) {
						return item.className.indexOf('--orders') !== -1 || item.className.indexOf('--downloads') !== -1;
					}
				},
				{
					title: 'My Account',
					match: function (item) {
						return item.className.indexOf('--dashboard') !== -1 || item.className.indexOf('--edit-account') !== -1 || item.className.indexOf('--edit-address') !== -1 || item.className.indexOf('--payment-methods') !== -1;
					}
				},
				{
					title: 'Sign Out',
					match: function (item) {
						return item.className.indexOf('--customer-logout') !== -1;
					}
				}
			];

			items.forEach(function (item) {
				item.remove();
			});

			groups.forEach(function (group) {
				var matchingItems = items.filter(group.match);

				if (!matchingItems.length) {
					return;
				}

				var heading = document.createElement('li');
				heading.className = 'muukal-account-nav-group';
				heading.textContent = group.title;
				navigationList.appendChild(heading);

				matchingItems.forEach(function (item) {
					navigationList.appendChild(item);
				});
			});
		}

		if (content && activeLink && !content.querySelector('.muukal-account-content-title')) {
			var title = document.createElement('h2');
			title.className = 'muukal-account-content-title';
			title.textContent = activeLink.textContent.trim();
			content.prepend(title);
		}

		return;
	}

	var mappings = [
		{ selector: '#username', placeholder: 'Email' },
		{ selector: '#password', placeholder: 'Password' },
		{ selector: '#reg_billing_first_name', placeholder: 'First Name' },
		{ selector: '#reg_billing_last_name', placeholder: 'Last Name' },
		{ selector: '#reg_email', placeholder: 'Email' },
		{ selector: '#reg_password', placeholder: 'Password' }
	];

	mappings.forEach(function (item) {
		var field = document.querySelector(item.selector);
		if (field) {
			field.setAttribute('placeholder', item.placeholder);
		}
	});
});
