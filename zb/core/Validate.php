<?php
namespace ZhiBo;

class Validate{
        private  $rule = [];
        protected static $type = [];
        protected static $typeMsg = [
                'empty' => ':field不能为空',
                'number' => ':field必须为整数'
        ];
        public $error ;
        public function __construct($rule = []) {
            $this->rule = array_merge($this->rule,$rule);
        }
        public function rule($name, $rule = ''){
                $this->rule = array_merge($this->rule, $name);
                return $this;
        }
        public function check(&$data, $rules = []){
                if (empty($rules)) {
                        $rules = $this->rule;
                }
                foreach ($rules as $key => $item) {
                        $rule = $item;
                        $msg  = [];
                        list($key, $title) = explode('|', $key);
                        // 获取数据 支持二维数组
                        $value = $this->getDataValue($data, $key);
                        // 字段验证
                        $result = $this->checkValue($key, $value, $rule, $data, $title, $msg);
                        if (true !== $result) {
                                $this->error = $result;
                                return false;
                        }
                }
                return !empty($this->error) ? false : true;
        }
        
        protected function getDataValue($data, $key){
                $value = isset($data[$key]) ? $data[$key] : null;
                return $value;
        }
        
        
        protected function checkValue($field, $value, $rules, &$data, $title = '', $msg = []){
                if ($rules instanceof \Closure) {
                        $result = call_user_func_array($rules, [$value, &$data]);
                } else {
                        if (is_string($rules)) {
                                $rules = explode('|', $rules);
                        }
                        $i = 0;
                        foreach ($rules as $key => $rule) {
                                    $type = 'is';
                                    $info = $rule;
                                     if (0 === strpos($info, 'empty') || (!is_null($value) && '' !== $value)) {
                                            // 验证类型
                                            $callback = isset(self::$type[$type]) ? self::$type[$type] : [$this, $type];
                                            // 验证数据
                                            $result = call_user_func_array($callback, [$value, $rule, &$data, $field]);
                                    } else {
                                            $result = true;
                                    }

                                    if (false === $result) {
                                            // 验证失败 返回错误信息
                                            if (isset($msg[$i])) {
                                                    $message = $msg[$i];
                                            } else {
                                                    $message = $this->getRuleMsg($title, $info, $rule);
                                            }
                                            return $message;
                                    } elseif (true !== $result) {
                                            // 返回自定义错误信息
                                            return $result;
                                    }
                                    $i++;
                        }
                }
                return true !== $result ? $result : true;
        }
        
        protected function is($value,$rule){
                switch($rule){
                        case 'empty' :
                                $result = !empty($value);
                                break;
                        case 'number' :
                                $result = is_numeric($value);
                                break;
                }
                return $result;
        }
        
        protected function getRuleMsg( $title, $type, $rule){echo $type;
                if (isset(self::$typeMsg[$type])) {
                        $msg = self::$typeMsg[$type];
                } else {
                        $msg = $title . '规则错误';
                }
                if (is_string($msg) && false !== strpos($msg, ':')) {
                    $msg = str_replace(
                        [':field', ':rule'],
                        [$title, (string) $rule],
                        $msg);
                }
                return $msg;
        }
}