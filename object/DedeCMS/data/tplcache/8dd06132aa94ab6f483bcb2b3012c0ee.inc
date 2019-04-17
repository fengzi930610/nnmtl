<li> <a href="DedeCMS{dede:field name='typeurl'/}" class="nav-link {dede:field.currentstyle/} {dede:field name=typeid runphp="yes"}
global $dsql;
$sql = "SELECT id From `#@__arctype` WHERE reid='@me' And ishidden<>1 order by sortrank asc limit 0, 100 ";
$row = $dsql->GetOne($sql);
              @me=is_array($row)?'nav-drop':'';
              {/dede:field} ">{dede:field name='typename'/}</a>
              <div class="nav-slide"> {dede:channel type='son'} <a href="[field:typeurl/]">[field:typename/]</a> {/dede:channel} </div>
            </li>