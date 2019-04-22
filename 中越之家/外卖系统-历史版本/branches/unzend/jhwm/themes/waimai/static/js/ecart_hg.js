/* ECart 通用型JS购物车
 * Author:wuquanyao
 * Date:2016-03-15
 * 说明：ECart购物车依赖jscookie.js库
 */
/* 构造函数
 * key,购物车的cookie的键名
 * shop,商铺id及shop_id
 */
window.ECart_hg = function(shop_id, huangou){
    this.shop_id = shop_id;
    this.huangou_list = JSON.parse(localStorage["huangou_list"] || '{}') || {};
    if(huangou){
        this.huangou_list[shop_id] = huangou;
    }
    localStorage['huangou_list'] = JSON.stringify(this.huangou_list);
    this.init();
}

//初始化购物车
ECart_hg.prototype.init = function(){
    this.cart_list = JSON.parse(localStorage["ECart_hg"] || '{}') || {};
    this.shop_cart = this.cart_list[this.shop_id] || {};

    this.huangou_list = JSON.parse(localStorage["huangou_list"] || '{}') || {};
    this.huangou = this.huangou_list[this.shop_id] || {};

    this.save();
}

//保存购物车信息
ECart_hg.prototype.save = function(){
    this.cart_list[this.shop_id] = this.shop_cart;
    localStorage['ECart_hg'] = JSON.stringify(this.cart_list);
    var cookie_cart = {};
    for(var sid in this.cart_list){
        var sp = [];
        if(typeof(this.cart_list[sid]) == 'object'){            
            for(var pid in this.cart_list[sid]){
                sp.push(this.cart_list[sid][pid]['sku_id']+":"+this.cart_list[sid][pid]['num']);
            }
        }
        cookie_cart[sid] = sp.join(",");
    }
    Cookie.set('ECart_hg', JSON.stringify(cookie_cart),"","/");
}

/* 增加
 * product,产品id及product_id
 * spec,规格id及spec_id,选填
 * info,要存放的数据,对象格式{'title':'','price':'',...}
 * 注意:请将商品数量自行更新
 */
ECart_hg.prototype.add = function(sku_id, num, info){
    var quota = this.huangou['quota'] || 0;

    if(this.shop_cart[sku_id]){
        var new_num = parseInt(this.shop_cart[sku_id]['num'], 10) + parseInt(num, 10);
        if(quota > 0 && new_num > quota){
            Widget.MsgBox.error('换购商品超出限购数量！');
            return false;
        }
        this.shop_cart[sku_id]['num'] = parseInt(this.shop_cart[sku_id]['num'], 10) + parseInt(num, 10);
    }else{
        if(quota > 0 && parseInt(num, 10) > quota){
            Widget.MsgBox.error('换购商品超出限购数量！');
            return false;
        }
        this.shop_cart[sku_id] = info;
        this.shop_cart[sku_id]['sku_id'] = sku_id;
        this.shop_cart[sku_id]['num'] = parseInt(num, 10);
    }
    if(this.shop_cart[sku_id]['num'] > 99){
        this.shop_cart[sku_id]['num'] = 99;
    }else{
        if(this.shop_cart[sku_id]['num'] > this.shop_cart[sku_id]['sale_sku']){
            Widget.MsgBox.error('商品库存不足');
            this.shop_cart[sku_id]['num'] = this.shop_cart[sku_id]['sale_sku'];
        }
    }
    var product_list = {};
    for(var k in this.shop_cart){
        this.shop_cart[k]['num'] = parseInt(this.shop_cart[k]['num'], 10)
        if(this.shop_cart[k]['num'] > 0){
            product_list[k] = this.shop_cart[k];
        }
    }
    this.shop_cart = product_list;
    this.save();
}

ECart_hg.prototype.remove = function (sku_id){
    var product_list = {};
    for(var k in this.shop_cart){
        this.shop_cart[k]['num'] = parseInt(this.shop_cart[k]['num'], 10)
        if(this.shop_cart[k]['num'] > 0 && k != sku_id){
            product_list[k] = this.shop_cart[k];
        }
    }
    this.shop_cart = product_list;
    this.save();      
}

ECart_hg.prototype.clear = function(){
    this.shop_cart = {};
    this.save();
}

/* 商品
 * sku_id
 */
ECart_hg.prototype.product = function(sku_id){
    return this.shop_cart[sku_id] || {};
}

/* 商品数量
 * sku_id
 */
ECart_hg.prototype.product_num = function(sku_id){
    if(typeof(this.shop_cart[sku_id]) == 'undefined'){
        return 0;
    }else{
        return this.shop_cart[sku_id]['num'] || 0;
    }
}

ECart_hg.prototype.sku_count = function(product_id){
    var count = 0;
    for(var i in this.shop_cart){
        if(this.shop_cart[i]['product_id'] == product_id){
            count += parseInt(this.shop_cart[i]['num'] || 0, 10);
        }
    }
    return count;
}

/** 
 * 购物车所有商品
 * shop_id，可选
 */    
ECart_hg.prototype.product_list = function(){
    return this.shop_cart;
}

ECart_hg.prototype.total_count = function(){
    var count = 0;
    for(var i in this.shop_cart){
        count += parseInt(this.shop_cart[i]['num'] || 0, 10);
    }
    return count;
}

ECart_hg.prototype.total_price = function(){
    var price = package_price = 0;
    for(var i in this.shop_cart){
        var num = parseInt(this.shop_cart[i]['num'] || 0, 10);
        price += parseFloat(this.shop_cart[i]['price'], 10) * num;
        package_price += parseFloat(this.shop_cart[i]['package'], 10) * num;
    }
    var total_price = price + package_price;
    return total_price.toFixed(2);
}

ECart_hg.prototype.total_package = function(){
    var price = 0;
    for(var i in this.shop_cart){
        var num = parseInt(this.shop_cart[i]['num'] || 0, 10);
        price += parseFloat(this.shop_cart[i]['package'], 10) * num;
    }
    return price.toFixed(2);
}

