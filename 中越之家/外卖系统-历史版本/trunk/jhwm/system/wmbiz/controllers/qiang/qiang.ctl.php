<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}

class Ctl_Qiang_Qiang extends Ctl {

    public function index($page = 1) {
        $filter = $pager = array();
        $filter['closed'] = 0;
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 20;
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if(in_array($SO['type'], array('goods','ticket'))){
                $filter['type'] = $SO['type'];
            }
            if ($SO['title']) {
                $filter['title'] = "LIKE:%" . $SO['title'] . "%";
            }
            if (is_array($SO['dateline'])) {
                if ($SO['dateline'][0] && $SO['dateline'][1]) {
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }
        }
        $filter['shop_id'] = $this->shop_id;
        if ($items = K::M('qiang/qiang')->items($filter, array('qiang_id'=>'desc'), $page, $limit, $count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("qiang/qiang/index", array('{page}')), array('SO' => $SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'qiang/qiang/index.html';
    }


    public function detail($qiang_id = null)
    {
        if (!$qiang_id = (int) $qiang_id) {
            $this->msgbox->add('未指定要查看内容的ID', 211);
        } else if (!$detail = K::M('qiang/qiang')->detail($qiang_id)) {
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        } else {
            $detail['info'] = htmlspecialchars_decode($detail['info']);
            $detail['rules'] = htmlspecialchars_decode($detail['rules']);
            $this->pagedata['shop'] = K::M('waimai/waimai')->detail($detail['shop_id']);
            $this->pagedata['notes_type'] = K::M('qiang/qiang')->get_notes_label();
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'qiang/qiang/detail.html';
        }
    }


}
