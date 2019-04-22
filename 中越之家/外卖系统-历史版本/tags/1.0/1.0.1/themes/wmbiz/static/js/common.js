function select(cates,level,cate_id,cate_id2){
    $('#select_1').on('change',function(){    
        var opt_id = $(this).val();
        var html = '<option value="0">请选择</option>';
        $("#select_2").html(html);
        if(level == 3){
            $("#select_3").html(html);
        }
        $.each(cates,function(k,v){
            if(opt_id > 0){
                if(v.parent_id == opt_id){
                    html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                }
            }
        });
        $("#select_2").html(html);
    })
    if(cate_id>0){
        var html = '<option value="0">请选择</option>';
        $("#select_2").html(html);
        $.each(cates,function(k,v){
            if(v.parent_id == cate_id){
                if(v.cate_id == cate_id2){
                    html+='<option selected="selected" value="'+v.cate_id+'">'+v.title+'</option>';
                }else{
                    html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                }
            }
        });
        $("#select_2").html(html);
    }
    if(level == 3){
        $('#select_2').on('change',function(){    
            var opt_id = $(this).val();
            var html = '<option value="0">请选择</option>';
            $("#select_3").html(html);
            $.each(cates,function(k,v){
                if(opt_id > 0){
                    if(v.parent_id == opt_id){
                        html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                    }
                }
            });
            $("#select_3").html(html);
        })
    } 
}


function select2(cates,level,cat_id,cat_id2,cat_id3){
    $('#select-1').on('change',function(){    
        var opt_id = $(this).val();
        var html = '<option value="0">请选择</option>';
        $("#select-2").html(html);
        if(level == 3){
            $("#select-3").html(html);
        }
        $.each(cates,function(k,v){
            if(opt_id > 0){
                if(v.parent_id == opt_id){
                    html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                }
            }
        });
        $("#select-2").html(html);
    })
    if(cat_id >0){
        var html = '<option value="0">请选择</option>';
        $("#select-2").html(html);
        $.each(cates,function(k,v){
            if(v.parent_id == cat_id){
                if(v.cate_id == cat_id2){
                    html+='<option selected="selected" value="'+v.cate_id+'">'+v.title+'</option>';
                }else{
                    html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                }
            }
        });
        $("#select-2").html(html);
    }
    
    if(level == 3){
        $('#select-2').on('change',function(){    
            var opt_id = $(this).val();
            var html = '<option value="0">请选择</option>';
            $("#select-3").html(html);
            $.each(cates,function(k,v){
                if(opt_id > 0){
                    if(v.parent_id == opt_id){
                        html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                    }
                }
            });
            $("#select-3").html(html);
        })
        if(cat_id2>0){
            var html = '<option value="0">请选择</option>';
            $("#select-3").html(html);
            $.each(cates,function(k,v){
                if(v.parent_id == cat_id2){
                    if(v.cate_id == cat_id3){
                        html+='<option selected="selected" value="'+v.cate_id+'">'+v.title+'</option>';
                    }else{
                        html+='<option value="'+v.cate_id+'">'+v.title+'</option>';
                    }
                }
            });
            $("#select-3").html(html);
        }
    } 
}