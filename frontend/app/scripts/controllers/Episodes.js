'use strict';
/**
 * Get the episodes array
 */

/*global angular*/
var dbg;
angular.module('tilosApp')
  .controller('EpisodesCtrl', ['$rootScope', '$routeParams', 'API_SERVER_ENDPOINT', 'tilosData', '$http',
    function ($scope, $routeParams, $server, $td, $http) {
      $scope.now = new Date();

      var nowDate = new Date();
      var start = Math.round((nowDate / 1000 - 60 * 60 * 3) / 10) * 10;
      var now = nowDate.getTime() / 1000;
      $http.get($server + '/api/v0/episode?start=' + start + '&end=' + (start + 8 * 60 * 60), {cache: true}).success(function (data) {
        dbg = data;
        for (var i = 0; i < data.length; i++) {
          if (data[i].plannedFrom <= now && data[i].plannedTo > now) {
            $scope.current = data[i];
          }
        }
        $http.get($server + '/api/v0/show/' + $scope.current.show.id, {cache: true}).success(function (sd) {
          $scope.current.show = sd;
          $scope.episodes = data;
          dbg = $scope;
        });
      });

    }
  ]);
