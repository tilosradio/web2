'use strict';

angular.module('tilosAdmin', [
      'ngCookies',
      'ngResource',
      'ngSanitize',
      'ngRoute',
      'configuration',
      'textAngular'
    ])
    .config(function ($routeProvider, $locationProvider, $httpProvider) {
      $httpProvider.defaults.withCredentials = true;

      $locationProvider.html5Mode(true);
      $routeProvider
          .when('/', {
            templateUrl: 'views/main.html',
            controller: 'MainCtrl'
          })
          .otherwise({
            redirectTo: '/'
          });
    });

angular.module('tilosAdmin').run(function ($rootScope, $location, $http, API_SERVER_ENDPOINT) {

  $rootScope.textAngularOpts = {
    toolbar: [
      ['h2', 'h3','p'],
      ['bold', 'italics'],
      ['ol', 'ul'],
      ['insertLink','insertImage'],
      ['html']

    ]
  };
  $rootScope.textAngularTools = {
    h2: {
      display: "<button type='button' ng-click='action()' ng-class='displayActiveToolClass(active)'>cím</button>"
    },
    h3: {
      display: "<button type='button' ng-click='action()' ng-class='displayActiveToolClass(active)'>alcím</button>"
    },
    p: {
      display: "<button type='button' ng-click='action()' ng-class='displayActiveToolClass(active)'>normál</button>"
    }
  };


  var endsWith = function (str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
  };
  var freeAccess = function (url) {
    return (/.*password_reset(\?.*)?/g.exec(url) || endsWith(url, '/password_reminder') || endsWith(url, '/login'));
  }
  $rootScope.$on('$locationChangeStart', function (evt, next) {
    if (!('user' in $rootScope)) {
      if (!freeAccess(next)) {
        //evt.preventDefault();
        $location.path("/login");

      }
    }

  });

  $rootScope.$on( "$routeChangeStart", function(event, next, current) {
      if (!('user' in $rootScope)) {
            // no logged user, we should be going to #login
            if ( next.templateUrl == "views/login.html" ) {
                // already going to #login, no redirect needed
            } else {
                // not going to #login, we should redirect now
                $location.path( "/login" );
            }
        }
  });

  $rootScope.initialPath = $location.path();
  $http.get(API_SERVER_ENDPOINT + "/api/v0/user/me").success(function (data) {
    if (data && data.username) {
      $rootScope.user = data;
      if ($rootScope.initialPath) {
        var redirectTo = $rootScope.initialPath;
        $rootScope.initialPath = null;
        $location.path(redirectTo);

      }
    } else {
      if (!freeAccess($location.path())) {
        $location.path("/login");
      }
    }
  });
});
var server = window.location.protocol + '//' + window.location.hostname;
if (window.location.port && window.location.port !== '9000') {
  server = server + ':' + window.location.port;
}

var tilosHost = window.location.hostname;

angular.module('configuration', []).constant('API_SERVER_ENDPOINT', server);