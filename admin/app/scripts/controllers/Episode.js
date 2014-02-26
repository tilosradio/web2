'use strict';

angular.module('tilosAdmin').config(['$routeProvider', function ($routeProvider) {
  $routeProvider.when('/episode/:id', {
    templateUrl: 'views/episode.html',
    controller: 'EpisodeCtrl',
      resolve: {
          data: function ($route, Episodes) {
              return Episodes.get({id: $route.current.params.id});
          },
      }

  });
  $routeProvider.when('/new/episode', {
    templateUrl: 'views/episode-form.html',
    controller: 'EpisodeNewCtrl'
  });
  $routeProvider.when('/edit/episode/:id', {
    templateUrl: 'views/episode-form.html',
    controller: 'EpisodeEditCtrl'
  });
}]);
angular.module('tilosAdmin')
    .controller('EpisodeCtrl', function ($scope, Episodes, $routeParams, data) {
      $scope.episode = data;
    });

angular.module('tilosAdmin')
    .controller('EpisodeEditCtrl', ['$location', '$scope', '$routeParams', 'API_SERVER_ENDPOINT', '$http', '$cacheFactory', '$rootScope', function ($location, $scope, $routeParams, server, $http, $cacheFactory, $rootScope) {
      var id = $routeParams.id;
      $scope.now = new Date().getTime();
      $http.get(server + '/api/v0/episode/' + id).success(function (data) {
        $scope.episode = data;
        $scope.episode.id = id;
        $scope.show = data['show'];
        $scope.episode.show = null;
        if ($scope.episode.text) {
          $scope.episode.content = $scope.episode.text.content;
          $scope.episode.title = $scope.episode.text.title;
        }
      });

      $scope.save = function () {

        $http.put(server + '/api/v0/episode/' + $scope.episode.id, $scope.episode).success(function (data) {
          var httpCache = $cacheFactory.get('$http');
          httpCache.remove(server + '/api/v0/episode/' + $scope.episode.id);
          httpCache.remove(server + '/api/v0/show/' + $scope.show.id);
          $location.path('/episode/' + $scope.episode.id);
        }).error(function (data) {
              if (data.error) {
                $scope.error = data.error;
              } else {
                $scope.error = "Unknown.error";
              }
            });
        ;

      };


    }])
;

angular.module('tilosAdmin')
    .controller('EpisodeNewCtrl', ['$location', '$scope', 'API_SERVER_ENDPOINT', '$http', '$cacheFactory', '$rootScope', function ($location, $scope, server, $http, $cacheFactory, $rootScope) {

      $scope.episode = $rootScope.newEpisode;
      $scope.episode.radioshow_id = $scope.episode.show.id;
      $scope.show = $scope.episode.show;
      $scope.episode.show = null;
      $scope.now = new Date().getTime();


      $scope.save = function () {
        $http.post(server + '/api/v0/episode', $scope.episode).success(function (data) {
          var httpCache = $cacheFactory.get('$http');
          httpCache.remove(server + '/api/v0/show/' + $scope.show.id);
          $location.path('/episode/' + data.data.id);
          $location.path('/show/' + scope.show.id);
        }).error(function (data) {
              if (data.error) {
                $scope.error = data.error;
              } else {
                $scope.error = "Unknown.error";
              }
            });

      };


    }])
;

angular.module('tilosAdmin').factory('Episodes', ['API_SERVER_ENDPOINT', '$resource', function (server, $resource) {
  return $resource(server + '/api/v0/episode/:id', null, {
    'update': { method: 'PUT'}
  });
}]);
