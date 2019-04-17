<?php
$z[0]=Array("field","",304,323);
$z[0][4]=array();
$z[0][4]['name']="title";
$z[1]=Array("global","",324,350);
$z[1][4]=array();
$z[1][4]['name']="cfg_webname";
$z[2]=Array("field","",390,419);
$z[2][4]=array();
$z[2][4]['name']="keywords";
$z[3]=Array("field","",458,516);
$z[3][4]=array();
$z[3][4]['name']="description";
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("include","",915,950);
$z[4][4]=array();
$z[4][4]['filename']="head.htm";
$z[5]=Array("channel"," <a href=\"[field:typeurl/]\">[field:typename/]</a> ",1060,1218);
$z[5][4]=array();
$z[5][4]['type']="son";
$z[5][4]['currentstyle']="<a href='~typelink~' class='active'>~typename~</a>";
$z[6]=Array("list","\n          <li>\n            <div class=\"notice-img fl\"> <a href=\"[field:arcurl/]\"> <img class=\"lazy\" src=\"[field:picname/]\" data-echo=\"[field:picname/]\" width=\"298\" height=\"198\" alt=\"[field:title/]\" /> </a> </div>\n            <div class=\"notice-info fr\">\n              <div class=\"notice-ti\">\n                <h3><a href=\"[field:arcurl/]\">[field:title/]</a></h3>\n                <p>[field:pubdate function=\"MyDate('Y-m-d',@me)\"/]</p>\n              </div>\n              <div class=\"notice-word\">\n                <p>[field:description function=\"cn_substr(@me,320)\"/]...</p>\n              </div>\n            </div>\n          </li>\n          ",1281,1956);
$z[6][4]=array();
$z[6][4]['pagesize']="6";
$z[7]=Array("pagelist","",2077,2133);
$z[7][4]=array();
$z[7][4]['listitem']="pre,next,pageno";
$z[7][4]['listsize']="5";
$z[8]=Array("include","",2314,2351);
$z[8][4]=array();
$z[8][4]['filename']="footer.htm";

?>