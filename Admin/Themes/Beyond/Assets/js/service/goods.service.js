//goods.service.js
define(["app", "api"], function ($app, $api){
	var WebApp = $app.WebApp;

	WebApp.service('GoodsService', ['$rootScope', '$http', function ($rootScope, $http){
		var Service = {
			goodslist: {},
			getGoodsList: function (param){
				var url = $api.host + $api.goods.goodslist.u;
				$http({
					method: $api.goods.goodslist.m,
					url: url,
					params: param
				}).success(function (data, status){
					Service.goodslist = data.goodslist;

					$rootScope.$broadcast('getGoodsList.success');
				}).error(function (data, status){
					$rootScope.$broadcast('apiRequest.failed');
				});
			}
		};

		return Service;
	}]);
});