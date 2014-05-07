'use strict';

angular.module('tilosApp').config(function ($routeProvider) {
    $routeProvider.when('/audiotest', {
        templateUrl: 'partials/audiotest.html',
        controller: 'AudioTestCtrl',
    });
});
;

angular.module('tilosApp').controller('AudioTestCtrl', function ($scope, Player) {
        var urls = [];
        urls[0] = {
           url: "http://archive.tilos.hu/online/2014/04/03/tilosradio-20140403-1000.mp3"
	};
        Player.play(urls);
    });

