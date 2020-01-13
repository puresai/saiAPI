<?php
/**
 * 常用函数
 */
 
if (!function_exists("p")) {
    function p($var)
    {
        if (is_bool($var)) {
            var_dump($var);
            die;
        } elseif (is_null($var)) {
            var_dump(null);
            die;
        } else {
            die("<meta charset='utf-8'/>
<pre style='position:relative;
z-index:999;
padding:10px;
border-radius:5px;
background:#f5f5f5;
border:1px solid #aaa;
font-size:14px;
line-height:18px;
opacity:0.8;'>".print_r($var, true)."</pre>");
        }
    }
}