//userservicejs
define(["app", "api"], function ($app, $api){
	var WebApp = $app.WebApp;

	WebApp.service('UserService', ['$rootScope', '$http', function ($rootScope, $http){
		var Service = {
			//getUserList
			userlist: {},
			getUserList: function (param){
				var url = $api.host + $api.user.userlist.u;
				$http({
					method: $api.user.userlist.m,
					url: url,
					params: param
				}).success(function (data, status){
					Service.userlist = data.userlist;

					$rootScope.$broadcast('getUserList.success');
				}).error(function (data, status){
					$rootScope.$broadcast('apiRequest.failed');
				});
			},

			//getUserProfile
			userprofile: {
				"userid": 0,
				"username": "",
				"age": 0
			},
			getUserProfile: function (param){
				var url = $api.host + $api.user.userprofile.u;
				$http({
					method: $api.user.userlist.m,
					url: url,
					params: param
				}).success(function (data, status){
					Service.userprofile = data;

					$rootScope.$broadcast('getUserProfile.success');
				}).error(function (data, status){
					$rootScope.$broadcast('apiRequest.failed');
				});
			}
		}

		return Service;
	}]);
});