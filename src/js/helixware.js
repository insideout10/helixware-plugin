$ = jQuery;

console.log('supported tech: ', flowplayer.engine);

if (/flash/.test(location.search))
    flowplayer.conf.engine = "flash";

flowplayer(function (api, root) {

    // check whether hls will be picked by the flowplayer engine
    var hls = flowplayer.support.video && api.conf.engine === "html5" &&
    !!$("<video/>")[0].canPlayType("application/x-mpegurl").replace("no", ""),
    qsel,
    selected = "fp-selectedres",

    // for demo info, not in production:
    playerindex = $(".flowplayer").index(root),
    srcinfo = $(".clip").eq(playerindex),
    resinfo = $(".res").eq(playerindex);

    // manual selection
    if (api.conf.resolutions !== undefined && flowplayer.support.inlineVideo && !hls) {

        // create the manual quality selection widget and append it to the UI
        qsel = $("<div/>").addClass("fp-qsel").appendTo(".fp-ui", root);

        $.each(api.conf.resolutions, function (i, resolution) {
        // generate a selector button for each resolution

            $("<div/>").addClass(resolution.isDefault ? "fp-defaultres " + selected : "")
                .text(resolution.label)
                .click(function() {
                    if (!$(this).hasClass(selected)) {
                        var buttons = $("div", qsel),
                        // store current position
                        pos = api.ready && !api.finished ? api.video.time : 0;

                        // THE SECRET TO HAPPYNESS
                        api.unload();

                        api.load(resolution.sources, function (e, api) {
                            // seek to stored position
                            if (pos) {
                                api.seek(pos);
                            }
                        });

                    buttons.each(function () {
                        $(this).toggleClass(selected, buttons.index(this) === i);
                    });
                }
            }).appendTo(qsel);
        });

        api.bind("unload", function () {
            // highlight default resolution
            $("div", qsel).each(function () {
                var button = $(this);

                button.toggleClass(selected, button.hasClass("fp-defaultres"));
            });
        });
    }


    // report current src and resolution for demonstratation purposes
    api.bind("ready", function(e, api, video) {
        srcinfo.text(video.src);
        if (video.type != "mpegurl") {
            resinfo.text("resolution: " + video.width + "x" + video.height);
        } else {
            resinfo.text("automatic choice of resolution");
        }
    }).bind("unload", function () {
        // reinsert non-breakable space
        srcinfo.text("\u00A0");
        resinfo.text("\u00A0");
    });

});

jQuery( function ( $ ) {

    // Looping over player divs
    $( '.hewa-player' ).each( function( i, el ) {

        // Calculate the height using the ratio.
        var height = parseInt( $(el).width() / $(el).data( 'ratio' ), 10 );

        var rtmpServer = "rtmp://totalerg.insideout.io/helixware/totalerg/wp-test/";
        //var rtmpServer = "rtmp://totalerg.insideout.io/helixware/tests/test/";
        var engine = "flash";

        // filename label for automatic selection
        // depending on device capabilities
        var label = !flowplayer.support.inlineVideo ? "-216p" : (flowplayer.support.touch ? "" : "-720p");
        var defaultresolutionindex = 0;

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
                label: $(s).data('res') + 'p',
                sources: [ src ]    // more sources will be added
            };
            
            // merge sources with same resolution
            var newResolution = true;
            if( resolutions !== [] ){
                for(var r=0; r<resolutions.length; r++){
                    if( resolutions[r].label == source.label ){
                        newResolution = false;
                        console.log(source.sources);
                        resolutions[r].sources.push( source.sources[0] );
                    }
                }
            }

            if( newResolution ){
                resolutions.push( source );
            }
        });

        var presolutions = [{
            // will be the only offer on devices not supportin inline video playback
            label: "160p",
            lofi: true,
            sources: [
                { mpegurl: "fp/enc/bauhaus.m3u8" },
                { flash:   "1-video_3.mp4" }
            ]
        }, {
            label: "260p",
            // default sources should be compatible with all platforms and browsers
            // they are also the ones which are offered for embedding
            // we only need the hls source in the default and lofi resolutions
            isDefault: true,
            sources: [
                { mpegurl: "fp/enc/bauhaus.m3u8" },
                { flash:   "2-video_3.mp4" }
            ]
        }, {
            label: "800p",
            sources: [
              { mpegurl:    "fp/enc/bauhaus-800p.webm" },
              { flash:   "3-video_3.mp4" }
            ]
        }];

        /*
        // set default resolution index depending on capabilities
        $.each(resolutions, function (i, resolution) {
            if (flowplayer.support.inlineVideo && resolution.isDefault ||
                !flowplayer.support.inlineVideo && resolution.lofi) {
              defaultresolutionindex = i;
              return false;
            }
        });
        */
        
        console.log(presolutions[0], resolutions[0]);

        // Empty container !!!
        $( el ).children().remove();

        // player instantiation
        $( el ).flowplayer({
            ratio: 5/12,
            engine: engine,
            rtmp: rtmpServer,
            resolutions: resolutions,
            playlist: [resolutions[defaultresolutionindex].sources]
        });
    });
});