<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Error {
    
    /**
     * 错误消息栈
     * @var type 
     */
    protected static $error=array();
    
    /**
     * 清除错误消息
     */
    public static function clear()
    {
        self::$error = array();
    }
    
    /**
     * 增加错误消息
     * @param type $msg
     */
    public static function add($msg)
    {
        self::$error[] = $msg;
    }
    
    /**
     * 最后一条错误消息
     * @return type
     */
    public static function last()
    {
        return end(self::$error);
    }
    
    /**
     * 所有错误消息
     * @return type
     */
    public static function all()
    {
        return self::$error;
    }
    
    /**
     * 是否存在错误消息
     * @return type
     */
    public static function hasError()
    {
        return !empty(self::$error);
    }
}
