<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: database.ctl.php 10028 2015-05-05 12:15:23Z youyi $
 */
class Ctl_Tools_Database extends Ctl
{
    
    public function index()
    {
        if($this->checksubmit()){
        }else{
            $tables = K::M('tools/database')->tables();
            $totalsize = 0;
            foreach ($tables as $key => $table) {
                $totalsize += $table['Data_length'] + $table['Index_length'];
                $tables[$key] = $table;
            }
            $pager['totalsize'] = $totalsize;
            $this->pagedata['table_list'] = $tables;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:tools/database/index.html';
        }
    }
    public function backup()
    {
        if($this->checksubmit()){
            if(!$tables = $this->GP('tables')){
                $this->msgbox->add("您没有选择备份的表！", 201);
            }else{
                K::M('cache/mfile')->set('backuptables', $tables, 3600);
                $hash = date('Ymd_').K::M('content/string')->Random(8);
                $url = "index.php?ctl=tools/database&act=backup&hash={$hash}&table_id=0&volumn=1&startfrom=0";
                $this->msgbox->add('开始备份库数据');
                $this->msgbox->set_data('forward', $url);
                //$this->msgbox->load($url, "正在备份数据库卷#1......");
            }
        }else{
            $limit_size = 1024;
            if(!$hash = $this->GP('hash')){
                $hash = date('Ymd_').K::M('content/string')->Random(8);
            }
            $tables = K::M('cache/mfile')->get('backuptables');
            if(empty($tables)){
                $this->msgbox->add("您没有选择备份的表！", 201)->show();
            }else{
                $table_id = (int)$this->GP('table_id');
                $startfrom = (int)$this->GP('startfrom');
                $volumn = (int)$this->GP('volumn');            
                $bakupdir = __CORE_DIR."/data/backup/{$hash}";
                $bakupfile = $bakupdir."/{$hash}_{$volumn}.php";
                $jh_version = JH_VERSION.' '.JH_RELEASE;
                $db_version = $this->system->db->version();
                $tablepre = $this->system->_tablepre;
                $sqldump =  "-- <?php exit();?>\n".
                            "-- IJHCMS Multi-Volume Data Dump Vol.{$volumn}\n".
                            "-- IJHCMS Version:  {$jh_version}\n".
                            "-- MySQL  Version:  {$db_version}\n".
                            "-- Create Time   : ".date('Y-m-d H:i:s')."\n".
                            "-- Table Prefix  : $tablepre\n".
                            "-- CopyRight     : http://www.ijh.cc\n\n\n";
                $currsize = strlen($sqldump);
                $table_count = count($tables);
                for($i=$table_id; $i<$table_count; $i++){
                    $table_id = $i;
                    if($table = $tables[$i]){
                        if($startfrom === 0){
                            $sqldump .= K::M('mysql/backup')->structtable($table);
                        }
                        if($tables == $this->system->_tablepre.'session'){
                            continue;
                        }
                        $sqldump .= K::M('mysql/backup')->dumptable($table, $startfrom, $currsize, $limit_size);
                        $startfrom = $startfrom < 0 ? 0 : $startfrom;
                        $currsize = strlen($sqldump); 
                        if($currsize + 500 > $limit_size*1000){
                            if(empty($startfrom)){
                                $i++;
                                $table_id ++;
                            }
                            break;
                        }
                    }
                }
                
                K::M('io/dir')->mkdir($bakupdir);
                @$fp = fopen($bakupfile, 'wb');
                if(!$fp){
                    exit("备份目录{$bakupdir}不可写");
                }            
                @flock($fp, 2);
                if(@!fwrite($fp, $sqldump, $currsize)) {
                    @fclose($fp);
                    exit("备份数据库写入文件{$bakupfile}失败");
                }
                if($i >= $table_count){
                    $this->msgbox->add("备份数据库成功");
                    $this->msgbox->set_data('forward', 'index.php?ctl=tools/database&act=backlist');
                }else{
                    $volumn ++ ;
                    $url = "index.php?ctl=tools/database&act=backup&hash={$hash}&table_id={$table_id}&volumn={$volumn}&startfrom={$startfrom}";
                    $this->msgbox->load($url, "请不要关闭浏览器，正在备份数据库卷#{$volumn}......");
                }
            }
        }
    }
    public function backlist()
    {
        $backupdir = __CORE_DIR."/data/backup/";
        $items = array();
        if(!$handler = opendir($source)){
            return false;
        }
        while(false !== ($name = readdir($handler))){
            if(is_dir($backupdir.$name)){
                if(preg_match('/^\d{8}_\w{8}$/i', $name)){
                    $row['name'] = $name;
                    $row['time'] = filectime($backupdir.$name);
                    $fp = dir($backupdir.$name);
                    $size = $count = 0;
                    while (false !== ($volumn = $fp->read())) {                        
                        if(preg_match("/^{$name}_\d+\.php$/i", $volumn)){
                            $file = $backupdir.$name.'/'.$volumn;
                            $size = $size + filesize($file);
                            $count ++;
                        }
                    }
                    $fp->close();
                    $row['size'] = $size;
                    $row['count'] = $count;
                    $items[] = $row;
                }
            }            
        }
        closedir($handler);
        //$items = asort($items);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'admin:tools/database/backlist.html';
    }
    public function backdel($hash)
    {
        $bakdir = __CORE_DIR.'/data/backup/'.$hash;
        if(!preg_match('/^\d{8}_\w{8}$/i', $hash)){
            $this->msgbox->add('你要删除的备份不存在', 211);
        }else if(!file_exists($bakdir)){
            $this->msgbox->add('你要删除的备份不存在或已经删除', 212);
        }else if(!is_dir($bakdir)){
            $this->msgbox->add('你要删除的备份不存在或已经删除', 212);
        }else{
            K::M('io/dir')->remove($bakdir);
            $this->msgbox->add('删除数据库备份成功');
        }
    }    
    public function restore($hash, $volumn=1)
    {
        @set_time_limit(0);
        @ini_set('memory_limit','128M');        
        if($ret = K::M('mysql/backup')->restore($hash, $volumn)){
            if($ret === true){
                $this->msgbox->add('还原数据库成功');
                $this->msgbox->set_data('forward', 'index.php?ctl=tools/database&act=backlist');
            }else{
                $volumn ++;
                $this->msgbox->load("index.php?tools/database-restore-{$hash}-{$volumn}.html", "正在还原数据库备份{$hash}卷#{$volumn}");
            }
        }
    }
    public function optimize()
    {
        if($this->checksubmit('tables')){
            K::M('mysql/backup')->optimize($tables);
            $this->msgbox->add('优化数据库成功');
        }else{
            $optimize_tables = array();
            if($items = K::M('tools/database')->tables()){
                foreach($items as $table){
                    if($table['Data_free'] && $table[$tabletype] == 'MyISAM') {
                        $tables[] = $table;
                        $totalsize += $table['Data_length'] + $table['Index_length'];
                    }
                }
            }
            $this->pagedata['tables'] = $tables;
            $this->tmpl = 'admin:tools/database/optimize.html';
        }
    }
}