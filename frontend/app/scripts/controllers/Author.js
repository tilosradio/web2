'use strict';

angular.module('tilosApp')
  .controller('AuthorCtrl', function ($scope, $rootScope, $routeParams, API_SERVER_ENDPOINT, $http) {
    $http.get(API_SERVER_ENDPOINT + '/api/v0/author/' + $routeParams.id, {cache: true}).success(function (data) {
      $scope.author = data;
      $rootScope.pageTitle = data.name

    });
  });