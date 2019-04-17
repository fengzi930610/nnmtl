<?php
$banner =selectOne('banner', 'imgsrc', '`group`=5 order by Id limit 1');
$contact =selectOne('system', '*', '1');
//print_r($contact);die;
include('app/home/view/contact/index.html');
?>