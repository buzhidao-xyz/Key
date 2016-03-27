//bootstrap.js
define(["require", "angular", "app"], function (require, angular){
    //文档加载完成 启动angular.module-WebApp
    angular.element(document).ready(function() {
        angular.bootstrap(document, ['WebApp']);
    });
});