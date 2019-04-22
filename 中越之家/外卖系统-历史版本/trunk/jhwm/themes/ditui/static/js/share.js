window.Widget.Share = window.Widget.Share || {};
var Waimai_Url = "<{link ctl='/' http='waimai'}>";
Widget.Share.init = function(params){
    Widget.Share._params = params;
    //console.log(JSON.stringify(Widget.Share._params));
    if(checkIsAppClient()){
        //Widget.Share.AppShare(JSON.stringify(Widget.Share._params));
    }else if(checkIsWeixin()){
        Widget.Share.initwxshare();
        Widget.Share.WeixinShare(Widget.Share._params);
    }else{
        Widget.Share.webshare(Widget.Share._params);
    }   
}

Widget.Share.onShare = function(){ //点击按钮调用
    if(checkIsAppClient()){
        Widget.Share.AppShare(JSON.stringify(Widget.Share._params));
    }else if(checkIsWeixin()){
        var html = '<div class="mask_bg" onclick="close_mask();" style="opacity:0.7 !important;"></div><div onclick="close_mask();" class="share_phone"><img src="'+window.CFG.waimai_domain+'/themes/waimai/static/img/sharePic.png"></div>';
        $("body").append(html);
        $(".mask_bg").show();
    }else{   
        $("body").append('<div class="mask_bg" style="opacity:0.7 !important;"></div>');
        $(".share_mask,.mask_bg").show();
        $(".share_mask").addClass("show");        
    }
}

Widget.Share.initwxshare = function(){
    //微信JS SDK开始
    window.WXJS_CFG.jsApiList = [
        'checkJsApi',
        'onMenuShareAppMessage',
        'onMenuShareTimeline',
        'onMenuShareQQ',
    ];
    wx.config(window.WXJS_CFG);
}

Widget.Share.webshare  = function(params){
    var html = '<div class="share_mask"><div class="tit">分享邀请</div><div class="cont"><ul>';
    html += '<li class="list bdsharebuttonbox"><a href="javascript:;" data-cmd="weixin"><img src="/themes/waimai/static/images/my_share01@3x.png" data-cmd="weixin"><P>微信</P></a></li>';
    html += '<li class="list bdsharebuttonbox"><a href="javascript:;" target="_blank" data-cmd="sqq"><img src="/themes/waimai/static/images/my_share02@3x.png" data-cmd="sqq"><P>QQ</P></a></li>';
    html += '<li class="list bdsharebuttonbox"><a href="javascript:;" target="_blank" data-cmd="weixin"><img src="/themes/waimai/static/images/my_share03@3x.png" data-cmd="weixin"><P>朋友圈</P></a></li>';
    html += '<li class="list bdsharebuttonbox"><a href="javascript:;" target="_blank" data-cmd="qzone"><img src="/themes/waimai/static/images/my_share04@3x.png" data-cmd="qzone"><P>QQ空间</P></a></li>';
    html += '<li class="list bdsharebuttonbox"><a href="javascript:;" target="_blank" data-cmd="tsina"><img src="/themes/waimai/static/images/my_share05@3x.png" data-cmd="tsina"><P>新浪微博</P></a></li>';
    html += '<li class="list bdsharebuttonbox"><a href="javascript:;" target="_blank" data-cmd="renren"><img src="/themes/waimai/static/images/my_share06@3x.png" data-cmd="renren"><P>人人网</P></a></li>';
    html += '</ul><div class="clear"></div></div><div><a href="javascript:;" class="btn cancel" >取消</a></div></div><style>.bdshare-button-style0-16 a, .bdshare-button-style0-16 .bds_more{}</style>';
    $("body").append(html);
    $(document).on("click",".share_mask .cancel,.mask_bg",function(){
        $(".mask_bg").remove();
        $(".share_mask").removeClass("show");
    });
    window._bd_share_config ={
        "common":{"bdSnsKey":{},bdText : params.title,bdDesc : params.desc,bdUrl : params.link,bdPic : params.imgUrl,"bdMini":"0","bdStyle":"888","bdSize":"888"},
        "share":{},"image":{"viewList":["qzone","tsina","tqq","renren","weixin"],
        "viewText":"分享到：","viewSize":"16"},
        "selectShare":{"bdContainerClass":null,"bdSelectMiniList":["qzone","tsina","tqq","renren","weixin"]}
    };
    //with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)]; 
    with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='/static/cdn/share/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
}

Widget.Share.AppShare = function(params){
    window.JHAPP.onShare(params);
}

Widget.Share.WeixinShare = function(params){
    wx.ready(function(){
        // 发送给朋友
        wx.onMenuShareAppMessage({
            title: params.title, 
            desc: params.desc, 
            link: params.link, 
            imgUrl: params.imgUrl, 
            type: '', 
            dataUrl: '', 
            success: function () {
                $(".mask_bg").remove();
                $(".share_phone").remove();
                layer.open({content: '分享成功！', time: 1});
            },
            cancel: function () { 
                $(".mask_bg").remove();
                $(".share_phone").remove();
            }
        });
        // 分享到朋友圈
        wx.onMenuShareTimeline({
            title: params.title, 
            link: params.link, 
            imgUrl: params.imgUrl, 
            success: function () { 
                $(".mask_bg").remove();
                $(".share_phone").remove();
                layer.open({content: '分享成功！', time: 1});
            },
            cancel: function () { 
                $(".mask_bg").remove();
                $(".share_phone").remove();
            }
        });
        // 分享到手机QQ
        wx.onMenuShareQQ({
            title: params.title, 
            desc: params.desc, 
            link: params.link, 
            imgUrl: params.imgUrl, 
            success: function () { 
                $(".mask_bg").remove();
                $(".share_phone").remove();
                layer.open({content: '分享成功！', time: 1});
            },
            cancel: function () { 
                $(".mask_bg").remove();
                $(".share_phone").remove();
            }
        });
    });
}
