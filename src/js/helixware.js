$ = jQuery;

$(document).ready(function(){
	
	// We could have many players, and in future they may interact with each other
	// (i.e. when a player starts the others should stop)
	var players = [];
	
	// Telling videojs where the flash fallback is.
	videojs.options.flash.swf = videojs_params.swfurl;
	
	// Css selector for the videos
	var videoCssClass = '.' + videojs_params.class;
	
	// Loop over player containers
	$( videoCssClass ).each( function(){
		
		// Creating new player in the current container
		container = '#' + this.id;
		players.push = videojs( container );
	});
});