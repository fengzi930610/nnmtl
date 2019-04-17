<?php
$z[0]=Array("global","",51,76);
$z[0][4]=array();
$z[0][4]['name']="cfg_ggtxet";
$z[1]=Array("global","",220,246);
$z[1][4]=array();
$z[1][4]['name']="cfg_webname";
$z[2]=Array("global","",449,475);
$z[2][4]=array();
$z[2][4]['name']="cfg_webname";
$z[3]=Array("global","",532,557);
$z[3][4]=array();
$z[3][4]['name']="cfg_cmsurl";
$z[4]=Array("global","",949,975);
$z[4][4]=array();
$z[4][4]['name']="cfg_webname";
$z[5]=Array("global","",1090,1112);
$z[5][4]=array();
$z[5][4]['name']="cfg_tel";
$z[6]=Array("global","",1146,1170);
$z[6][4]=array();
$z[6][4]['name']="cfg_phone";
$z[7]=Array("field","(@me==\"\")? @me=\" class='nav-link active' \":@me=\"\";",1367,1467);
$z[7][4]=array();
$z[7][4]['name']="typeid";
$z[7][4]['runphp']="yes";
$z[8]=Array("channelartlist","\r\n            <li> <a href=\"{dede:field name='typeurl'/}\" class=\"nav-link nav-drop {dede:field.currentstyle/}\">{dede:field name='typename'/}</a>\r\n              <div class=\"classify\">\r\n                \r\n                \r\n                <div class=\"classify-box\">\r\n                  <div class=\"classify-ti\"> <img src=\"/skin/img/c-icon2.png\" alt=\"设计风格\" /> <span>设计风格</span> </div>\r\n                  <div class=\"classify-list\"> {dede:php} AddFilter(2,1,'fg');{/dede:php} </div>\r\n                </div>\r\n                \r\n                <div class=\"classify-box\">\r\n                  <div class=\"classify-ti\"> <img src=\"/skin/img/c-icon3.png\" alt=\"装修户型\" /> <span>装修户型</span> </div>\r\n                  <div class=\"classify-list\"> {dede:php} AddFilter(2,1,'hx');{/dede:php} </div>\r\n                </div>\r\n                \r\n                <div class=\"classify-box\">\r\n                  <div class=\"classify-ti\"> <img src=\"/skin/img/c-icon4.png\" alt=\"装修面积\" /> <span>装修面积</span> </div>\r\n                  <div class=\"classify-list\">{dede:php} AddFilter(2,1,'mj');{/dede:php}</div>\r\n                </div>\r\n              </div>\r\n            </li>\r\n            ",1514,2793);
$z[8][4]=array();
$z[8][4]['typeid']="3,0";
$z[8][4]['currentstyle']="active";
$z[9]=Array("channelartlist","\r\n            <li> <a href=\"{dede:field name='typeurl'/}\" class=\"nav-link {dede:field.currentstyle/} {dede:field name=typeid runphp=\"yes\"}\r\nglobal \$dsql;\r\n\$sql = \"SELECT id From `#@__arctype` WHERE reid='@me' And ishidden<>1 order by sortrank asc limit 0, 100 \";\r\n\$row = \$dsql->GetOne(\$sql);\r\n              @me=is_array(\$row)?'nav-drop':'';\r\n              {/dede:field} \">{dede:field name='typename'/}</a>\r\n              <div class=\"nav-slide\"> {dede:channel type='son'} <a href=\"[field:typeurl/]\">[field:typename/]</a> {/dede:channel} </div>\r\n            </li>\r\n            ",2808,3472);
$z[9][4]=array();
$z[9][4]['typeid']="9,16,5,55,26,0";
$z[9][4]['currentstyle']="active";
$z[10]=Array("channelartlist","\r\n        <div class=\"free-btn fl\"> <a href=\"{dede:field name='typeurl'/}\"> <img src=\"/skin/img/free.png\" alt=\"免费获取报价\" /> </a> </div>\r\n        ",3515,3727);
$z[10][4]=array();
$z[10][4]['typeid']="50,0";

?>