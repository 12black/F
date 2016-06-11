<?php

namespace ZhiBo;
class App{
        public static function run($request = null){
                is_null($request) && $request = Request::instance();
                define('REQUEST_METHOD', $request->method());
                $config = self::initModule(Config::get());
                $dispatch = self::route($request, $config);
                $data = self::module($dispatch['module'], $config);
                //$type = IS_AJAX ? Config::get('default_ajax_return') : Config::get('default_return_type');
                return Response::create($data, Config::get('default_return_type'))->send();
        }
        private static function initModule( $config){
                $config = Config::load(APP_PATH .'config' . EXT);
                if ($config['extra_config_list']) {
                        foreach ($config['extra_config_list'] as $name => $file) {
                               $filename = APP_PATH .  $file . EXT;
                               Config::load($filename, is_string($name) ? $name : pathinfo($filename, PATHINFO_FILENAME));
                        }
                }
                return Config::get();
        }
        public static function route($request, array $config){
                $path   = $request->path();
                $pathinfo_explode   = $config['pathinfo_explode'];
                $result = false;
                if ($config['urlrewrite']) {
                        if (!empty($config['route'])) {
                                    //Route::import($config['route']);
                        }
                        $result = Route::check($request, $path, $pathinfo_explode, false);
                }
                if (false === $result) {
                        $result = Route::parseUrl($path, $pathinfo_explode, $config['controller_search']);
                }
                //保证$_REQUEST正常取值
                $_REQUEST = array_merge($_POST, $_GET, $_COOKIE);
                // 注册调度机制
                return $request->dispatch($result);
        }
        
        public static function module($result, $config){
                if (APP_MULTI_MODULE) {
                } else {
                        define('MODULE_NAME', '');
                        define('MODULE_PATH', APP_PATH);
                        define('VIEW_PATH', MODULE_PATH . VIEW_LAYER . DS);
                }
                $controllerName = strip_tags($result[1] ?: $config['default_controller']);
                defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', strtolower($controllerName));
                $actionName = strip_tags($result[2] ?: $config['default_action']);
                defined('ACTION_NAME') or define('ACTION_NAME', strtolower($actionName));
                if (!preg_match('/^[A-Za-z](\/|\.|\w)*$/', CONTROLLER_NAME)) {
                         throw new \Exception('illegal controller name:' . CONTROLLER_NAME, 10000);
                }
                $instance = AutoLoader::controller(CONTROLLER_NAME);
                $action = ACTION_NAME ;

                try {
                        $call = [$instance, $action];
                        $data = self::invokeMethod($call);
                }  catch (Exception  $e){
                    
                }
                return $data;
        }
        
        public static function invokeMethod($method, $vars = []){
                if (empty($vars)) {
                        $vars = Request::param();
                }
                if (is_array($method)) {
                        $class   = is_object($method[0]) ? $method[0] : new $method[0];
                        $reflect = new \ReflectionMethod($class, $method[1]);
                } else {
                        $reflect = new \ReflectionMethod($method);
                }
                $args = self::bindParams($reflect, $vars);
                return $reflect->invokeArgs(isset($class) ? $class : null, $args);
        }
        private static function bindParams($reflect, $vars){
                $args = [];
                $type = key($vars) === 0 ? 1 : 0;
                if ($reflect->getNumberOfParameters() > 0) {
                        $params = $reflect->getParameters();
                        foreach ($params as $param) {
                                $name  = $param->getName();
                                $class = $param->getClass();
                                if ($class && 'think\Request' == $class->getName()) {
                                        $args[] = Request::instance();
                                } elseif (1 == $type && !empty($vars)) {
                                        $args[] = array_shift($vars);
                                } elseif (0 == $type && isset($vars[$name])) {
                                        $args[] = $vars[$name];
                                } elseif ($param->isDefaultValueAvailable()) {
                                        $args[] = $param->getDefaultValue();
                                } else {
                                        throw new \Exception('method param miss:' . $name, 10004);
                                }
                        }
                        // 全局过滤
                        array_walk_recursive($args, 'ZhiBo\\Input::filterExp');
                }
                return $args;
        }
        
        public static function model($name = '', $layer = 'Model', $appendSuffix = false){
                static $_model = [];
                if (isset($_model[$name . $layer])) {
                        return $_model[$name . $layer];
                }
                if (strpos($name, '/')) {
                        list($module, $name) = explode('/', $name, 2);
                } else {
                        $module = APP_MULTI_MODULE ? MODULE_NAME : '';
                }
                $class = self::parseClass($module, $layer, $name, $appendSuffix);
                if (class_exists($class)) {
                        $model = new $class();
                } else {
                    $class = str_replace('\\' . $module . '\\', '\\' . COMMON_MODULE . '\\', $class);
                    if (class_exists($class)) {
                            $model = new $class();
                    } else {
                            throw new Exception('class [ ' . $class . ' ] not exists', 10001);
                    }
                }
                $_model[$name . $layer] = $model;
                return $model;
        }
}