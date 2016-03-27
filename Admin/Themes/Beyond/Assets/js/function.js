define(["jquery"], function ($){
    return {
        HelloWorld: function (){
            alert("Hello World!");
        },
        getHtml: function (){
            alert($("#Container").html());
        }
    }
});