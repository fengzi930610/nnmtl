<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: config.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Ctl_System_Config extends Ctl
{
    public $__call = 'index';
    public function index($k='index')
    {
        if($k == 'ucenter'){
            $this->ucenter();
        }else if($this->checksubmit()){
            $this->save($k);
        }else{
            $this->setting($k);
        }
    }
    
    
    public function appcate_create(){
        if($data = $this->checksubmit('data')){
        }
    }
    public function setting($k=null)
    {
        if(empty($k)){
            $this->msgbox->add('很抱歉，您请求的页面不存在1', 201);
        }else if(($cfg = $this->system->config->get($k)) === null){
            $this->msgbox->add('很抱歉，您请求的页面不存在', 201);
        }else{
            $pager['K'] = $k;
            if($k == 'sms'){
                //查询短信余额
                $this->pagedata['have_sms_count'] = K::M('sms/sms')->query();
            }
            if($k == 'jifen'){
                $this->pagedata['jifen_modules'] = K::M('jifen/jifen')->get_modules();
            }

            $this->pagedata['pager'] = $pager;
            $this->pagedata['config'] = $cfg;
            $this->tmpl = "admin:config/{$k}.html";
        }
    }

    public function save()
    {

        if(!$pk = $this->GP('K')){
            $this->msgbox->add('非法的请求', 201);
        }else if(!$data = $this->GP('config')){
            $this->msgbox->add('非法的数据提交', 202);
        }else if(($cfg = $this->system->config->get($pk)) === null){
            $this->msgbox->add('你要保存的设置不存在', 203);
        }else{
            if($pk == 'attach'){
                if($dir = $data['dir']){
                    if(preg_match('/\.(asp|php|aspx|jsp|cgi)/i', $dir)){
                        $this->msgbox->add('目录名不能含有不安全信息', 211);
                        $this->msgbox->response();
                    }else if(preg_match('/;/i', $dir)){
                        $this->msgbox->add('目录名不能含有不安全信息', 211);
                        $this->msgbox->response();
                    }                    
                }
            }
            if($_FILES['config']){
                foreach($_FILES['config'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'config')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            if($pk == 'moneypack') {
                // 充值配置 单独处理  by zhang 20170520 14:59
                $pack_list = array();
                foreach($data as $k=>$v){
                    $_money = (int)$v['money'];
                    $_total_coupon_amount = 0;
                    if(intval($v['money']) > 0){
                        $_hb_list = array();
                        foreach($v['hongbao'] as $kk=>$vv){
                            $a = (float)$vv['order_amount'];
                            $b = (float)$vv['coupon_amount'];
                            $c = (int)$vv['day'];
                            /*if((trim($vv['stime'])&&trim($vv['ltime']))&&(strtotime($vv['stime'])>=strtotime($vv['ltime']))){
                                $this->msgbox->add('红包使用时间不正确',201)->response();
                            }*/

                            //v3.6 次日
                            $stime = trim($vv['stime']);
                            $ltime = trim($vv['ltime']);
                            if(!K::M('helper/format')->checkTimes($stime, $ltime)){
                                $this->msgbox->add('红包使用时间不正确',201)->response();
                            }

                            if($a > 0 && $b > 0 && $a >= $b && $c >0 && $c <= 7){
                                $_hb_list[] = array('order_amount'=>$a, 'coupon_amount'=>$b, 'day'=>$c,'type'=>$vv['type'],'cate_id'=>$vv['cate_id'],'stime'=>$vv['stime'],'ltime'=>$vv['ltime']);
                                $_total_coupon_amount += $b;
                            }
                        }
                        if($_total_coupon_amount > 0){
                            $pack_list[] = array('money'=>$_money, 'give'=>$_total_coupon_amount,'hongbao'=>$_hb_list);
                        }
                    }
                }
                $data = $pack_list;
            }elseif($pk == 'hongbao'){
                $_hongbao_data = array();
                foreach($data['hongbao'] as $k=>$v){                    
                    /*if($v['min_amount'] > 0 && $v['amount'] > 0){
                        $_hongbao_data[] = $v;
                    }
                    if((trim($v['stime'])&&trim($v['ltime']))&&(strtotime($v['stime'])>=strtotime($v['ltime']))){
                        $this->msgbox->add('红包时间设置不正确',201)->response();
                    }*/
                    //v3.6 次日
                    $a = (float)$v['min_amount'];
                    $b = (float)$v['amount'];
                    $c = (int)$v['day'];
                    $stime = trim($v['stime']);
                    $ltime = trim($v['ltime']);
                    if(!K::M('helper/format')->checkTimes($stime, $ltime)){
                        $this->msgbox->add('红包使用时间不正确',201)->response();
                    }
                    if($a > 0 && $b > 0 && $a >= $b && $c > 0 && $c <= 7){
                        $_hongbao_data[] = $v;
                    }
                }
                $data['hongbao'] = $_hongbao_data;
                if(!$title = $data['title']){
                    $this->msgbox->add('标题不能为空', 213)->response();
                }elseif(!$desc = $data['desc']){
                    $this->msgbox->add('描述不能为空', 214)->response();
                }
                $data['title'] = $title;
                $data['desc'] = $desc;
                $upload = K::M('magic/upload');
                if($_FILES['coin']['error'] == UPLOAD_ERR_OK){
                    if($a = $upload->upload($_FILES['coin'])){
                        $data['coin'] = $a['photo'];
                    }
                }
            }elseif($pk == 'tjhongbao'){
                $_hongbao_data = array();
                foreach($data['hongbao'] as $k=>$v){                    
                    /*if($v['min_amount'] >0 && $v['amount'] > 0){
                        $_hongbao_data[] = $v;
                    }
                    if((trim($v['stime'])&&trim($v['ltime']))&&(strtotime($v['stime'])>=strtotime($v['ltime']))){
                        $this->msgbox->add('红包时间设置不正确',201)->response();
                    }*/

                    //v3.6 次日
                    $a = (float)$v['min_amount'];
                    $b = (float)$v['amount'];
                    $c = (int)$v['day'];
                    $stime = trim($v['stime']);
                    $ltime = trim($v['ltime']);
                    if(!K::M('helper/format')->checkTimes($stime, $ltime)){
                        $this->msgbox->add('红包使用时间不正确',201)->response();
                    }
                    if($a > 0 && $b > 0 && $a >= $b && $c >0 && $c <= 7){
                        $_hongbao_data[] = $v;
                    }
                }
                $data['hongbao'] = $_hongbao_data;
                if(!$title = $data['title']){
                    $this->msgbox->add('标题不能为空', 213)->response();
                }elseif(!$intro = $data['intro']){
                    $this->msgbox->add('副标题不能为空', 214)->response();
                }
                $data['title'] = $title;
                $data['intro'] = $intro;
            }elseif($pk == 'paotui'){
                $_paotui_data = array();
                foreach ($data['mileage'] as $k => $v) {
                    if($v['fkm'] >0 && $v['fm'] > 0){
                        $_paotui_data['mileage'][$k] = $v;
                    }
                }
                foreach ($data['weight'] as $k => $v) {
                    if($v['fkg'] >0 ){
                        $_paotui_data['weight'][$k] = $v;
                    }
                }
                foreach ($data['time'] as $k => $v) {
                    if($v['stime'] && $v['ltime'] && (int)$v['ratio'] > 0){
                        if((preg_match('/^\d{1,2}\:\d{2}$/i', $v['stime']))&&(preg_match('/^\d{1,2}\:\d{2}$/i', $v['ltime']))){
                            $_paotui_data['time'][$k] = $v;
                        }                        
                    }
                }
                foreach ($data['tip'] as $k => $v) {
                    if($v > 0){
                        $_paotui_data['tip'][$k] = $v;
                    }
                }
                foreach ($data['price'] as $k => $v) {
                    if($v >0){
                        $_paotui_data['price'][$k] = $v;
                    }
                }

                $data = $_paotui_data;
            }else if($pk == 'fright'){
                $data = K::M('helper/format')->overturn($data);
                $min_array = array();
                foreach($data as $v){
                    $min_array[] =  $v['fm'];
                }
                sort($min_array);
                $min_amount = $min_array[0]?$min_array[0]:0;
                K::M('waimai/waimai')->edit_shop_freight($min_amount);
            }else if($pk=='badweather'){
                $data['config'] = K::M('helper/format')->overturn($data['config']);


                foreach ($data['config'] as $kkk=>$vvv){
                    if(!$vvv['fkm']){
                      unset($data['config'][$kkk]);
                    }
                    if(!$vvv['fm']&&$vvv['fkm']){
                        $data['config'][$kkk]['fm'] = 0;
                    }
                }

            } else if($pk=='ditui'){
                $_hongbao_data = array();
                foreach($data['hongbao'] as $k=>$v){
                    /*if(!$v['min_amount']||!$v['amount']){
                        unset($data['hongbao'][$k]);
                    }else if($v['min_amount']<$v['amount']){
                        $this->msgbox->add('红包金额设置不正确',201)->response();
                    }
                    if((trim($v['stime'])&&trim($v['ltime']))&&(strtotime($v['stime'])>=strtotime($v['ltime']))){
                        $this->msgbox->add('红包时间设置不正确',202)->response();
                    }*/
                    //v3.6 次日
                    $a = (float)$v['min_amount'];
                    $b = (float)$v['amount'];
                    $c = (int)$v['day'];
                    $stime = trim($v['stime']);
                    $ltime = trim($v['ltime']);
                    if(!K::M('helper/format')->checkTimes($stime, $ltime)){
                        $this->msgbox->add('红包使用时间不正确',201)->response();
                    }
                    if($a > 0 && $b > 0 && $a >= $b && $c >0 && $c <= 7){
                        $_hongbao_data[] = $v;
                    }
                }
                $data['hongbao'] = $_hongbao_data;
            }else if($pk=="frighttime"){
                $tmp =array();
                $tmp['fkm'] = $data['fkm'];
                $tmp['fm'] = $data['fm'];
                unset($data['fkm']);
                unset($data['fm']);
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])||!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'])){
                    $this->msgbox->add('设置时间不正确',203)->response();
                }else if(strtotime($data['stime'])==strtotime($data['ltime'])){
                    $this->msgbox->add('设置时间错误',205)->response();
                }
                else if(!$tmp_data = K::M('helper/format')->overturn($tmp)){
                    $this->msgbox->add('请添加数据',204)->response();
                }else{
                    foreach ($tmp_data as $kk1=>$vv1){
                        if(!$vv1['fkm']||($vv1['fkm']&&!$vv1['fm'])){
                            unset($tmp_data[$kk1]);
                        }
                    }
                    $data['config'] = $tmp_data;
                }
            }else if($pk=='automatic'){
                if($data['unpay_cancel_time']<5||$data['unpay_cancel_time']>300){
                    $this->msgbox->add('超时未支付时间请设置在5分钟到3个小时')->response();
                }
            }
            $checkobj = K::M('system/config/check');
            if(method_exists($checkobj, "check_{$pk}")) {

                // call_user_method_array
                /*if ($_data = call_user_func_array("check_{$pk}", $checkobj, array($data))) {
                    $data = $_data;
                }*/
                if ($_data = call_user_func_array(array($checkobj, "check_{$pk}"), array($data))) {
                    $data = $_data;
                }
            }
            if($this->system->config->set($pk, $data,false)){
                $this->msgbox->add('保存数据成功');
            }
        }
    }
    public function ucenter()
    {
        $uc = 'APPID,KEY,CHARSET,API,IP,CONNECT,DBHOST,DBUSER,DBPW,DBNAME,DBCHARSET,DBTABLEPRE';
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 211);
            }else{
                $content = "<?php \n";
                $oHtml = K::M('content/html');
                foreach(explode(',', $uc) as $v){
                    $content .= "define('UC_{$v}', '".$oHtml->encode($data[$v])."');\n";
                }
                $content .= '?>';
                file_put_contents(__CFG::DIR.'uc_config.php', $content);
            }
        }else{
            $this->system->config->ucenter();
            if(defined('UC_API')){
                foreach(explode(',', $uc) as $v){
                    $UCENTER[$v] = constant("UC_{$v}");
                }
            }
            $this->pagedata['UCENTER'] = $UCENTER;
            $this->tmpl = 'admin:config/ucenter.html';
        }
    }

}
