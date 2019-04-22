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

class Ctl_Adv_Themes extends Ctl {

    public function index()
    {  
        $items = K::M('adv/themes')->fetch_all();
        foreach ($items as $k => $v) {
            $v['photo'] = K::M('magic/upload')->geturl($v['photo']);
            $items[$k] = $v;
        }
        $this->pagedata['items'] = $items ? $items : array();      
        $this->tmpl = "admin:adv/themes/items.html";
    }

    public function edit($theme_id=null)
    {
        if($data = $this->checksubmit('data')){            
            if(!$title = $data['title']){
                $this->msgbox->add('风格名称不能为空', 212);
            }else if(!$photo = $data['photo']){
                $this->msgbox->add('封面图片不能为空', 213);
            }else if($theme_id && !$theme = K::M('adv/themes')->detail($theme_id)){
                $this->msgbox->add('要编辑的风格不存在或已删除', 214);
            }else{
                $new_data = array('title'=>$title, 'photo'=>$data['photo']);
                if($theme_id){
                    $res = K::M('adv/themes')->update($theme_id, $new_data);
                    $this->msgbox->set_data('data', $theme_id);
                }else{
                    $new_data['dateline'] = __TIME;
                    $new_data['config'] = serialize(K::M('adv/themes')->getDefaultConfig());
                    if(!K::M('adv/themes')->count()){
                        $new_data['default'] = 1;
                    }
                    $res = K::M('adv/themes')->create($new_data);
                    $this->msgbox->set_data('data', $res);
                }
                if($res){
                    K::M('adv/themes')->flush();
                    $this->msgbox->add('操作成功');
                }else{
                    $this->msgbox->add('操作失败', 215);
                }
            }
        }else{
            $this->msgbox->add('数据有误', 211);
        }
    }
 
