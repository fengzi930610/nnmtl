<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: adv.mdl.php 14903 2015-08-12 10:17:27Z xiaorui $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Adv_Themes extends Mdl_Table
{       
    protected $_table = 'adv_themes';
    protected $_pk = 'theme_id';
    protected $_cols = 'theme_id,title,photo,config,default,dateline';
    protected $_pre_cache_key = 'adv-themes-list';
    protected $_interimCache_key = 'adv-themes-modules';
    protected $_iconGallery_cache_key = 'icon-gallery';
    protected $_themeGallery_cache_key = 'theme-gallery';


    protected function _format_row($row)
    {
    	$row['config'] = $row['config'] ? unserialize($row['config']) : array();
    	return $row;
    }

    public function getTheme()
    {
        if(!$theme = K::M('adv/themes')->find(array('default'=>1))){
            $theme = K::M('adv/themes')->find();
        }
        $theme = $this->format_data($theme);
        return $theme;
    }

    public function show_huodong()
    {
        $show_huodong = true;
        if(!$theme = K::M('adv/themes')->find(array('default'=>1))){
            $theme = K::M('adv/themes')->find();
        }
        $show_huodong = isset($theme['config']['module9']['show_huodong']) ? $theme['config']['module9']['show_huodong'] : true;
        return $show_huodong;
    }

    public function getFooterNav()
    {
        if(!$theme = K::M('adv/themes')->find(array('default'=>1))){
            $theme = K::M('adv/themes')->find();
        }
        $default = $this->getDefaultConfig();
        $footerNav = $theme['config']['module10'] ? $theme['config']['module10'] : $default['module10'];
        foreach ($footerNav['content'] as $k => $v) {
            $v['icon_checked'] = K::M('magic/upload')->geturl($v['icon_checked']);
            $v['icon_nochecked'] = K::M('magic/upload')->geturl($v['icon_nochecked']);
            $footerNav['content'][$k] = $v;
        }
        return $footerNav;
    }

    public function format_data($theme)
    {
        $city_name = '';
        $cfg = K::M('system/config')->get('site');
        if($city = K::M('data/city')->detail($cfg['city_id'])){
            $city_name = $city['city_name'];
        }
        $weather = K::M('magic/weather')->weather($city_name);
        
        $config = array();
        if($theme['config']){
            foreach ($theme['config'] as $k => $v) {
                if($k=='module1'){
                    $v['weather'] = $weather;
                    if(is_array($v['background'])){
                        if(in_array($v['class'], array(1, 3)) && $v['searchBox']['open']){
                            $v['background'] = $v['background'][1];
                        }else if(in_array($v['class'], array(1, 3)) && !$v['searchBox']['open']){
                            $v['background'] = $v['background'][2];
                        }else if(in_array($v['class'], array(2)) && $v['searchBox']['open']){
                            $v['background'] = $v['background'][3];
                        }else if(in_array($v['class'], array(2)) && !$v['searchBox']['open']){
                            $v['background'] = $v['background'][4];
                        }else{
                            $v['background'] = $v['background'][1];
                        }
                    }                   
                }

                if($k=='module9' && $v['open'] && $v['show_huodong']){
                    $show_huodong = true;
                }
                if($k=='module10'){
                    foreach ($v['content'] as $kk => $vv) {
                        if(isset($vv['open']) && $vv['open']==0){
                            unset($v['content'][$kk]);
                        }
                    }
                }
                if(in_array($k, array('module5', 'module6', 'module7'))){
                    $v['content'] = $v['content'][$v['class']];
                }

                if($v['background']){
                    $v['background'] = K::M('magic/upload')->geturl($v['background']);
                }

                if($v['photo']){
                    $v['photo'] = K::M('magic/upload')->geturl($v['photo']);
                }

                if($v['icon_location']){
                    $v['icon_location'] = K::M('magic/upload')->geturl($v['icon_location']);
                }

                if($v['icon_down']){
                    $v['icon_down'] = K::M('magic/upload')->geturl($v['icon_down']);
                }

                if($v['content']){
                    foreach ($v['content'] as $kk => $vv) {
                        if($vv['photo']){
                            $vv['photo'] = K::M('magic/upload')->geturl($vv['photo']);
                        }
                        if($vv['logo']){
                            $vv['logo'] = K::M('magic/upload')->geturl($vv['logo']);
                        }
                        if($vv['icon_checked']){
                            $vv['icon_checked'] = K::M('magic/upload')->geturl($vv['icon_checked']);
                        }
                        if($vv['icon_nochecked']){
                            $vv['icon_nochecked'] = K::M('magic/upload')->geturl($vv['icon_nochecked']);
                        }
                        $v['content'][$kk] = $vv;
                    }
                    $v['content'] = array_values($v['content']);
                }
                $theme['config'][$k] = $v;
                if(isset($v['open']) && $v['open']==0){
                    unset($theme['config'][$k]);
                }else{
                    $v['module'] = $k;
                    if(isset($v['class'])){
                        $v['type'] = $v['class'];
                        unset($v['class']);
                    }                   
                    $config[] = $v;
                }
            }
        }
        $theme['config'] = $config;
        return $theme;
    }

    public function format_imgurl($theme)
    {
        $theme['photo'] = K::M('magic/upload')->geturl($theme['photo']);
        if($theme['config'] && is_array($theme['config'])){
            foreach ($theme['config'] as $k => $v) {
                if($k=='module1'){
                    if(is_array($v['background'])){
                        foreach ($v['background'] as $kk => $vv) {
                            if($vv){
                                $v['background'][$kk] = K::M('magic/upload')->geturl($vv);
                            }                            
                        }
                    }
                }

                if($v['background'] && $k!='module1'){
                    $v['background'] = K::M('magic/upload')->geturl($v['background']);
                }

                if($v['photo']){
                    $v['photo'] = K::M('magic/upload')->geturl($v['photo']);
                }

                if($v['icon_location']){
                    $v['icon_location'] = K::M('magic/upload')->geturl($v['icon_location']);
                }

                if($v['icon_down']){
                    $v['icon_down'] = K::M('magic/upload')->geturl($v['icon_down']);
                }

                if($v['content']){
                    if(in_array($k, array('module5', 'module6', 'module7'))){
                        foreach ($v['content'] as $kk => $vv) {
                            foreach ($vv as $kkk => $vvv) {
                                if($vvv['photo']){
                                    $vvv['photo'] = K::M('magic/upload')->geturl($vvv['photo']);
                                }
                                if($vvv['logo']){
                                    $vvv['logo'] = K::M('magic/upload')->geturl($vvv['logo']);
                                }
                                $vv[$kkk] = $vvv;
                            }
                            $v['content'][$kk] = $vv;
                        }
                    }else{                        
                        foreach ($v['content'] as $kk => $vv) {
                            if($vv['photo']){
                                $vv['photo'] = K::M('magic/upload')->geturl($vv['photo']);
                            }
                            if($vv['logo']){
                                $vv['logo'] = K::M('magic/upload')->geturl($vv['logo']);
                            }
                            if($vv['icon_checked']){
                                $vv['icon_checked'] = K::M('magic/upload')->geturl($vv['icon_checked']);
                            }
                            if($vv['icon_nochecked']){
                                $vv['icon_nochecked'] = K::M('magic/upload')->geturl($vv['icon_nochecked']);
                            }
                            $v['content'][$kk] = $vv;
                        }
                    }
                }
                $theme['config'][$k] = $v;
            }
        }
        return $theme;
    }

    public function set_default($theme_id)
    {
        if(!($theme_id = (int)$theme_id)){
            return false;
        }
        $this->db->update($this->_table, array('default'=>0));
        $this->db->update($this->_table, array('default'=>1), "theme_id={$theme_id}");
        return true;
    }

    public function set_interimCache($module, $config)
    {
        $data = $this->get_interimCache();
        $data[$module] = $config;
        K::M('cache/cache')->set($this->_interimCache_key, $data);
        return true;
    }

    public function get_interimCache()
    {
        if(!$data = K::M('cache/cache')->get($this->_interimCache_key)){
            $data = array();
        }
        return $data;
    }

    public function delete_interimCache()
    {
        K::M('cache/cache')->set($this->_interimCache_key, '');
        return true;
    }

    public function getDefaultConfig()
    {
        //module0:首页背景，module1:导航栏模块，module2:轮播图模块，module3:分类模块，module4:新订单弹幕模块，module5:优惠专区模块，module6:优惠专区二模块，module7:大牌甄选模块
        //module8:单条广告模块，module9:商户列表模块，module10:底部导航栏模块
        //open:开启状态，class:样式，background:背景图，background-color:背景色，font-color:字体颜色，searchBox:搜索栏选项，color:颜色，
        //keywords:搜索关键词，title:模块标题，link:链接，content:模块数据，photo:图片，show_huodong:是否显示优惠，
        //checked:选中，nochecked:未选中，icon:图标
        $config = array(
            'module0'=>array(
                'open'=>1,
                'background'=>'',
                'background_color'=>''
                ),
            'module1'=>array(
                'open'=>1,
                'class'=>1,
                'background'=>array(
                    '1'=>'',
                    '2'=>'',
                    '3'=>'',
                    '4'=>''
                    ),
                'background_color'=>'',
                'color'=>'666666',
                'icon_location'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_lat_1.png',
                'icon_down'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_dow_1.png',
                'searchBox'=>array(
                    'open'=>1,
                    'color'=>'666666',
                    'keywords'=>array('脆皮鸡', '排骨饭', '寿司', '凉皮', '老乡鸡', '汉堡')
                    )
                ),
            'module2'=>array(
                'open'=>1,
                'class'=>1,
                'background'=>'',
                'background_color'=>'',
                'link'=>'',
                'wxlink'=>'',
                'content'=>array(
                    array(
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png',
                        'link'=>'',
                        'wxlink'=>''
                        )
                    )
                ),
            'module3'=>array(
                'open'=>1,
                'class'=>1,
                'background'=>'',
                'background_color'=>'',
                'color'=>'333333',
                'content'=>array(
                    array(
                        'title'=>'分类1',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_1.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类2',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_2.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类3',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_3.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类4',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_4.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类5',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_5.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类6',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_6.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类7',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_7.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类8',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_8.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类9',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_9.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    array(
                        'title'=>'分类10',
                        'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_cla_10.png',
                        'link'=>'',
                        'wxlink'=>''
                        ),
                    )
                ),
            'module4'=>array(
                'open'=>1
                ),
            'module5'=>array(
                'open'=>1,
                'class'=>1,
                'title'=>'优惠专区',
                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png',
                'link'=>'',
                'wxlink'=>'',
                'background_color'=>'',
                'content'=>array(
                    '1'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '2'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '3'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '4'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '5'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '6'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '7'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    )
                ),
            'module6'=>array(
                'open'=>0,
                'class'=>1,
                'title'=>'优惠专区二',
                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png',
                'link'=>'',
                'wxlink'=>'',
                'background_color'=>'',
                'content'=>array(
                    '1'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '2'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '3'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '4'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '5'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '6'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    '7'=>array(
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                            ),
                    )

                ),
            'module7'=>array(
                'open'=>1,
                'class'=>1,
                'title'=>'大牌甄选',
                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png',
                'link'=>'',
                'wxlink'=>'',
                'background_color'=>'',
                'content'=>array(
                    '1'=>array(
                            array(
                                'title'=>'必胜客',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png',
                                //'logo'=>'',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'吉祥混沌',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png',
                                //'logo'=>'',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'青年餐厅',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png',
                                //'logo'=>'',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'老乡鸡',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png',
                                //'logo'=>'',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'台资味',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png',
                                'logo'=>'',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'大脸鸡排',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png',
                                //'logo'=>'',
                                'link'=>'',
                                'wxlink'=>''
                                )
                        ),
                    '2'=>array(
                            array(
                                'title'=>'必胜客',
                                //'logo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act10_5.png',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'吉祥混沌',
                                //'logo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act10_6.png',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'青年餐厅',
                                //'logo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act10_7.png',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png',
                                'link'=>'',
                                'wxlink'=>''
                                ),
                            array(
                                'title'=>'台资味',
                                //'logo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act10_8.png',
                                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png',
                                'link'=>'',
                                'wxlink'=>''
                                )
                        )                    
                    )
                ),
            'module8'=>array(
                'open'=>1,
                'photo'=>'https://img01.jhcms.com/wmdemo/default/image/img_act8.png',
                'link'=>'',
                'wxlink'=>'',
                'background_color'=>'',
                ),
            'module9'=>array(
                'open'=>1,
                'show_huodong'=>1
                ),
            'module10'=>array(
                'open'=>1,
                'color_checked'=>'20AD20',
                'color_nochecked'=>'999999',
                'content'=>array(
                    array(
                        'open'=>1,
                        'title'=>'首页',
                        'link'=>'',
                        'icon_checked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot1_2.png',
                        'icon_nochecked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot1_1.png'                       
                        ),
                    array(
                        'open'=>0,
                        'title'=>'购物车',
                        'link'=>'',
                        'icon_checked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_2.png',
                        'icon_nochecked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_1.png'
                        ),
                    array(
                        'open'=>1,
                        'title'=>'订单',
                        'link'=>'',
                        'icon_checked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_2.png',
                        'icon_nochecked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_1.png'
                        ),
                    array(
                        'open'=>1,
                        'title'=>'我的',
                        'link'=>'',
                        'icon_checked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot4_2.png',
                        'icon_nochecked'=>'https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot4_1.png'
                        )
                    )
                )
            );
        return $config;
    }

    public function getIconGallery()
    {
        if(!$data = K::M('cache/cache')->get($this->_iconGallery_cache_key)){
            /*$data = array(
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_02.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_03.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_04.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_05.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_06.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_07.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_08.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_09.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_10.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_11.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_12.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_13.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_14.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_15.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_16.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_17.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_18.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_19.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_20.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_21.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_22.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_23.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_24.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_25.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_26.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_27.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_28.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_29.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_30.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_31.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_32.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_33.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_34.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_35.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_36.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_37.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_38.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_39.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_40.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_41.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_42.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_43.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_44.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_45.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_46.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_47.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_48.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_49.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_50.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_51.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_52.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_53.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_54.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_55.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_56.png'),
                array('icon'=>'https://img01.jhcms.com/wmdemo/default/icon/icon_57.png'),
            );*/
            $data = array();
            $url = "http://www.jhcms.com/api.php?API=gallery/icons";
            if($res =  K::M('net/http')->get($url,array())){
                if($json = json_decode($res,true)){
                    $data = $json['data'];
                }
            }
            K::M('cache/cache')->set($this->_iconGallery_cache_key, $data);
        }
        return $data;
    }

    public function getThemeGallery()
    {
        if(!$data = K::M('cache/cache')->get($this->_themeGallery_cache_key)){
            //$data = K::M('adv/themes')->items();
            $data = array();
            $url = "http://www.jhcms.com/api.php?API=gallery/advthemes2";
            if($res =  K::M('net/http')->get($url,array())){
                if($json = json_decode($res,true)){
                    $data = $json['data'];
                }
            }
            K::M('cache/cache')->set($this->_themeGallery_cache_key, $data);
        }
        return $data;
    }

}