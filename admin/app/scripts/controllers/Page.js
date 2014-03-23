'use strict';

angular.module('tilosAdmin').config(['$routeProvider', function ($routeProvider) {
    $routeProvider.when('/page/:id', {
        templateUrl: 'views/page.html',
        controller: 'TextCtrl',
        resolve: {
            data: function ($route, Texts) {
                return Texts.get({id: $route.current.params.id});
            },
        }});
    $routeProvider.when('/pages', {
        templateUrl: 'views/pages.html',
        controller: 'TextListCtrl'
    });

    $routeProvider.when('/edit/page/:id', {
        templateUrl: 'views/page-form.html',
        controller: 'TextEditCtrl',
        resolve: {
            data: function ($route, Texts) {
                return Texts.get({id: $route.current.params.id});
            },
        }});

    $routeProvider.when('/new/page', {
        templateUrl: 'views/page-form.html',
        controller: 'TextNewCtrl'
        });
}]);


angular.module('tilosAdmin')
    .controller('TextListCtrl', function ($http, $routeParams, API_SERVER_ENDPOINT, $scope) {
        $http.get(API_SERVER_ENDPOINT + '/api/v0/text/page/list').success(function (data) {
            $scope.pages = data;
        });

    }
);


angular.module('tilosAdmin')
    .controller('TextEditCtrl', function ($http, $routeParams, API_SERVER_ENDPOINT, $location, $scope, Texts, $cacheFactory, data) {
        $scope.text = data;
        $scope.save = function () {
            $http.put(API_SERVER_ENDPOINT + '/api/v0/text/' + $scope.text.id, $scope.text).success(function (data) {
                var httpCache = $cacheFactory.get('$http');
                httpCache.remove(API_SERVER_ENDPOINT + '/api/v0/text/' + $scope.text.id);
                httpCache.remove(API_SERVER_ENDPOINT + '/api/v0/show');
                $location.path('/page/' + $scope.text.id);
            });
        }
    }
);

angular.module('tilosAdmin')
    .controller('TextNewCtrl', function ($http, $routeParams, API_SERVER_ENDPOINT, $location, $scope, Texts) {
        $scope.text = {};
        $scope.save = function () {
            $http.post(API_SERVER_ENDPOINT + '/api/v0/text', $scope.text).success(function (data) {
                $location.path('/page/' + data.data.id);
            });
        }
    }
);
;

angular.module('tilosAdmin')
    .controller('TextCtrl', function ($scope, data) {
        $scope.page = data;


    });


angular.module('tilosAdmin').factory('Texts', ['API_SERVER_ENDPOINT', '$resource', function (server, $resource) {
    return $resource(server + '/api/v0/text/:id', null, {
        'update': { method: 'PUT'}
    });
}]);


