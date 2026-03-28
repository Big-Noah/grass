(function ($) {
	'use strict';

	function renderModelEyes($preview) {
		var raw = $preview.attr('data-facepp-eyes') || '';
		var data = null;
		var $img = $preview.find('.facepp-admin-preview__image');

		$preview.find('.facepp-admin-preview__eye').remove();

		if (!raw || !$img.length || !$img.get(0).naturalWidth || !$img.get(0).naturalHeight) {
			return;
		}

		try {
			data = JSON.parse(raw);
		} catch (error) {
			data = null;
		}

		if (!data || !data.left_eye || !data.right_eye) {
			return;
		}

		var displayWidth = $img.width();
		var displayHeight = $img.height();
		var naturalWidth = $img.get(0).naturalWidth;
		var naturalHeight = $img.get(0).naturalHeight;

		[['left_eye', '#ff4d6d'], ['right_eye', '#4aa2d3']].forEach(function (entry) {
			var point = data[entry[0]];

			if (!point) {
				return;
			}

			$('<span class="facepp-admin-preview__eye"></span>')
				.css({
					position: 'absolute',
					left: ((point.x / naturalWidth) * displayWidth - 5) + 'px',
					top: ((point.y / naturalHeight) * displayHeight - 5) + 'px',
					width: '10px',
					height: '10px',
					borderRadius: '50%',
					background: entry[1],
					border: '2px solid #fff',
					boxShadow: '0 0 0 1px rgba(0,0,0,0.15)'
				})
				.appendTo($preview);
		});
	}

	function updatePreview($input) {
		var url = String($input.val() || '').trim();
		var $cell = $input.closest('td');
		var $preview = $cell.find('.facepp-admin-preview').first();

		if (!$preview.length) {
			return;
		}

		var $img = $preview.find('.facepp-admin-preview__image');

		if (!url) {
			$img.remove();
			$preview.find('.facepp-admin-preview__eye').remove();
			return;
		}

		if (!$img.length) {
			$img = $('<img class="facepp-admin-preview__image" alt="">').css({
				display: 'block',
				width: $preview.hasClass('facepp-admin-preview--model') ? '100%' : 'auto',
				height: 'auto',
				maxWidth: $preview.hasClass('facepp-admin-preview--model') ? '100%' : '140px',
				maxHeight: $preview.hasClass('facepp-admin-preview--model') ? 'none' : '60px'
			}).appendTo($preview);
		}

		$img.off('load.faceppPreview').on('load.faceppPreview', function () {
			renderModelEyes($preview);
		});
		$img.attr('src', url);

		if ($img.get(0).complete) {
			renderModelEyes($preview);
		}
	}

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

	$(document).on('input change', 'input[id^="facepp-frame-url-"], input[id^="facepp-model-url-"]', function () {
		updatePreview($(this));
	});

	$(function () {
		$('input[id^="facepp-frame-url-"], input[id^="facepp-model-url-"]').each(function () {
			updatePreview($(this));
		});
	});
})(jQuery);

