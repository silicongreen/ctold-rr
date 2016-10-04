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
class champs21header extends widget {

    public $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    function run($ci_key, $ci_key_for_cover) {
        $can_school_canlde = FALSE;
        $this->CI->load->config("huffas");
        $data = array("ci_key" => $ci_key, "ci_key_for_cover" => $ci_key_for_cover);

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
        
        $data['school_icon_class'] = 'icon-my-school';
        if ($user_school_data != FALSE && !empty($user_id)) {

            $school_obj = new schools($user_school_data[0]->school_id);
            if($school_obj->is_paid == 1) {
                $data['school_icon_class'] = 'icon-diary21-school';
            }
            
            $data['my_school_menu_uri'] = base_url() . 'redirect_to_paid_school/' . sanitize($school_obj->name);
        } else {
            $data['my_school_menu_uri'] = base_url() . 'schools';
        }

        $data['user_profile_complete'] = TRUE;

        if (free_user_logged_in()) {

            $user_sess_data = get_free_user_session();

            if (empty($user_sess_data['full_name'])) {
                $data['user_profile_complete'] = FALSE;
            }
        }
        print "here";

        $this->CI->load->config('user_register');
        $b_need_approval = $this->CI->config->config['join_user_approval'][$user_type];
        $b_mulit_school_join = $this->CI->config->config['multi_school_join'];
        print "here2";
        exit;

        $user_school = new User_school();

        $user_school_data = ($b_mulit_school_join) ? $user_school->get_user_school($user_id, $school_id) : $user_school->get_user_school($user_id);

        if ($user_school_data !== FALSE) {
            if ($user_school_data['0']->is_approved == 1) {
                $can_school_canlde = TRUE;
            }
        }

        $data['can_school_canlde'] = $can_school_canlde;
        // User Data

        $this->render($data);
    }

    private function get_country() {
        $country = new Country();
        $country = $country->formatCounrtyForDropdown($country->get_country());

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

