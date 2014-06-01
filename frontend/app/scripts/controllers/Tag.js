'use strict';

angular.module('tilosApp').config(function ($routeProvider) {
    $routeProvider.when('/tag/:id', {
        templateUrl: 'partials/tag.html',
        controller: 'TagCtrl',
        resolve: {
            data: function ($route, Tags) {
                return Tags.get({id: $route.current.params.id});
            },
        }

    });
    $routeProvider.when('/tags', {
        templateUrl: 'partials/tags.html',
        controller: 'TagListCtrl',
        resolve: {
            list: function ($route, Tags) {
                return Tags.query();
            },
        }
    });
});
angular.module('tilosApp')
    .controller('TagCtrl', function ($scope, Tags, $routeParams, data, $sce) {
        $scope.tag = data;
    });

angular.module('tilosApp')
    .controller('TagListCtrl', function ($scope, Tags, $routeParams, list, $sce) {
        $scope.tags = list;

    });

angular.module('tilosApp').factory('Tags', ['API_SERVER_ENDPOINT', '$resource', function (server, $resource) {
    return $resource(server + '/api/v0/tag/:id', null, {

    });
}]);
