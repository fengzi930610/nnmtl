<li>
          <div class="news-time"> <em>[field:pubdate function="MyDate('m-d',@me)"/]</em> <span>[field:pubdate function="MyDate('Y',@me)"/]</span> </div>
          <div class="news-ti"> <a href="[field:arcurl/]">[field:title/]</a> </div>
          <div class="news-con">
            <p>[field:description function="cn_substr(@me,120)"/]...</p>
          </div>
        </li>