jQuery(function ($) {

    // Capture the preset *filters.max_file_size*.
    var maxFileSize, fileDataName;

    console.log('Initializing.');

    var handleBeforeUpload = function( uploader, file ) {

        // TODO: restore global settings if the asset is not a video, otherwise continue with the following settings.

        var params = uploader.settings;

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

        console.log( 'handleBeforeUpload' );
        console.log( uploader.settings );

    };

    // Check if the uploader is defined on this page.
    if (typeof uploader === 'undefined') {
        return;
    }

    // Set the default *filters.max_file_size* to restore it later on.
    maxFileSize  = uploader.settings.filters.max_file_size;

    // Set the default *file_data_name*.
    fileDataName = uploader.settings.file_data_name;

    uploader.bind( "BeforeUpload", handleBeforeUpload );

});