function showLoader(msg, st) {
    if(st){
       var message = '<div class="preloader txt_center next">' + msg + '</div>';
    }else{
       var message = '<div class="preloader txt_center">' + msg + '</div>';
    }
    $(".loadding").html(message).show();
}

function hideLoader()
{
    $(".loadding").hide();
}

function loaddata(link, page, params) {
    if (!page) {
        page = 1;
    }
    showLoader('点击加载更多', true);
    $.getJSON(link.replace('#page#', page), params, function (ret) {
        console.log(ret.loadst);
        if (ret.loadst == 0) {
            hideLoader();
        }
        if (page == 1) {
            $("#index_goods_items").html(ret.html);
            $("#index_goods_items").append("<div class='clear'></div>");
            if (ret.html == " " || ret.html == "") {
                showLoader('没有找到数据', false);
            }
        } else {
            if(ret.html != " "&&ret.html != ""){
                $("#index_goods_items").append(ret.html);
                $("#index_goods_items").append("<div class='clear'></div>");
            }else{
                showLoader('没有更多了', false);
            }
        }
    });
}