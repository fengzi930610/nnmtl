<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Comment_Index extends Ctl
{
    public function index($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;

        $filter['closed'] = 0;
        $filter['shop_id'] = $this->shop_id;
        $orderby = array('comment_id'=>'DESC');
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if (isset($SO['satisfy'])) {
                switch ($SO['satisfy']) {// 满意程度
                    case '1':
                        //$filter['score_avg'] = "3.01~5";// 满意
                        $filter['score'] = "3.01~5";// 满意
                        break;
                    case '2':
                        //$filter['score_avg'] = "0~2.99";// 不满意
                        $filter['score'] = "0~2.99";// 不满意
                        break;
                    case '3':
                        $filter['score'] = "3";// 一般
                        //$filter['score_avg'] = "3";// 一般
                        break;

                }
            }
            if (isset($SO['content']) && $SO['content'] == 1) {// 有内容
                $filter[':SQL'] = "`content` <> ''";
            }
           
        }
        if($items = K::M('waimai/comment')->items($filter, $orderby, $page, $limit, $count)){
            $uids = $product_list = $comment_ids = array();
            foreach ($items as $k => $v) {
                $uids[$v['uid']] = $v['uid'];
                $items[$k]['score'] *= 20;
                $items[$k]['score_peisong'] *= 20;
                $comment_ids[$v['comment_id']] = $v['comment_id'];
            }
            if (!$users = K::M('member/member')->items_by_ids($uids)) {
                $users = array();
            }
            $photos = K::M('waimai/commentphoto')->items(array('comment_id'=>$comment_ids));
            foreach ($items as $k => $v) {
                foreach($photos as $k1=>$v1){
                    if($v['comment_id'] == $v1['comment_id']){
                        $items[$k]['photos'][] = $v1; 
                    }
                }
                $items[$k]['users'] = $users[$v['uid']] ? $users[$v['uid']] : array();
            }
        }
        $pager['count'] = $count;
        $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('comment/index:index', array('{page}')), array('SO'=>$SO));
        //print_r($this->system->db->SQLLOG());die;
        //print_r($items);die;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'comment/index.html';
    }

    // 商家评价回复
    public function reply()
    {
        if (!$comment_id = (int)$this->GP('comment_id')) {
            $this->msgbox->add(L('未指定要回复的内容ID!'), 211);
        }elseif (!$comment = K::M('waimai/comment')->detail($comment_id)) {
            $this->msgbox->add(L('该评论不存在或已被删除！'), 212);
        }elseif ($comment['shop_id'] != $this->shop_id) {
            $this->msgbox->add(L('请勿越权操作！'), 213);
        }elseif (!$reply = htmlspecialchars($this->GP('reply'))) {
            $this->msgbox->add(L('回复内容不能为空'), 214);
        }elseif (K::M('waimai/comment')->update($comment_id, array('reply'=>$reply, 'reply_ip'=>__IP, 'reply_time'=>__TIME))) {
            $log_data = array();
            $log_data['uid'] = $comment['uid'];
            $log_data['title'] = '商家回复评论';
            $log_data['content'] = "商家回复评论:".$reply;
            $log_data['type'] = 2;
            $log_data['is_read'] = 0;
            $log_data['can_id'] = 0;
            K::M('member/message')->create($log_data);

            $this->msgbox->add(L('回复成功'));
        }else{
            $this->msgbox->add(L('回复失败'), 215);
        }
    }
    
}