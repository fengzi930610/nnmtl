<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Jpush_Device extends Mdl_Table
{   
  
    protected $_table = 'jpush_device';
    protected $_pk = 'device_id';
    protected $_cols = 'device_id,uid,staff_id,shop_id,from,register_id,platform,tag_ids';


    public function delete_alias($id, $register_id, $from='member')
    {
        if($info = $this->detail_by_register_id($register_id)){
            $a = array();
            switch ($from) {
                case 'staff':
                    $a['staff_id'] = $id; break;
                case 'shop':
                    $a['shop_id'] = $id; break;
                default:
                    $a['uid'] = $id; break;
            }
            try {
                try {
                    if (!$client = $this->client($from)) {
                        return false;
                    }
                    $client->device()->updateDevice($register_id, null);
                } catch (Exception $e) {
                    ///{{{ios ltd
                    if (!$client = $this->client($from, true)) {
                        return false;
                    }
                    $client->device()->updateDevice($register_id, null);
                    ///ios ltd}}}
                }
            }catch(Exception $e){
                return false;
            }
            return true;
        }
    }

    public function init_device($id, $register_id, $from='member', $tags=array(), $remove_tags=null)
    {
        if(empty($remove_tags) && $remove_tags !== null) {
            if ($device_info = $this->device_info($register_id, $from)) {
                $remove_tags = array();
                if($device_tags = $device_info->data->tags){
                    foreach($device_tags as $v){
                        if(!in_array($v, $tags)){
                            $remove_tags[] = $v;
                        }
                    }
                }
            }
        }
        $remove_tags = empty($remove_tags) ? null : $remove_tags;
        if(!$info = $this->detail_by_register_id($register_id)){
            $a = array('register_id'=>$register_id, 'from'=>$from);
            if(defined('CLIENT_OS')){
                $a['platform'] = strtolower(CLIENT_OS);
            }
            switch ($from) {
                case 'staff':
                    $a['staff_id'] = $id; break;
                case 'shop':
                    $a['shop_id'] = $id; break; 
                default:
                    $a['uid'] = $id; break; 
            }
            if($device_id = $this->create($a)){
                $tags = empty($tags) ? array('default') : $tags;

                try{
                    try{
                        if(!$client = $this->client($from)){
                            return false;
                        }
                        $client->device()->updateDevice($register_id, $id, null, $tags, $remove_tags);
                    }catch(Exception $e){
                        ///{{{ios ltd
                        if(!$client = $this->client($from, true)){
                            return false;
                        }
                        $client->device()->updateDevice($register_id, $id, null, $tags, $remove_tags);
                        ///ios ltd}}}
                    }
                    return $this->detail($device_id);
                }catch(Exception $e){
                    K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array('init_device_create'=>$a, 'exception'=>$e->getMessage()));
                    return false;
                }
            }
        }else if($register_id){
            $a = array();
            if($from == 'member' && $info['uid'] != $id){
                $a['uid'] = $info['uid'] = $id;
            }else if($from == 'staff' && $info['staff_id'] != $id){
                $a['staff_id'] = $info['staff_id'] = $id;
            }else if($from == 'shop' && $info['shop_id'] != $id){
                $a['shop_id'] = $info['shop_id'] = $id;
            }
            $tags = array_merge((array)$tags, (array)$info['tags']);
            try{
                try{
                    if(!$client = $this->client($from)){
                        return false;
                    }
                    $client->device()->updateDevice($register_id, $id, null, $tags, $remove_tags);
                    if($a){
                        $this->update($info['device_id'], $a);
                    }
                }catch(Exception $e){
                    ///{{{ios ltd
                    if(!$client = $this->client($from, true)){
                        return false;
                    }
                    $client->device()->updateDevice($register_id, $id, null, $tags, $remove_tags);
                    if($a){
                        $this->update($info['device_id'], $a);
                    }
                    ///ios ltd}}}
                }
            }catch(Exception $e){
                K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array('init_device_info'=>$info, 'exception'=>$e->getMessage()));
                return false;
            }
        }
        return $info;
    }
    public function detail_by_register_id($register_id)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE ".self::field('register_id', $register_id);
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;        
    }
    public function detail_by_id($id, $from='member')
    {
        if(!$id = (int)$id){
            return false;
        }
        switch($from){
            case 'staff' : 
                $where = "staff_id='{$id}'"; break;
            case 'shop' : 
                $where = "shop_id='{$id}'"; break;
            default : 
                $where = "uid='{$id}'"; break;
        }
         if(!in_array($from, array('member', 'shop', 'staff'))){
            return false;
        }
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;
    }
    protected function _format_row($row)
    {
        static $_tag_list = null;
        if($_tag_list === null){
            $_tag_list = K::M('jpush/tag')->fetch_all();
        }
        static $_from_list = array('member'=>'用户端', 'staff'=>'服务端', 'shop'=>'商户端');
        $row['from_title'] = $_from_list[$row['from']];
        $tags = array();
        foreach(explode(',', $row['tag_ids']) as $id){
            if($tag = $_tag_list[$id]){
                $tags[$id] = $tag['tag'];
            }
        }
        switch($row['from']){
            case 'staff' : 
                $row['alias'] = $row['staff_id']; break;
            case 'shop' : 
                $row['alias'] = $row['shop_id']; break;
            default : 
                $row['alias'] = $row['uid']; break;
        }
        $row['tags'] = !empty($tags) ? $tags : array('default');
        return $row;
    }
    public function client($from='member', $ltd=false)
    {
        Import::L('JPush/JPush.php');
        $cfg = K::$system->config->get('apppush');
        try{
            if($ltd && $cfg[$from]['appkey_ltd'] && $cfg[$from]['secret_ltd']){
                $client = new JPush($cfg[$from]['appkey_ltd'], $cfg[$from]['secret_ltd']);
            }elseif($cfg[$from]['appkey'] && $cfg[$from]['secret']){
                $client = new JPush($cfg[$from]['appkey'], $cfg[$from]['secret']);
            }else{
                return false;
            }
            return $client;
        }catch(Exception $e){
            K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array('init_device_info'=>$info, 'exception'=>$e->getMessage()));
            return false;
        }
    }
    
    public function get_appkey($from='member', $ltd=false)
    {
        $cfg = K::$system->config->get('apppush');
        return $cfg[$from];
    }

    public function jpush($title, $content, $params, $extras=null,  $schedule=null)
    {

        $tag_id = intval($params['tag_id']);
        $platform = strtolower($params['platform']);
        $device_id = (int)$params['device_id'];
        $from = strtolower($params['from']);
        if(!in_array($from, array('member', 'staff', 'shop'))){
            $from = 'member';
        }
        if(!in_array($platform, array('all', 'android', 'ios'))){
            $platform = 'all';
        }
        $log = array('title'=>$title, 'content'=>$content, 'from'=>$from, 'platform'=>$platform, 'device_id'=>$device_id);
        try{
            if(!$client = $this->client($from)){
                return false;
            }
            if(in_array($extras['type'], array('newOrder', 'cuiOrder', 'tuiOrder', 'paiOrder','cancelOrder'))){
                $sound = $extras['type'].'.mp3';
            }else{
                $sound = 'default';
            }
            $pushLoad = $client->push();
            $tag_list = K::M('jpush/tag')->fetch_all();
            $have_filter = false;
            if($device_id && ($device = K::M('jpush/device')->detail($device_id))){
                $pushLoad->addRegistrationId($device['register_id']);
                $log['register_id'] = $device['register_id'];
                $have_filter = true;
            }else if($register_id = $params['register_id']){
                $pushLoad->addRegistrationId($register_id);
                $log['register_id'] = $register_id;
                $have_filter = true;
            }else{
                if($alias = $params['alias']){
                    $pushLoad->addAlias($alias);
                    $log['alias'] = $params['alias'];
                    $have_filter = true;
                }
                if($tag_id && ($tag = $tag_list[$tag_id])){
                    $pushLoad->addTag($tag['tag']);
                    $log['tag'] = $tag['tag'];
                    $have_filter = true;
                }
                if($tag = $params['tag']){
                    $pushLoad->addTag($tag);
                    $log['tag'] = implode(',', $tag);
                    $have_filter = true;
                }
                if($tag_and = $params['tag_and']){
                    $pushLoad->addTagAnd($tag_and);
                    $have_filter = true;
                }
                if($tag_not = $params['tag_not']){
                    $have_filter = true;
                    //
                }
                if(empty($have_filter)){
                    $pushLoad->addAllAudience();
                }
            }
            if($platform == 'ios'){
                $pushLoad->setPlatform('ios');
                //$pushLoad->addIosNotification($content, 'default', '+1', true, 'iOS category', $extras);
                //$pushLoad->addIosNotification($content, $sound, '+1', true, null, $extras);
                $pushLoad->addIosNotification(array('title'=>$title, 'body'=>$content), $sound, '+1', true, null, $extras);
                $pushLoad->setOptions(null, null, null, true, null);
            }else if($platform == 'android'){
                $pushLoad->setPlatform('android');
                $pushLoad->addAndroidNotification($content, $title, 1, $extras);
            }else{
                $pushLoad->setPlatform('all');
                $pushLoad->addAndroidNotification($content, $title, 1, $extras);
                //$pushLoad->addIosNotification($content, 'default', '+1', true, 'iOS category', $extras);
                //$pushLoad->addIosNotification($content, $sound, '+1', true, null, $extras);
                $pushLoad->addIosNotification(array('title'=>$title, 'body'=>$content), $sound, '+1', true, null, $extras);
                $pushLoad->setOptions(null, null, null, true, null);
            }
            if($schedule === null){
                try{
                    $respone = $pushLoad->send();
                }catch(Exception $e){
                    K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array('pushload'=>$pushLoad, 'respone'=>$respone, 'exception'=>$e->getMessage()));
                }
                ///{{{ios ltd
                try{
                    $cfg = $this->get_appkey($from, true);
                    if($cfg['appkey_ltd'] && $cfg['secret_ltd']){
                        $pushLoad->client->set_appkey($cfg['appkey_ltd'], $cfg['secret_ltd']);
                        $pushLoad->send();
                    }
                }catch(Exception $e){
                    K::M('system/logs')->log('jpush.Exception.ltd.'.date('Ymd'), array('pushload'=>$pushLoad, 'respone'=>$respone, 'exception'=>$e->getMessage()));
                }
                ///ios ltd}}}
                $respone = (array)$respone;
                if(isset($respone['data'])){
                    $log['status'] = 1;
                    $res = true;
                }else{
                    $log['status'] = 0;
                    $res = false;
                }
            }else{
                $_schedule = array();
                if(is_numeric($schedule) && $schedule > __TIME){
                    $_schedule = array('time'=>date('Y-m-d H:i:s', $schedule));
                }elseif(is_string($schedule) && strtotime($schedule) > __TIME){
                    $_schedule = array('time'=>$schedule);
                }else if(is_array($schedule)){
                    $_schedule = $schedule;
                }
                try{
                    $payload = $pushLoad->build();
                    $respone = $client->schedule()->createSingleSchedule("定时推送任务".date("YmdHis"), $payload, $_schedule);
                }catch(Exception $e){
                    K::M('system/logs')->log('jpush.Exception', array('pushload'=>$pushLoad, 'respone'=>$respone, 'exception'=>$e->getMessage()));
                }
                ///{{{ios ltd
                try{
                    $cfg = $this->get_appkey($from, true);
                    if($cfg['appkey_ltd'] && $cfg['secret_ltd']){
                        $pushLoad->client->set_appkey($cfg['appkey_ltd'], $cfg['secret_ltd']);
                        $payload = $pushLoad->build();
                        $client->schedule()->createSingleSchedule("定时推送任务".date("YmdHis"), $payload, $_schedule_ltd);
                    }
                }catch(Exception $e){
                    K::M('system/logs')->log('jpush.Exception.ltd.'.date('Ymd'), array('pushload'=>$pushLoad, 'respone'=>$respone, 'exception'=>$e->getMessage()));
                }
                ///ios ltd}}}
                $log['push_time'] = strtotime($_schedule['time']);
                $respone = (array)$respone;
                if(isset($respone['data']['schedule_id']) || isset($_schedule_ltd['schedule_id'])){
                    $log['status'] = 1;
                    $res = true;
                }else{
                    $log['status'] = 0;
                    $res = false;
                }
            }
            $log['link_title'] = $params['link_title'];// 推送链接标题
            $log['link_url'] = $params['link_url'];// 推送链接地址
            if(is_array($log['alias'])){
                $log['alias'] = implode(',', $log['alias']);
            }
            K::M('jpush/log')->create($log);
            return $res;
        }catch(Exception $e) {
            K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array('respone'=>$respone, 'exception'=>$e->getMessage()));
            return false;
        }  
    }

    public function device_info($register_id, $from='member')
    {
        $device_info = array();
        try{
            if($client = K::M('jpush/device')->client($from)){
                $device_info = $client->device()->getDevices($register_id);
            }            
        }catch( Exception $e){
            try{
                if($client = K::M('jpush/device')->client($from,'ltd')){
                    $device_info = $client->device()->getDevices($register_id);
                }
            }catch(Exception $e){

            }
        }
        return $device_info;
    }
    
    public function send_member($uid, $title, $content, $extras=null)
    {
        return $this->jpush($title, $content, array('alias'=>$uid, 'from'=>'member', 'tag_and'=>array('login_on')), $extras);
    }
    public function send_staff($staff_id, $title, $content, $extras=null)
    {
        return $this->jpush($title, $content, array('alias'=>$staff_id, 'from'=>'staff', 'tag_and'=>array('login_on')), $extras);
    }
    public function send_shop($shop_id, $title, $content, $extras=null)
    {
        return $this->jpush($title, $content, array('alias'=>$shop_id, 'from'=>'shop', 'tag_and'=>array('login_on')), $extras);
    }

    public function update_tags($id, $tags=null, $removeTags=null, $from='member')
    {
        try{

            if(is_null($tags) && is_null($removeTags)){
                return false;
            }
            $device = $this->client($from)->device();
            $registration_ids = $device->getAliasDevices($id)->data->registration_ids;
            if($tags) {
                foreach ((array)$tags as $v) {
                    $device->updateTag($v, $registration_ids);
                }
            }
            if($removeTags) {
                foreach ((array)$removeTags as $v) {
                    $device->updateTag($v, null, $registration_ids);
                }
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function update_staff_tag($staff, $parmas = null){
        if(!$staff){
            return false;
        }else if(!$parmas||!is_array($parmas)){
            return false;
        }else {
            $device = $this->client('staff')->device();
            $reguset_ids = $device->getAliasDevices($staff['staff_id'])->data->registration_ids;
            if(!$reguset_ids){
                return false;
            }else{
                foreach($reguset_ids as $v){
                   if($parmas['add_tag']){
                       try{
                           $this->init_device($staff['staff_id'], $v, 'staff', $parmas['add_tag'],null);
                       }catch(Exception $e){
                           K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array( 'exception'=>$e->getMessage()));
                       }
                   }
                    if($parmas['remove_tag']){
                        try{
                            $this->init_device($staff['staff_id'], $v, 'staff', null, $parmas['remove_tag']);
                        }catch(Exception $e){
                            K::M('system/logs')->log('jpush.Exception.'.date('Ymd'), array( 'exception'=>$e->getMessage()));
                        }
                    }
                }
            }

        }
    }
}