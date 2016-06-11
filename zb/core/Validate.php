<?php
namespace ZhiBo;

class Validate{
        private  $rule = [];
        protected static $type = [];
        protected static $typeMsg = [
                'require' => '不能为空',
        ];
        public function __construct($rule = []) {
            $this->rule = array_merge($this->rule,$rule);
        }
        public function rule($name, $rule = ''){
                $this->rule = array_merge($this->rule, $name);
                return $this;
        }
        public function check(&$data, $rules = []){
                $this->error = [];
                if (empty($rules)) {
                        $rules = $this->rule;
                }
                foreach ($rules as $key => $item) {
                        $rule = $item;
                        $msg  = [];
                        if (strpos($key, '|')) {
                                // 字段|描述 用于指定属性名称
                                list($key, $title) = explode('|', $key);
                        } else {
                                $title = $key;
                        }

                        // 获取数据 支持二维数组
                        $value = $this->getDataValue($data, $key);
                        // 字段验证
                        $result = $this->checkItem($key, $value, $rule, $data, $title, $msg);

                        if (true !== $result) {
                                // 没有返回true 则表示验证失败
                                if (!empty($this->batch)) {
                                        // 批量验证
                                        if (is_array($result)) {
                                                $this->error = array_merge($this->error, $result);
                                        } else {
                                                $this->error[$key] = $result;
                                        }
                                } else {
                                        $this->error = $result;
                                        return false;
                                }
                        }
                }
                return !empty($this->error) ? false : true;
        }
        
        protected function getDataValue($data, $key){
                $value = isset($data[$key]) ? $data[$key] : null;
                return $value;
        }
        
        
        protected function checkItem($field, $value, $rules, &$data, $title = '', $msg = []){
                if ($rules instanceof \Closure) {
                        $result = call_user_func_array($rules, [$value, &$data]);
                } else {
                        // 支持多规则验证 require|in:a,b,c|... 或者 ['require','in'=>'a,b,c',...]
                    
                        if (is_string($rules)) {
                                $rules = explode('|', $rules);
                        }
                        $i = 0;
                        foreach ($rules as $key => $rule) {var_dump($key);
                                    $type = 'is';
                                    $info = $rule;
                                     if (0 === strpos($info, 'require') || (!is_null($value) && '' !== $value)) {
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
                        case 'require' :
                                $result = !empty($value);
                                break;
                        case 'number' :
                                $result = is_numeric($value);
                                break;
                }
                
                return $result;
        }
        
        protected function getRuleMsg( $title, $type, $rule){
                if (isset(self::$typeMsg[$type])) {
                        $msg = self::$typeMsg[$type];
                } else {
                        $msg = $title . '规则错误';
                }
                if (is_string($msg) && false !== strpos($msg, ':')) {
                    if (strpos($rule, ',')) {
                            $array = array_pad(explode(',', $rule), 3, '');
                    } else {
                            $array = array_pad([], 3, '');
                    }
                    $msg = str_replace(
                        [':attribute', ':rule', ':1', ':2', ':3'],
                        [$title, (string) $rule, $array[0], $array[1], $array[2]],
                        $msg);
                }
                return $msg;
        }
}