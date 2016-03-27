//appjs
define(["require", "angular", "angular-route", "angular-cookies"], function (require, angular){
    //注册angular.module-WebApp
	var WebApp = angular.module("WebApp", ["ngRoute", "ngCookies"]);
	//配置angularmodule
	// WebApp.config(['$locationProvider', function($locationProvider) {
	// 	$locationProvider.html5Mode();
	// }]);

    //文档加载完成 启动angular.module-WebApp
    // angular.element(document).ready(function() {
    //     angular.bootstrap(document, ['WebApp']);
    // });

	//返回模块对象
	return {
		WebApp: WebApp
	}
});