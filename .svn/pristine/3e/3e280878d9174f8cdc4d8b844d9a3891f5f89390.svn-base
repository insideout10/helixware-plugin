<?php
/**
 * Provide custom meta-boxes to the admin screens.
 */

/**
 * Adds an ingest meta-box to HelixWare clip custom type.
 *
 * @uses hewa_admin_metabox_clip_ingest_callback to display the metabox contents.
 */
function hewa_admin_metaboxes_add() {

    // Add the ingest metabox for the HelixWare clip custom post type.
    add_meta_box(
        'hewa-clip-ingest',
        __( 'Ingest Video', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_metabox_clip_ingest_callback',
        HEWA_POST_TYPE_CLIP
    );

}
add_action( 'add_meta_boxes', 'hewa_admin_metaboxes_add' );

/**
 * Prints the contents of the ingest metabox.
 *
 * @param WP_Post $post The current post.
 */
function hewa_admin_metabox_clip_ingest_callback( $post ) {

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'hewa_admin_metabox_clip_ingest', 'hewa_admin_metabox_clip_ingest_nonce' );

    $url_j        = json_encode( hewa_get_option( HEWA_SETTINGS_SERVER_URL ) . '/4/users/files' );
    $app_key_j    = json_encode( hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY ) );
    $app_secret_j = json_encode( hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET ) );

    wp_enqueue_script( 'plupload-handlers' );

    echo <<<EOF
        <ul id="filelist"></ul>
        <br />

        <div id="container">
            <a id="browse" href="javascript:;">[Browse...]</a>
            <a id="start-upload" href="javascript:;">[Start Upload]</a>
        </div>

        <br />
        <pre id="console"></pre>

        <script type="text/javascript">
            jQuery(function($) {
                var uploader = new plupload.Uploader({
                  browse_button: 'browse', // this can be an id of a DOM element or the DOM element itself
                  url: $url_j,
                  headers: {
                    'X-Application-Key': $app_key_j,
                    'X-Application-Secret': $app_secret_j
                  },
                  multi_selection: false
                });

                uploader.bind('FilesAdded', function(up, files) {
                  var html = '';
                  plupload.each(files, function(file) {
                    html += '<li id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></li>';
                  });
                  document.getElementById('filelist').innerHTML += html;
                });

                uploader.bind('UploadProgress', function(up, file) {
                  document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                });

                uploader.bind('Error', function(up, err) {
                  document.getElementById('console').innerHTML += "\\nError #" + err.code + ": " + err.message;
                });

                document.getElementById('start-upload').onclick = function() {
                  uploader.start();
                };

                uploader.init();
            });
        </script>
EOF;


}