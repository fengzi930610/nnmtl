/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: widget.bmap.js 9926 2015-04-28 06:09:32Z youyi $
 */
window.KT = window.KT || { "verison" : "1.0a" };
window.Widget  = window.Widget || {};
(function(K, $){
    var BMap = Widget.BMap = Widget.BMap || {};
    window.Marker_handler = function(){};
    BMap.Marker = function(point, handler){
        window.Marker_handler = handler;
        var link = "/static/baidu/gaode.html";
        if(typeof(point) == 'object'){
            if(link.indexOf("?")>-1){
                link += "&lng="+point.lng+"&lat="+point.lat;
            }else{
                link += "?lng="+point.lng+"&lat="+point.lat;
            }
        }
        layer.open({
            type: 2,
            area: ['700px', '530px'],
            skin: 'layui-layer-rim',
            content: link,
            success: function(layero, index){

            },
            btn: ['确认选择','取消选择'],
            yes: function(index,layero){
                var body = $(window.frames["layui-layer-iframe"+index].document).find("body");
                var point = body.find('#Baidu_Map_Marker').val().split(",");
                if(point.length > 1){
                    window.Marker_handler({"lng":$.trim(point[0]),"lat":$.trim(point[1])});
                }
                layer.close(index);         
            }
        });
    }
})(window.KT, window.jQuery);