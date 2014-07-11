jQuery(function ($) {

	// Used to compute height of player from width and ratio
	getOptimalRatio = function( el ){

		// Get width of the player in pixels
    	//( could not be done from php if user gives width:100% )
    	var vWidth = $(el).width();

    	// Get ratio
    	var vRatio = $(el).data( 'ratio' );

    	return parseInt( vWidth/vRatio );
	};



    // Telling videojs where the flash fallback is.
    videojs.options.techOrder = ['html5', 'flash'];
    videojs.options.flash.swf = videojs_params.swfurl;

    // Looping over players
    $('.hewa-player').each(function( i, el ){

	    var vHeight = getOptimalRatio( el );

	    // Find quality buttons on DOM
		var qualityButtons = $(el).next('.hewa-player-toolbar').children();
		// Add listeners
		qualityButtons.on('click', function(){
			console.log('aaa');
		});
		console.log(qualityButtons);


	    // Create video
    	var id = $(el).attr('id');	// Get id of the video element
	    var vid = videojs( id );	// Launch videojs

	    // Assign height
	    vid.height( vHeight );

	    vid.ready( function(){

	    	// Activate plugins
		    vid.persistvolume({namespace: 'So-Viral-So-Hot'});
		});
	});

});