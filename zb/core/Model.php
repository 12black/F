<?php

namespace ZhiBo;

class Model{
        protected $validate;
        public function __construct() {}
        protected  function _set($name,$value){
                $this->data[$name] = $value;
        }
        
        public function save($data = [],$where = [],$getId = true){
                foreach($data as $key => $value){
                        $this->_set($key, $value);
                }
                if(!$this->validateData()){
                        return false;
                }
        }
        protected function validateData(){
                if(!empty($this->validate)){
                        $_validate = new Validate();
                        $_validate->rule($this->validate);
                        $_validate->check($this->data);
                }
                return;
        }
}

