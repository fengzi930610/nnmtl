<div class="area box-slide">
  <div class="area-content">
    <div class="index-ti">
      <h3>{dede:field name='typename'/}</h3>
    </div>
    <ul class="ifs-bt mo">
      {dede:arclist titlelen=80 orderby ='pubdate' row='7'}
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
      {/dede:arclist}
      <form action="/plus/diy.php" enctype="multipart/form-data" method="post" class="area-sign" id="form2">
        <input type="hidden" name="action" value="post" />
        <input type="hidden" name="diyid" value="1" />
        <input type="hidden" name="do" value="2" />
        <h4>报名到店即送属于您家的设计方案一套</h4>
        <ul>
          <li>
            <input type="text" placeholder="请输入您的称呼" name="ch"  value="" id="ch2" />
          </li>
          <li>
            <input type="text" placeholder="请输入您的联系方式" name="tel"  value="" id="tel2" />
          </li>
          <li>
            <input type="text" placeholder="请输入您的小区名称" name="xq"  value="" id="xq2" />
          </li>
          <li>
            <label for="mj">
              <input type="text" placeholder="请输入您的房屋面积"  name="mj"  value="" id="mj2" />
              <span>㎡</span> </label>
          </li>
        </ul>
        <input type="hidden" name="dede_fields" value="hx,text;mj,text;xq,text;ch,text;tel,text;fg,select" />
        <input type="hidden" name="dede_fieldshash" value="fe2f763c04647b9a7ab7a0ac331a0711" />
        <div class="area-sign-btn">
          <input type="submit" name="submit" value="提交" id="form2"/>
        </div>
      </form>
      <script>
                    $(document).ready(function () {

                        $("#form2").submit(function () {
                            if ($("#ch2").val() == '') {
                                alert('请添加姓名');
                                $("#ch2").focus();
                                return false;
                            }
                            if ($("#tel2").val() == '') {
                                alert('请添加电话');
                                $("#tel2").focus();
                                return false;
                            }
                        })
                    })
                </script>
    </ul>
  </div>
</div>