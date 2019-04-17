<div class="news box-slide">
  <div class="news-content">
    <div class="index-ti">
      <h3>{dede:field name='typename'/}</h3>
    </div>
    <div class="news-list">
      <ul class="clearfix">
        {dede:arclist titlelen=80 orderby ='pubdate' row='3'}
        <li>
          <div class="news-time"> <em>[field:pubdate function="MyDate('m-d',@me)"/]</em> <span>[field:pubdate function="MyDate('Y',@me)"/]</span> </div>
          <div class="news-ti"> <a href="[field:arcurl/]">[field:title/]</a> </div>
          <div class="news-con">
            <p>[field:description function="cn_substr(@me,120)"/]...</p>
          </div>
        </li>
        {/dede:arclist}
      </ul>
    </div>
    <div class="news-btn"> <a href="{dede:field name='typeurl'/}">more</a> </div>
  </div>
</div>