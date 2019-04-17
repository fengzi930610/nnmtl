//判断name是否为空
function is_empty_input(name, msg) {
    var thisval;
    var type = arguments[2] ? arguments[2] : 'text';
    if (type == 'text') {
        thisval = $.trim($("input[name=" + name + "]").val());
    }
    else if (type == 'check') {
        thisval = $.trim($("input[name=" + name + "]:checked").val());
    }
    else if (type == 'select') {
        thisval = $.trim($("select[name=" + name + "]").val());
    }
    if (thisval == null || thisval == "" || thisval == undefined) {
        layer.msg(msg,2,-1);
        throw msg;
        return false;

    }
}
//判断name的长度
function is_len_input(name, msg, min, max) {
    var thisval;
    var flag;
    var type = arguments[4] ? arguments[4] : 'text';
    if (type == 'text') {
        thisval = $.trim($("input[name=" + name + "]").val());
        if (min > thisval.length || thisval.length > max) {
            flag = false;
        }
    }
    else if (type == 'check') {
        thisval = $.trim($("input[name=" + name + "]:checked").val());
        if (min > thisval.length || thisval.length > max) {
            flag = false;
        }
    }
    else if (type == 'select') {
        thisval = $.trim($("select[name=" + name + "]").val());
        if (min > thisval.length || thisval.length > max) {
            flag = false;
        }
    }
    if (flag == false) {
        layer.msg(msg,2,-1);
        throw msg;
        return false;

    }
}


//获取name的值
function get_name_val(name) {
    var thisval;
    var type = arguments[1] ? arguments[1] : 'text';
    if (type == 'text') {
        thisval = $.trim($("input[name=" + name + "]").val());
    }
    else if (type == 'check') {
        thisval = $.trim($("input[name=" + name + "]:checked").val());
    }
    else if (type == 'select') {
        thisval = $.trim($("select[name=" + name + "]").val());
    }
    return thisval;

}

//转化日期
function toDate(str) {
    var sd = str.split("-");
    return (sd[0] + sd[1] + sd[2]).toString();
}

//转化时间
function toTime(str) {
    var sd = str.split(":");
    return (sd[0] + sd[1] + sd[2]).toString();
}

//判断时间大于当前
function date_istrue() {
    var sel_date = $(".express_date").val();
    sel_date = toDate(sel_date);
    var current_date = new Date();
    var current_date_month = current_date.getMonth() + 1;
    if (current_date_month.toString().length == 1) {
        current_date_month = "0" + current_date_month.toString();
    }
    current_date = (current_date.getFullYear().toString() + current_date_month.toString() + current_date.getDate().toString()).toString();
    if (sel_date < current_date) {
        layer.msg('配送日期不能小于当前日期',2,-1);
        throw "配送日期不能小于当前日期";
    }
}
//验证手机
function checkMobile(name) {
    var myreg = /^(((1[0-9][0-9]{1}))+\d{8})$/;
    var mobile = $.trim($("input[name=" + name + "]").val());
    if (!myreg.test(mobile))
    {
        layer.msg('手机号码格式有误',2,-1);
        throw "手机号码格式有误";
    }

}
//验证邮箱
function check_email(name) {
    var strEmail = $.trim($("input[name=" + name + "]").val());
    var emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    if (emailReg.test(strEmail)) {

    } else {
         layer.msg('邮箱格式有误',2,-1);
        throw "邮箱格式有误";
    }
}

//是否相等
function is_eq(var1, var2, msg) {
    if ($.trim($("input[name=" + var1 + "]").val()) != $.trim($("input[name=" + var2 + "]").val())) {
        layer.msg(msg);
        throw msg;
    }

}

//是否大于
function is_tq(var1, var2, msg) {
    if (parseFloat(var1) < parseFloat($.trim($("input[name=" + var2 + "]").val()))) {
        layer.msg(msg,2,-1);
        throw msg;
    }
}

//textarea光标在第一个
function s(e, a)
{
    if (e && e.preventDefault) {
        e.preventDefault();
    }
    else {
        window.event.returnValue = false;
    }
    a.focus();

}


/*
 ajax请求方法，默认为Post方法,json数据类型返回 {'success','success msg',[url:'http://www.baidu.com']} 
 如果存在返回的数据存在msg参数的话，则会在函数执行后页面自动弹出该msg.
 如果存在返回的数据存在url参数的话，则会在函数执行后页面自动跳转到该url.
 url:请求的路径
 data:发送的数据
 success:'status'为'success'回调函数 function(data){}
 error:'status'为'error'回调函数 function(data){}
 */
function myajax(url, post_data) {
    var success = arguments[2] ? arguments[2] : null;
    var error = arguments[3] ? arguments[3] : null;
    $.post(url, post_data,
            function(data) {

                if (data.status == "success") {
                    if (isnoempty(data.msg) == true) {
                        layer.msg(data.msg, 2, -1, function() {

                            (success && typeof (success) == "function") && success(data);
                        });
                    } else {

                        (success && typeof (success) == "function") && success(data);
                    }
                } else if (data.status == "error") {
                    if (isnoempty(data.msg) == true) {
                        
                        layer.msg(data.msg, 2, -1, function() {
                            layer.close();
                            (error && typeof (error) == "function") && error(data);
                        });
                    } else {
                        (error && typeof (error) == "function") && error(data);
                    }
                }
            }, "json"
            );

}

function isnoempty(val) {
    if (val != '' && val != null && val != undefined)
        return true;
    else
        return false;
}

//验证姓名是否为中文
function checkName(name) {
    var val = $.trim($("input[name=" + name + "]").val());
    if (val.length != 0) {
        if (val.match(/^[\u4e00-\u9fa5]+$/)) {

        }
        else {
            layer.msg('姓名只能输入中文',2,-1);
            throw "姓名只能输入中文";
        }
        ;
    } else {
        layer.msg('姓名不能为空',2,-1);
        throw "姓名不能为空";
    }
}
//清空表单
function clearForm(id) {
    var objId = document.getElementById(id);
    if (objId == undefined) {
        return;
    }
    for (var i = 0; i < objId.elements.length; i++) {
        if (objId.elements[i].type == "text") {
            objId.elements[i].value = "";
        }
        else if (objId.elements[i].type == "password") {
            objId.elements[i].value = "";
        }
        else if (objId.elements[i].type == "radio") {
            objId.elements[i].checked = false;
        }
        else if (objId.elements[i].type == "checkbox") {
            objId.elements[i].checked = false;
        }
        else if (objId.elements[i].type == "select-one") {
            objId.elements[i].options[0].selected = true;
        }
        else if (objId.elements[i].type == "select-multiple") {
            for (var j = 0; j < objId.elements[i].options.length; j++) {
                objId.elements[i].options[j].selected = false;
            }
        }
        else if (objId.elements[i].type == "textarea") {
            objId.elements[i].value = "";
        }

    }
}
//以新窗口打开链接
function openLink(url)
{
    window.open(url);
}
 