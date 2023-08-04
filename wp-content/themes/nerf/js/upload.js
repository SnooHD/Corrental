jQuery(document).ready(function($){
	"use strict";
	var nerf_upload;
	var nerf_selector;

	function nerf_add_file(event, selector) {

		var upload = $(".uploaded-file"), frame;
		var $el = $(this);
		nerf_selector = selector;

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( nerf_upload ) {
			nerf_upload.open();
			return;
		} else {
			// Create the media frame.
			nerf_upload = wp.media.frames.nerf_upload =  wp.media({
				// Set the title of the modal.
				title: "Select Image",

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: "Selected",
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			// When an image is selected, run a callback.
			nerf_upload.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = nerf_upload.state().get('selection').first();

				nerf_upload.close();
				nerf_selector.find('.upload_image').val(attachment.attributes.url).change();
				if ( attachment.attributes.type == 'image' ) {
					nerf_selector.find('.nerf_screenshot').empty().hide().prepend('<img src="' + attachment.attributes.url + '">').slideDown('fast');
				}
			});

		}
		// Finally, open the modal.
		nerf_upload.open();
	}

	function nerf_remove_file(selector) {
		selector.find('.nerf_screenshot').slideUp('fast').next().val('').trigger('change');
	}
	
	$('body').on('click', '.nerf_upload_image_action .remove-image', function(event) {
		nerf_remove_file( $(this).parent().parent() );
	});

	$('body').on('click', '.nerf_upload_image_action .add-image', function(event) {
		nerf_add_file(event, $(this).parent().parent());
	});

});