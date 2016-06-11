<?php

namespace App\controller;
use ZhiBo\AutoLoader;
class A{
        public $a;
        function a($c,$b){
                $m = AutoLoader::model('user');
                $m->save(array('title'=>'a'));
                return $c.'===='.$b;
        }
}
