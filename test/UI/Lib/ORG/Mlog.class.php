<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

/**
 * 日志处理类
 */
class Mlog {
    private $fp = null;
    private $max_filesize = 0;
    private $ext = '.txt';

    public function __construct($path, $filename){
        $this->max_filesize =  10 * 1024 * 1024;
        $last = substr($path, strlen($path)-1, 1);
        if ($last != '/' && $last != "\\"){
            $path .= '/';
        }
        $fullname = $path.$filename.$this->ext;
        if (is_file($fullname) && filesize($fullname) >= $this->max_filesize){
            rename($fullname, $path.$filename.'_'.date('YmdHis').$this->ext);
        }
        $this->fp = fopen($path.$filename.$this->ext, "a+");
    }

    public function __destruct(){
        if ($this->fp){
            fclose($this->fp);
        }
    }

    public function mwrite($str){
        fwrite($this->fp, $str);
    }
}