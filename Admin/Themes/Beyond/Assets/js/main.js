//init
requirejs(["./config"], function (){
	requirejs(["function"], function ($function){
        $function.HelloWorld();
        
		$function.getHtml();
	});
});
