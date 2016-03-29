//定义模块KeyController
define(["require", "app", "function", "commoncontroller", "keycontroller", "keyservice", "jquery.purl"], function ($require, $app, $function){
	var WebApp = $app.WebApp;

	//KeyController
	WebApp.controller("KeyController", ['$scope', '$controller', 'KeyService', function ($scope, $controller, $KeyService) {
		var CommonController = $controller('CommonController', {$scope: $scope});

		//获取userprofile
		var getUserProfile = function (){
			//获取userprofile
			var userid = $.url().param("userid");
			$UserService.getUserProfile({
				"userid": userid
			});
			//监听事件 - getUserProfile.success
			$scope.$on('getUserProfile.success', function (event, d){
				$scope.$userprofile = $UserService.userprofile;
			});
		};
	}]);

	return {
		
	}
});