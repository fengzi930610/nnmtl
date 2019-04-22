<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Hongbao_Huodong extends Mdl_Table
{   
  
    protected $_table = 'hongbao_huodong';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,title,intro,stime,ltime,times,weeks,limit,config,status,closed,clientip,dateline,background,background_color';

    protected function _format_row($row)
    {
    	$row['is_send'] = 0;
    	$row['weeks'] = $row['weeks'] || $row['weeks'] == 0 ? explode(',',$row['weeks']) : array(1, 2, 3, 4, 5, 6, 0);
    	$row['times'] = $row['times'] ? unserialize($row['times']) : array('stime'=>'00:00','ltime'=>'23:59');
    	$row['config'] = $row['config'] ? unserialize($row['config']) : array();

    	$now_week = date('w',__TIME);
    	$s_time = strtotime(date('Y-m-d').' '.$row['times']['stime']);
    	$l_time = strtotime(date('Y-m-d').' '.$row['times']['ltime']);
        if($row['closed'] == 0 && in_array($now_week, $row['weeks']) && $row['status'] && ($row['stime'] <= __TIME && $row['ltime'] >= __TIME) && ($s_time <= __TIME && $l_time >= __TIME)){
            $row['is_send'] = 1;
        }

        foreach($row['config'] as $k=>$v){
            $v['stime_time'] = K::M('helper/format')->format_morrowTime($v['stime']);
            $v['ltime_time'] = K::M('helper/format')->format_morrowTime($v['ltime']);
            $row['config'][$k] = $v;
        }

    	return $row;
    }

    public function getTjHongbao($uid=0)
    {
        $uid = (int)$uid;
        $tjhongbao = K::M('hongbao/huodong')->find(array('closed'=>0));  //天降红包修改 2018-03-13 by yufan
        $hongbao = array('title'=>'','intro'=>'', 'background'=>'', 'background_color'=>'', 'items'=>array());
        if($tjhongbao['is_send'] == 1 && $uid){
            $day = date('Ymd', __TIME);
            $lin_hongbao_list = array();
            if(K::M('cache/cache')->islock('tjhongbao_'.$uid, 3)){
                if($tjhongbao['limit'] > K::M('hongbao/huodonglog')->count(array('uid'=>$uid,'huodong_id'=>$tjhongbao['huodong_id'])) && !K::M('hongbao/huodonglog')->count(array('uid'=>$uid,'day'=>$day))){
                    foreach($tjhongbao['config'] as $k=>$v){
                        $hb = array(
                            'title'=>$tjhongbao['title'],
                            'min_amount'=>$v['min_amount'],
                            'amount'=>$v['amount'],
                            'ltime'=>$v['day']*86400 + strtotime(date('Y-m-d',__TIME)) + 86399,
                            'from'=>$v['type'],
                            'limit_stime'=>$v['stime'],
                            'limit_ltime'=>$v['ltime'],
                            'type'=>3
                        );
                        if($hongbao_id = K::M('hongbao/hongbao')->send($uid,$hb)){
                            $a = array(
                                    'hongbao_id'=>$hongbao_id,
                                    'min_amount' => $v['min_amount'],
                                    'amount'     => $v['amount'],
                                    'uid'        => $uid,
                                    'day'        => $day,
                                    'type'       => 1,
                                    'huodong_id' => $tjhongbao['huodong_id'],
                                );
                            
                            K::M('hongbao/linqulog')->create($a);
                            $v['dateline'] = $v['day']*86400 + strtotime(date('Y-m-d',__TIME)) + 86399;
                            $lin_hongbao_list[] = $v;
                        }
                    }
                }
                if(!empty($lin_hongbao_list)){
                    $hongbao = array(
                        'title'=>$tjhongbao['title'],
                        'intro'=>$tjhongbao['intro'],
                        'background'=>$tjhongbao['background'] ? K::M('magic/upload')->geturl($tjhongbao['background']) : '',
                        'background_color'=>$tjhongbao['background_color'],
                        'items'=>$lin_hongbao_list
                    );
                    //天降红包领取日志（记录领取次数）
                    $hdlog = array(
                        'huodong_id'=>$tjhongbao['huodong_id'],
                        'uid'=>$uid,
                        'day'=>$day,
                        'dateline'=>__TIME,
                        'clientip'=>__IP,
                        );
                    K::M('hongbao/huodonglog')->create($hdlog);
                }
                K::M('cache/cache')->unlock('tjhongbao_'.$uid);
            }           
        }
        return $hongbao;
    }    
}
