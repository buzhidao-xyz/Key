//user.js
requirejs([JS_SERVER+'config.js'], function (){
	requirejs(["app", "keycontroller"], function (){
		requirejs(["boot"]);
	});
});