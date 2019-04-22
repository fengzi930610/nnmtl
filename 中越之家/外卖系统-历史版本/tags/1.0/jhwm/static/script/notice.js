/**
 * Created by Administrator on 2016/12/2.
 */
//参数
//title 推送标题
//msg 消息内容 
//url 点击事件需要跳转的地址
//icon 图标

function notify(title, msg, url,filter) {

   /* if(filter.data.biz_order_new_count||filter.data.staff_order_new_count){
     var  emod = '<embed class="tixing"   height="0" width="0" src="/static/audio/dd.OGG"></embed>';
     $('.audio').append(emod);

     }*/
    
    var options = {
        body: msg,
        icon: "/static/images/notice.png"
    };
    var Notification = window.Notification || window.mozNotification || window.webkitNotification;
    
    if (Notification && Notification.permission === "granted") {
        var instance = new Notification(title, options);
        instance.onclick = function() {
            if(url){
                $('.J_iframe').attr('src',url)

            }
           

            // Something to do  
        };
        instance.onerror = function() {
            // Something to do  
        };
        instance.onshow = function() {

//                          setTimeout(instance.close, 3000);  
            setTimeout(function () {
                instance.close();
            },1000*30)


        };
        instance.onclose = function() {
            $('#notice').html('')
            // Something to do  
        };

    } else if (Notification && Notification.permission !== "denied") {

        Notification.requestPermission(function(status) {
            if (Notification.permission !== status) {
                Notification.permission = status;
            }
            // If the user said okay  
            if (status === "granted") {
                var instance = new Notification(title, options);
                instance.onclick = function() {
                    if(url){
                        window.location.href=url; 
                    }
                    


                    // Something to do  
                };
                instance.onerror = function() {

                    // Something to do  
                };
                instance.onshow = function() {

                    // Something to do  
                    setTimeout(instance.close, 1000*30);
                };
                instance.onclose = function() {
                    $('#notice').html('');

                    // Something to do  
                };
            } else {
              layer.msg(msg);
            }
        });
    } else {
        layer.msg(msg);
    }
}

