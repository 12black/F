<?php

namespace ZhiBo;
require  __DIR__.'/base.php';
require ZB_PATH.'/core/AutoLoader.php';

$import = require ZB_PATH . 'import'.EXT;

AutoLoader::addNamespace($import['namespace']);
AutoLoader::register();
AutoLoader::addMap($import['alias']);
Config::load($import['config']);

App::run();