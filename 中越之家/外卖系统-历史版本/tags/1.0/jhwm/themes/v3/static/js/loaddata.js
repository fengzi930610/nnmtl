window.__scroll_load_lock = false;

var pages = 1;
var url = '';
var list_ids = '';
var parmas1 = '';
var obj21 = '';


function loadpage(link,params,page,list_id){
    url = link;
    list_ids = list_id;
    parmas1 = params;
   /* if(!page){
        pages = 1;
    }else{
        pages = page;
    }*/
    pages = 1;
    if(!list_id){
        list_ids = "index_goods_items";
    }
    loaddata(url,pages,params,list_ids);
}


function showLoader(msg, st) {
    if(st){
       var message = '<div class="weui-loadmore"><i class="weui-loading"></i><span class="weui-loadmore__tips">' + msg + '</span></div>';
    }else{
       var message = '<div class="weui-loadmore weui-loadmore_line"><span class="weui-loadmore__tips">' + msg + '</span></div>';
    }
    $(".loadding").html(message).show();
}

function hideLoader()
{
    $(".loadding").hide();
}



function loaddata(link, page, params,list_id) {
    if (!page) {
        page = 1;
    }
    if(!list_id){
        list_id = "index_goods_items";
    }
    if(pages==1){
        $('#'+list_id).html('');
    }
    showLoader('正在加载中', true);
    $.getJSON(link.replace('#page#', pages), params, function (ret) {
        if (ret.loadst == 0) {
            hideLoader();
        }
        if (page == 1) {
            if (ret.html == " " || ret.html == "") {
                showLoader('没有找到数据', false);
            }
            $("#"+list_id).html(ret.html);
            $("#"+list_id).append("<div class='clear'></div>");
            window.__scroll_load_lock = false;
        } else {
            if(ret.html != " "&&ret.html != ""){
                $("#"+list_id).append(ret.html);
                $("#"+list_id).append("<div class='clear'></div>");
                window.__scroll_load_lock = false;
            }else{
                showLoader('没有更多了', false);

            }

        }

    });
    pages++;


}

function scroll(link,params,page,obj,obj2,list_id){
    url = link;
    list_ids = list_id;
    if(window.__scroll_load_lock){
        return false;
    }
    if(!obj){
        obj = ".container_mid";
    }
    if(!obj2){
        obj2 = ".list_cont_product";
    }

    obj21 = obj2;
    $(obj).scroll(function () {//监听滚动条改变
        //console.log('obj滚动条偏移为：'+$(obj).scrollTop()+'obj2:'+($('.mall-mydingdan-list-cont').height()-$(window).height()));
        if($(obj).scrollTop() >= ($(obj2).height()-$(window).height())){

            if (window.__scroll_load_lock) {
                 return false;
            }
            loaddata(url,pages,params,list_ids);
            window.__scroll_load_lock = true;
        }
    });   
}