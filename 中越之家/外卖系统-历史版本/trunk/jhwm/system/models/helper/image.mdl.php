<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: gd.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Helper_Image
{
    public $params = array();
    public function __construct(&$system)
    {
        ini_set('memory_limit', '-1');
        $this->params = $system->config->get('attach');
    }
    public function thumbnail($im,$file,$w=200, $h=1000)
    {
        $im->thumbnailImage($w, $h, true,false);
        $im->writeImage($file);
        return true;
    }
    

    public function thumb($source, $target, $w=200, $h=null, $method=false)
    {
        $success = false;
        if(!$info = $this->info($source)){
            return false;
        } 
        $w = (int)$w;
        if($im = $this->resource($source, $info['mime'])){
            if(!is_numeric($h)){
                $h = $info['width'] / $info['height'] * $w;
            }
            $size = $this->sizevalue($info['width'], $info['height'], $w, $h, $method);
            K::M('io/dir')->create(dirname($target));
            if($info['width'] >= $w || $info['height'] >= $h) {
                $thumb = array();
                list(,,$thumb['width'], $thumb['height']) = $size;
                $cx = $info['width'];
                $cy = $info['height'];
                $dst_im = imagecreatetruecolor($thumb['width'], $thumb['height']);
                imagecopyresampled($dst_im, $im, 0, 0, 0, 0, $thumb['width'], $thumb['height'], $cx, $cy);
                K::M('io/dir')->create(dirname($target));
                if($info['mime'] == 'image/jpeg' || preg_match('/\.(jpg|jpeg)$/i', $target)) {
                    @imagejpeg($dst_im, $target, $this->params['thumbquality']);
                } else {
                    $this->save($dst_im, $target, $info['mime']);
                }                
                @imagedestroy($im);
                @imagedestroy($dst_im);
                $success = true;
            }else{
                K::M('io/file')->copy($source, $target);
                $success = true;            
            }
        }
        return $success;
    }
    public function thumbs($source, $thumbs=array(), $method=false)
    {   
        if(!$info = $this->info($source)){
            return false;
        }
        if($im = $this->resource($source, $info['mime'])){
            foreach($thumbs as $k=>$target){
                $w = $h = 0;
                if(preg_match("/(\d+)X(\d+)/i",$k,$match)){
                    $method = true;
                    $w = $match[1]; $h = $match[2];
                }else if($w = intval($k)){
                    $h = $info['height'] * $w / $info['width'];
                }else{
                    continue;
                }
                ///
                unset($thumb_im);
                if($method){
                    /*if($info['width'] < $w && $info['height'] < $h){
                        $thumb_im = imagecreatetruecolor($w, $h);
                        $bgcolor = imagecolorallocate($thumb_im, 255, 255, 255);
                        imagefill($thumb_im, 0, 0, $bgcolor);
                        $x = ($w - $info['width'])/2;
                        $y = ($h - $info['height'])/2;
                        imagecopyresampled($thumb_im, $im, $x, $y, 0, 0, $info['width'], $info['height'],  $info['width'], $info['height']);                                                
                    }else */
                    if(!($info['width'] <= $w || $info['height'] <= $h)) {
                        list($startx, $starty, $cutw, $cuth) = $this->sizevalue($info['width'], $info['height'], $w, $h, true);
                        $dst_im = imagecreatetruecolor($cutw, $cuth);
                        imagecopymerge($dst_im, $im, 0, 0, $startx, $starty, $cutw, $cuth, 100);
                        $thumb_im = imagecreatetruecolor($w, $h);
                        imagefilledrectangle($thumb_im, 0, 0, $w, $h, imagecolorallocate($thumb_im, 255, 255, 255));
                        imagecopyresampled($thumb_im, $dst_im ,0, 0, 0, 0, $w, $h, $cutw, $cuth);
                        @imagedestroy($dst_im);
                    } else {
                        $thumb_im = imagecreatetruecolor($w, $h);
                        imagefilledrectangle($thumb_im, 0, 0, $w, $h, imagecolorallocate($thumb_im, 255, 255, 255));
                        if($info['width']/$w > $info['height']/$h){
                            $startx = 0;
                        }
                        $startx = (int)($w - $info['width']) / 2;
                        $starty = (int)($h - $info['height']) / 2;                      
                        //imagecopymerge($thumb_im, $im, $startx, $starty, 0, 0, $info['width'], $info['height'], 100);
                        imagecopyresampled($thumb_im, $im, $startx, $starty, 0, 0, $info['width'], $info['height'], $info['width'], $info['height']);
                    }
                }else{
                    if($info['width'] >= $w || $info['height'] >= $h) {
                        $thumb = array();
                        list(,,$thumb['width'], $thumb['height']) = $this->sizevalue($info['width'], $info['height'], $w, $h, false);
                        $cx = $info['width'];
                        $cy = $info['height'];
                        $thumb_im = imagecreatetruecolor($thumb['width'], $thumb['height']);
                        imagefilledrectangle($thumb_im, 0, 0, $w, $h, imagecolorallocate($thumb_im, 255, 255, 255));
                        imagecopyresampled($thumb_im, $im ,0, 0, 0, 0, $thumb['width'], $thumb['height'], $cx, $cy);
                    }else{
                        $thumb_im = $this->resource($source, $info['mime']);
                        //K::M('io/file')->copy($source, $target);
                    }
                }
                //clearstatcache();
                if($thumb_im){
                    K::M('io/dir')->create(dirname($target));
                    if($info['mime'] == 'image/jpeg' || preg_match('/\.(jpg|jpeg)$/i', $target)) {
                        @imagejpeg($thumb_im, $target, $this->params['thumbquality']);
                    } else {
                        $this->save($thumb_im, $target, $info['mime']);
                    }
                    @imagedestroy($thumb_im);
                }
            }
            @imagedestroy($im);
            return true;
        }
        return false;
    }
    public function sizevalue($iw, $ih, $tw, $th, $method=true)
    {
        $x = $y = $w = $h = 0;
        if($method) {
            $imgratio = $iw / $ih;
            $thumbratio = $tw / $th;
            /*
            if($iw >= $tw && $ih >= $th){
                if($iw/$tw > $ih/$th){
                    $x = 0;
                    $w = $iw;
                    $h = $iw/$tw * $th;
                    $y = ($ih - $h) /2;
                }else{
                    $y = 0;
                    $h = $ih;
                    $w = $ih/$th * $tw;
                    $x = ($iw - $w)/2;
                }
            }else */
            if($imgratio >= 1 && $imgratio >= $thumbratio || $imgratio < 1 && $imgratio > $thumbratio) {
                $h = $ih;
                $w = $h * $thumbratio;
                $x = ($iw - $thumbratio * $ih) / 2;
            } elseif($imgratio >= 1 && $imgratio <= $thumbratio || $imgratio < 1 && $imgratio <= $thumbratio) {
                $w = $iw;
                $h = $w / $thumbratio;
            }
        } else {
            $x_ratio = $tw / $iw;
            $y_ratio = $th / $ih;
            if(($x_ratio * $ih) < $th) {
                $h = ceil($x_ratio * $ih);
                $w = $tw;
            } else {
                $w = ceil($y_ratio * $iw);
                $h = $th;
            }
        }
        return array($x, $y, $w, $h);
    }
    public function info($source)
    {
        $imginfo = @getimagesize($source);
        if($imginfo === FALSE) {
            return false;
        }
        $info = array();
        $info['source'] = $source;
        $info['width'] = $imginfo[0];
        $info['height'] = $imginfo[1];
        $info['mime'] = $imginfo['mime'];
        $info['size'] = @filesize($source);
        return $info; 
    }
    public function resource($source, $mime='jpg')
    {
        switch (strtolower($mime)) {
            case 1: case 'image/gif': case 'gif': $res = imagecreatefromgif($source);
                break;
            case 2: case 'image/pjpeg': case 'image/jpeg': case 'jpg': $res = imagecreatefromjpeg($source);
                break;
            case 3: case 'image/x-png': case 'image/png': case 'png': $res = imagecreatefrompng($source);
                break;
            case 6: case 'image/bmp': case 'bmp': $res = imagecreatefromwbmp($source);
                break;
            default:
                return false;
        }
        return $res;
    }
    public function loadsource($source, $mime='jpg')
    {
        if(!$im = $this->resource($source, $mime)){
            if(!function_exists('imagecreatefromstring')) {
                return false;
            }
            $fp = @fopen($source, 'rb');
            $contents = @fread($fp, filesize($source));
            fclose($fp);
            $im = @imagecreatefromstring($contents);        
        }
        return $im;
    }
    public function save($res, $target, $mime='jpg')
    {
        switch(strtolower($mime)) {
            case '2': case 'jpg': case 'jpeg': case 'image/jpeg': case 'image/pjpeg':
                $fun = 'jpeg'; break;
            case '3': case 'png': case 'image/png':
                $fun = 'png'; break;
            case '1': case 'gif': case 'image/gif':
                $fun = 'gif'; break;
            case 'bmp': case 'wbmp': case 'image/bmp':
                $fun = 'wbmp'; break;
            default:
                $fun = 'jpeg'; break;
        }
        $ImageFun='Image'.$fun;
        $ImageFun($res, $target);
    }
    public function watermark($source, $name='')
    {
        if(!$info = $this->info($source)){
            return false;
        }
        if($this->params['watermarktype'] == 'png'){
            if(!function_exists('imagecopy') || !function_exists('imagecreatefrompng') || !function_exists('imagealphablending') || !function_exists('imagecopymerge')) {
                return -4;
            }
            $watermarkfile = __CFG::DIR.'data/watermark/watermark.png';
            $watermarkinfo = @getimagesize($watermarkfile);
            if($watermarkinfo === FALSE) {
                return -3;
            }
            if(!$watermark_logo = @imageCreateFromPNG($watermarkfile)) {
                return 0;
            }
            list($logo_w, $logo_h) = $watermarkinfo;            
        }else if($this->params['watermarktype'] == 'text'){
            if(!function_exists('imagettfbbox') || !function_exists('imagettftext') || !function_exists('imagecolorallocate')) {
                return -4;
            }
            
            $font_file = __CFG::DIR.'data/watermark/'.$this->params['watermarktext']['font'];
            $font_size = $this->params['watermarktext']['size'] ? $this->params['watermarktext']['size'] : '14';
            if(!file_exists($font_file)){
                return -5;
            }
            
            $font_text = str_replace(array("\n", "\r", "'"), array('', '', '\''), $this->params['watermarktext']['text']);
            $font_text = str_replace('{name}', $name, $font_text);
            //$font_text = pack("H*", $text);
            $box = imagettfbbox($font_size, 0, $font_file, $font_text);
            $logo_h = max($box[1], $box[3]) - min($box[5], $box[7]);
            $logo_w = max($box[2], $box[4]) - min($box[0], $box[6]);
            $ax = min($box[0], $box[6]) * -1;
            $ay = min($box[5], $box[7]) * -1;           
        }else{
            return -6;
        }
        $wmwidth = $info['width'] - $logo_w;
        $wmheight = $info['height'] - $logo_h;
        if($wmwidth > 10 && $wmheight > 10) {
            switch($this->params['watermarkstatus']) {
                case 1:
                    $x = 5;
                    $y = 5;
                    break;
                case 2:
                    $x = ($info['width'] - $logo_w) / 2;
                    $y = 5;
                    break;
                case 3:
                    $x = $info['width'] - $logo_w - 5;
                    $y = 5;
                    break;
                case 4:
                    $x = 5;
                    $y = ($info['height'] - $logo_h) / 2;
                    break;
                case 5:
                    $x = ($info['width'] - $logo_w) / 2;
                    $y = ($info['height'] - $logo_h) / 2;
                    break;
                case 6:
                    $x = $info['width'] - $logo_w;
                    $y = ($info['height'] - $logo_h) / 2;
                    break;
                case 7:
                    $x = 5;
                    $y = $info['height'] - $logo_h - 5;
                    break;
                case 8:
                    $x = ($info['width'] - $logo_w) / 2;
                    $y = $info['height'] - $logo_h - 5;
                    break;
                case 9: default:
                    $x = $info['width'] - $logo_w - 5;
                    $y = $info['height'] - $logo_h - 5;
                    break;
            }
            if(!$dst_photo = $this->loadsource($source, $info['mime'])){
                return false;
            }
            imagealphablending($dst_photo, true);
            imagesavealpha($dst_photo, true);
            if($info['mime'] != 'image/png') {
                $color_photo = imagecreatetruecolor($info['width'], $info['height']);
                imageCopy($color_photo, $dst_photo, 0, 0, 0, 0, $info['width'], $info['height']);
                $dst_photo = $color_photo;
            }
            if($this->params['watermarktype'] == 'png') {
                imageCopy($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h);
            }else if($this->params['watermarktype'] == 'text') {
                $colorrgb = sscanf($this->params['watermarktext']['color'], '#%2x%2x%2x');
                $color = imagecolorallocate($dst_photo, $colorrgb[0], $colorrgb[1], $colorrgb[2]);
                @imagettftext($dst_photo, $font_size, 0, $x + $ax, $y + $ay, $color, $font_file, $font_text);
            } else {
                imageAlphaBlending($watermark_logo, true);
                imageCopyMerge($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h, $this->params['watermarktrans']);
            }
            clearstatcache();
            if($info['mime'] == 'image/jpeg') {
                $watermarkquality = $this->params['watermarkquality'] ? (int)$this->params['watermarkquality'] : 90;
                @Imagejpeg($dst_photo, $source, $watermarkquality);
            } else {
                @$this->save($dst_photo, $source, $info['mime']);
            }
        }
        return 1;
    }
}