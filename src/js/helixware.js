
flowplayer( function (api, root) {

    // check whether hls will be picked by the flowplayer engine
    var hls = flowplayer.support.video &&
            !!$('<video/>')[0].canPlayType('application/x-mpegURL').replace('no', '');
    var qsel;
    var selected = 'fp-selectedres';

    // build resolutions menu
    if ( api.conf.resolutions !== undefined ) {

        // create the manual quality selection widget and append it to the UI
        qsel = $('<div/>').addClass('fp-qsel').appendTo('.fp-ui', root);

        $.each(api.conf.resolutions, function (i, resolution) {
        // generate a selector button for each resolution

            $('<div/>').addClass(resolution.isDefault ? 'fp-defaultres ' + selected : '')
                .text(resolution.label)
                .click(function() {
                    if (!$(this).hasClass(selected)) {
                        var buttons = $('div', qsel),
                        
                        // store current position
                        pos = api.ready && !api.finished ? api.video.time : 0;

                        // Piero 1 - Flowplayer 0
                        api.unload();   // destroy current streaming

                        // restart streaming
                        api.load(resolution.sources, function (e, api, video) {
                            // seek to stored position
                            if (pos) {
                                api.seek(pos);
                            }
                        });

                        buttons.each(function () {
                            $(this).toggleClass(selected, buttons.index(this) === i);
                        });
                    }
                })
                .appendTo( $(root).find(qsel) );
        });

        api.bind('unload', function () {
            // highlight default resolution
            $('div', qsel).each(function () {
                var button = $(this);

                button.toggleClass(selected, button.hasClass('fp-defaultres'));
            });
        });

        // Check for local storage support (should be for all but Opera-mini)
        if(typeof(Storage) !== "undefined") {
            // Volume persistence.
            var volumeLabel = 'flowplayerVolume';
            // when volume changes, register in local storage
            api.bind('volume', function ( e, api, video ) {
                window.localStorage.setItem( volumeLabel, api.volumeLevel );
            });
            // when player loads, set stored volume (if any)
            api.bind('ready', function ( e, api, video ) {
                var vol = window.localStorage.getItem( volumeLabel );
                if( vol !== null ) {        // check if there was a value stored
                    // we will try to set the volume many later (flowplayer bug)
                    setTimeout(function(){
                        if( vol == 'NaN' ) // the value is always returned as a string, don't use isNaN()
                            api.mute( true );
                        else
                            api.volume( vol );
                    }, 3000);
                }
            });
        }
    }
});

$ = jQuery;
jQuery( function ( $ ) {

    // Looping over player divs
    $( '.' + helixwareParams.playerClass ).each( function( i, el ) {

        // get rtmp server address from data-rtmp-server attribute
        var rtmpServer = $(el).data('rtmp-server');

        // build resolutions array
        var resolutions = [];
        $( el ).find('source').each( function( i, s ){
            
            // retrieve type and src
            var type = $(s).attr('type');
            var src = $(s).attr('src');

            // build a src object
            if( type == 'rtmp/mp4' ) {
                // for some reason, in rtmp streaming flowplayer doesn't want the full url to the clip.
                // that's why there is a separate variable 'rtmpServer' (see player instantiation).
                src = src.replace( rtmpServer, '' );    // take away server address
                src = { flash: src };
            }
            else if( type == 'application/x-mpegURL' )
                src = { mpegurl: src };

            // prepare a single source obj
            var source = {
                width: $(s).data('res'),
                label: $(s).data('res') + 'p',
                sources: [ src ]    // more sources will be added
            };
            
            // merge sources with same label (es. 460p, 680p, ...)
            var newResolution = true;
            if( resolutions !== [] ){
                for(var r=0; r<resolutions.length; r++){
                    if( resolutions[r].label == source.label ){
                        newResolution = false;
                        resolutions[r].sources.push( source.sources[0] );
                    }
                }
            }
            if( newResolution ){
                resolutions.push( source );
            }
        });
        
        if( resolutions !== [] ) {       // Only proceed if sources were found
            
            // Order resolutions by width
            resolutions.sort( function(a,b) {
                if (a.width > b.width)
                    return 1;
                return 0;
            });

            // Choose lowest resolution as default
            resolutions[0].isDefault = true;

            // Establish video width and ratio.
            var width = $(el).data('width');    // passed with data because in css gets overwritten from flowplayer
            $( el ).width( width );               // assign width
            width = $(el).width();              // to get the width in number of pixels even if it was a percentage
            var ratio = $(el).data( 'ratio' );  // flowplayer wants the inverse of ratio
            var height = width / ratio;

            // Assign width
            $( el ).width( width )
                 .height( height )
                 .css('background-color', 'gray');

            // player instantiation
            $( el ).flowplayer({
                rtmp: rtmpServer,
                resolutions: resolutions,
                playlist: [resolutions[0].sources]
            });
        }
    });
});