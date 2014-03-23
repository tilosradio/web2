'use strict';

angular.module('tilosAdmin').config(function ($routeProvider) {
  $routeProvider.when('/url/:id', {
    templateUrl: 'views/url.html',
    controller: 'UrlCtrl',
    resolve: {
      url: function ($route, Urls) {
        return Urls.get({show: $route.current.params.show, id: $route.current.params.id});

      }
    }});
  $routeProvider.when('/edit/url/:show/:id', {
    templateUrl: 'views/url-form.html',
    controller: 'UrlEditCtrl',
    resolve: {
      data: function ($route, Urls) {
        return Urls.get({id: $route.current.params.id});
      }
    }});
  $routeProvider.when('/new/url/:show', {
    templateUrl: 'views/url-form.html',
    controller: 'UrlNewCtrl',
  });
});
angular.module('tilosAdmin')
    .controller('UrlCtrl', ['$scope', 'url', function ($scope, url) {
      $scope.url = url;

    }]);
angular.module('tilosAdmin')
    .controller('UrlEditCtrl', ['$scope', 'data', 'Urls', 'dateFilter', '$location', '$routeParams', function ($scope, data, resource, dateFilter, $location, $routeParams) {
      $scope.url = data;
      $scope.save = function () {
        resource.update({show: $routeParams.show, id: $scope.url.id}, $scope.url, function (data) {
          $location.path('/show/' + $routeParams.show);
        });
      }
    }]);

angular.module('tilosAdmin')
    .controller('UrlNewCtrl', ['$scope', 'Urls', '$location', '$routeParams', function ($scope, resource, $location, $routeParams) {
     $scope.url = {};
     $scope.save = function () {
        $scope.url.showId = $routeParams.show;
        resource.save({show: $routeParams.show}, $scope.url, function (data) {
          $location.path('/show/' + $routeParams.show);
        });
      }
    }]);



angular.module('tilosAdmin').factory('Urls', ['API_SERVER_ENDPOINT', '$resource', function (server, $resource) {
  return $resource(server + '/api/v0/url/:id', null, {
    'update': { method: 'PUT'}
  });
}]);

