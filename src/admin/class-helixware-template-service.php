<?php

/**
 * Intercepts WordPress client-side templates and provides own customizations.
 *
 * @since 1.3.0
 */
class HelixWare_Template_Service {

	/**
	 * The VideoJS Player service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Player_VideoJS $player The VideoJS Player service.
	 */
	private $player;

	/**
	 * The Log service.
	 *
	 * @since 1.3.7
	 * @access private
	 * @var \HelixWare_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The plugin version (used when queueing scripts and styles).
	 *
	 * @since 1.4.0
	 * @access private
	 * @var string $version The plugin version.
	 */
	private $version;

	/**
	 * Create an instance of the HelixWare_Template_Service.
	 *
	 * @since 1.3.0
	 *
	 * @param \HelixWare_Player_VideoJS $player The VideoJS Player service.
	 */
	public function __construct( $player ) {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Template_Service' );

		// Set the plugin version, for queueing scripts and styles.
		$this->version = HelixWare::get_instance()->get_version();

		// Set the player to preview videos.
		$this->player = $player;

	}

	/**
	 * Enqueue the required scripts.
	 *
	 * @since 1.3.0
	 */
	public function admin_enqueue_scripts() {

		// Enqueue the player scripts.
		$this->player->queue_scripts();

	}

	/**
	 * Load scripts required to enhance the upload page.
	 *
	 * @since 1.4.0
	 */
	public function admin_head_upload() {

		// Load ZeroClipboard to allow quick clipboard copy of the hw embed code.
		wp_enqueue_script( 'wl-zeroclipboard', plugin_dir_url( __FILE__ ) . 'js/zeroclipboard/ZeroClipboard.min.js', array(), $this->version, true );

	}

