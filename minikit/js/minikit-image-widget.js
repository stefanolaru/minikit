var j = jQuery.noConflict();

jQuery(document).ready(function ($) {

    $(document).on('click', '.select_minikit_image', function (e) {

        var self = this;
        var p = $(this).closest('.widget');
        //
        e.preventDefault();

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image',
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            attachments = file_frame.state().get('selection').toJSON();

            $.each(attachments, function (k, v) {
            	console.log(v.sizes);
                $('.image-container', p).html('<img src="' + v.sizes['full'].url + '" style="margin: 0 0 10px 0; float: left; max-width: 100%; height: auto;" />');
                $('.minikit_image_id', p).val(v.id);
            });
            // close frame
            file_frame.close();
        });

        // Finally, open the modal
        file_frame.open();

    });

});