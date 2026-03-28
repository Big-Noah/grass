(function ($) {
	'use strict';

	$(document).on('click', '.facepp-tryon-pick', function (event) {
		event.preventDefault();
		var target = $(this).data('target');
		var targetId = $(this).data('target-id');
		var pngOnly = String($(this).data('facepp-png') || '') === '1';
		var frame = wp.media({
			title: 'Choose image',
			button: { text: 'Use image' },
			multiple: false,
			library: { type: 'image' }
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();

			if (pngOnly && attachment.mime !== 'image/png') {
				window.alert('Please choose a transparent PNG for the try-on frame.');
				return;
			}

			$(target).val(attachment.url).trigger('change');
			if (targetId) {
				$(targetId).val(attachment.id).trigger('change');
			}
		});

		frame.open();
	});
})(jQuery);