    public function delete($theme_id=null)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$theme = K::M('adv/themes')->detail($theme_id)){
            $this->msgbox->add('模板风格不存在或已删除', 212);
        }else if($theme['default'] == 1){
            $this->msgbox->add('默认模板风格不能删除', 213);
        }else if(K::M('adv/themes')->delete($theme_id)){
            K::M('adv/themes')->flush();
            $this->msgbox->add('删除成功');
        }else{
            $this->msgbox->add('删除失败', 214);
        }
    }

    public function copy($theme_id=null)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$theme = K::M('adv/themes')->detail($theme_id)){
            $this->msgbox->add('模板风格不存在或已删除', 212);
        }else{
            $data = array(
                'title'=>$theme['title'],
                'photo'=>$theme['photo'],
                'config'=>serialize($theme['config']),
                'default'=>0,
                'dateline'=>__TIME
            );
            if(K::M('adv/themes')->create($data)){
                K::M('adv/themes')->flush();
                $this->msgbox->add('复制成功');
            }else{
                $this->msgbox->add('复制失败', 213);
            }
        }
    }

    public function setDefault($theme_id=null)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$theme = K::M('adv/themes')->detail($theme_id)){
            $this->msgbox->add('模板风格不存在或已删除', 212);
        }else if($theme['default'] == 1){
            $this->msgbox->add('当前模板风格已是默认风格', 213);
        }else if(K::M('adv/themes')->set_default($theme_id)){
            K::M('adv/themes')->flush();
            $this->msgbox->add('设置成功');
        }else{
            $this->msgbox->add('设置失败', 214);
        }
    }

    public function preview($theme_id)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$theme = K::M('adv/themes')->detail($theme_id)){
            $this->msgbox->add('模板风格不存在或已删除', 212);
        }else{
            $interimCache = K::M('adv/themes')->get_interimCache();
            foreach ($theme['config'] as $k => $v) {
                if($module = $interimCache[$k]){
                    $theme['config'][$k] = $module;
                }
            }
            
            $theme = K::M('adv/themes')->format_imgurl($theme);
            if(is_array($theme['config']['module1']['background'])){
                if(in_array($theme['config']['module1']['class'], array(1, 3)) && $theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][1];
                }else if(in_array($theme['config']['module1']['class'], array(1, 3)) && !$theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][2];
                }else if(in_array($theme['config']['module1']['class'], array(2)) && $theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][3];
                }else if(in_array($theme['config']['module1']['class'], array(2)) && !$theme['config']['module1']['searchBox']['open']){
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][4];
                }else{
                    $theme['config']['module1']['background'] = $theme['config']['module1']['background'][1];
                }
            }
            //echo '<pre>';print_r($theme['config']);die;
            $this->pagedata['config'] = $theme['config'];
            $this->tmpl = "admin:adv/themes/preview.html";
        }       
    }

    public function returnModules()
    {
        return array('module0', 'module1', 'module2', 'module3', 'module4', 'module5', 'module6', 'module7', 'module8', 'module9', 'module10');
    }

    public function module($theme_id=null)
    {   
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$theme = K::M('adv/themes')->detail($theme_id)){
            $this->msgbox->add('模板风格不存在或已删除', 212);
        }else if($config = $this->checksubmit('config')){
            $interimCache = K::M('adv/themes')->get_interimCache();
            foreach ($config as $k => $v) {
                if($interimCache[$k]){
                    $v = array_merge($interimCache[$k], $v);
                }else if($theme['config'][$k]){
                    $v = array_merge($theme['config'][$k], $v);
                }
                $config[$k] = $v;
            }
            //echo '<pre>';print_r($config);die;
            if(K::M('adv/themes')->update($theme_id, array('config'=>serialize($config)))){
                K::M('adv/themes')->delete_interimCache();
                $this->msgbox->add('保存成功');
            }else{
                $this->msgbox->add('保存失败', 213);
            }
        }else{
            K::M('adv/themes')->delete_interimCache();
            $theme = K::M('adv/themes')->format_imgurl($theme);
            //echo '<pre>';print_r($theme['config']);die;
            $this->pagedata['config'] = $theme['config'];
            $this->pagedata['theme_id'] = $theme_id;
            $this->pagedata['system_modules'] = $this->getModules();
            $this->tmpl = "admin:adv/themes/module.html";            
        }
    }

    public function editModule($theme_id=null, $module=null)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$theme = K::M('adv/themes')->detail($theme_id)){
            $this->msgbox->add('模板风格不存在或已删除', 212);
        }else if(!$module || !in_array($module, $this->returnModules())){
            $this->msgbox->add('模块不存在', 213);
        }else if($config = $this->checksubmit('config')){
            $config[$module]['open'] = 1;
            if(isset($config[$module]['content'])){
                if(in_array($module, array('module5', 'module6', 'module7'))){
                    foreach ($config[$module]['content'] as $k => $v) {
                        $config[$module]['content'][$k] = K::M('helper/format')->overturn($v);
                    }
                }else{
                    $config[$module]['content'] = K::M('helper/format')->overturn($config[$module]['content']);
                }

                foreach ($config[$module]['content'] as $k => $v) {
                    if(isset($v['title']) && !$v['title']){
                        $this->msgbox->add('标题不能为空', 214)->response();
                    }else if(isset($v['photo']) && !$v['photo']){
                        $this->msgbox->add('图片不能为空', 215)->response();
                    }else if(isset($v['logo']) && !$v['logo']){
                        $this->msgbox->add('logo不能为空', 216)->response();
                    }
                }
            }
            if(K::M('adv/themes')->set_interimCache($module, $config[$module])){
                $this->msgbox->add('保存成功');
            }else{
                $this->msgbox->add('保存失败', 214);
            }
        }else{
            $interimCache = K::M('adv/themes')->get_interimCache();
            if(!$config = $interimCache[$module]){
                $config = $theme['config'][$module];
            }
            $this->pagedata['config'] = $config;
            $this->pagedata['theme_id'] = $theme_id;
            $this->tmpl = "admin:adv/themes/{$module}.html";
        }        
    }

    public function save($theme_id=null)
    {
        
    }

    public function getTypes()
    {
        return array(
            '_shop'=>'商家',
            '_cate'=>'分类',
            '_huodong'=>'活动',
            '_module'=>'模块'
            );
    }

    public function getModules()
    {
        $modules = array();
        if(defined('HAVE_PAOTUI') && HAVE_PAOTUI){
            $modules[] = array(
                'id'=>1,
                'title'=>'跑腿',
                'advlink'=>K::M('helper/link')->mklink('',array(),array(),'paotui'),
                'advwxlink'=>K::M('helper/link')->mklink('',array(),array(),'paotui'),
                );
        }
        if(defined('HAVE_JIFEN') && HAVE_JIFEN){
            $modules[] = array(
                'id'=>2,
                'title'=>'积分',
                'advlink'=>K::M('helper/link')->mklink(null,array(),array(),'jifen'),
                'advwxlink'=>K::M('helper/link')->mklink(null,array(),array(),'jifen'),
                );
        }
        if(defined('HAVE_QIANG') && HAVE_QIANG){
            $modules[] = array(
                'id'=>3,
                'title'=>'抢购',
                'advlink'=>K::M('helper/link')->mklink(null,array(),array(),'qiang'),
                'advwxlink'=>K::M('helper/link')->mklink(null,array(),array(),'qiang'),
                );
        }

        $modules[] = array(
            'id'=>4,
            'title'=>'配送会员卡',
            'advlink'=>K::M('helper/link')->mklink('ucenter/peicard-mycard',array(),array(),'waimai'),
            'advwxlink'=>'/pages/membersCard/membersCard',
            );
        foreach ($modules as $k => $v) {
            if($v['advlink']){
                $v['advlink'] = trim(str_replace("?","",$v['advlink']));
                $v['advwxlink'] = trim(str_replace("?","",$v['advwxlink']));
            }
            $modules[$k] = $v;
        }
        return $modules;
    }

    public function dialog($type="_shop", $page)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        switch ($type) {
            case '_shop':
                $id = 'shop_id';
                $photo = 'logo';
                $filter['closed'] = 0;
                $filter['verify_name'] = 1;
                $model = K::M('waimai/waimai');
                $ctl = 'shop/detail';
                $wxctl = '/pages/shoptail/shoptail?id=';
                break;
            case '_cate':
                $id = 'cate_id';
                $photo = 'icon';
                $model = K::M('waimai/cate');
                $filter['parent_id'] = 0;
                $ctl = 'shoplist/index';
                $wxctl = '/pages/shoplist/shoplist?cateid=';
                break;
            case '_huodong':
                $id = 'huodong_id';
                $photo = 'banner';
                $filter['stime'] = '<=:'.__TIME;
                $filter['ltime'] = '>=:'.__TIME;
                $model = K::M('waimai/huodong');
                $ctl = 'huodong/detail';
                $wxctl = '/pages/huodong/huodong?huodong_id=';
                break;
            case '_module':
                $items = $this->getModules();
            default:
                break;
        }

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            
            if($SO['keywords']){
                $filter[':OR'] = array($id=>(int)$SO['keywords'], 'title'=>'LIKE:%'.$SO['keywords'].'%');
            }
        }

        if($model){
            if($items = $model->items($filter, null, $page, $limit, $count)){
                foreach($items as $k=>$v){
                    $advlink = K::M('helper/link')->mklink($ctl,array($v[$id]),array(),'waimai');
                    $v['advlink'] = trim(str_replace("?","",$advlink));
                    $v['advwxlink'] = $wxctl.$v[$id];
                    $v['id'] = $v[$id];
                    $v['photo'] = $v[$photo];
                    $items[$k] = $v; // $this->filter_fields('id,title,photo,advlink,advwxlink,dateline', $v);
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($type,'{page}')), array('SO'=>$SO, 'multi'=>$multi));
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['types'] = $this->getTypes();
        $this->pagedata['type'] = $type;
        $this->tmpl = 'admin:adv/themes/dialog.html';
    }

    public function photoGallery($cate_id=0, $page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 20;
        $filter['from'] = 'photoGallery';
        $filter['cate_id'] = (int)$cate_id;
        if($photos = K::M('magic/upload')->items($filter, array('photo_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($cate_id,'{page}')), array());
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['photos'] = $photos;
        $this->pagedata['cates'] = K::M('upload/cate')->fetch_all();
        $this->pagedata['count'] = $count = K::M('upload/cate')->getcounts(array('from'=>'photoGallery'));
        $this->pagedata['cate_id'] = (int)$cate_id;
        $this->tmpl = "admin:adv/themes/photoGallery.html";
    }

    public function iconGallery($page=1)
    {
        $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 40;
        $icons = K::M('adv/themes')->getIconGallery();
        $count = count($icons);
        $pager['count'] = $count;
        $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array());
        
        $this->pagedata['pager'] = $pager;
        $this->pagedata['icons'] = array_slice($icons, ($page-1)*$limit, $limit, true);
        $this->tmpl = "admin:adv/themes/iconGallery.html";
    }

    //异步上传文件
    public function upload($from='theme', $cate_id=0)
    {
        $attach = $_FILES['photo'];
        if($data = K::M('magic/upload')->upload($attach, $from, null, array(), (int)$cate_id)){
            $this->msgbox->set_data('data', array('photo'=>$data['photo']));
        }else{
            $this->msgbox->add('上传图片失败', 501);
        }
        $this->msgbox->json();
    }

    public function upload_by_data($from='photoGallery', $cate_id=0)
    {
        if($attach = $this->checksubmit('data')){
            /*if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $attach, $result)){
                $ext = '.'.$result[2];
            }else{
                $ext = '.png';
            }*/
            $start=strpos($attach,',');
            $attach= substr($attach,$start+1);
            $attach = str_replace(' ', '+', $attach);
            $attach = base64_decode($attach);
            if($data = K::M('magic/upload')->upload_by_data($attach, $from, (int)$cate_id)){
                $this->msgbox->set_data('data', array('photo'=>$data['photo']));
            }else{
                $this->msgbox->add('上传图片失败', 501);
            }
        }else{
            $this->msgbox->add('请选择要上传的图片', 502);
        }
        $this->msgbox->json();
    }

}