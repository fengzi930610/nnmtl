<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}
class Mdl_Member_Member extends Mdl_Table {
    protected $_table = 'member';
    protected $_pk = 'uid';
    protected $_cols = 'uid,mobile,paypasswd,passwd,nickname,sex,money,coin,total_money,orders,jifen,face,wx_openid,wx_unionid,loginip,lastlogin,pmid,regip,closed,dateline,status,register_from';
    protected $_orderby = array('lastlogin'=>'DESC', 'uid'=>'DESC');
    public function member_by_uids($uids){
        $sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE `uid` IN ($uids)";
        $rs = $this->db->Execute($sql);
        $items = array();
        while($row = $rs->fetch()){
            $items[] = $row;
        }
        return $items;
    }
    public function member($u, $l = 'uid')
    {
        $l = strtolower($l);
        switch ($l) {
            case 'uid':
                $field = 'uid';
                break;
            case 'mobile':
                $field = 'mobile';
                break;
            case 'wx_unionid': case 'unionid':
                $field = 'wx_unionid';
                break;
            case 'wx': case 'wx_openid': case 'openid':
                $field = 'wx_openid';
                break;
            default:
                return false;
        }
        $sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE `closed`=0 AND " . $this->field($field, $u);
        if ($row = $this->db->GetRow($sql)) {
            $row = $this->_format_row($row);
            return $row;
        }else{
            return false;
        }
    }
    public function guest()
    {
        static $guest = array('uid' => 0, 'mobile' => '', 'nickname' => '游客');
        return $this->_format_row($guest);
    }
    public function create($data, $checked = false)
    {
        if (!$checked && !($data = $this->_check($data))) {
            return false;
        }
        $data['regip'] = $data['regip'] ? $data['regip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        return $this->db->insert($this->_table, $data, true);
    }
    public function getPaynum()
    {
        return array(
            '1' => '1',
            '500'  => '50',
            '2000' => '250',
            '5000' => '600',
        );
    }
    
    public function getRecharge()
    {  //充值夺宝币比例
        return array(
            '10' => '1',
            '20' => '2',
            '50' => '5',
            '100' => '12',
            '200' => '25',
            '500'  => '70',
            '1000' => '150',
            '5000' => '1000',
        );
    }
    
    public function update_money($uid, $money, $intro='')
    {
        return K::M('member/money')->update($uid, $money, $intro);
    }
    public function update_total_money($uid, $money)
    {
        if($uid = (int)$uid){
            $sql = "UPDATE ".$this->table($this->_table)." SET `total_money`=`total_money`+{$money} WHERE uid='$uid'";
            return $this->db->Execute($sql);
        }
        return false;
    }
    public function update_jifen($uids, $jifen, $intro='', $admin='')
    {
        if(!$jifen = (int)$jifen){
            $this->msgbox->add('更变的积分值非法', 411);
        }else if(empty($intro)){
            $this->msgbox->add('变更日志不可为空', 412);
        }else{
            if($uids = K::M('verify/check')->ids($uids)){
                foreach(explode(',', $uids) as $uid){
                    $sql = "UPDATE ".$this->table($this->_table)." SET `jifen`=`jifen`+{$jifen} WHERE uid='$uid'";
                    if($this->db->Execute($sql)){
                        K::M('member/log')->log($uid, 'jifen', $jifen, $intro, $admin);
                    }
                }
                return true;
            }
        }
        return false;
    }
    
    public function update_coin($uid, $coin, $intro='', $admin='')
    {
        if(!$coin = (int)$coin){
            $this->msgbox->add('更变的夺宝币值非法', 411);
        }else if(empty($intro)){
            $this->msgbox->add('变更日志不可为空', 412);
        }else{
            $sql = "UPDATE ".$this->table($this->_table)." SET `coin`=`coin`+{$coin} WHERE uid='$uid'";
            if($this->db->Execute($sql)){
                K::M('member/log')->log($uid, 'coin', $coin, $intro, $admin);
                return true;
            }
        }
        return false;
    }
    
    
    public function update_account($uid, $type, $num, $intro='', $admin='')
    {
        if($type == 'money'){
            return $this->update_money($uid, $num, $intro);
        }else{
            return $this->update_jifen($uid, $num, $intro);
        }
    }
    public function update_face($uid, $file='', $data=null)
    {
        $cfg = K::$system->config->get('attach');
        $D = $cfg['attachdir'];
        $a = strtoupper(md5($uid));
        $b = substr($a,0,3);
        $face = "face/{$b}/{$a}.jpg";
        if($data !== null){
            if(preg_match("/\<(\?php|\<\? )/is", $data)){
                $this->msgbox->add('不是安全的图片', 999);
                return false;
            }
            K::M('io/dir')->create(dirname($D.$face));
            if(!file_put_contents($D.$face,$data)){
                $this->msgbox->add('保存图片数据失败',501);
                return false;
            }
        }else if($file != $D.$face){
            if(!K::M('image/gd')->thumb($file, $D.$face,180,180,true)){
                $this->msgbox->add('图片处理失败',502);
                return false;
            }
        }
        $face = $face."?".rand(100,999);
        $a = array('face'=>$face);
        $this->update($uid, array('face'=>$face), true);
        //刷新用户缓存
        return $face;
    }
    public function regain($val)
    {
        $ret = false;
        if(!empty($val)) {
            if(is_array($val)){
                $val = implode(',', $val);
            }
            if(!K::M('verify/check')->ids($val)){
                return false;
            }
            $val = explode(',', $val);
            $ret = $this->db->update($this->_table, array('closed'=>0), self::field($this->_pk, $val));
            $this->clear_cache($val);
        }
        return $ret;
    }
    
    //更改信息通过手机号码
    public function update_by_mobile($mobile, $data)
    {
        $str = "";
        foreach($data as $k=>$v){
            $str .= "`{$k}`='{$v}',";
        }
        $sql = "UPDATE ".$this->table($this->_table)." SET ".rtrim($str,',')." WHERE `mobile`={$mobile}";
        return $this->db->Execute($sql);
    }
    public function check_mobile($mobile)
    {
        if(!K::M('verify/check')->vietnamMobile($mobile)){
            $this->msgbox->add('手机号码格式不正确', 511);
            return false;
        }else if($member = K::M('member/member')->member($mobile, 'mobile')){
            $this->msgbox->add('此手机号已被占用', 512);
            return false;
        }
        return $mobile;
    }
    
    public function send($uid, $title, $content, $extras=array())
    {
        if($res = K::M('jpush/device')->send_member($uid, $title, $content, $extras)){
            if(is_numeric($uid)){
                $a = array('uid'=>$uid, 'title'=>$title, 'content'=>$content);
                //0:所有消息 1:红包消息, 2:订单消息,3:其它消息  
                switch ($extras['type']) {
                    case 'hongbao': case 1:
                        $a['type'] = 1; break;
                    case 'order': case 2:
                        $a['order_id'] = (int)$order_id;
                        $a['type'] = 2; break;
                    case 3:
                        $a['type'] = 3; break;
                    default:
                        $a['type'] = 0; break;
                }
                K::M('member/message')->create($a);
            }
        }
        return $res;
    }

    protected function _format_row($row)
    {
        if(!$row['face']){
            $row['face'] = 'default/member_face.png';
        }
        //=====20181128新增，尝试对昵称进行rawurldecode解码，如果解码失败，则不修改原值
        if($row['nickname'] !== "")
        {
            $tmpStr = rawurldecode($row['nickname']);
            if($tmpStr)
                $row['nickname'] = $tmpStr;
        }
        //===============================================================
        if(!$row['nickname']) {
            $row['nickname'] = '匿名';
        }
        $row['pid'] = sprintf("M%05d", $row['uid']);
        return $row;
    }
}
