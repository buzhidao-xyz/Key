//定义模块CommonController
define(["app"], function ($app){
	var WebApp = $app.WebApp;

	WebApp.controller("CommonController", ['$scope', function ($scope) {
		//监听事件 - apiRequest.failed
		$scope.$on('apiRequest.failed', function (event, d){
			var msg = '网络错误 请求失败！';
			alert(msg);
		});
	}]);
});