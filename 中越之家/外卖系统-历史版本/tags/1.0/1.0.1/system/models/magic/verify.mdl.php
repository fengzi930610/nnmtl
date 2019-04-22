<?php
/**
 * Copy	Right Anhuike.com
 * $Id verify.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Magic_Verify
{
    function output($W = 120, $H = 35)
    {
        $S = $this->reset();
        $L = strlen($S);
        $char_X = 6;
        $char_Y = 0;
        $char_S = $W / $L;
        $font = __CFG::DIR . 'data/font/verify.ttf';
        $font_size = 20;
        $font_color = "";
        $im = imagecreatetruecolor($W, $H);
        sscanf("#FFFFFF", "#%2x%2x%2x", $R, $G, $B);
        $backColor = imagecolorallocate($im, $R, $G, $B);
        imagefill($im, 0, 0, $backColor);
        for($i = 0; $i < $L; $i++){
            $font_color = $this->rand_color($im);
            imagettftext($im, $font_size, mt_rand(-20, 20), $i * ($W / $L) + $font_size - floor($font_size / 2), floor($H / 2 + $font_size / 2), $font_color, $font, $S[$i]);
        }
        $line_X1 = $line_Y1 = $line_X2 = $line_Y2 = 0;
        for($i = 0; $i < mt_rand(0, 64); $i++){
            $line_X1 = mt_rand(0, $W);
            $line_Y1 = mt_rand(0, $H);
            $line_X2 = mt_rand(0, $W);
            $line_Y2 = mt_rand(0, $H);
            $line_X2 = $line_X1 + mt_rand(1, 8);
            $line_Y2 = $line_Y1 + mt_rand(1, 8);
            $color_Line = $this->rand_color($im);
            imageline($im, $line_X1, $line_Y1, $line_X2, $line_Y2, $color_Line);
        }
        ob_clean();
        header("Expires: -1");
        header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
        header("Pragma: no-cache");
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
        exit();
    }
    public function rand_color($im)
    {
        return imagecolorallocate($im, mt_rand(0, 230), mt_rand(0, 230), mt_rand(0, 230));
    }
    public function check($code)
    {
        $session = K::M('system/session')->start();
        if($vcode = $session->get('VIMGCODE')){
            if(strtoupper($code) == $vcode){
                return true;
            }else{
                $session->delete('VIMGCODE');
            }
        }
        return false;
    }
    public function reset()
    {
        if($GUID = K::M('system/cookie')->GUID){
            $session = K::M('system/session')->start();
            $code = K::M('content/string')->random(4, 1);
            $session->set('VIMGCODE', strtoupper($code), 900); //15分钟缓存
            return $code;
        }
        return false;
    }
}
