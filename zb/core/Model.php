<?php

namespace ZhiBo;

class Model{
        protected $validate;
        public  $error ;
        protected  $field = [];
        public function __construct() {}
        protected  function _set($name,$value){
                $this->data[$name] = $value;
        }
        
        public function update($data = [],$where = []){
                $this->save($data = [],$where = []);
        }
        public function insert($data){
                $this->save($data);
        }
        public function save($data = [],$where = [],$getId = true){
                foreach($data as $key => $value){
                        $this->_set($key, $value);
                }
                if(!$this->validateData()){
                        return false;
                }
                //===检查字段预留
                if(!empty($this->$field)){}
                return true;
        }
        protected function validateData(){
                if(!empty($this->validate)){
                        $_validate = new Validate();
                        $_validate->rule($this->validate);
                        if(!$_validate->check($this->data)){
                                $this->error = $_validate->error;
                                return false;
                        }
                }
                return true;
        }
}

