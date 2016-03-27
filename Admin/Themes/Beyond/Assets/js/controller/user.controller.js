//定义模块UserController
define(["require", "app", "function", "commoncontroller", "userservice", "jquery.purl"], function ($require, $app, $function){
	var WebApp = $app.WebApp;

	//UserListController
	WebApp.controller("UserListController", ['$scope', '$controller', 'UserService', function ($scope, $controller, $UserService) {
		var CommonController = $controller('CommonController', {$scope: $scope});

		$UserService.getUserList();
		//监听事件 - getUserList.success
		$scope.$on('getUserList.success', function (event, d){
			$scope.$userlist = $UserService.userlist;
		});
	}]);

	//UserProfileController
	WebApp.controller("UserProfileController", ['$scope', '$controller', 'UserService', function ($scope, $controller, $UserService) {
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
		}();
	}]);

	return {
		
	}
});