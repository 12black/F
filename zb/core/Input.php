<?php

namespace ZhiBo;

class Input{
        public static function get(){
                return self::data($_GET);
        }
        public static function data($input){
                $data = $input;
                return $data;
        }
        public static function filterExp(){}
}
