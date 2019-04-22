/* ECart 通用型JS购物车
 * Author:wuquanyao
 * Date:2016-03-15
 * 说明：ECart购物车依赖jscookie.js库
 */
/* 构造函数
 * key,购物车的cookie的键名
 * shop,商铺id及shop_id
 */
window.ECart = function(shop_id,title){
    this.shop_id = shop_id;
    this.title_list = JSON.parse(localStorage["title_list"] || '{}') || {};
    if(!this.title_list[shop_id]&&title){
        this.title_list[shop_id] = title;
    }
    localStorage['title_list'] = JSON.stringify(this.title_list);
    this.init();
}

//初始化购物车
ECart.prototype.init = function(){
    this.cart_list = JSON.parse(localStorage["ECart"] || '{}') || {};
    this.shop_cart = this.cart_list[this.shop_id] || {}; 
    this.save();
}

//保存购物车信息
ECart.prototype.save = function(){
    this.cart_list[this.shop_id] = this.shop_cart;
    localStorage['ECart'] = JSON.stringify(this.cart_list);
    var cookie_cart = {};
    for(var sid in this.cart_list){
        var sp = [];
        if(typeof(this.cart_list[sid]) == 'object'){            
            for(var pid in this.cart_list[sid]){
                sp.push(pid+":"+this.cart_list[sid][pid]['num']);
            }
        }
        cookie_cart[sid] = sp.join(",");        
    }
    Cookie.set('ECart', JSON.stringify(cookie_cart));
},

/* 增加
 * product,产品id及product_id
 * spec,规格id及spec_id,选填
 * info,要存放的数据,对象格式{'title':'','price':'',...}
 * 注意:请将商品数量自行更新
 */
ECart.prototype.add = function(sku_id, num, info){
    if(this.shop_cart[sku_id]){
        this.shop_cart[sku_id]['num'] = parseInt(this.shop_cart[sku_id]['num'],  10) +   parseInt(num, 10);
    }else{
        this.shop_cart[sku_id] = info;
        this.shop_cart[sku_id]['sku_id'] = sku_id;
        this.shop_cart[sku_id]['num'] = parseInt(num, 10);
    }
    if(this.shop_cart[sku_id]['num'] > 99){
        this.shop_cart[sku_id]['num'] = 99;
    }else if(parseInt(this.shop_cart[sku_id]['sale_type'], 10) > 0){
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

ECart.prototype.remove = function (sku_id){
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

ECart.prototype.clear = function(){
    this.shop_cart = {};
    this.save();
}

/* 商品
 * sku_id
 */
ECart.prototype.product = function(sku_id){
    return this.shop_cart[sku_id] || {};
}

/* 商品数量
 * sku_id
 */
ECart.prototype.product_num = function(sku_id){
    if(typeof(this.shop_cart[sku_id]) == 'undefined'){
        return 0;
    }else{
        return this.shop_cart[sku_id]['num'] || 0;
    }
}

/** 
 * 购物车所有商品
 * shop_id，可选
 */    
ECart.prototype.product_list = function(){
    return this.shop_cart;
}

ECart.prototype.total_count = function(){
    var count = 0;
    for(var i in this.shop_cart){
        count += parseInt(this.shop_cart[i]['num'] || 0, 10);
    }
    return count;
}


ECart.prototype.total_price = function(){
    var price = 0;
    for(var i in this.shop_cart){
        var num = parseInt(this.shop_cart[i]['num'] || 0, 10);
        price += parseFloat(this.shop_cart[i]['price'], 10) * num;
    }
    return price.toFixed(2);
}
