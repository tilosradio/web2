'use strict';


angular.module('tilosApp').factory("Player", function ($rootScope) {
    var jplayer = $("#jquery_jplayer_1");
    var ready = false;
    var todo = null;
    $("#jquery_jplayer_1").jPlayer({
        ready: function () {
	    ready = true;
            if (todo != null) {
		$(this).jPlayer("setMedia", {
		    title: "Tilos",
		    mp3: todo
		});
		$(this).jPlayer('play');
		todo = null;
	    }
        },
        swfPath: "/jplayer/Jplayer.swf",
        nativeSupport: false,
        solution: "flash, html",
        errorAlerts: true,
        warningAlerts: true,
    });
    var player = {};

    player.play = function(url) {
	if (ready) {
            jplayer.jPlayer("setMedia", {
		title: "Tilos",
		mp3: url[0].url
            });
	    jplayer.jPlayer('play');
	} else {
	    todo = url[0].url;
	}
        $rootScope.player = true;

    };
    return player;

})


