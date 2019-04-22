<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: msgbox.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Magic_Msgbox 
{
    
    public $error = 0;
    protected $_message = array();
    protected $_data = array();
    protected $_js = array();
    protected static $system = null;
    protected $tmpl = 'page/notice.html';
    public function __construct(&$system)
    {
        self::$system = &$system;
    }
    
    public function template($tmpl)
    {
        $this->tmpl = $tmpl;
    }
    public function add($msg,$error=0)
    {
        $this->error = $error;
        $this->_message[] = $msg;
        return $this;
    }
    public function last()
    {
        $last = end($this->_message);
        return $last;
    }
    public function set_data($k,$v=null)
    {
        if(is_array($k)){
            $this->_data = array_merge((array)$this->_data, (array)$k);
        }else{
            $this->_data[$k] = $v;
        }
    }
    public function get_data()
    {
        return $this->_data;
    }
    public function set($k,$v=null)
    {
        $this->set_data($k, $v);
    }
    public function set_js($js)
    {
        if(strpos($js, '</script>')===false){
            $js = '<script type="text/javascript">'.$js.'</script>';
        }
        $this->_js[] =$js;
    }
    public function forward($url)
    {
        $this->_data['forward'] = $url;
    }
    public function load($url, $title='', $timer=2)
    {
        $pager = array('link'=>$url, 'title'=>$title, 'timer'=>$timer);
        $objctl = &K::$system->objctl;
        $objctl->pagedata['pager'] = $pager;
        $objctl->pagedata['_OO_'] = 'admin:page/load.html';
        $objctl->output(); 
    }
    public function clean()
    {
        $this->_message = array();
        $this->error = 0;
    }
    public function show($url='',$t='HTML', $timer=3)
    {
        $t = strtoupper($t);
        $pager = $this->_data;
        if($this->_js){
            $pager['appendjs'] = implode('', $this->_js);
        }
        $pager['error'] = $this->error;
        $pager['message'] = $this->_message;
        if('HTML' == $t){           
            if(is_array($url)){
                foreach($url as $k=>$v){
                    $url_list[$k]['title'] = $v[0]; 
                    $url_list[$k]['link'] = $v[1]; 
                }
                $pager['link'] = $url[0][1];
                $pager['url_list'] = $url_list;
            }else{
                $pager['link'] = $url;
            }
            $pager['res'] = __CFG::RES_URL;
            $attach = K::$system->config->get('attach');
            $pager['img'] = $attach['attachurl'];
            $pager['timer'] = (int)$timer; //3秒跳转
            $pager['message'] = implode('<br />',$pager['message']);
            $objctl = &K::$system->objctl;
            if(!is_object($objctl)){
                Import::C(__APP__.':index');
                $objctl = new Ctl_Index(K::$system);
            }
            $objctl->pagedata['pager'] = $pager;
            $objctl->tmpl = $this->tmpl;
            $objctl->output();
        }else if('JSON' == $t){
            header("Content-type: text/plain; charset=UTF-8");
            if($pager['message']){
                $pager['message'] = implode(",",$pager['message']);
            }else{
                $pager['message'] = 'success';
            }
            $this->outputdata =  K::M('utility/json')->encode($pager);
            echo $this->outputdata;
        }else if('JSONP' == $t){
            header("Content-Type:text/plain; charset=UTF-8");            
            echo $url.'('.K::M('utility/json')->encode($pager).');';
        }else if('XML' == $t){
            header("Content-Type:text/xml; charset=UTF-8");
            echo K::M('utility/xml')->xml($pager);
        }else if('JS'){
            $pager['message'] = implode("\n",$pager['message']);
            $output = &K::M('system/frontend');
            $output->assign('pager', $pager);
            $output->display($this->tmpl);
        }
        exit();
    }
    public function alert($url)
    {
        if(defined('IN_ADMIN')){
            $output->display('admin:page/alert.html');
        }else{
            $output->display('view:page/alert.html');
        }       
        $this->show($url,'JS');
    }
    public function jsonp($data=array())
    {
        if(!empty($data)){
            $this->set_data($data);
        }
        if($callback = trim($_GET['jsonpcallback'])){
            if(!preg_match("/^(\w+)$/i",$callback)){
                $callback = 'jsonpcallback';
            }
        }else{
            $callback = 'jsonpcallback';
        }
        $this->show($callback, 'JSONP');
    }
    public function json($data=array())
    {
        if(!empty($data)){
            $this->set_data($data);
        }
        $this->show('', 'JSON');
    }
    public function response($url='', $timer=3)
    {
        $request = K::$system->request;
        $objctl = &K::$system->objctl;
        if(!$tmpl = $objctl->tmpl){
            $tmpl = $objctl->pagedata['_OO_'];
        }
        if($request['XREQ'] || defined('IN_APP')){
            if($tmpl){
                $this->_data['html'] = $objctl->output(false);
            }else if($request['MINI'] === 'load'){
                $this->miniload($url, $timer);
            }
            $this->show('', 'JSON');
        }else if($request['MINI'] === 'iframe'){
            $this->miniframe($url, $timer);
        }else if($tmpl){
            $objctl->output();
        }else if($url){
            $this->show($url, 'HTML');
        }else if($forward = $this->_data['forward']){
            $this->show($forward, 'HTML', $timer);
        }else if($forward = $request['forward']){
            $this->show($forward, 'HTML', $timer);
        }else{
            $this->show(K::M('helper/link')->mklink('index'), 'HTML', $timer);
        }
    }
    public function miniload($url='', $timer=3)
    {
        $pager = $this->_data;
        if($this->_js){
            $pager['appendjs'] = implode('', $this->_js);
        }       
        $pager['error'] = $this->error;
        $pager['message'] = $this->_message;
        if(is_array($url)){
            foreach($url as $k=>$v){
                $url_list[$k]['title'] = $v[0]; 
                $url_list[$k]['link'] = $v[1]; 
            }
            $pager['link'] = $url[0][1];
            $pager['url_list'] = $url_list;
        }else if($url){
            $pager['link'] = $url;
        }else if($pager['forward']){
            $pager['link'] = $pager['forward'];
        }
        $pager['timer'] = $timer;
        $pager['message'] = implode(",",$pager['message']);
        $output = K::M('system/frontend');
        $output->assign('pager', $pager);
        if(defined('IN_ADMIN')){
            $output->display('admin:page/miniload.html');
        }else{
            $output->display('view:page/miniload.html');
        }
        exit();     
    }
    public function miniframe($url='', $timer=3)
    {
        $pager = $this->_data;
        if($this->_js){
            $pager['appendjs'] = implode('', $this->_js);
        }       
        $pager['error'] = $this->error;
        $pager['message'] = $this->_message;
        if(is_array($url)){
            foreach($url as $k=>$v){
                $url_list[$k]['title'] = $v[0]; 
                $url_list[$k]['link'] = $v[1]; 
            }
            $pager['link'] = $url[0][1];
            $pager['url_list'] = $url_list;
        }else if($url){
            $pager['link'] = $url;
        }else if($pager['forward']){
            $pager['link'] = $pager['forward'];
        }
        $pager['timer'] = $timer;
        $pager['message'] = implode(",",$pager['message']);
        $output = K::M('system/frontend');
        $output->assign('pager', $pager);
        if(defined('IN_ADMIN')){
            $output->display('admin:page/miniframe.html');
        }else{
            $output->display('view:page/miniframe.html');
        }
        exit();
    }
    public function redirect($url, $code=null)
    {
        if(is_numeric($code)){
            K::$system->response_code($code);
        }
        header("location:{$url}");
        exit();
    }
}