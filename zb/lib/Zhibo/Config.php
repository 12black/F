<?php
namespace ZhiBo;
class Config{
        private static $config = [];
        private static $space = 'config';
        public static function  load($file,$name= '',$space = ''){
                $space = $space ?: self::$space;
                if(!isset(self::$config[$space])){
                        self::$config[$space] = [] ;
                }
                if(is_file($file)){
                            $file = require $file;
                            if (!empty($name)) {
                                    self::$config[$space][$name] = isset(self::$config[$space][$name]) ? array_merge(self::$config[$space][$name], $file) : self::$config[$space][$name] = $file;
                                    return self::$config[$space][$name];
                            }else{
                                    return self::$config[$space] = array_merge(self::$config[$space], array_change_key_case($file));
                            }
                } else {
                        return self::$config[$space];
                }
        }
        public static function get($name = '',$space = ''){
                $space = $space ?: self::$space;
                if (empty($name) && isset(self::$config[$space])) {
                        return self::$config[$space];
                }else{
                        return isset(self::$config[$space][$name]) ? self::$config[$space][$name] : null;
                }
        }
}