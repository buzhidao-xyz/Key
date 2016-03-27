requirejs.config({
	//lib基础路径
	baseUrl: "js",
	//包名-路径
	paths: {
		"app": "app",
		"boot": "boot",
		"function": "function",
		"jquery": "../../assets/jquery/jquery-2.1.4.min",
		"jquery.purl": "../../assets/jquery/jquery.purl",
		"bootstrap": "../../assets/bootstrap-3.3.4/js/bootstrap.min",
		"angular": "../../assets/angular-1.4.2/angular.min",
		"angular-route": "../../assets/angular-1.4.2/angular-route.min",
		"angular-cookies": "../../assets/angular-1.4.2/angular-cookies.min",
		"commoncontroller": "controller/common.controller",
		"commonservice": "service/common.service",
		"usercontroller": "controller/user.controller",
		"userservice": "service/user.service",
		"goodscontroller": "controller/goods.controller",
		"goodsservice": "service/goods.service"
	},
	//包依赖
	shim: {
		"function": {
			deps: ["jquery"],
			exports: "function"
		},
		"angular": {
			exports: "angular"
		},
		"angular-route": {
			deps: ["angular"],
			exports: "angular-route"
		},
		"angular-cookies": {
			deps: ["angular"],
			exports: "angular-cookies"
		},
		"bootstrap": {
			deps: ["jquery"],
			exports: "bootstrap"
		},
		"jquery.purl": {
			deps: ["jquery"],
			exports: "jquery.purl"
		}
	},
	//不缓存js
	urlArgs: "bust=" +  (new Date()).getTime()
});