jQuery( function ( $ ) {

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


// Looping over players
$( '.hewa-player' ).each( function( i, el ) {
    $(el).children().children().each(function(i, s){
        console.log(s);
    });

    // install players
    var cdn1 = "yeeee";
    // filename label for automatic selection
    // depending on device capabilities
    label = !flowplayer.support.inlineVideo ? "-216p" : (flowplayer.support.touch ? "" : "-720p"),
    defaultresolutionindex = 0,
    // the resolutions offered for manual selection in the first player
    resolutions = [{
        label: "260p",
        // default sources should be compatible with all platforms and browsers
        // they are also the ones which are offered for embedding
        // we only need the hls source in the default and lofi resolutions
        isDefault: true,
        sources: [
            { mpegurl: cdn1 + "fp/enc/bauhaus.m3u8" },
            { webm:    cdn1 + "fp/enc/bauhaus.webm" },
            { mp4:     cdn1 + "fp/enc/bauhaus.mp4" },
            { flash:   "2-video_3.mp4" }
        ]
    }, {
        // will be the only offer on devices not supportin inline video playback
        label: "160p",
        lofi: true,
        sources: [
          { mpegurl: cdn1 + "fp/enc/bauhaus.m3u8" },
          { webm:    cdn1 + "fp/enc/bauhaus-160p.webm" },
          { mp4:     cdn1 + "fp/enc/bauhaus-160p.mp4" },
          { flash:   "1-video_3.mp4" }
        ]
    }],
    hdresolutions = [{
        label: "800p",
        sources: [
          { webm:    cdn1 + "fp/enc/bauhaus-800p.webm" },
          { mp4:     cdn1 + "fp/enc/bauhaus-800p.mp4" },
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
        
        $(el).flowplayer({
            ratio: 5/12,
            resolutions: resolutions,
            playlist: [resolutions[defaultresolutionindex].sources]
        });
    });

/*
    // Looping over players
    $( '.hewa-player' ).each( function( i, el ) {

        // Calculate the height using the ratio.
	    var height = parseInt( $(el).width() / $(el).data( 'ratio' ), 10 )

        $(el).flowplayer({

        });
	});
*/
});