<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$config['medium'] = array(NULL => 'Medium', '0' => 'Bangla', '1' => 'English');
$config['free_user_types'] = array('1' => 'Visitor', '2' => 'Student', '3' => 'Teacher', '4' => 'Parent');
$config['join_user_types'] = array('1' => 'Alumni', '2' => 'Student', '3' => 'Teacher', '4' => 'Parent');
$config['join_user_approval'] = array(
    1 => FALSE,
    2 => TRUE,
    3 => TRUE,
    4 => FALSE
);
$config['free_user_folders'] = array(
    '0' => 'Unread',
    '1' => 'Articles',
    '2' => 'Recipes',
    '3' => 'Resources'
);

$config['multi_school_join'] = FALSE;

//$config['google'] = array(
//    'client_id_1' => '650847745730-ijbdhl82is942vboamrb2lqradnqk23v.apps.googleusercontent.com',
//    'client_secret_1' => 'V4lv34OdU7sTJGUQx4sM_F2E',
//    'redirect_url_1' => 'http://free.champs21-school.com' . '/register_user',
//    
//    'client_id_2' => '650847745730-27tmea49dcp01iho24eun3tgol7gq772.apps.googleusercontent.com',
//    'client_secret_2' => 'GOOgGaYCnSXbpG5NXFg3NvvG',
//    'redirect_url_2' => 'http://free.champs21-school.com' . '/login_user',
//);
//
//$config['facebook'] = array(
//    'app_id'        => '786122001434558',
//    'app_secret'    => 'f00cad12f23dbb179ba2372d2f9b8032',
//    'redirect_url'  => 'http://free.champs21-school.com' . '/register_user',
//    'permissions'   => array(
//        'email',
//        'user_location',
//        'user_birthday'
//    ),
//);
