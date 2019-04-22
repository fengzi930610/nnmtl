<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: safecheck.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
//code copy from by discuz
class Mdl_Mysql_Safecheck
{
    protected static $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
    protected static $config = array(
            'function'  => array('load_file','hex','substring','if','ord','char'),
            'action'    => array('intooutfile','intodumpfile','unionselect','(select','unionall','uniondistinct'),
            'notice'    => true,
            'fullnotice'=> false
        );
    public static function checkquery($sql)
    {
        $cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
        if (in_array($cmd, self::$checkcmd)) {
            $test = self::_do_query_safe($sql);
            if ($test < 1) {
                trigger_error('Mysql:It is not safe to do this query', E_USER_ERROR);
            }
        }
        return true;
    }
    private static function _do_query_safe($sql)
    {
        $sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
        $mark = $clean = '';
        if (strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false) {
            $clean = preg_replace("/'(.+?)'/s", '', $sql);
        } else {
            $len = strlen($sql);
            $mark = $clean = '';
            for ($i = 0; $i < $len; $i++) {
                $str = $sql[$i];
                switch ($str) {
                    case '\'':
                        if (!$mark) {
                            $mark = '\'';
                            $clean .= $str;
                        } elseif ($mark == '\'') {
                            $mark = '';
                        }
                        break;
                    case '/':
                        if (empty($mark) && $sql[$i + 1] == '*') {
                            $mark = '/*';
                            $clean .= $mark;
                            $i++;
                        } elseif ($mark == '/*' && $sql[$i - 1] == '*') {
                            $mark = '';
                            $clean .= '*';
                        }
                        break;
                    case '#':
                        if (empty($mark)) {
                            $mark = $str;
                            $clean .= $str;
                        }
                        break;
                    case "\n":
                        if ($mark == '#' || $mark == '--') {
                            $mark = '';
                        }
                        break;
                    case '-':
                        if (empty($mark) && substr($sql, $i, 3) == '-- ') {
                            $mark = '-- ';
                            $clean .= $mark;
                        }
                        break;
                    default:
                        break;
                }
                $clean .= $mark ? '' : $str;
            }
        }
        $clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));
        if (self::$config['fullnotice']) {
            $clean = str_replace('/**/', '', $clean);
        }
        if (is_array(self::$config['function'])) {
            foreach (self::$config['function'] as $fun) {
                if (strpos($clean, $fun . '(') !== false)
                    return '-1';
            }
        }
        if (is_array(self::$config['action'])) {
            foreach (self::$config['action'] as $action) {
                if (strpos($clean, $action) !== false)
                    return '-3';
            }
        }
        if (self::$config['likehex'] && strpos($clean, 'like0x')) {
            return '-2';
        }
        if (is_array(self::$config['notice'])) {
            foreach (self::$config['notice'] as $note) {
                if (strpos($clean, $note) !== false)
                    return '-4';
            }
        }
        return 1;
    }
}