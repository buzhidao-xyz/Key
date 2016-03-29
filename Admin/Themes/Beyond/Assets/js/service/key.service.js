//keyservice.js
define(["app", "api"], function ($app, $api){
	var WebApp = $app.WebApp;

	WebApp.service('KeyService', ['$rootScope', '$http', function ($rootScope, $http){
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
			}
		}

		return Service;
	}]);
});