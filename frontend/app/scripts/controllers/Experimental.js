'use strict';

angular.module('tilosApp').config(function ($routeProvider) {
    $routeProvider.when('/experimental', {
        templateUrl: 'partials/experimental.html',
        controller: 'ExperimentalCtrl',
    });
});
;

angular.module('tilosApp').controller('ExperimentalCtrl', function ($rootScope, $scope) {
    $rootScope.experimental = true;

    $scope.switch = function () {
        if (!$rootScope.experimental) {
            $rootScope.experimental = true;
        } else {
            $rootScope.experimental = false;
        }
    };
});
