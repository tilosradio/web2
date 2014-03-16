'use strict';

angular.module('tilosApp')
	.directive('scroll', function ($window) {
    return function (scope) {
        angular.element($window).bind('scroll', function () {
          if (this.pageYOffset >= 100) {
            scope.boolChangeClass = true;
            if(this.pageYOffset >= 1000){
            	scope.showLink = true;
			}else{
				scope.showLink = false;
			}
            // console.log('Scrolled below header.');
          } else {
            scope.boolChangeClass = false;
            scope.showLink = false;
            // console.log('Header is in view.');
          }
          scope.$apply();
        });
      };
  });

