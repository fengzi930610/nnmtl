<li [field:global runphp='yes' name=autoindex]
            $a="class='mro'";
            $b="";
            if ((@me%4)==0 && @me!=30) @me=$a;
            else  @me=$b;
            [/field:global]>
      <a href="[field:arcurl/]"> <img src="[field:picname/]" class="lazy" data-echo="[field:picname/]" alt="[field:title/]">
      <div class="ifsb-show">[field:title/]</div>
      <div class="ifsb-hide" style="top: 351px;">
        <p>[field:title/]</p>
        <b></b> <span>[field:click/]人已浏览</span> <i>立即查看</i> </div>
      </a>
      </li>