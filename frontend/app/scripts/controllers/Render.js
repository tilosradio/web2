'use strict';

angular.module('tilosApp').controller('RenderCtrl', function ($scope, FeedService, $http, API_SERVER_ENDPOINT, $location, $anchorScroll) {
	$scope.gotoTop = function (){
		$location.hash('top');
		$anchorScroll();
	};
});