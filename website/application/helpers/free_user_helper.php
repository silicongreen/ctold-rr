<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if (!function_exists("get_user_school_joined")) {

    function get_user_school_joined() {
        $CI = &get_instance();

        if (free_user_logged_in() && get_free_user_session('paid_id')) {
            $user_id = get_free_user_session("id");
            $CI->db->select("paid_school_id");
            $CI->db->where("id", $user_id);
            $result = $CI->db->get("free_users")->row();
            if (count($result) > 0 && $result->paid_school_id) {
                $CI->db->select("name");
                $CI->db->where("paid_school_id", $result->paid_school_id);
                $school_data = $CI->db->get("school")->row();
                return $school_data->name;
            } else {
                return false;
            }
        } else if (free_user_logged_in()) {

            $user_id = get_free_user_session("id");
            $CI->db->select("school_id,is_approved,deny_by,type");
            $CI->db->where("user_id", $user_id);

            $result = $CI->db->get("user_school")->row();



            if (count($result) > 0) {
                $CI->db->select("name");
                $CI->db->where("id", $result->school_id);
                $school_data = $CI->db->get("school")->row();
                return $school_data->name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}


if (!function_exists("get_user_school")) {

    function get_user_school($school_id = 0) {
        if (free_user_logged_in()) {
            $CI = &get_instance();
            $user_id = get_free_user_session("id");
            $CI->db->select("is_approved,deny_by,type");
            $CI->db->where("user_id", $user_id);
            if ($school_id) {
                $CI->db->where("school_id", $school_id);
                $CI->db->limit(1);
                $result = $CI->db->get("user_school")->row();
            } else {
                $result = $CI->db->get("user_school")->result();
            }
            if (count($result) > 0) {
                return $result;
            } else {
                return false;
            }
        } else {
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
if ( ! function_exists('user_admission_top'))
{
	function user_admission_top()
	{
            return '<div class="text-fields-wrapper">
                <div>
                    <img src="/styles/layouts/tdsfront/image/privacy_policy.png" />
                </div>
         </div>';
	}
}
if ( ! function_exists('user_admission_right'))
{
	function user_admission_right()
	{
            return '<div class="createpage_right">
                <img src="'.base_url('Profiler/images/right/SB-Web-300x600.jpg').'" style="width:100%;" />
                
                <p>All your 
                <span class="a">Information</span> need to be
                <span class="b">Parfect</span>.
                </p>
            </div>';
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

    function set_user_sessions($obj_user, $pwd = NULL, $remember = false, $b_refresh_cookie = false) {

        $CI = &get_instance();

        $remeber = TRUE;
        $sessionData['free_user']['id'] = $obj_user->id;
        $sessionData['free_user']['mobile_no'] = $obj_user->mobile_no;
        $sessionData['free_user']['email'] = $obj_user->email;

        $sessionData['free_user']['nick_name'] = $obj_user->first_name;
        if ($obj_user->nick_name == 2) {
            $sessionData['free_user']['nick_name'] = $obj_user->middle_name;
        } elseif ($obj_user->nick_name == 3) {
            $sessionData['free_user']['nick_name'] = $obj_user->last_name;
        }

        $sessionData['free_user']['full_name'] = $obj_user->first_name . ' ' . $obj_user->middle_name . ' ' . $obj_user->last_name;
        $sessionData['free_user']['type'] = $obj_user->user_type;

        $sessionData['free_user']['first_name'] = $obj_user->first_name;
        $sessionData['free_user']['middle_name'] = $obj_user->middle_name;
        $sessionData['free_user']['last_name'] = $obj_user->last_name;
        $sessionData['free_user']['dob'] = $obj_user->dob;
        $sessionData['free_user']['bng_pwd'] = $pwd;
        $sessionData['free_user']['country_id'] = $obj_user->tds_country_id;
        $sessionData['free_user']['division'] = $obj_user->division;
        $sessionData['free_user']['is_joined_spellbee'] = $obj_user->is_joined_spellbee;
        $sessionData['free_user']['school_name'] = $obj_user->school_name;
        $sessionData['free_user']['gender'] = $obj_user->gender;

        $sessionData['free_user']['paid_id'] = $obj_user->paid_id;
        $sessionData['free_user']['paid_username'] = $obj_user->paid_username;
        $sessionData['free_user']['paid_password'] = $obj_user->paid_password;
        $sessionData['free_user']['paid_school_code'] = $obj_user->paid_school_code;
        $sessionData['free_user']['paid_school_id'] = $obj_user->paid_school_id;
        $sessionData['free_user']['profile_image'] = $obj_user->profile_image;
        
        $CI->session->set_userdata($sessionData);

        
//        if ($remember || $b_refresh_cookie) {
//
//            $cookie_key = get_session_key();
//            $cookie_token = get_session_cookie_token($obj_user, $cookie_key);
//            set_session_cookie($cookie_token);

            $cookie_data = array(
//                'cookie_token' => $cookie_token,
                'cookie_token' => $_COOKIE['champs_session'],
//                'cookie_key' => $cookie_key,
//                'cookie_expire' => date('Y-m-d', time() + 2592000)
            );

            $CI->db->where('id', $obj_user->id);
            $CI->db->update('tds_free_users', $cookie_data);
//        }
        
        set_type_cookie($obj_user->user_type);
    }

}
