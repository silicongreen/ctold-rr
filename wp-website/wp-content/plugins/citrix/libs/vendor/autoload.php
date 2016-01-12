<?php

define('DS', '/');
define('CITRIX_PATH_VENDOR_PATH', __DIR__ . DS . 'citrix' . DS . 'src');

// Autoload any classes that are required

spl_autoload_register(function($className) {

    $rootPath = CITRIX_PATH_VENDOR_PATH . DS;
    $classNameParts = explode('\\', $className);
    $classFile = $rootPath . implode(DS, $classNameParts) . '.php';

    require_once($classFile);
});
