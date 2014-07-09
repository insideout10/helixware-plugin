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
        var divId = 'hewa-asset-' + asset.id;

        elem.html(
            '<img class="pinkynail"><div class="filename new">' +
                '<span class="title">' + asset.title + '</span>' +
                '<div id="' + divId + '" style="position: absolute; top: 0px; right: 0px; vertical-align: middle;">' +
                postTypeSelect +
                '<input type="hidden" name="asset_id" value="' + asset.id +'">' +
                '<input type="text" name="post_title" placeholder="' + hewa_admin_options.labels.title +
                    '" value="' +asset.title +'">' +
                '<input type="text" name="post_tags" placeholder="' + hewa_admin_options.labels.tags + '">' +
                '<button type="button" class="hewa-submit-button">' + hewa_admin_options.labels.save + '</button>' +
                '</div></div>' );


        $( '#' + divId + ' .hewa-submit-button').click( function( event ) {

            var div = $( event.target).parent();

            var data   = {
                'assetId'  : div.children('input[name="asset_id"]').val(),
                'postType' : div.children('select[name="post_type"]').val(),
                'postTitle': div.children('input[name="post_title"]').val(),
                'postTags' : div.children('input[name="post_tags"]').val()
            };

            $.post( hewa_admin_options.form_action + '?action=' + hewa_admin_options.ajax_action, data)
                .done( function( data ) {

                    div.html( '<a href="' + hewa_admin_options.form_action + '?action=hewa_edit_post&id=' + data +
                        '">Edit</a>' );

                });

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
            params.filters.max_file_size = maxFileSize;
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

        // Increase the max file size limit.
        params.filters.max_file_size = "1gb";

        // Set the filename.
        params.file_data_name = 'file';

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

    uploader.bind( 'BeforeUpload', handleBeforeUpload );

    // Bind our *file uploaded* handler.
    uploader.bind( 'FileUploaded', fileUploaded );

    // Set the new file size limit.
    uploader.settings.filters.max_file_size = "1gb";

});