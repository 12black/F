<?php
define('DS', DIRECTORY_SEPARATOR);
define('ZB_PATH', dirname(__FILE__) . DS);
define('EXT','.php');
define('LIB_PATH', ZB_PATH . 'lib' . DS);
define('APP_PATH',dirname(__FILE__) . DS.'..'.DS.'public'.DS);
define('IS_CLI','CLI' == PHP_SAPI ?  1 : 0);
define('APP_MULTI_MODULE',false);
define('VIEW_LAYER','view');
define('CLASS_APPEND_SUFFIX',false);
define('APP_NAMESPACE','App');