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

	    // Create video
    	var id = $(el).attr('id');	// Get id of the video element
	    var vid = videojs( id );	// Launch videojs

	    vid.ready( function(){
		    // Assign height
	    	vid.height( vHeight );

	    	// Activate plugins
		    vid.persistvolume({namespace: 'So-Viral-So-Hot'});
		    vid.resolutionSelector();

		    // Listen for the changeRes event
			vid.on( 'changeRes', function() {

				// player.getCurrentRes() can be used to get the currently selected resolution
				console.log( 'Current Res is: ' + vid.getCurrentRes() );
			});

			//console.log(vid);

		});
	});

});