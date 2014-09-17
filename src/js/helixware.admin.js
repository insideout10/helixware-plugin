jQuery(function ($) {

    // Capture the preset *filters.max_file_size*.
    var url, maxFileSize, fileDataName;

    // Create the select with the post options, this is used when building the asset uploaded line.
    var postTypeSelect = '<select name="post_type">';
    for ( var type in hewa_admin_options.post_types ) {

        postTypeSelect += '<option value="' + type + '"' + ('post' === type ? ' selected' : '') + '>' +
            hewa_admin_options.post_types[type].title + '</option>';

    }
    postTypeSelect += '</select>';


    /**
     * Check whether the specified file is video or not.
     *
     * @param file The file.
     * @returns {boolean} True if is a video, otherwise false.
     */
    var isVideo = function( file ) {

        return ( 0 === file.type.indexOf("video/") );

    }

    /**
     * Our handler for the *fileUploaded* method from the uploader. At the end of the process, the handler set back
     * again to the WordPress handler.
     *
     * @param file The uploaded file.
     * @param response The response from the server.
     */
    var fileUploaded = function( up, file, response ) {

        // If it's not a video we have nothing to do here.
        if ( ! isVideo( file ) ) {
            return;
        }

        // Get the asset data, the element where the data is shown and a reference to the div containing the meta-data.
        var asset  = $.parseJSON( response.response );
        var elem   = $('#media-item-' + file.id);
        var divId  = 'hewa-asset-' + asset.id;

        elem.html(
            '<img src="' + hewa_admin_options.form_action + '?action=hewa_still_image&id=' + asset.id +
                '&w=150&tc=10" class="pinkynail" onerror="this.style.visibility = \'hidden\';"><div class="filename new">' +
                '<span class="title">' + asset.title + '</span>' +
                '<div id="' + divId + '" class="hewa-asset">' +
                postTypeSelect +
                '<input type="hidden" name="asset_id" value="' + asset.id +'">' +
                '<input type="text" name="post_title" placeholder="' + hewa_admin_options.labels.title +
                '" value="' + asset.title +'">' +
                '<input type="text" name="post_tags" placeholder="' + hewa_admin_options.labels.tags + '">' +
                '<button type="button" class="hewa-submit-button button">' + hewa_admin_options.labels.save + '</button>' +
                '</div></div>' );


        $( '#' + divId + ' .hewa-submit-button').click( function( event ) {
            
            // After the button is clicked, disable it and notify user that saving is in progress.
            $( event.target ).prop('disabled', true)
                .text('Saving...');

            var div = $( event.target).parent();

            var assetData   = {
                'assetId'  : div.children('input[name="asset_id"]').val(),
                'postType' : div.children('select[name="post_type"]').val(),
                'postTitle': div.children('input[name="post_title"]').val(),
                'postTags' : div.children('input[name="post_tags"]').val()
            };

            $.post( hewa_admin_options.form_action + '?action=' + hewa_admin_options.ajax_action, assetData)
                .done( function( data ) {

                    div.html( '<a href="' + hewa_admin_options.form_action + '?action=hewa_edit_post&id=' + data +
                        '">Edit</a>' );

                } );

        } );

    }

    /**
     * Handle events before the upload, this function is hooked later during initialization.
     *
     * @param uploader The uploader instance.
     * @param file The file being uploaded.
     */
    var handleBeforeUpload = function( uploader, file ) {

        // Get the uploader settings.
        var params = uploader.settings;


        // If it's not a video restore the default settings.
        if ( ! isVideo( file ) ) {

            params.url = url;
            params.file_data_name = fileDataName;

            return;

        }

        // It's a video, then direct the uploader towards HelixWare.

        // Set the URL and app key/secret headers.
        params.url = hewa_admin_options.url;

        // Create the headers property if missing.
        if ( typeof params.headers === 'undefined' || null === params.headers ) {
            params.headers = {};
        }

        // Add the headers.
        params.headers['X-Application-Key']    = hewa_admin_options.key;
        params.headers['X-Application-Secret'] = hewa_admin_options.secret;

        // Set the filename.
        params.file_data_name = 'file';

    };


    /**
     * Check if the file size is in the constraint according to its type. This function is
     *
     * @param maxSizes An object with a *default* size and a *helixware* size.
     * @param file     The file being uploaded.
     * @param cb       The uploader callback.
     */
    var maxFileSizeFilter = function( maxSizes, file, cb ) {
        var undef;

        // Set the max size according to the file type.
        var maxSize = plupload.parseSize( isVideo( file ) ? maxSizes.helixware : maxSizes.default );

        // Invalid file size
        if (file.size !== undef && maxSize && file.size > maxSize) {

            this.trigger('Error', {
                code : plupload.FILE_SIZE_ERROR,
                message : plupload.translate('File size error.'),
                file : file
            });
            cb(false);
        } else {
            cb(true);
        }
    };


    // Initialize if the uploader is set.
    if (typeof uploader === 'undefined') {

        return;

    }

    // Initialize.

    // Set the default URL.
    url = uploader.settings.url;

    // Set the default *filters.max_file_size* to restore it later on.
    maxFileSize  = uploader.settings.filters.max_file_size;

    // Set the default *file_data_name*.
    fileDataName = uploader.settings.file_data_name;

//    uploader.bind( 'UploadFile', handleUploadFile );

    uploader.bind( 'BeforeUpload', handleBeforeUpload );

    // Bind our *file uploaded* handler.
    uploader.bind( 'FileUploaded', fileUploaded );

    // Filter the max file size according to the file type.
    plupload.addFileFilter( 'hewa_max_file_size', maxFileSizeFilter );
    uploader.settings.filters.max_file_size = hewa_admin_options.max_file_size;
    uploader.settings.filters.hewa_max_file_size = {
        'default'  : maxFileSize,
        'helixware': hewa_admin_options.max_file_size
    };

});