<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: csv.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Dataio_Csv
{
    public function export_begin($keys, $file)
	{
		K::M('dataio/file')->download($file.'-'.date('Ymd').'.csv');
        if($keys) $this->export_rows(array($keys));
    }
    public function export_rows($rows){
        foreach($rows as $row){
            echo implode('","',$this->_escape($row))."\r\n";
        }
        flush();
    }
    public function export_finish()
	{
    
	}
    public function _escape($arr){
        foreach($arr as $k=>$v){
            $arr[$k] = str_replace("\r",'\r',str_replace("\n",'\n',str_replace('"','""',$v)));
        }
        return $arr;
    }
    public function import_row(&$handle)
	{
        $data = fgetcsv($handle, 100000, ",");
        foreach($data as $k=>$v){
            $data[$k] = $v;
        }
        return $data;
    }
    public function import_rows(&$handle)
	{
        return false;
    }
	public function export($keys, $rows, $file)
	{
		$this->export_begin($keys, $file);
		$this->export_rows($rows);
		$this->export_finish();
	}
}
?>
