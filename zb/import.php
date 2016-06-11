<?php
return [
        'namespace' => [
                'ZhiBo' => LIB_PATH.'ZhiBo'.DS,
                 APP_NAMESPACE => APP_PATH,
        ],
        'config' =>  ZB_PATH.'config'.EXT,
        'alias' => [
                'ZhiBo\App' => ZB_PATH . 'core' . DS . 'App' . EXT,
                'ZhiBo\Request' => ZB_PATH . 'core' . DS . 'Request' . EXT,
                'ZhiBo\Route' => ZB_PATH . 'core' . DS . 'Route' . EXT,
                'ZhiBo\Input' => ZB_PATH . 'core' . DS . 'Input' .EXT,
                'ZhiBo\Response' => ZB_PATH . 'core' .DS . 'Response'.EXT,
                'ZhiBo\Model'  => ZB_PATH . 'core' .DS . 'Model'.EXT,
                'ZhiBo\Validate'  => ZB_PATH . 'core' .DS . 'Validate'.EXT,
        ],
];
