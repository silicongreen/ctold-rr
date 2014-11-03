<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('get_free_user_session')) {

    function get_free_user_session($key = NULL) {
        
        $CI = &get_instance();
        
        $free_user_session_data = $CI->session->userdata('free_user');
        
        return (empty($key)) ? $free_user_session_data : $free_user_session_data[$key];
    }
}

if (!function_exists('free_user_logged_in')) {

    function free_user_logged_in() {
        
        $CI = &get_instance();
        
        $free_user_session_data = $CI->session->userdata('free_user');
        
        return (!empty($free_user_session_data)) ? TRUE : FALSE;
    }
}

if (!function_exists('get_user_data')) {

    function get_user_data() {
        
        $CI = &get_instance();
        $CI->load->database();
        
        $sql = "SELECT * FROM tds_free_users WHERE tds_free_users.id = ? ";
        
        $user_id = get_free_user_session('id');
        
        $data = $CI->db->query($sql, $user_id)->row();
        
        return ($data) ? $data : FALSE;
    }
}

if (!function_exists('set_user_sessions')) {

    function set_user_sessions($obj_user){
        
        $CI = &get_instance();
        
        $remeber = TRUE;
        $sessionData['free_user']['id'] = $obj_user->id;
        $sessionData['free_user']['mobile_no'] = $obj_user->mobile_no;
        $sessionData['free_user']['email'] = $obj_user->email;
        
        $sessionData['free_user']['nick_name'] = $obj_user->first_name;
        if($obj_user->nick_name == 2){
            $sessionData['free_user']['nick_name'] = $obj_user->middle_name;
        }elseif($obj_user->nick_name == 3){
            $sessionData['free_user']['nick_name'] = $obj_user->last_name;
        }
        
        $sessionData['free_user']['full_name'] = $obj_user->first_name . ' ' . $obj_user->middle_name . ' ' . $obj_user->last_name;
        $sessionData['free_user']['type'] = $obj_user->user_type;
        $sessionData['free_user']['profile_image'] = $obj_user->profile_image;
        
        if($remeber)
        {
           $CI->session->sess_expiration = (60*60*24*30);
        }

        $CI->session->set_userdata($sessionData);
    }
}
