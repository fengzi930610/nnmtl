<?php
$banner = selectOne('banner', '*', '`group`=2 order by Id desc');
$team = selectAll('team', '*', "1");
$list = selectAll('category', '*', "fId=15");
$type = empty($_GET['type'])?16:$_GET['type'];
$re = selectOne('about','*',1);
include('app/home/view/about/index.html');
?>