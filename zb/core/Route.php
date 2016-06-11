<?php

namespace ZhiBo;
class Route{
        private static $map = [];
        private static $rules = [
                                                'GET'     => [],
                                                'POST'    => [],
                                                'PUT'     => [],
                                                'DELETE'  => [],
                                                'HEAD'    => [],
                                                'OPTIONS' => [],
                                                '*'       => [],
                                        ];
        private static $bind = [];
        public static function check($request, $url, $pathinfo_explode = '/', $checkDomain = false){
                /*if ($checkDomain) {
                        self::checkDomain($request);
                }*/

                if ('/' != $pathinfo_explode) {
                        $url = str_replace($pathinfo_explode, '/', $url);
                }
                if (isset(self::$map[$url])) {
                        //return self::parseUrl(self::$map[$url], $pathinfo_explode);
                }
                $rules = self::$rules[REQUEST_METHOD];
                if (!empty(self::$rules['*'])) {
                        $rules = array_merge(self::$rules['*'], $rules);
                }

                // 检测URL绑定
                $return = self::checkUrlBind($url, $rules);
                if ($return) {
                        return $return;
                }

                return false;
        }
        
        private static function checkUrlBind(&$url, &$rules){
                return false;
        }
        
        public static function parseUrl($url, $pathinfo_explode = '/', $autoSearch = false){
                if (isset(self::$bind['module'])) {
                    // 如果有模块/控制器绑定
                        $url = self::$bind['module'] . '/' . $url;
                }
                // 分隔符替换 确保路由定义使用统一的分隔符
                if ('/' != $pathinfo_explode) {
                    $url = str_replace($pathinfo_explode, '/', $url);
                }

                $result = self::parseRoute($url, $autoSearch, true);

                if (!empty($result['var'])) {
                    $_GET = array_merge($result['var'], $_GET);
                }
                return ['type' => 'module', 'module' => $result['route']];
        }
        private static function parseRoute($url, $autoSearch = false, $reverse = false){
                $url = trim($url, '/');
                $var = [];
                if (false !== strpos($url, '?')) {
                        $info = parse_url($url);
                        $path = explode('/', $info['path']);
                        parse_str($info['query'], $var);
                } elseif (strpos($url, '/')) {
                        $path = explode('/', $url);
                } elseif (false !== strpos($url, '=')) {
                        parse_str($url, $var);
                } else {
                        $path = [$url];
                }
                $route = [null, null, null];
                if (isset($path)) {
                        if ($reverse) {
                            $module = APP_MULTI_MODULE ? array_shift($path) : null;
                            if (!$autoSearch) {
                                     $controller = !empty($path) ? array_shift($path) : null;
                            }
                            
                            // 解析操作
                            $action = !empty($path) ? array_shift($path) : null;
                            // 解析额外参数
                            if (!empty($path)) {
                                    preg_replace_callback('/([^\/]+)\/([^\/]+)/', function ($match) use (&$var) {
                                            $var[strtolower($match[1])] = strip_tags($match[2]);
                                    }, implode('/', $path));
                            }
                        } else {
                                $action     = array_pop($path);
                                $controller = !empty($path) ? array_pop($path) : null;
                                $module     = APP_MULTI_MODULE && !empty($path) ? array_pop($path) : null;
                                // REST 操作方法支持
                                if ('[rest]' == $action) {
                                        $action = REQUEST_METHOD;
                                } elseif (Config::get('use_action_prefix') && !empty(self::$methodPrefix[REQUEST_METHOD])) {
                                    // 操作方法前缀支持
                                        $action = 0 !== strpos($action, self::$methodPrefix[REQUEST_METHOD]) ? self::$methodPrefix[REQUEST_METHOD] . $action : $action;
                                }
                        }
                        // 封装路由
                        $route = [$module, $controller, $action];
                }
                return ['route' => $route, 'var' => $var];
        }
}
