<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: face.mdl.php 9590 2015-04-09 06:28:37Z maoge $
 */
Import::M('member/member');
class Mdl_Member_Face extends Mdl_Member_Member
{
    
    /**
     * 更新会员头像
     * @param   $uid    用户UID
     * @param   $face   头像大图file文件路径
     * @param   $data   头像大图数据流{$face,$data,两选一即可}
     */
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
            /*K::M('io/dir')->create(dirname($D.$face));
            if(!file_put_contents($D.$face,$data)){
                $this->msgbox->add('保存图片数据失败',501);
                return false;
            }*/
            $dir = $cfg['attachdir'].'face'.DIRECTORY_SEPARATOR.$b.DIRECTORY_SEPARATOR;
            $fname = "{$a}.jpg";
            if(!K::M('storage/upload')->upload_by_data($data, $dir, $fname)){
                $this->msgbox->add('保存图片数据失败',501);
                return false;
            }
        }else if($file != $D.$face){
            if(!K::M('image/gd')->thumb($file, $D.$face,180,180,true)){
                $this->msgbox->add('图片处理失败',502);
                return false;
            }
        }
        $a = array('face'=>$face);
        $this->db->update($this->_table, $a, "uid='$uid'");
        //刷新用户缓存
        return true;
    }
}