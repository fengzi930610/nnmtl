(function($){
    //联动菜单
    $.fn.cate_select = function(options) {
        var settings = {
			field: 'J_cate_id',
			top_option: '全部',
			first_option : true,
			attrs:null
        };
        if(options) {
            $.extend(settings, options);
        }

        var self = $(this),
            pid = self.attr('data-pid'),
            uri = self.attr('data-uri'),
            selected = self.attr('data-pids'),
            selected_arr = [];
        if(selected != undefined && selected != '0'){
        	if(selected.indexOf(',')){
        		selected_arr = selected.split(',');
        	}else{
        		selected_arr = [selected];
        	}
        }
        self.nextAll('.J_cate_select').remove();
		if(settings.first_option && settings.top_option){
			$('<option value="">--'+settings.top_option+'--</option>').appendTo(self);
		}
        $.getJSON(uri.replace("#pid#", pid), function(ret){
            if(ret.error){
				return false;
			}else if(ret.cats.length > 0){
                for(var i=0; i<ret.cats.length; i++){
					$('<option value="'+ret.cats[i].cat_id+'">'+ret.cats[i].title+'</option>').appendTo(self);
                }
            }
            if(selected_arr.length > 0){
            	//IE6 BUG
            	setTimeout(function(){
            		self.find('option[value="'+selected_arr[0]+'"]').attr("selected", true);
	        		self.trigger('change');
            	}, 100);
            }
        });

        var j = 1;
        $('.J_cate_select').die('change').live('change', function(){
            var _this = $(this),
            _pid = _this.val();
            _this.nextAll('.J_cate_select').remove();
            if(_pid != ''){
                $.getJSON(uri.replace("#pid#", _pid), function(ret){
                    if(ret.error){
						return false;
					}else if(ret.cats.length > 0){
						var attrs = settings.attrs || ""; 
						var top_option = "";
						if(settings.top_option){ top_option = '<option value="">--'+settings.top_option+'--</option>'}
						var _childs = $('<select class="J_cate_select mgr10" data-pid="'+_pid+'" '+attrs+'>'+top_option+'</select>');
                        for(var i=0; i<ret.cats.length; i++){
                            $('<option value="'+ret.cats[i].cat_id+'">'+ret.cats[i].title+'</option>').appendTo(_childs);
                        }
                        _childs.insertAfter(_this);
                        if(selected_arr[j] != undefined){
                        	//IE6 BUG
                        	//setTimeout(function(){
			            		_childs.find('option[value="'+selected_arr[j]+'"]').attr("selected", true);
				        		_childs.trigger('change');
			            	//}, 1);
			            }
                        j++;
                    }
                });
                $('#'+settings.field).val(_pid);
            }else{
            	$('#'+settings.field).val(_this.attr('data-pid'));
            }
        });
    }
})(window.jQuery);