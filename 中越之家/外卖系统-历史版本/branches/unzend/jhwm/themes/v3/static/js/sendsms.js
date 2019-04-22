var minute = 60;
var mobile_timeout;
var mobile_count = minute;
var mobile_lock = 0;

function BtnCount(btn) {
    if(!btn){
        btn = 'get_yzm';
    }
    if (mobile_count == 0) {
        $("."+btn).addClass("graybg");
        $("."+btn).removeAttr("disabled");
        $("."+btn).text("重新获取");
        mobile_lock = 0;
        clearTimeout(mobile_timeout);
        $("."+btn).removeClass("graybg");
    } else {
        mobile_count--;
        $("."+btn).text(+mobile_count.toString() + "秒...");
        mobile_timeout = setTimeout(BtnCount, 1000);
    }
};

function sendsms(link,code_link,mobile,img_code,btn,is_register){
    if(!btn){
        btn = 'get_yzm';
    }
    if (mobile_lock == 0) {
        $.getJSON(link, {mobile:mobile,img_code:img_code,"is_register":is_register}, function (ret) {
            if (ret.error == 0) {
                if(ret.data.sms_code == 1){                    
                    code_alert(code_link,true);
                    //Widget.MsgBox.error('输入图形验证码重新获取短信验证码');
                    mobile_lock = 0;
                    $('.cancel').click(function(){
                        code_alert(code_link,false);
                    });
                    $('.confirm').click(function(){
                        var img_code2 = $('#img_code').val();
                        if(img_code2){
                            sendsms(link,code_link,mobile,img_code2,btn,is_register);
                        }else{
                            Widget.MsgBox.error('图形验证码不能为空！');
                        }
                        
                    });
                    $('#pass-verify').click(function () {
                        var time = new Date();
                        $('#pass-verify').attr('src', code_link + '?' + time);
                    })
                }else{
                    code_alert(code_link,false);
                    BtnCount(btn);
                    mobile_lock = 1;
                    $("."+btn).addClass("graybg");
                    $('.'+btn).attr("disabled", "disabled");
                    Widget.MsgBox.success(ret.message);
                }                        
            } else {
                //code_alert(code_link,false);
                Widget.MsgBox.error(ret.message);
                mobile_lock = 0;
            }
        });
        mobile_count = minute;
    }
}

function code_alert(link,is_show){

    if(is_show){
        var time = new Date();
        link = link + '?' + time;
        var html = '<div id="code_box">';
        html += '<div class="identifycode_mask_box">';
        html += '<div class="tit">输入如图所示验证码</div>';
        html += '<div class="cont">';
        html += '<div class="int_box"><input type="text" value="" id="img_code"><img verify="#pass-verify" id="pass-verify" class="yzm_img" src="'+link+'"/></div>';
        html += '</div>';
        html += '<div class="btn_box">';
        html += '<a href="javascript:;" class="btn cancel">取消</a>';
        html += '<a href="javascript:;" class="btn confirm">验证</a>';
        html += '</div>';
        html += '</div>';
        html += '<div class="identifycode_mask_bg"></div></div>';
        $('body').append(html);    
    }else{
        //$('.identifycode_mask_box').remove();
        //$('.identifycode_mask_bg').remove();
        $('#code_box').remove();
    }
    
}
