<?php
namespace ZhiBo;
class Request{
        protected static $_instance;
        protected  $pathinfo;
        protected  $method;
        protected  $dispatch;
        protected  $param;
        public function __construct() {}
        private function  pathinfo(){
                if (is_null($this->pathinfo)) {
                        if (isset($_GET[Config::get('var_pathinfo')])) {
                                    $_SERVER['PATH_INFO'] = $_GET[Config::get('var_pathinfo')];
                                    unset($_GET[Config::get('var_pathinfo')]);
                        } 
                        $this->pathinfo = empty($_SERVER['PATH_INFO']) ? '/' : ltrim($_SERVER['PATH_INFO'], '/');
                }
                return $this->pathinfo;
        }
        public static function instance() {
                if(is_null(self::$_instance)){
                        self::$_instance = new static();
                }
                return self::$_instance;
        }
        
        public  function path(){
                return $this->pathinfo();
        }
        public function method($method = ''){
                if ($method) {
                        $this->server = $method;
                        return;
                } elseif (!$this->method) {
                        $this->method = IS_CLI ? 'GET' :  $_SERVER['REQUEST_METHOD'];
                }
                return $this->method;
        }
        
        public function dispatch($dispatch = []){
                if (!empty($dispatch)) {
                    $this->dispatch = $dispatch;
                }
                return $this->dispatch;
        }
        protected function param($name = '', $default = null){
                if (empty($this->param)) {
                        $method = $this->method();
                        switch ($method) {
                                case 'POST':
                                    $vars = Input::post();
                                    break;
                                default:
                                    $vars = [];
                        }
                        // 当前请求参数和URL地址中的参数合并
                        $this->param = array_merge(Input::get(), $vars);
                }
                if ($name) {
                        return isset($this->param[$name]) ? $this->param[$name] : $default;
                } else {
                        return $this->param;
                }
        }
        
        public static function __callStatic($method, $params) {
                if(is_null(self::$_instance)){
                        self::instance();
                }
                return call_user_func_array([self::$_instance, $method], $params);
        }
}