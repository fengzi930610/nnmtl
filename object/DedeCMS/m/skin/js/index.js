$(document).ready(function(){	

        

  		var hh = $('.header').height();
        $('.menu-btn').css({width: hh,height: hh});

        $('.header-margin').css({height: hh});


        $('.menu-btn').click(function(){
            if($(this).hasClass('act')){
                $(this).removeClass('act');
                $('.waterfall').animate({top: '-100%'},200);
                $('body').removeClass('ov');
            }else{
                $(this).addClass('act');
                $('.waterfall').animate({top: 0},200);
            	$('body').addClass('ov');
            }
        });

        $('.nav-link').click(function(){
            $(this).addClass('active').parent().siblings().find('a').removeClass('active');
        });

        $('.cal-bar').click(function(){
            $('.mask').show();
            $('.free').show();
        });

        $('.free-close').click(function(){
            $('.mask').hide();
            $('.free').hide();
            $('.free-design').hide();
        });

        $('.classify-box-btn').click(function(e){
            $(this).parent().siblings().find('.classify-box-btn').removeClass('active');
            $(this).parent().siblings().find('.classify-drop').slideUp('fast');
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $(this).siblings().slideUp('fast');
                $('.mask2').hide();
            }else{
                $(this).addClass('active');
                $(this).siblings().slideDown('fast');
                $('.mask2').show();
            }
            e.stopPropagation();
        });

        $('.classify-drop a').click(function(){
            $('.classify-drop a').removeClass('active');
            $(this).addClass('active');
        });
        
        
        

        $("body").click(function(e){
            $('.classify-drop').slideUp('fast');
            $('.classify-box-btn').removeClass('active');
            e.stopPropagation();
        });

        // $('.date-btn').click(function(){
        //     $('.mask').show();
        //     $('.free-design').show();
        // });

        $('.sub-nav a').click(function(){
            $(this).addClass('active').siblings().removeClass('active');
        });


    // 首页报价


    $("#baojia").click(function(){
     
        var root="";
        $.post(root+'/fguestbook/add',$("#baojia_form2").serialize(),function(data){
            if(data.status=='success'){

                layer.msg(data.msg);
                $("#xingming").val(null);
                $("#tel").val(null);

                 $("#mianji").val(null);
                $("#xiaoqu").val(null);

      
            }else{
                layer.msg(data.msg);
            }
            
        },'json');
    });



     // 底部弹窗框

    $(".free-btn").click(function(){
     
        var root="";
        $.post(root+'/fguestbook/add1',$("#baojia_form").serialize(),function(data){
            if(data.status=='success'){

                layer.msg(data.msg);
                $("#xingming2").val(null);
                $("#tel2").val(null);
      
            }else{
                layer.msg(data.msg);
            }
            
        },'json');
    });

    $(".date-btn,.foryou a,.de-btn,.area-detail-con a").click(function(){
     
        $('.mask2').show();
        $('.free2').show();
    })


    $('.free-close2').click(function(){
        $('.mask2').hide();
        $('.free2').hide();
        $('.free-design2').hide();
    });

    // 预约设计师


    $(".free-btn2").click(function(){
     
        var root="";
        $.post(root+'/fguestbook/add',$("#form2").serialize(),function(data){
            if(data.status=='success'){

                layer.msg(data.msg);
                $("#xingming3").val(null);
                $("#tel3").val(null);

                 $("#mianji3").val(null);
                $("#xiaoqu3").val(null);

      
            }else{
                layer.msg(data.msg);
            }
            
        },'json');
    });






})