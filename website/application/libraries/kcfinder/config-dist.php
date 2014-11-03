<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

// IMPORTANT!!! Do not remove uncommented settings in this file even if
// you are using session configuration.
// See http://kcfinder.sunhater.com/install for setting descriptions

$_CONFIG = array(

    'disabled' => false,
    'denyZipDownload' => false,
    'denyUpdateCheck' => false,
    'denyExtensionRename' => false,

    'theme' => "oxygen",
    'base_url_ci' => "http://www.dailystarnews.dev/",
    'base_url' => "http://www.dailystarnews.dev/ckeditor/kcfinder/",
    'uploadURL' => "upload",
    'uploadDir' => "upload/gallery",

    'dirPerms' => 0777,
    'filePerms' => 0755,

    'access' => array(

        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy' => true,
            'move' => true,
            'rename' => true
        ),

        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),

    'deniedExts' => "exe com msi bat php phps phtml php3 php4 cgi pl",
    
    'db_config' => array(
        "host"  => "localhost",
        "user"  => "root",
        "pass"  => "",
        "port"  => "3306",
        "db"    => "dailystardev_0.0.6",
        "prefix"=> "tds_"
    ),
    
    'types' => array(

        // CKEditor & FCKEditor types
        'cartoon'   =>  "*img",
        'docs'   =>  "doc dot docx dotx docm dotm xls xlt xla xlsx xltx xlsm xltm xlam xlsb ppt pot pps ppa pptx potx ppsx ppam pptm potm ppsm  ",
        'image'  =>  "*img",

        // TinyMCE types
        'pdf'    =>  "pdf",
        'video'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
        'podcast'   =>  "wma mp3 ac3",
    ),

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'thumbsDir' => "thumbs",
    
    "thumbArray" => array(
		"thumbs" => array(49,49),
		"carrousel"=>array(460,225),
		"main"=>array(130,80),
		"otherRightFirst"=>array(162,0),
		"otherSixBottom"=>array(222,105),
		"weekly"=>array(200,0),
		"magazineHome"=>array(113,158),
		"magazine"=>array(263,283)
    ),


    'jpegQuality' => 90,

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',

    // THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION CONFIGURATION
    '_check4htaccess' => true,
    //'_tinyMCEPath' => "/tiny_mce",

    '_sessionVar' => &$_SESSION['KCFINDER'],
    //'_sessionLifetime' => 30,
    //'_sessionDir' => "/full/directory/path",

    //'_sessionDomain' => ".mysite.com",
    //'_sessionPath' => "/my/path",
);

?>
