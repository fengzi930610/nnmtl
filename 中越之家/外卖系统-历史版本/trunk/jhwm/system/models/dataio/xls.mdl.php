<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: xls.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Dataio_Xls{

    public function export_begin($keys, $file)
	{
        K::M('dataio/file')->download($file.'-'.date('Ymd').'.xls');
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style>td{vnd.ms-excel.numberformat:@}</style></head>';
        echo '<table width="100%" border="1">';
        echo '<tr><th filter=all>'.implode('</th><th filter=all>',$keys)."</th></tr>\r\n";
        flush();
    }
    public function export_rows($rows)
	{
        foreach($rows as $row){
            echo '<tr><td>'.implode('</td><td>',$row)."</td></tr>\r\n";
        }
        flush();
    }
    public function export_finish()
	{
        echo '</table>';
        flush();
        exit;
    }
	public function export($keys, $rows, $file)
	{
		$this->export_begin($keys, $file);
		$this->export_rows($rows);
		$this->export_finish();
	}
}