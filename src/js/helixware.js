$ = jQuery;

$(document).ready(function(){
	
	// Telling videojs where the flash fallback is.
	// (this include did not work via wp_enqueues_script)	
	videojs.options.flash.swf = videojs_params.swfurl;
	
	// Including hls in the player, after html5 and flash
	videojs.options.techOrder.push('hls');
	
	// Requesting
	$.post(
		videojs_params.ajax_url,
		{ action: videojs_params.action, post_id: 123 },
		function(data) {
			console.log(data);
			var player = videojs( videojs_params.id );
			player.play();
		}
	);

	console.log('fix id param from wp');
});