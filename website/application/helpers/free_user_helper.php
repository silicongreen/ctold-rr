<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if( !function_exists("get_user_school") )
{
    function get_user_school($school_id=0)
    {
        if(free_user_logged_in())
        {
            $CI = &get_instance();
            $user_id = get_free_user_session("id");
            $CI->db->select("is_approved,deny_by,type");
            $CI->db->where("user_id",$user_id);
            if($school_id)
            {
                $CI->db->where("school_id",$school_id);
                $CI->db->limit(1);
                $result = $CI->db->get("user_school")->row();
            }
            else
            {
                $result = $CI->db->get("user_school")->result();
            } 
            if(count($result)>0)
            {
                return $result;
            } 
            else
            {
                return false;
            }
            
        }
        else
        {
            return false;
        }    
        
    }        
} 

if (!function_exists('wow_login')) {

    function wow_login() {
        
        $CI = &get_instance();
        
        $CI->load->config("huffas");
        
        return $CI->config->config['wow_login'];
        
       
    }
}

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
        $sessionData['free_user']['paid_id'] = $obj_user->paid_id;
        $sessionData['free_user']['paid_username'] = $obj_user->paid_username;
        $sessionData['free_user']['paid_password'] = $obj_user->paid_password;
        $sessionData['free_user']['paid_school_code'] = $obj_user->paid_school_code;
        $sessionData['free_user']['paid_school_id'] = $obj_user->paid_school_id;
        $sessionData['free_user']['profile_image'] = $obj_user->profile_image;
        
        if($obj_user->paid_school_code && $obj_user->paid_username && $obj_user->paid_password)
        {
                $CI->load->library('plus_api');

                $ar_params = array(
                    'school_code' => $obj_user->paid_school_code
                );

                $int_response = $CI->plus_api->init($ar_params, false);

                if($int_response != FALSE)
                {
                    $res = $CI->plus_api->call__('get', 'users/loginhook', 'get_data_login');
                    var_dump($res);
                }
        }
        
        if($remeber)
        {
           $CI->session->sess_expiration = (60*60*24*30);
        }

        $CI->session->set_userdata($sessionData);
    }
}
