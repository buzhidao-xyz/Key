//user.js
requirejs(['./config'], function (){
	requirejs(["app", "usercontroller"], function (){
		requirejs(["boot"]);
	});
});