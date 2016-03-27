//goods.controller.js
define(["require", "app", "function", "commoncontroller", "goodsservice"], function ($require, $app, $function){
	var WebApp = $app.WebApp;

	WebApp.controller('GoodsController', ['$scope', '$controller', 'GoodsService', function ($scope, $controller, $GoodsService){
		var CommonController = $controller('CommonController', {$scope: $scope});

		var getGoodsList = function (){
			//获取商品列表
			$GoodsService.getGoodsList();
			//监听事件 - getGoodsList.success
			$scope.$on('getGoodsList.success', function (event, d){
				$scope.$goodslist = $GoodsService.goodslist;
			});
		}();
	}]);

	return {
		
	}
});