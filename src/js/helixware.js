jQuery(function ($) {

    // Telling videojs where the flash fallback is.
    videojs.options.flash.swf = videojs_params.swfurl;

    // Looping over players
    $('.hewa-player').each(function( i, el ){

    	var id = $(el).attr('id');

	    // Adding persistvolume plugin
	    var vid = videojs( id );
	    vid.persistvolume({namespace: 'So-Viral-So-Hot'});
	});

});