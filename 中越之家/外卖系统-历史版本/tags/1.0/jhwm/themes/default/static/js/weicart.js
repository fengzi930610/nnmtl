/* WeiCart 微店通用型JS购物车
 * Author:ijh.cc
 * Date:2016-09-09
 * 说明：WeiCart购物车依赖jscookie.js库、本地存储localStorage
 */
/* 构造函数
 * key,购物车的cookie的键名
 * shop,商铺id及shop_id
 */


window.WeiCart = function(shop_id,key){
    this.shop_id = shop_id;
    this.init(key);

}


//初始化购物车
WeiCart.prototype.init = function(key){
    this.cart_list = JSON.parse(localStorage[key] || '{}') || {};
    this.shop_cart = this.cart_list[this.shop_id] || {};
    this.CatchKey = key;
    this.save(key);
}

//保存购物车信息
WeiCart.prototype.save = function(){
    this.cart_list[this.shop_id] = this.shop_cart;
    localStorage[this.CatchKey] = JSON.stringify(this.cart_list);
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
    Cookie.set(this.CatchKey, JSON.stringify(cookie_cart));
},

/* 增加
 * product,产品id及stock_id
 * spec,规格id及spec_id,选填
 * info,要存放的数据,对象格式{'title':'','price':'',...}
 * 注意:请将商品数量自行更新
 */
WeiCart.prototype.add = function(stock_id, num, info){
    //console.log(this.shop_cart);
    if(this.shop_cart[stock_id]){ //
        this.shop_cart[stock_id]['num'] = parseInt(this.shop_cart[stock_id]['num'],  10) +   parseInt(num, 10);
    }else{
        //alert(stock_id);
        this.shop_cart[stock_id] = info;
        this.shop_cart[stock_id]['stock_id'] = stock_id;
        this.shop_cart[stock_id]['num'] = parseInt(num, 10);
    }
    if(this.shop_cart[stock_id]['num'] > 999){
        this.shop_cart[stock_id]['num'] = 999;
    }else if(this.shop_cart[stock_id]['num'] > this.shop_cart[stock_id]['stock']){
        layer.open({content: "商品库存不足", skin: 'msg', time: 2 });
        this.shop_cart[stock_id]['num'] = this.shop_cart[stock_id]['stock'];
        return false;
    }
    var product_list = {};
    for(var k in this.shop_cart){
        this.shop_cart[k]['num'] = parseInt(this.shop_cart[k]['num'], 10)
        if(this.shop_cart[k]['num'] > 0){
            product_list[k] = this.shop_cart[k];
        }
    }
    this.shop_cart = product_list;
    this.save(this.CatchKey);
}

WeiCart.prototype.remove = function (stock_id){
    var product_list = {};
    for(var k in this.shop_cart){
        this.shop_cart[k]['num'] = parseInt(this.shop_cart[k]['num'], 10);
        if(this.shop_cart[k]['num'] > 0 && k != stock_id){
            if(this.shop_cart[k]){
                product_list[k] = this.shop_cart[k];
            }
        }
    }
    this.shop_cart = product_list;
    this.save(this.CatchKey);
}




WeiCart.prototype.clear = function(){
    this.shop_cart = {};
    this.save(this.CatchKey);
}

/* 商品
 * stock_id
 */
WeiCart.prototype.product = function(stock_id){
    return this.shop_cart[stock_id] || {};
}

/* 商品数量
 * stock_id
 */
WeiCart.prototype.product_num = function(stock_id){
    if(typeof(this.shop_cart[stock_id]) == 'undefined'){
        return 0;
    }else{
        return this.shop_cart[stock_id]['num'] || 0;
    }
}

/** 
 * 购物车所有商品
 * shop_id，可选
 */    
WeiCart.prototype.product_list = function(){
    return this.shop_cart;
}



WeiCart.prototype.total_count = function(){
    var count = 0;
    for(var i in this.shop_cart){
        count += parseInt(this.shop_cart[i]['num'] || 0, 10);
    }
    return count;
}


WeiCart.prototype.total_price = function(){
    var price = 0;
    for(var i in this.shop_cart){
        var num = parseInt(this.shop_cart[i]['num'] || 0, 10);
        price += parseFloat(this.shop_cart[i]['price'], 10) * num;
    }
    return price.toFixed(2);
}
