<?php
$z[0]=Array("field","",14,42);
$z[0][4]=array();
$z[0][4]['name']="typeurl";
$z[1]=Array("field","",60,86);
$z[1][4]=array();
$z[1][4]['name']="currentstyle";
$z[2]=Array("field","\nglobal \$dsql;\n\$sql = \"SELECT id From `#@__arctype` WHERE reid='@me' And ishidden<>1 order by sortrank asc limit 0, 100 \";\n\$row = \$dsql->GetOne(\$sql);\n              @me=is_array(\$row)?'nav-drop':'';\n              ",87,350);
$z[2][4]=array();
$z[2][4]['name']="typeid";
$z[2][4]['runphp']="yes";
$z[3]=Array("field","",353,382);
$z[3][4]=array();
$z[3][4]['name']="typename";
$z[4]=Array("channel"," <a href=\"[field:typeurl/]\">[field:typename/]</a> ",425,515);
$z[4][4]=array();
$z[4][4]['type']="son";

?>