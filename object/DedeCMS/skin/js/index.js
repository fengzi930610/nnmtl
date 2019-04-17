
$(document).ready(function(){   

    /*首页*/      
    function adhr(adhrs){
        $(adhrs).hover(function(){
            $(this).addClass("act");    
        },function(){
            $(this).removeClass("act"); 
        })  
    }

   $(".aouce").hover(function(){
        $(this).find(".dou").show();    
    },function(){
        $(this).find(".dou").hide();    
    })


    $(".ifs-bt li").hover(function(){
        $(this).find(".ifsb-hide").stop().animate({top:0},300);
    },function(){
        $(this).find(".ifsb-hide").stop().animate({top:"351px"},300);
    })
    adhr(".ifsb-hide i");

    $(".itbl").hover(function(){
        $(this).parents(".itb-lt").siblings(".itb-ct").find("span").text($(this).find("img").attr("zdy0"));
        $(this).parents(".itb-lt").siblings(".itb-ct").find("a").attr({href:$(this).find("img").attr("zdy3")});
        $(this).parents(".itb-lt").siblings(".itb-ct").find("em").html($(this).find("img").attr("zdy1")+"<i>"+$(this).find("img").attr("zdy2")+"</i>");
        // $(this).parents(".itb-lt").siblings(".itb-ct").find("i").text($(this).find("img").attr("zdy2"));
        $(this).parents(".itb-lt").siblings(".itb-ct").find("img").attr({src:$(this).find("img").attr("src")});
        $(".itbl p").stop(true,true).fadeIn(200);
        $(this).find("p").stop(true,true).fadeOut(200);
    })




    $(".house-ti li").hover(function(){
        $(this).addClass("active").siblings("li").removeClass("active");    
        $(this).parents(".house-ti").siblings(".house-con").find(".dn").hide().eq($(this).index()).show();
    })
    

    $('.h-float li').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
    });




    $('.nav-link').click(function(){
        $(this).addClass('active').parent().siblings().find('.nav-link').removeClass('active');
    });

     // $(".diamond-list li:eq(0)").addClass("move"); 

     //        setTimeout(function(){
     //           $(".diamond-list li:eq(1)").addClass("move"); 
     //        },500);
     //        setTimeout(function(){
     //           $(".diamond-list li:eq(2)").addClass("move"); 
     //        },1000);
     //        setTimeout(function(){
     //           $(".diamond-list li:eq(3)").addClass("move"); 
     //        },1500);

    var caseListWidth = $('.case-list li').width();
    var caseListIndex = $('.case-list li').length;
    $('.case-list ul').width(caseListWidth * caseListIndex);

    $('.case-list a').click(function(){

        $(this).addClass('active').siblings().removeClass('active').parent().siblings().find('a').removeClass('active');

        var ROOT="";

        $.post(ROOT + '/Findex/information',{cid:$(this).attr("cid")}, function (m) {
           
            var json = eval(m); //数组

            $.each(json, function(index, item){
                 //循环获取数据
                var title=json[index].title;

                var thumb=json[index].thumb;

                var mianji=json[index].mianji;

                var zaojia=json[index].zaojia;

                var url=json[index].url;

                var gurl=json[index].gurl;

                $(".case-big-info h3").text(title);

                $(".case-big-info p").html('面积：'+mianji+'㎡    造价：'+zaojia+'万');

                $(".case-big-img img").attr('src','/public/uploads/'+thumb);

                $(".case-big a").attr("href",gurl+url);

            });

        })


    });



    $('.pick-down').click(function(){
        $(this).parent().siblings().find('.pick-down').removeClass('active');
        $(this).parent().siblings().find('.team-info').removeClass('active');
        if($(this).hasClass('active')) {
            $(this).parent().siblings().find('.position-down').animate({bottom: '-400px'},300);
            $(this).siblings('.team-info').removeClass('active');
            $(this).removeClass('active');
            $(this).siblings('.position-down').animate({bottom: '-400px'},300);
        } else {
            $(this).parent().siblings().find('.position-down').animate({bottom: '-400px'},300);
            $(this).addClass('active');
            $(this).siblings('.team-info').addClass('active');
            $(this).siblings('.position-down').animate({bottom: '93px'},300);
        }
    });

    $('.pick-up').click(function(){
        $(this).parent().siblings().find('.pick-up').removeClass('active');
        $(this).parent().siblings().find('.team-info').removeClass('active');
        if($(this).hasClass('active')) {
            $(this).parent().siblings().find('.position-up').animate({top: '-400px'},300);
            $(this).removeClass('active');
            $(this).siblings('.team-info').removeClass('active');
            $(this).siblings('.position-up').animate({top: '-400px'},300);
        } else {
            $(this).parent().siblings().find('.position-up').animate({top: '-400px'},300);
            $(this).addClass('active');
            $(this).siblings('.team-info').addClass('active');
            $(this).siblings('.position-up').animate({top: '5px'},300);
        }
    });

    $('.follow').click(function(){
        $(this).addClass('active');
         var $this = $(this);
        $.post(root + '/findex/dianzan', {cid : $(this).attr('cid')}, function(re){
            if (re.status==1) {

                // layer.msg(re.info);
              
                $(".follow").html('+'+re.data);

            } else {
                layer.msg(re.info);
                

            }

        }, 'json')
    });

    // honur
    // var we_ul = $(".team-show-slide");
    // var we_len = we_ul.find('div').length;
    // var weli_width = we_ul.find('div:first').width();
    // var we_width = we_len * weli_width;
    // we_ul.css({width : we_width});
    // // 左
    // $(".a-left").click(function () {
    //     if (!we_ul.is(':animated')) {
    //         we_ul.stop().animate({'left' : '-' + weli_width}, 500, function () {
    //             $(this).append(we_ul.find('li:first'));
    //             $(this).css({left : 0});
    //         })
    //     }
        
    // })
    // // 右
    // $(".a-right").click(function () {
    //     if (!we_ul.is(':animated')) {
    //         var last_li = we_ul.find('div:last');
    //         we_ul.find('div:first').before(last_li);
    //         we_ul.stop().css({'left' : '-' + weli_width + 'px'}).animate({left : 0}, 500);
    //     }
    // });
    // $(this).children("input:last-child")


     $('.team-show-slide').each(function(){
        var we_ul = $(this);
        var we_len = we_ul.find('.team-show-img').length;
        var weli_width = we_ul.find('.team-show-img').first().width();
        var we_width = we_len * weli_width;
        we_ul.css({width : we_width});
        // 左
        $(document).on("click",".a-left",function(){

            if (!we_ul.is(':animated')) {
                we_ul.stop().animate({'left' : '-' + weli_width}, 500, function () {
                    $(this).append(we_ul.find('.team-show-img').first());
                    $(this).css({left : 0});
                })
            }
            
        })
        // 右
        
         $(document).on("click",".a-right",function(){
            if (!we_ul.is(':animated')) {
                var last_li = we_ul.find('.team-show-img').last();
                we_ul.find('.team-show-img').first().before(last_li);
                we_ul.stop().css({'left' : '-' + weli_width + 'px'}).animate({left : 0}, 500);
            }
        });
    });




    // 浮动条

    // $(".close").click(function(){
    //     $(this).parents(".float-bar").animate({width: 0},300,function(){
    //         $(this).hide();
    //         $(this).siblings(".float-boy").show().animate({left: 0},300);
    //     });
    // });
    // $(".float-boy").click(function(){
    //     $(this).animate({left: "-229"},300,function(){
    //         $(this).hide();
    //         $(this).siblings(".float-bar").show().animate({width: "100%"},300);
    //     });
    // });


    var hd=parseInt($(window).height());
    $(window).scroll(function(){
        GetPayBtn();
    });
    GetPayBtn = function(){
        $('.box-slide').each(function (i){
             if($(document).scrollTop()>$(".box-slide").eq(i).offset().top-hd){
                $(".box-slide").eq(i).addClass("move");
            }
        })
    };
    GetPayBtn();



    $('.case-classify-list a').click(function(){
        $(this).addClass('active').siblings('a').removeClass('active');
    });

    $('.style-list li').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
    });


    $('.design-content .team-box').click(function(){
        var ROOT="";
        $.post(ROOT + '/Findex/shejiinfo',{cid:$(this).attr("cid")}, function (m) {
            var obj = JSON.parse(m); 
        
            $(".designer-info-ti h3").html(obj.title+'<em>'+obj.zhiwei+'</em>');

            $(".designer-info-ti p").text(obj.jingyan);

            $(".follow").text('+'+obj.dianzane);

            $(".designer-style").empty().append(obj.fengged);  

            $(".team-show-slide").empty().append(obj.anli); 

            $(".follow").attr("cid",obj.cid);

            $(".designer-date a").attr("cid",obj.cid);

            $(".designer-date a").attr("title",obj.title);

            $(".designer-date a").attr("yuyue",obj.yuyue);
            
            $(".designer-info-con p").empty().append('设计理念：'+obj.description);

        })

        $('.team').find('.team-box').removeClass('active');
        $(this).addClass('active').siblings(".team-box").removeClass('active');
        $(this).parent().siblings().slideDown(300).parent().siblings('.design-content').find('.design-slide').slideUp(300);
    });

    $('.team-close').click(function(){
        $('.team').find('.team-box').removeClass('active');
        $('.design-slide').slideUp(300);
    });

    $('.about-right li a').click(function(){
        $(this).parent().addClass('active').siblings().removeClass('active');
    });

    $('.notice-bar a').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
    });



    // 获取装修预算报价报价

    $(".price-btn").click(function(){
        var root="";
        $.post(root+'/fguestbook/add',$("#baojia_form").serialize(),function(data){
            if(data.status=='success'){
                layer.msg(data.msg);
                $("#xingming1").val(null);
                $("#tel1").val(null);
                $("#mianji1").val(null);
                $("#xiaoqu1").val(null);
            }else{
                 layer.msg(data.msg);
            }
            
        },'json');
    });


     // 装修团购页面获取团购信息

    $(".raid-btn").click(function(){
     
        var root="";
        $.post(root+'/fguestbook/add',$("#tuangou_form").serialize(),function(data){
            if(data.status=='success'){

                layer.msg(data.msg);
                $("#xingming4").val(null);
                $("#tel4").val(null);
                $("#xiaoqu4").val(null);
                $("#house").val(null);
            }else{
                layer.msg(data.msg);
            }
            
        },'json');
    });


    // 尾部提交表单
    $(".float-btn").click(function(){
        var root="";
        $.post(root+'/fguestbook/add1',$(".float-bar").serialize(),function(data){
            if(data.status=='success'){
                layer.msg(data.msg);
                $("#xingming3").val(null);
                $("#tel3").val(null);
                
               
            }else{
                 layer.msg(data.msg);
            }
            
        },'json');
    });


    //首页报名到店提交表单
    $("#tijiao").click(function(){
        var root="";
        $.post(root+'/fguestbook/add',$(".area-sign").serialize(),function(data){
            if(data.status=='success'){
                layer.msg(data.msg);
                $("#xingming2").val(null);
                $("#tel2").val(null);
                $("#xiaoqu2").val(null);
                $("#mianji2").val(null);
                
            }else{
                 layer.msg(data.msg);
            }
            
        },'json');
    });


    // $('.free-btn').click(function(){
    //     $('.mask').show();
    //     $('.change').show();
    // });

    $('.change-close').click(function(){
        $('.mask').hide();
        $('.change').hide();
    });

    // 点击免费获取报价
    $(".change-btn").click(function(){
         var root="";
        $.post(root+'/fguestbook/add3',$(".baojia_forme3").serialize(),function(data){
            if(data.status=='success'){
                layer.msg(data.msg);
         
                $("#mianji5").val(data.data);
                $("#tel5").val(data.data);
            }else{
                 layer.msg(data.msg);
            }
            
        },'json');
    })


    $(".inpo-yy").css({height:$("body").height()}); 
    adhr(".inpof-st input");


    $(".inpof-off,.inpo-yy").click(function(){
        $(".inpo-yy").stop(true,true).fadeOut(300); 
        $(".inpo-form").stop().animate({top:"-800px"},300); 
    })
     $(".close").click(function(){
        $(this).parents(".float-bar").animate({width: 0},300,function(){
            $(this).hide();
            $(this).siblings(".float-boy").show().animate({left: 0},300);
        });
    });

    $(".alb-bt a,.work-man a.adiw,.now-link").click(function(){


        $(".inpo-form #sheji_input").val($(this).attr('title'));  
        // $(".inpof-num i").text($(this).attr('yuyue'));  
        // $(".inpo-form #yuyue_input").val($(this).attr('yuyue')); 
        $(".inpo-form #cid_input").val($(this).attr('cid'));  
        $(".inpo-yy").stop(true,true).fadeIn(300);  
        $(".inpo-form").stop().animate({top:($(window).height()-$(".inpo-form").height())/2},300);  
    })


    $(".yuyuew").click(function(){
        $(".inpo-yy").stop(true,true).fadeIn(300);  
        $(".inpo-form").stop().animate({top:($(window).height()-$(".inpo-form").height())/2},300);  
    })
    // 预约设计师
    $(".inpof-st").click(function(){
         var root="";
        $.post(root+'/fguestbook/add4',$("#form_yuyue").serialize(),function(data){
            if(data.status=='success'){
                layer.msg(data.msg);
                var eiwn=Number($(".inpof-num i").text()); 
                $(".inpof-num i").text(Number(eiwn)+Number(1));
                $("#xingming6").val("您的姓名");
                $("#tel6").val("您的手机号码");
                $("#xiaoqu6").val("所在楼盘");
                $("#mianji6").val("房屋面积");
            }else{
                 layer.msg(data.msg);
            }
            
        },'json');
    })


    // 预约参观工地
    $(".cgdiv").click(function(){
        $(".inpo-yy").stop(true,true).fadeIn(300);  
        $(".inpo-form").stop().animate({top:($(window).height()-$(".inpo-form").height())/2},300);  
    })


    $(".inpof-st").click(function(){
         var root="";
        $.post(root+'/fguestbook/add4',$("#form_gongdi").serialize(),function(data){
            if(data.status=='success'){
                layer.msg(data.msg);
                var eiwn=Number($(".inpof-num i").text()); 
                $(".inpof-num i").text(Number(eiwn)+Number(1));
                
                $("#xingming6").val("您的姓名");
                $("#tel6").val("您的手机号码");
                $("#xiaoqu6").val("所在楼盘");
                $("#mianji6").val("房屋面积");
            }else{
                 layer.msg(data.msg);
            }
            
        },'json');
    })



    // 设计师列表点击
    $(".designer-date a").click(function(){

        $(".inpo-form #sheji_input").val($(this).attr('title'));  
        // $(".inpof-num i").text($(this).attr('yuyue'));  
        // $(".inpo-form #yuyue_input").val($(this).attr('yuyue')); 
        $(".inpo-form #cid_input").val($(this).attr('cid'));  


        $(".inpo-yy").stop(true,true).fadeIn(300);  
        $(".inpo-form").stop().animate({top:($(window).height()-$(".inpo-form").height())/2},300); 
    })



    $(".float-boy").click(function(){
        $('.calculator').show();
    });

    $(".cal-close").click(function(){
        $('.calculator').hide();
    });


    $('#mianji3').on('keyup', function () {
        var square = $(this).val();
        var sq = parseInt(square);
        if (sq > 0 && sq < 60) {
            $('[name="shi"]').val(1);
            $('[name="ting"]').val(1);
            $('[name="wei"]').val(1);
            $('[name="tai"]').val(1);
            $('[name="chu"]').val(1);
        }
        if(sq>=60 && sq<=80){
            $('[name="shi"]').val(2);
            $('[name="ting"]').val(1);
            $('[name="wei"]').val(1);
            $('[name="tai"]').val(1);
            $('[name="chu"]').val(1);
        }
        if(sq>80 && sq<150){
            $('[name="shi"]').val(3);
            $('[name="ting"]').val(2);
            $('[name="wei"]').val(2);
            $('[name="tai"]').val(1);
            $('[name="chu"]').val(1);
        }
        if(sq>=150 && sq<250){
            $('[name="shi"]').val(4);
            $('[name="ting"]').val(2);
            $('[name="wei"]').val(2);
            $('[name="tai"]').val(2);
            $('[name="chu"]').val(1);
        }
        if(sq>=250 && sq<350){
            $('[name="shi"]').val(5);
            $('[name="ting"]').val(2);
            $('[name="wei"]').val(3);
            $('[name="tai"]').val(3);
        }
        if(sq>=350){
            $('[name="shi"]').val(6);
        }
    });



    // 弹出框报价

    $(".cal-btn").click(function(){
       

        var formData = $(".calculator").serialize();

        $.post(root + "/fguestbook/add5",formData,function(data){

            if (data.status=='success'){

                $(".calculator-done-ti em").empty().text(data.data);

                $(':input','.calculator') 
                .not(':button, :submit, :reset, :hidden') 
                .val('') 
                .removeAttr('checked') 
                .removeAttr('selected');

                var weon=$(".calculator-ti i").text();
                $(".calculator-ti i").text(Number(weon)+Number(1));

            }else{
                layer.msg(data.msg);

            }

        }, "json");
    })


    $(".designer-pick li").click(function () {
        var th = $(this);
        var aclass = 'active';
        var box = $(this).parents('.designer-pick').siblings('.designer-con');
        var pclass = '.designer-con-';
        var thid = $(this).attr('aa');
        x(th, aclass, box, pclass, thid);

    });
    /**
     * 选项卡
     * @param  {[this]}
     * @param  {[要删除或者添加的li Class 名]}
     * @param  {[DIV的父级节点]}
     * @param  {[要查找的DIV 的 Class 或 ID]}
     * @param  {[DIV的第几个]}
     */
    function x (th, aclass, box, pclass, thid) {
        // 统计li的个数
        var len = th.siblings('li').length;
        // 当点击li的时候给所有的同级li删除class
        th.siblings().removeClass(aclass);
        // 给当前点击的li添加class
        th.addClass(aclass);
        
        // 循环隐藏对应的div选项框
        for ( var i = 1; i < len + 2; i++) {
            box.find(pclass + i).stop().fadeOut(300).hide();
        }

        // 显示对应的div
        box.find(pclass + thid).stop().fadeIn(300).show();

    };





      // 弹出框报价

    $(".cal-btn2").click(function(){
       

        var formData = $(".calculator2").serialize();

        $.post(root + "/fguestbook/add5",formData,function(data){

            if (data.status=='success'){

                $(".calculator2-done-ti em").empty().text(data.data);

                $(':input','.calculator2') 
                .not(':button, :submit, :reset, :hidden') 
                .val('') 
                .removeAttr('checked') 
                .removeAttr('selected');

                var weon=$(".calculator2-ti2 i").text();
                $(".calculator2-ti i").text(Number(weon)+Number(1));

            }else{
                layer.msg(data.msg);

            }

        }, "json");
    })

    $(".h-float").height($(".h-float li").length*56-20);

   $(".h-float2").height($(".h-float2 li").length*56-20);
    setTimeout(function(){

        $('.calculator').show();

    },10000);

})


