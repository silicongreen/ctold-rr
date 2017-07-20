<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
include 'DbCon.php';

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'timeZone' => 'Asia/Dhaka',
    'name' => 'champs21-school.com',
    // preloading 'log' component
    'preload' => array('log', 'zend'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.components.vo.*',
        'application.vendors.*',
        'application.helpers.*',
        'ext.YiiMailer.YiiMailer',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '12345',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
        'api',
    ),
    // application components
    'components' => array(
        'zend' => array(
            'class' => 'ext.zend.EZendAutoloader'
        ),
        'session' => array(
            "timeout" => 3153600000
        ),
        /* 'cache'  => array(
          'class'  => 'system.caching.CFileCache',
          ), */
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'autoRenewCookie' => true,
            'authTimeout' => 3153600000
        ),
        /* 'cache'  => array(
          'class'  => 'system.caching.CFileCache',
          ), */


        // uncomment the following to enable URLs in path-format
//        'session' => array(
//            'autoStart' => true,
//            'cookieMode' => 'none',
//            'useTransparentSessionID' => true,
//            'sessionName' => 'session',
//            'timeout' => 3600 * 24 * 30 * 12,
//        ),
        'curl' => array(
            'class' => 'ext.curl.Curl',
            'options' => array(/* additional curl options */),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'caseSensitive' => false,
            'rules' => array(
                'gii'=>'gii',
                'gii/<controller:\w+>'=>'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                array('api/user/auth', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/dashboard', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/notice', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/notice/acknowledge', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/homework', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/report', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/report/getclasstestproject', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/calender/getAttendence', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/event', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/event/acknowledge', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/club', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/club/acknowledge', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/routine', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/syllabus', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/syllabus/terms', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                array('api/freeuser/create', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
            ),
        ),
        /*
          'db'=>array(
          'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
          ),
         */

        // uncomment the following to use a MySQL database
        'db' => $db_con,
        'cache' => array(
            'class' => 'system.caching.CFileCache',
            'cachePath' => 'protected/runtime/cache/news'
        ),
        'errorHandler' => array(
            'errorAction' => 'api/user/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, trace, log',
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
    ),
);
