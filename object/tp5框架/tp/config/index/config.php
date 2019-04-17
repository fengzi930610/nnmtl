<?php
return[
//	'model' =>'admin',
	'app_status'             => 'true',
	
	'view_replace_str' =>  [
		'__PUBLIC__'  =>  '/public/static/index/',
	
	],
	'url_route_on'  =>  true,
	'url_route_must'=>  false,
	
	'template'  =>  [
    	'layout_on'     =>  true,
    	'layout_name'   =>  'layout',
	],
//	
];
?>