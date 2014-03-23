'use strict';


angular.module('tilosApp').config(function ($routeProvider) {
  $routeProvider.when('/index', {
    templateUrl: 'partials/index.html',
    controller: 'MainCtrl'
  });
  $routeProvider.when('/', {
    templateUrl: 'partials/index.html',
    controller: 'MainCtrl'
  });
});

angular.module('tilosApp').controller('MainCtrl', function ($scope, FeedService, $http, API_SERVER_ENDPOINT) {
      FeedService.parseFeed('http://hirek.tilos.hu/?feed=rss2').then(function (res) {
        $scope.feeds = res.data.responseData.feed.entries;
      });
      $http.get(API_SERVER_ENDPOINT + "/api/v0/episode/next").success(function (data) {
        $scope.next = data;
      });
      $http.get(API_SERVER_ENDPOINT + "/api/v0/episode/last").success(function (data) {
        $scope.last = data;
      });
      $http.get(API_SERVER_ENDPOINT + "/api/v0/text/lead").success(function (data) {
        $scope.lead = data;
      });
    });