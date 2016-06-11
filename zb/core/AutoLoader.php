<?php
namespace ZhiBo;

class AutoLoader{
        private static $namespace = [];
        private static $load = [];
        private static $map = [] ;
        private static function init($class){
                if (!empty(self::$namespaceAlias)) {
                        $namespace = dirname($class);
                        if (isset(self::$namespaceAlias[$namespace])) {
                                $original = self::$namespaceAlias[$namespace] . '\\' . basename($class);
                                if (class_exists($original)) {
                                        return class_alias($original, $class, false);
                                }
                        }
                }
                if (isset(self::$map[$class])) {
                        if (is_file(self::$map[$class])) {
                                include self::$map[$class];
                        } else {
                                return false;
                        }
                } else{
                        if(!strpos($class, '\\')){
                                return false;
                        }
                        list($name,$class) = explode('\\',$class,2);
                        $path = self::$namespace[$name];
                        $filename = $path . str_replace('\\', DS, $class) . EXT;
                        if (is_file($filename)) {
                                self::$load[] = $filename;
                                include $filename;
                        } else {
                                return false;
                        }
                }
        }
        
        public static function addNamespace($namespace, $path = '')
        {
                if (is_array($namespace)) {
                        self::$namespace = array_merge(self::$namespace, $namespace);
                } else {
                        self::$namespace[$namespace] = $path;
                }
        }
        
       public  function register($autoload = ''){
                spl_autoload_register($autoload ? $autoload : 'ZhiBo\\AutoLoader::init');
        }
        
        public static function addMap($class,$map = ''){
                if (is_array($class)) {
                        self::$map = array_merge(self::$map, $class);
                } else {
                        self::$map[$class] = $map;
                }
        }
        public static function controller($name, $layer = '', $appendSuffix = false, $empty = 'Error'){
                static $_instance = [];
                $layer   = $layer ? : 'controller';
                if (isset($_instance[$name . $layer])) {
                        return $_instance[$name . $layer];
                }
                if (strpos($name, '/')) {
                        list($module, $name) = explode('/', $name);
                } else {
                        $module = APP_MULTI_MODULE ? MODULE_NAME : '';
                }
                $class = self::parseClass($module, $layer, $name, $appendSuffix);
                if (class_exists($class)) {
                        $action                    = new $class(Request::instance());
                        $_instance[$name . $layer] = $action;
                        return $action;
                } elseif ($empty && class_exists($emptyClass = self::parseClass($module, $layer, $empty, $appendSuffix))) {
                        return new $emptyClass(Request::instance());
                } else {
                        throw new \Exception('class [ ' . $class . ' ] not exists', 10001);
                }
        }
        public static function parseClass($module, $layer, $name, $appendSuffix = false){
                $name  = str_replace(['/', '.'], '\\', $name);
                $array = explode('\\', $name);
                $class = self::parseName(array_pop($array), 1) . (CLASS_APPEND_SUFFIX || $appendSuffix ? ucfirst($layer) : '');
                $path  = $array ? implode('\\', $array) . '\\' : '';
                return APP_NAMESPACE . '\\' . (APP_MULTI_MODULE ? $module . '\\' : '') . $layer . '\\' . $path . $class;
        }
        
        public static function parseName($name, $type = 0){
                if ($type) {
                        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {return strtoupper($match[1]);}, $name));
                } else {
                        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
                }
        }
        public static function model($name = '', $layer = 'model', $appendSuffix = false){
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
                       throw new \Exception('class [ ' . $class . ' ] not exists', 10001);
                }
                $_model[$name . $layer] = $model;
                return $model;
        }
}