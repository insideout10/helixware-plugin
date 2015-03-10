<?php
/**
 * This file contains functions that display notices in the admin screen.
 */

function hewa_admin_quota_notice() {
	?>
	<div id="hewa-notice-quota" class="updated">
		<p><strong>HelixWare Quota:</strong> <?php _e( 'loading...', HEWA_LANGUAGE_DOMAIN ); ?></p>
	</div>
	<script type="text/javascript">
		jQuery(function ($) {
			var data = {
				action: 'hewa_quota'
			};
			$.post(ajaxurl, data, function (response) {
				$('#hewa-notice-quota').html('<p><strong>HelixWare Quota:</strong> ' + response.message + '</p>');
			});
		});
	</script>
<?php
}

add_action( 'admin_notices', 'hewa_admin_quota_notice' );