	/**
	 * Callback for the admin_footer-upload.php action.
	 *
	 * @since 1.3.0
	 */
	public function admin_footer_upload() {

		?>

        <script type="text/html"
                id="tmpl-helixware-attachment-details-two-column">

            <div class="attachment-media-view {{ data.orientation }}">
                <div class="thumbnail thumbnail-{{ data.type }}">

                </div>
            </div>
            <div class="attachment-info">
			<span class="settings-save-status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e( 'Saved.' ); ?></span>
			</span>

                <div class="details">
                    <div class="filename">
                        <strong><?php _e( 'File type:' ); ?></strong> HelixWare
                    </div>
                    <div class="uploaded">
                        <strong><?php _e( 'Uploaded on:' ); ?></strong> {{data.dateFormatted }}
                    </div>

                    <# if ( 'image' === data.type && ! data.uploading ) { #>
                        <# if ( data.width && data.height ) { #>
                            <div class="dimensions">
                                <strong><?php _e( 'Dimensions:' ); ?></strong>
                                {{ data.width }} &times; {{ data.height }}
                            </div>
                            <# } #>
                                <# } #>

                                    <# if ( data.fileLength ) { #>
                                        <div class="file-length">
                                            <strong><?php _e( 'Length:' ); ?></strong>
                                            {{ data.fileLength }}
                                        </div>
                                        <# } #>

                                            <# if ( 'audio' === data.type && data.meta.bitrate ) { #>
                                                <div class="bitrate">
                                                    <strong><?php _e( 'Bitrate:' ); ?></strong>
                                                    {{ Math.round(data.meta.bitrate / 1000 )}}kb/s
                                                    <# if ( data.meta.bitrate_mode ) { #>
                                                        {{ ' ' +data.meta.bitrate_mode.toUpperCase()}}
                                                        <# } #>
                                                </div>
                                                <# } #>

                                                    <div class="compat-meta">
                                                        <# if ( data.compat && data.compat.meta ) { #>
                                                            {{{ data.compat.meta }}}
                                                            <# } #>
                                                    </div>
                </div>

                <div class="settings">
                    <label class="setting">
                        <span class="name"><?php _e( 'Embed code:' ); ?></span>
                        <input type="text" class="hw-txt-embed-code"
                               value="[hw_embed id='{{ data.id }}']"
                               readonly/>
                        <button class="hw-btn-copy">Copy</button>
                    </label>

                    <label class="setting" data-setting="url">
                        <span class="name"><?php _e( 'URL' ); ?></span>
                        <input type="text" value="{{ data.url }}" readonly/>
                    </label>
                    <# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
						<?php if ( post_type_supports( 'attachment', 'title' ) ) : ?>
                            <label class="setting" data-setting="title">
								<span
                                        class="name"><?php _e( 'Title' ); ?></span>
                                <input type="text" value="{{ data.title }}" {{
                                       maybeReadOnly }}/>
                            </label>
						<?php endif; ?>
                        <# if ( 'audio' === data.type ) { #>
							<?php foreach (
								array(
									'artist' => __( 'Artist' ),
									'album'  => __( 'Album' ),
								) as $key => $label
							) : ?>
                                <label class="setting"
                                       data-setting="<?php echo esc_attr( $key ) ?>">
									<span
                                            class="name"><?php echo $label ?></span>
                                    <input type="text"
                                           value="{{ data.<?php echo $key ?> || data.meta.<?php echo $key ?> || '' }}"/>
                                </label>
							<?php endforeach; ?>
                            <# } #>
                                <label class="setting" data-setting="caption">
									<span
                                            class="name"><?php _e( 'Caption' ); ?></span>
                                    <textarea {{ maybeReadOnly }}>{{ data.caption }}</textarea>
                                </label>
                                <# if ( 'image' === data.type ) { #>
                                    <label class="setting" data-setting="alt">
										<span
                                                class="name"><?php _e( 'Alt Text' ); ?></span>
                                        <input type="text"
                                               value="{{ data.alt }}" {{
                                               maybeReadOnly }}/>
                                    </label>
                                    <# } #>
                                        <label class="setting"
                                               data-setting="description">
											<span
                                                    class="name"><?php _e( 'Description' ); ?></span>
                                            <textarea {{ maybeReadOnly }}>{{ data.description }}</textarea>
                                        </label>
                                        <label class="setting">
											<span
                                                    class="name"><?php _e( 'Uploaded By' ); ?></span>
                                            <span class="value">{{ data.authorName }}</span>
                                        </label>
                                        <# if ( data.uploadedToTitle ) { #>
                                            <label class="setting">
												<span
                                                        class="name"><?php _e( 'Uploaded To' ); ?></span>
                                                <# if ( data.uploadedToLink ) { #>
													<span class="value"><a
                                                                href="{{ data.uploadedToLink }}">{{
															data.uploadedToTitle
															}}</a></span>
                                                    <# } else { #>
                                                        <span class="value">{{ data.uploadedToTitle }}</span>
                                                        <# } #>
                                            </label>
                                            <# } #>
                                                <div class="attachment-compat"></div>
                </div>

                <div class="actions">
                    <a class="view-attachment"
                       href="{{ data.link }}"><?php _e( 'View attachment page' ); ?></a>
                    <# if ( data.can.save ) { #> |
                        <a href="post.php?post={{ data.id }}&action=edit"><?php _e( 'Edit more details' ); ?></a>
                        <# } #>
                            <# if ( ! data.uploading && data.can.remove ) { #> |
								<?php if ( MEDIA_TRASH ): ?>
                                <# if ( 'trash' === data.status ) { #>
                                    <button type="button"
                                            class="button-link untrash-attachment"><?php _e( 'Untrash' ); ?></button>
                                    <# } else { #>
                                        <button type="button"
                                                class="button-link trash-attachment"><?php _ex( 'Trash', 'verb' ); ?></button>
                                        <# } #>
											<?php else: ?>
                                                <button type="button"
                                                        class="button-link delete-attachment"><?php _e( 'Delete Permanently' ); ?></button>
											<?php endif; ?>
                                            <# } #>
                </div>

            </div>
        </script>
        <style>
            /* Ensure the generated video-js player is responsive */
            .thumbnail > .video-js {
                width: 100%;
                height: 100%;
            }
        </style>
        <script>
            (function ($) {

                // A replacement for the TwoColumn template, that kicks in only if the currently
                // displayed asset is HelixWare.
                var TwoColumn = wp.media.view.Attachment.Details.TwoColumn.extend({
                    initialize: function () {

                        // Extend the super events.
                        _.extend(this.events, wp.media.view.Attachment.Details.TwoColumn.prototype.events);

                        // A choice of two templates:
                        //  1. the original one provided by WordPress, we select this one, when the
                        //     attachment is not HelixWare's
                        //  2. the customized HelixWare attachment
                        this.templates = [
                            wp.template('attachment-details-two-column'),
                            wp.template('helixware-attachment-details-two-column')
                        ];

                    },
                    render: function () {

                        // If it's not a HelixWare asset, just call the superclass
                        // render method on the standard template.
                        if ('application/x-helixware-ondemand' !== this.model.get('mime')) {
                            this.template = this.templates[0];
                            // Call the superclass render.
                            TwoColumn.__super__.render.apply(this, arguments);
                            return this;
                        }

                        // Set the HelixWare template.
                        this.template = this.templates[1];

                        TwoColumn.__super__.render.apply(this, arguments);

                        // Set a reference to the view for async events.
                        var view = this;

                        // Set up the ZeroClipboard button for the hw_embed shortcode.
                        var client = new ZeroClipboard(this.$(".hw-btn-copy"));
                        client.on("copy", function (event) {
                            var clipboard = event.clipboardData;
                            clipboard.setData("text/plain", view.$(".hw-txt-embed-code").val());
                        });

                        // Get the
                        wp.ajax.post('hw_hls_url', {id: this.model.get('id')})
                        // We got a URL back.
                            .done(function (response) {

                                // Create the video element and append it to the thumbnail div.
                                // We create the video element dynamically to avoid MediaElement.js
                                // to be instantiated on it.
                                var $video = $(
                                    '<video class="video-js vjs-default-skin" controls>'
                                    + '<source src="' + response + '" type="application/x-mpegURL" />'
                                    + '</video>'
                                );

                                view.$('.thumbnail').append($video);

                                // Instantiate the video on the video player.
                                videojs($video[0], {}, function () {
                                });

                                // Allow others to do something after the video has loaded.
                                wp.media.events.trigger('hx:attachment:details:update', view);

                            })
                            // Something went wrong.
                            .fail(function (response) {
                                view.$('.thumbnail').html('Something wrong happened: ' + response);
                            });

                        return this;

                    }

                });

                // Override the TwoColumn view with our customized view.
                wp.media.view.Attachment.Details.TwoColumn = TwoColumn;

            })(jQuery);
        </script>
		<?php

	}

}
