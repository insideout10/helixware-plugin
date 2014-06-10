$ = jQuery;
$(document).ready(function(){
	videojs.options.flash.swf = videojs_params.swfurl;
	videojs.options.techOrder.push('hls');
	var player = videojs( videojs_params.id );
	player.play();
	
	console.log(videojs.options);
	console.log('fix absolute url in helixware.js');
});
