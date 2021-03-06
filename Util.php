<?php

class Util
{
    /**
    * 生成一个唯一标识GUID
    */
    public static function guid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid   = chr(123)
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);
            return $uuid;
        }
    }

    /**
     * 格式化输出友好的日期
     * @param int|string 可以接受时间戳或者字符串日期格式
     * @return String  3天前
     */
    public static function friendlyDateTime($time)
    {
        if (! is_numeric($time)) {
            $time = strtotime($time);
        }
        $t = time() - $time;
        $f = array(
            '31536000'=> '年',
            '2592000' => '个月',
            '604800'  => '星期',
            '86400'   => '天',
            '3600'    => '小时',
            '60'      => '分钟',
            '1'       => '秒'
        );
        foreach ($f as $k=>$v){        
            if (0 !=$c=floor($t/(int)$k)){
                return $c.$v.'前';
            }
        }
    }

    public static function randomInt($length = 6)
    {
        // var_dump($_SERVER);
        return self::random($length, true);
    }

    public static function randomString($length = 32)
    {
        return self::random($length, false);
    }

    protected static function random($length, $numeric = FALSE)
    {
        // $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = base_convert(md5(microtime() . __DIR__), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /*------------------------------------------*/

    /**
     * 浏览器友好的变量输出
     * @param mixed $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    public static function dump($var, $echo=true, $label=null, $strict=true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else {
            return $output;
        }
    }
}
