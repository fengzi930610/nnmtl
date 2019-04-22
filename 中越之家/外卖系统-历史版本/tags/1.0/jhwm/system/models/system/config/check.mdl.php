<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: config.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_System_Config_Check extends Model
{
    public function check_invite($data)
    {
        if ($data['is_inviter_hongbao'] == 1 && empty($data['inviter_hongbao_cfg'])) {
            $this->msgbox->add('邀请人红包设置不能为空', 211)->response();
        }elseif ($data['is_inviter_hongbao'] != 1) {
            unset($data['inviter_hongbao_cfg']);
        }else{
            $data['inviter_hongbao_cfg'] = $this->invite_data($data['inviter_hongbao_cfg']);
        }
        if ($data['is_invitee_hongbao'] == 1 && empty($data['invitee_hongbao_cfg'])) {
            $this->msgbox->add('被邀请人红包设置不能为空', 211)->response();
        }elseif ($data['is_invitee_hongbao'] != 1) {
            unset($data['invitee_hongbao_cfg']);
        }else {
            $data['invitee_hongbao_cfg'] = $this->invite_data($data['invitee_hongbao_cfg']);
        }
        return $data;
    }

    protected function invite_data($data)
    {
        foreach ($data as $k => $v) {
            $stime = trim($v['stime']);
            $ltime = trim($v['ltime']);
            if (($v['hongbao_min_amount'] = (float) $v['hongbao_min_amount']) <= 0) {
                $this->msgbox->add('满减金额不能小于等于0', 212)->response();
            }elseif (($v['hongbao_amount'] = (float) $v['hongbao_amount']) <= 0) {
                $this->msgbox->add('红包金额不能小于等于0', 213)->response();
            }elseif (($v['hongbao_amount_ltime'] = (int) $v['hongbao_amount_ltime']) <= 0 || $v['hongbao_amount_ltime'] > 7) {
                $this->msgbox->add('过期时间设置范围不正确', 214)->response();
            }elseif ($v['hongbao_min_amount'] <= $v['hongbao_amount']) {
                $this->msgbox->add('红包金额不能大于满减金额', 215)->response();
            }/*else if((trim($v['stime'])&&trim($v['ltime']))&&(strtotime($v['stime'])>strtotime($v['ltime']))){
                $this->msgbox->add('红包使用时间不正确',216)->response();
            }*//*else if(!($stime = trim($v['stime'])) || !($ltime = trim($v['ltime']))){
                $this->msgbox->add('请填写完整的红包使用时间',216)->response();
            }else if(!($stime = K::M('helper/format')->checkTime($stime)) || !($ltime = K::M('helper/format')->checkTime($ltime))){
                $this->msgbox->add('红包使用时间格式不正确',217)->response();
            }else if((strpos($ltime,'次日') === false) && (strtotime($stime) >= strtotime($ltime))){
                $this->msgbox->add('红包使用时间不正确',218)->response();
            }*/else if(!K::M('helper/format')->checkTimes($stime, $ltime)){
                $this->msgbox->add('红包使用时间有误',216)->response();
            }else{
                $data[$k] = $v;
            }
        }
        return $data;
    }

    
}
