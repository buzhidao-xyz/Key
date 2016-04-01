requirejs.config({
	//lib基础路径
	// baseUrl: JS_SERVER,
	//包名-路径
	paths: {
		"app": "app",
		"boot": "boot",
		"function": "function",
		"jquery": "../jquery/jquery-1.11.3.min",
		"jquery.purl": "../jquery/jquery.purl",
		"bootstrap": "../bootstrap-3.3.4/js/bootstrap.min",
		"bootstrap-fileinput": "../plugins/bootstrap-fileinput/js/fileinput.min",
		"angular": "../angular-1.4.2/angular.min",
		"angular-route": "../angular-1.4.2/angular-route.min",
		"angular-cookies": "../angular-1.4.2/angular-cookies.min",
		"commoncontroller": "controller/common.controller",
		"commonservice": "service/common.service",
		"keycontroller": "controller/key.controller",
		"keyservice": "service/key.service"
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
		"bootstrap-fileinput": {
			deps: ["jquery"],
			exports: "bootstrap-fileinput",
		},
		"jquery.purl": {
			deps: ["jquery"],
			exports: "jquery.purl"
		}
	},
	//不缓存js
	urlArgs: "bust=" +  (new Date()).getTime()
});