<?php
$z[0]=Array("field","",21,49);
$z[0][4]=array();
$z[0][4]['name']="typeurl";
$z[1]=Array("field","",67,93);
$z[1][4]=array();
$z[1][4]['name']="currentstyle";
$z[2]=Array("field","\r\nglobal \$dsql;\r\n\$sql = \"SELECT id From `#@__arctype` WHERE reid='@me' And ishidden<>1 order by sortrank asc limit 0, 100 \";\r\n\$row = \$dsql->GetOne(\$sql);\r\n              @me=is_array(\$row)?'nav-drop':'';\r\n              ",94,362);
$z[2][4]=array();
$z[2][4]['name']="typeid";
$z[2][4]['runphp']="yes";
$z[3]=Array("field","",365,394);
$z[3][4]=array();
$z[3][4]['name']="typename";
$z[4]=Array("channel"," <a href=\"[field:typeurl/]\">[field:typename/]</a> ",438,528);
$z[4][4]=array();
$z[4][4]['type']="son";

?>