jQuery(function ($) {
	// Instantiate player
    var player = jwplayer( hewaPlayerParams.id )
        .setup( hewaPlayerParams.setup );

    // Reference to listbar <div>
   	var listbarSelector = '#' + hewaPlayerParams.id + '-listbar';

   	// Build listbar
    if( hewaPlayerParams.listbar !== null ) {
        player.onReady( function () {
            var html     = '';
            var playlist = player.getPlaylist();

            for (var i = 0; i < playlist.length; i++) {

                var description = ( playlist[i].description != undefined ? playlist[i].description : '');

                html += "<li><span class='dropt' title='" + playlist[i].title +
                        "'><a href='javascript:jwplayer(hewaPlayerParams.id).playlistItem(" + i +
                        ")'><img height='75' width='120' src='" + playlist[i].image +
                        "'</img></br>" + playlist[i].title + "</a></br><span style='width:500px;'><p>" +
                        description + "</p></span></span></li>"

                $( listbarSelector ).html( html );

            }

            $( listbarSelector ).css('height', player.getHeight() + 'px');

        })
        .onResize(function (event) {

            $( listbarSelector ).css('height', event.height + 'px');

        });
    }
});