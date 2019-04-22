<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: file.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Dataio_File
{
    public function download($fname='data', $data=null, $mimeType='application/force-download')
    {
        $file = $fname;
        if(headers_sent($file, $line)){
            echo 'Header already sent @ '.$file.':'.$line;
            exit();
        }
        //header('Pragma: no-cache');
        //header("Expires: Wed, 26 Feb 1997 08:21:57 GMT"); 
        header('cache-control:must-revalidate'); 
        if(strpos($_SERVER["HTTP_USER_AGENT"],'MSIE')){
            $fname = urlencode($fname);
            header('Content-type: '.$mimeType);
            //header("Content-type: application/octet-stream");
            header('cache-control:must-revalidate');
            Header("Content-Disposition: inline; filename=\"".$fname.'"');
            header("Pragma:public");            
        }else{
            header('Content-type: '.$mimeType.';charset=utf-8');
            header("Content-Disposition: attachment; filename=\"".$fname.'"');
        }
        
        if($data){
            header('Content-Length: '.strlen($data));
            echo $data;
            exit();
        }
    }
}