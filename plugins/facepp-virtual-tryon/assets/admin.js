(function ($) {
	'use strict';

	$(document).on('click', '.facepp-tryon-pick', function (event) {
		event.preventDefault();
		var target = $(this).data('target');
		var frame = wp.media({
			title: 'Choose image',
			button: { text: 'Use image' },
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$(target).val(attachment.url).trigger('change');
		});

		frame.open();
	});
})(jQuery);

