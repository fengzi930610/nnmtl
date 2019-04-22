<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/24
 * Time: 15:07
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Adv_Export extends Ctl {

    public function index()
    {  
        $items = K::M('adv/themes')->getThemeGallery();
        $this->pagedata['items'] = $items ? $items : array();      
        $this->tmpl = "admin:adv/themes/export.html";
    }

    public function export($theme_id=null)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$themes = K::M('adv/themes')->getThemeGallery()){
            $this->msgbox->add('数据有误', 212);
        }else if(!$theme = $themes[$theme_id]){
            $this->msgbox->add('模板风格不存在或已删除', 213);
        }else{
            $data = array(
                'title'=>$theme['title'],
                'photo'=>$theme['photo'],
                'config'=>serialize($theme['config']),
                'default'=>0,
                'dateline'=>__TIME
            );
            if(!K::M('adv/themes')->count()){
                $data['default'] = 1;
            }
            if(K::M('adv/themes')->create($data)){
                K::M('adv/themes')->flush();
                $this->msgbox->add('导入成功');
            }else{
                $this->msgbox->add('导入失败', 213);
            }
        }
    }

    public function preview($theme_id)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$themes = K::M('adv/themes')->getThemeGallery()){
            $this->msgbox->add('数据有误', 212);
        }else if(!$theme = $themes[$theme_id]){
            $this->msgbox->add('模板风格不存在或已删除', 213);
        }else{            
            $theme = K::M('adv/themes')->format_imgurl($theme);
            if(is_array($theme['config']['module1']['background'])){
                if(in_array($theme['config']['module1']['class'], array(1, 3)) && $theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][1];
                }else if(in_array($v['class'], array(1, 3)) && !$theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][2];
                }else if(in_array($v['class'], array(2)) && $theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][3];
                }else if(in_array($v['class'], array(2)) && !$theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][4];
                }else{
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][1];
                }
            }
            $this->pagedata['config'] = $theme['config'];
            $this->tmpl = "admin:adv/themes/preview.html";
        }       
    }

}