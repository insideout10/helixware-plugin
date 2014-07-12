jQuery( function ( $ ) {

    // Telling videojs where the flash fallback is.
    //videojs.options.flash.swf = videojs_params.swfurl;

    // Looping over players
    $( '.hewa-player' ).each( function( i, el ) {

        // <div class="hewa-player-toolbar">
        //     <span class="hewa-ld">LD </span><span class="hewa-sd"> SD </span><span class="hewa-hd"> HD </span>
        // </div>

	    /*// Find quality buttons on DOM
		var qualityButtons = $(el).next('.hewa-player-toolbar').children();
		// Add listeners
		qualityButtons.on('click', function(){
			console.log('aaa');
		});
		console.log(qualityButtons);*/

        // Calculate the height using the ratio.
	    var height = parseInt( $(el).width() / $(el).data( 'ratio' ), 10 )

	    // Assign height
        videojs( $(el).attr('id') )
            .height( height )
	        .ready( function( ) {

                // Activate plugins
                this.persistvolume( { namespace: 'hewa' } );
                this.resolutionSelector();
		    });
            
	});

});