<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/13
 * Time: 10:44
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}
class Ctl_Group_Stafflevel extends Ctl {

    public function items($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page =  max((int)$page,1);
        $pager['limit'] = $limit = 50;
        $filter = array();
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = 'LIKE:%'.$SO['title'].'%';
            }

            //4.0模糊查询
            if($SO['keywords']){
               $filter['title'] = 'LIKE:%'.$SO['keywords'].'%';
            }
        }

        if($items = K::M('staff/level')->items($filter,array('level_id'=>"DESC"),$page,$limit,$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('group/stafflevel:items', array('{page}')), array('SO' => $SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:group/stafflevel/items.html";
    }

    public function create(){
        if($data = $this->checksubmit('data')){

          if(!$title = $data['title']){
              $this->msgbox->add('请填写骑手等级标题',201);
          }else if(!in_array($data['config_waimai']['type'],array(1,2,3))){
              $this->msgbox->add('非法数据提交',202);
          }else if(!in_array($data['config_paotui']['type'],array(1,2,3))){
              $this->msgbox->add('非法的数据提交',203);
          }else if($data['config_waimai']['type']==1&&!(int)$data['config_waimai']['fixed']){
              $this->msgbox->add('请填写每单固定金额',204);
          }else if($data['config_waimai']['type']==2&&($data['config_waimai']['bl']<0||!(int)$data['config_waimai']['bl'])){
              $this->msgbox->add('请填写订单配送费提成比例',205);
          }else if($data['config_waimai']['type']==3&&(!$data['config_waimai']['base']||!$data['config_waimai']['step']||!$data['config_waimai']['amplitude']||!$data['config_waimai']['max'])){
              $this->msgbox->add('请填写正确的每单基础配送费信息',206);
          }else if($data['config_paotui']['type']==1&&!(int)$data['config_paotui']['fixed']){
              $this->msgbox->add('请填写每单固定金额',204);
          }else if($data['config_paotui']['type']==2&&($data['config_paotui']['bl']<0||!(int)$data['config_paotui']['bl'])){
              $this->msgbox->add('请填写订单配送费提成比例',205);
          }else if($data['config_paotui']['type']==3&&(!$data['config_paotui']['base']||!$data['config_paotui']['step']||!$data['config_paotui']['amplitude']||!$data['config_paotui']['max'])){
              $this->msgbox->add('请填写正确的每单基础配送费信息',206);
          }else{
              $insert_data = array();
              if($data['stime']&&$data['ltime']){
                  if(!in_array($data['config_waimai_time']['type'],array(1,2,3))){
                      $this->msgbox->add('非法数据提交',207)->response();;
                  }else if(!in_array($data['config_paotui_time']['type'],array(1,2,3))){
                      $this->msgbox->add('非法的数据提交',208)->response();
                  }else if($data['config_waimai_time']['type']==1&&!(int)$data['config_waimai_time']['fixed']){
                      $this->msgbox->add('请填写每单固定金额',209)->response();
                  }else if($data['config_waimai_time']['type']==2&&($data['config_waimai_time']['bl']<0||!(int)$data['config_waimai_time']['bl'])){
                      $this->msgbox->add('请填写订单配送费提成比例',210)->response();
                  }else if($data['config_waimai_time']['type']==3&&(!$data['config_waimai_time']['base']||!$data['config_waimai_time']['step']||!$data['config_waimai_time']['amplitude']||!$data['config_waimai_time']['max'])){
                      $this->msgbox->add('请填写正确的每单基础配送费信息',211)->response();
                  }else if($data['config_paotui_time']['type']==1&&!(int)$data['config_paotui_time']['fixed']){
                      $this->msgbox->add('请填写每单固定金额',212)->response();
                  }else if($data['config_paotui_time']['type']==2&&($data['config_paotui_time']['bl']<0||!(int)$data['config_paotui_time']['bl'])){
                      $this->msgbox->add('请填写订单配送费提成比例',213)->response();
                  }else if($data['config_paotui_time']['type']==3&&(!$data['config_paotui_time']['base']||!$data['config_paotui_time']['step']||!$data['config_paotui_time']['amplitude']||!$data['config_paotui_time']['max'])){
                      $this->msgbox->add('请填写正确的每单基础配送费信息',214)->response();
                  }
                  $insert_data['config_waimai_time'] = serialize($data['config_waimai_time']);
                  $insert_data['config_paotui_time'] = serialize($data['config_paotui_time']);
                  $insert_data['stime'] = $data['stime'];
                  $insert_data['ltime'] = $data['ltime'];
              }
              $insert_data['title'] = $title;
              $insert_data['config_waimai'] = serialize($data['config_waimai']);
              $insert_data['config_paotui'] = serialize($data['config_paotui']);
              if(K::M('staff/level')->create($insert_data)){
                  $this->msgbox->add('创建成功');
              }else{
                  $this->msgbox->add('创建失败',207);
              }
              $this->msgbox->set_data('forward', '?group/stafflevel-items.html');
          }

        }else{
            $this->tmpl = "admin:group/stafflevel/create.html";
        }
    }


    public function so(){
        $this->tmpl = "admin:group/stafflevel/so.html";
    }

    public function edit($level_id){
        if(!$level_id){
            $this->msgbox->add('未指定需要修改的等级',201);
        }else if(!$staff_level = K::M('staff/level')->detail($level_id)){
            $this->msgbox->add('指定修改的配送员等级不存在',202);
        }else if($data = $this->checksubmit('data')){
            if(!$title = $data['title']){
                $this->msgbox->add('请填写骑手等级标题',201);
            }else if(!in_array($data['config_waimai']['type'],array(1,2,3))){
                $this->msgbox->add('非法数据提交',202);
            }else if(!in_array($data['config_paotui']['type'],array(1,2,3))){
                $this->msgbox->add('非法的数据提交',203);
            }else if($data['config_waimai']['type']==1&&!(int)$data['config_waimai']['fixed']){
                $this->msgbox->add('请填写每单固定金额',204);
            }else if($data['config_waimai']['type']==2&&($data['config_waimai']['bl']<0||!(int)$data['config_waimai']['bl'])){
                $this->msgbox->add('请填写订单配送费提成比例',205);
            }else if($data['config_waimai']['type']==3&&(!$data['config_waimai']['base']||!$data['config_waimai']['step']||!$data['config_waimai']['amplitude']||!$data['config_waimai']['max'])){
                $this->msgbox->add('请填写正确的每单基础配送费信息',206);
            }else if($data['config_paotui']['type']==1&&!(int)$data['config_paotui']['fixed']){
                $this->msgbox->add('请填写每单固定金额',204);
            }else if($data['config_paotui']['type']==2&&($data['config_paotui']['bl']<0||!(int)$data['config_paotui']['bl'])){
                $this->msgbox->add('请填写订单配送费提成比例',205);
            }else if($data['config_paotui']['type']==3&&(!$data['config_paotui']['base']||!$data['config_paotui']['step']||!$data['config_paotui']['amplitude']||!$data['config_paotui']['max'])){
                $this->msgbox->add('请填写正确的每单基础配送费信息',206);
            }else{
                $insert_data = array();
                if($data['stime']&&$data['ltime']){
                    if(!in_array($data['config_waimai_time']['type'],array(1,2,3))){
                        $this->msgbox->add('非法数据提交',207)->response();;
                    }else if(!in_array($data['config_paotui_time']['type'],array(1,2,3))){
                        $this->msgbox->add('非法的数据提交',208)->response();
                    }else if($data['config_waimai_time']['type']==1&&!(int)$data['config_waimai_time']['fixed']){
                        $this->msgbox->add('请填写每单固定金额',209)->response();
                    }else if($data['config_waimai_time']['type']==2&&($data['config_waimai_time']['bl']<0||!(int)$data['config_waimai_time']['bl'])){
                        $this->msgbox->add('请填写订单配送费提成比例',210)->response();
                    }else if($data['config_waimai_time']['type']==3&&(!$data['config_waimai_time']['base']||!$data['config_waimai_time']['step']||!$data['config_waimai_time']['amplitude']||!$data['config_waimai_time']['max'])){
                        $this->msgbox->add('请填写正确的每单基础配送费信息',211)->response();
                    }else if($data['config_paotui_time']['type']==1&&!(int)$data['config_paotui_time']['fixed']){
                        $this->msgbox->add('请填写每单固定金额',212)->response();
                    }else if($data['config_paotui_time']['type']==2&&($data['config_paotui_time']['bl']<0||!(int)$data['config_paotui_time']['bl'])){
                        $this->msgbox->add('请填写订单配送费提成比例',213)->response();
                    }else if($data['config_paotui_time']['type']==3&&(!$data['config_paotui_time']['base']||!$data['config_paotui_time']['step']||!$data['config_paotui_time']['amplitude']||!$data['config_paotui_time']['max'])){
                        $this->msgbox->add('请填写正确的每单基础配送费信息',214)->response();
                    }
                    $insert_data['config_waimai_time'] = serialize($data['config_waimai_time']);
                    $insert_data['config_paotui_time'] = serialize($data['config_paotui_time']);
                    $insert_data['stime'] = $data['stime'];
                    $insert_data['ltime'] = $data['ltime'];
                }else{
                    $insert_data['stime'] = "";
                    $insert_data['ltime'] = "";
                    $insert_data['config_waimai_time'] = "";
                    $insert_data['config_paotui_time'] = "";

                }


                $insert_data['title'] = $title;
                $insert_data['config_waimai'] = serialize($data['config_waimai']);
                $insert_data['config_paotui'] = serialize($data['config_paotui']);
                if(K::M('staff/level')->update($level_id,$insert_data)){
                    $this->msgbox->add('更新成功');
                }else{
                    $this->msgbox->add('更新失败',207);
                }
                $this->msgbox->set_data('forward', '?group/stafflevel-items.html');
            }

        }else{

            $this->pagedata['detail'] = $staff_level;
            $this->tmpl = "admin:group/stafflevel/edit.html";
        }

    }

    public function delete($level_id){
        if($level_id){
         // $count = K::M('staff/staff')->count(array('level_id'=>$level_id));
           if(!$level = K::M('staff/level')->detail($level_id)){
               $this->msgbox->add('需要删除的等级不存在',201);
           }else if(($count = K::M('staff/staff')->count(array('level_id'=>$level_id)))>0){
               $this->msgbox->add('当前等级有绑定的配送员,不可删除',202);
           }else if(K::M('staff/level')->delete($level_id)){
                $this->msgbox->add('删除成功');
           }else{
               $this->msgbox->add('删除失败',203);
           }

        }else if($level_ids = $this->GP('level_id')){
            foreach ($level_ids as $k=>$v){
                if(!$staff_level = K::M('staff/level')->detail($v)){
                    $this->msgbox->add('需要删除的等级不存在',204)->response();
                }else if(($count = K::M('staff/staff')->count(array('level_id'=>$v))>0)){
                    $this->msgbox->add('所选等级有绑定配送员，不可删除',205)->response();
                }else if(!K::M('staff/level')->delete($v)){
                    $this->msgbox->add('删除失败',206)->response();
                }
            }
            $this->msgbox->add('删除成功');
        }else{
            $this->msgbox->add('未指定需要删除的骑手等级',201);
        }


    }





}