$ = jQuery;

$(document).ready(function(){
	
	// Telling videojs where the flash fallback is.
	// (this include did not work via wp_enqueues_script)	
	videojs.options.flash.swf = videojs_params.swfurl;
	
	// Including hls as a fallback, after html5 and flash
	//videojs.options.techOrder.push('hls');
	
	var player = videojs( videojs_params.id );

	console.log('fix id param from wp');
});