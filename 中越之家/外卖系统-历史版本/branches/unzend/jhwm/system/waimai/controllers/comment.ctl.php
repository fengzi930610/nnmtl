<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 * check view code by shzhrui
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Comment extends Ctl
{
	/*商家评价列表*/
    public function index($shop_id)
    {
        if(!$shop_id){
            $this->msgbox->add('参数不正确',121);
        }elseif(!$detail=K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('非法访问',122);
        }else{
            $filter = array('shop_id'=>$shop_id,'closed'=>0);
            $order  = array('dateline'=>'desc');
            $items  = K::M('waimai/comment')->items($filter, $order, 1, 50);
            $ids    = '';
            $uids   = '';
            foreach($items as $item){
                $ids  .= $item['comment_id'].',';
                $uids .= $item['uid'].',';
            }
            $uids = rtrim($uids, ',');
            $users = K::M('member/member')->items_by_ids($uids);
            foreach($items as $k=>$item){
                $u = array();
                foreach($users as $user){
                    if($item['uid'] == $user['uid']){
                        $u['mobile']   = substr_replace($user['mobile'],'****',3,4);
                        $u['face']     = $user['face'];
                        $u['nickname'] = $user['nickname'];
                    }
                }
                $items[$k]['user'] = $u;
            }
            if($ids){
                $ids = rtrim($ids, ',');
                $photos = K::M('waimai/commentphoto')->items_by_ids($ids);
                foreach($items as $k=>$item){
                    foreach($photos as $photo){
                        if($item['comment_id'] == $photo['comment_id']){
                            $items[$k]['photos'][] = $photo['photo'];
                        }
                    }
                }
            }
            $detail['avg_score'] = round($detail['score']/$detail['comments'],2);
            $this->pagedata['scores'] = $detail['score'];
            $this->pagedata['shop_id'] = $shop_id;
            $this->pagedata['items'] = $items;
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'comment/index.html';
        }
    }
}
