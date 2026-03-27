document.addEventListener('DOMContentLoaded', function () {
	var body = document.body;

	if (!body || !body.classList.contains('woocommerce-account') || body.classList.contains('logged-in')) {
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
