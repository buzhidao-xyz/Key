//goods.js
requirejs(['./config'], function (){
	requirejs(["app", "goodscontroller"], function ($goodscontroller){
        requirejs(["boot"]);
	});
});