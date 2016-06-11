<?php
namespace ZhiBo;
class Response{
        static $instance = [];
        private  $header = [];
        private  $data = [];
        public function __construct($data = [], $type = '', $options = []) {
                $this->data = $data;
                $this->header['Content-Type'] =  'text/html; charset=utf-8';
                
        }
        public static function create($data = [], $type = '', $options = []){
                $type = strtolower($type);
                if (!isset(self::$instance[$type])) {
                        $class = (isset($options['namespace']) ? $options['namespace'] : '\\ZhiBo\\response\\') . ucfirst($type);
                        if (class_exists($class)) {
                                $response = new $class($data, $type, $options);
                        } else {
                                $response = new static($data, $type, $options);
                        }
                        self::$instance[$type] = $response;
                }
                return self::$instance[$type];
        }
        public function send($data = []){
                $data = $data ? : $this->data;

                if (!headers_sent() && !empty($this->header)) {
                    if (isset($this->header['status'])) {
                            http_response_code($this->header['status']);
                            unset($this->header['status']);
                    }

                    foreach ($this->header as $name => $val) {
                            header($name . ':' . $val);
                    }
                }
                if (is_scalar($data)) {
                        echo $data;
                } elseif (!is_null($data)) {
                        throw new \Exception('不支持的数据类型输出：' . gettype($data));
                }

                if (function_exists('fastcgi_finish_request')) {
                        fastcgi_finish_request();
                }
                return $data;
        }
}
