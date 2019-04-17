<?php
$banner = selectAll('banner', '*', '`group`=1 order by Id desc limit 3');
$jianjie = selectOne('about', '*', '1');
//首页新闻展示区
$list1 = selectOne('news', '*', 'type=3 order by Id limit 1');
$list2 = selectOne('news', '*', 'type=4 order by Id limit 1');
$list3 = selectOne('news', '*', 'type=10 order by Id limit 1');
//迷彩服首页展示
$micailist = selectAll('product', '*', 'type=11 and state=1 order by Id desc limit 5');
//服饰定制专区
//保安服
$display1 = selectOne('product', '*', 'type=2 order by Id limit 1');
//工作服
$display2 = selectOne('product', '*', 'type=14 order by Id limit 1');
//迷彩服
$display3 = selectOne('product', '*', 'type=11 order by Id limit 1');
//职业服
$display4 = selectOne('product', '*', 'type=13 order by Id limit 1');
//print_r($micailist);die;
include("app/home/view/index/index.html");

	
		
?>