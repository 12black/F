<?php

namespace App\controller;
use ZhiBo\AutoLoader;
class A{
        public $a;
        function a($c,$b){
                $m = AutoLoader::model('user');
                if(!$m->save(array('title'=>'123'))){
                        var_dump($m->error);
                }
                return $c.'===='.$b;
        }
}
