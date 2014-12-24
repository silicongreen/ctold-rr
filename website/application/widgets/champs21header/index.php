<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Widget Plugin 
 * 
 * Install this file as application/plugins/widget_pi.php
 * 
 * @version:     0.1
 * $copyright     Copyright (c) Wiredesignz 2009-03-24
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class champs21header extends widget
{
    public $CI;
    
    public function __construct(){
        $this->CI = & get_instance();
    }

    function run($ci_key, $ci_key_for_cover)
    {   
        $this->CI->load->config("huffas");
        $data = array("ci_key"  => $ci_key, "ci_key_for_cover" => $ci_key_for_cover);
        
        // User Data
        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

        $data['model'] = $this->get_free_user($user_id);
        
        $data['free_user_types'] = $this->get_free_user_types();
        
        $data['country'] = $this->get_country();
        $data['country']['id'] = $data['model']->tds_country_id;

        $data['grades'] = $this->get_grades();

        $data['medium'] = $this->get_medium();
        
        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
        
        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();
        
        $user_school = new User_school();
        $user_school_data = $user_school->get_user_school($user_id);
        
        if( $user_school_data != FALSE && !empty($user_id) ) {
            if(get_free_user_session('paid_id') && get_free_user_session('paid_school_code'))
            {
                $paid_id = get_free_user_session('paid_id');
                $paid_username = get_free_user_session('paid_username');
                $paid_password = get_free_user_session('paid_password');
                
                $user_rand = $this->CI->input->cookie("auth_".$paid_id);
                if($user_rand)
                {
                    $random = $user_rand;
                }  
                else
                {    
                    $random = md5(rand());

                    $insert['auth_id'] = $random;
                    $insert['user_id'] = $paid_id;
                    $insert['expire'] = date("Y-m-d H:i:s",  strtotime("+1 Day"));

                    $this->CI->db->insert("user_auth",$insert);
                    $cookie = array(
                        'name'   => "auth_".$paid_id,
                        'value'  => $random,
                        'expire' => '82800',
                        'domain' => '.stage.champs21.com',
                        'path'   => '/'
                    );

                    $this->CI->input->set_cookie($cookie);
                   
                }
                
                $params = "?username=".$paid_username."&password=".$paid_password."&auth_id=".$random."&user_id=".$paid_id;
                $data['my_school_menu_uri'] = "http://".get_free_user_session('paid_school_code').".plus.champs21.com".$params;
            } 
            else
            {    
                $school_obj = new schools($user_school_data[0]->school_id);
                $data['my_school_menu_uri'] = base_url().'schools/' . sanitize($school_obj->name);
            }
        } else {
            $data['my_school_menu_uri'] = base_url().'schools';
        }
        
        // User Data
        
        $this->render($data);
        
    }
    
    private function get_country() {
        $country = new Country();
        $country = $country->formatCounrtyForDropdown($country->get());
        
        return $country;
    }
    
    private function get_grades() {
        
        $grades = new Grades();
        return $grades->getActiveGrades();
    }
    
    private function get_medium() {
        $this->CI->load->config("user_register");
        return $this->CI->config->config['medium'];
    }
    
    private function get_free_user_types() {
        $this->CI->load->config("user_register");
        return $this->CI->config->config['free_user_types'];
    }
    
    private function get_free_user($id = null) {
        return (!empty($id)) ? new Free_users($id) : new Free_users();
    }
    
    
    
 
}

