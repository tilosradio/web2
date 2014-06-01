'use strict';
angular.module('tilosApp').directive('compiledText', function($compile) {
    // directive factory creates a link function
    return function(scope, element, attrs) {
        scope.$watch(
            function(scope) {
                return scope.$eval(attrs.compile);
            },
            function(value) {
                element.html(value);

                $compile(element.contents())(scope);
            }
        );
    };
})