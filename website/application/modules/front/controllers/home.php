<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class home extends MX_Controller {

    public function __construct() {
        parent::__construct();

        $ar_segmens = $this->uri->segment_array();

//        if (empty($ar_segmens) )
//        {
//            $cache_name = "home/CONTENT_CACHE_LAYOUT_" . str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
//            if ( $s_content = $this->cache->file->get($cache_name) )
//            {
//                echo $s_content;
//                exit;
//            }
//        }

        $this->load->database();
        $this->load->library('datamapper');
        $this->load->helper('form');


        $this->load->config("huffas");

        if (free_user_logged_in() && isset($_COOKIE['champs_session'])) {
            $this->db->where("cookie_token", $_COOKIE['champs_session']);

            $user_data_valid = $this->db->get("free_users");

            if ($user_data_valid->num_rows() < 1) {
                $this->logout_user();
            }
        }


//        $ar_accept_without_cookie = $this->config->config['accept_without_cookie'];
//        $sess_cookie = $_COOKIE['c21_session'];
        
//        if(!free_user_logged_in() && !empty($sess_cookie) && !in_array($this->router->fetch_method(), $ar_accept_without_cookie) ) {
//            $this->validate_cookie($sess_cookie);
//        }

        $ar_not_loggable = $this->config->config['not_loggable'];
        if(!empty($ar_segmens[1]) && !in_array($ar_segmens[1], $ar_not_loggable)) {
            $uri_segments = explode('-', $ar_segmens[1]);
            if(!is_numeric(end($uri_segments))) {
                $this->load->model('Activity_logs', 'log');
                $this->log->record('home', $ar_segmens[1]);
            }
        }
    }

    function search_full_site() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->layout_front = false;
        $this->db->select("*");
        $this->db->like("headline", $this->input->post("searchword"));
        $this->db->where("status", 5);
        $this->db->where("post_type", 1);
        $this->db->order_by("published_date", "DESC");
        $this->db->limit(7);
        $posts = $this->db->get("post");
        if (count($posts->result()) > 0) {
            echo '<div class="display_box" style="float:left; clear:both; width:100%;background-color: #C9364A;
    color: white;
    padding: 10px" align="left"><b style="font-size:15px;">News</b></div>';
            foreach ($posts->result() as $values) {
                $fb_image = getImageForFacebook($values);
                echo '<a href="' . create_link_url("index", $values->headline, $values->id) . '" style="float:left; clear:both; width:100%;">';

                echo '<div class="display_box" align="left" style="float:left; clear:both; width:100%;">';
                if ($fb_image) {
                    echo '<img src="' . $fb_image . '" style="width:50px; height:50px; float:left; margin-right:6px;" />';
                }
                echo '<span style="font-size:13px; color:black" class="name">' . $values->headline . '</span>';
                echo '<br/><span style="font-size:11px; color:#999999">Published: ' . $values->published_date . '</span>';
                echo '</div>';
                echo '</a>';
            }
        }



        $this->db->select("*");
        $this->db->like("name", $this->input->post("searchword"));
        $posts = $this->db->get("school");
        if (count($posts->result()) > 0) {
            echo '<div class="display_box" style="float:left; clear:both; width:100%;background-color: #C9364A;
    color: white;
    padding: 10px" align="left"><b style="font-size:15px;">Schools</b></div>';
            foreach ($posts->result() as $values) {
                $fb_image = $values->logo;
                echo '<a href="' . base_url() . 'schools/' . sanitize($values->name) . '" style="float:left; clear:both; width:100%;">';
                echo '<div class="display_box" align="left" style="float:left; clear:both; width:100%;">';
                if ($fb_image) {
                    echo '<img src="' . base_url($fb_image) . '" style="width:50px; height:50px; float:left; margin-right:6px;" />';
                }

                echo '<span style="font-size:13px; color:black" class="name">' . $values->name . '</span>';
                echo '<br/><span style="font-size:11px; color:#999999">Published: ' . $values->district . '&nbsp;' . $values->location . '</span>';
                echo '</div>';
                echo '</a>';
            }
        }
    }

    public function school_list() {
        $data['ci_key'] = 'school_list';

        $sb_db = $this->load->database('sb', TRUE);

        $sb_db->from('sites');
        $sb_db->where('sites_trashed', 0);
        $sb_db->where('sites_published', 1);
        $qry = $sb_db->get();
        $data['schooldata'] = $qry->result_array();

        $s_content = $this->load->view('school_list', $data, true);

        // User Data
        $data['join_user_types'] = $this->get_school_join_user_types();
        // User Data

        $s_right_view = '';

        $str_title = "School List";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schools",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function create_school_website() {

        $data['all_ar_templates'] = $this->config->config['school_templates'];



        $s_content = $this->load->view('create_school_website', $data, true);

        // User Data
        $data['join_user_types'] = $this->get_school_join_user_types();
        // User Data

        $s_right_view = '';

        $str_title = "New School";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schools",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function demo_school_template() {
        $data['all_ar_templates'] = $this->config->config['school_templates'];



        $this->load->view('demo_school_template', $data);

        $this->layout_front = false;
    }

    public function submit_new_school() {

        $data['all_ar_templates'] = $this->config->config['school_templates'];

        if ($this->input->post('template_id')) {
            $User_school_information = new User_school_information();

            $User_school_information->name = $this->input->post('full_name');
            $User_school_information->email = $this->input->post('email_addr');
            $User_school_information->school_name = $this->input->post('school_name');
            $User_school_information->school_address = $this->input->post('school_addr');
            $User_school_information->phone = $this->input->post('phone_number');
            $User_school_information->hphone = $this->input->post('home_phone');
            $User_school_information->about_school = $this->input->post('school_about');
            $User_school_information->admission_details = $this->input->post('school_admission');
            $User_school_information->facilities = $this->input->post('school_facilities');
            $User_school_information->achievement = $this->input->post('school_achievements');
            $User_school_information->template_id = $this->input->post('template_id');



            if ($User_school_information->save()) {
                $file_data = array();
                if (isset($_FILES['school_image'])) {
                    $school_image = $this->doUpload("school_image");
                    $file_data[] = array("school_id" => $User_school_information->id, "file_location" => $school_image, "file_type" => "image");
                    //$User_school_information->image_location = $school_image;
                }
                if (isset($_FILES['school_file'])) {
                    $school_file = $this->doUpload("school_file");
                    $file_data[] = array("school_id" => $User_school_information->id, "file_location" => $school_file, "file_type" => "file");
                    //$User_school_information->file_location = $school_file;
                }
                if ($file_data) {
                    $this->db->insert_batch('user_school_file', $file_data);
                }

                redirect('/successfully-school-information_send?id=' . $this->input->post('template_id'));
            } else {
                redirect('/submit-new-school?id=' . $this->input->post('template_id'));
            }
        }

        if (!isset($_GET['id']) || !isset($data['all_ar_templates'][$_GET['id']])) {
            redirect('/create-school-website');
        }

        $id = $_GET['id'];
        $data['ci_key'] = 'new_school';

        $data['ar_templates'] = $data['all_ar_templates'][$id];

        $s_content = $this->load->view('new_school_frm', $data, true);

        // User Data
        $data['join_user_types'] = $this->get_school_join_user_types();
        // User Data

        $s_right_view = '';

        $str_title = "New School";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schools",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    function join_to_school() {

        if ($this->input->is_ajax_request()) {

            $b_need_approval = TRUE;
            $additional_info = array();
            $response = array(
                'saved' => false,
            );

            $user_id = get_free_user_session('id');
            $user_type = get_free_user_session('type');

            if ($user_type == 1) {
                $user_type = $this->input->post('user_type');
            }

            if ($user_type == 2) {
                $activaiton_code = $this->input->post('admission_no');
                $paid_school_id = $this->input->post('paid_school_id');
                unset($_POST['admission_no']);
            }

            $school_id = $this->input->post('school_id');
            $grade = $this->input->post('grade_ids');

            unset($_POST['school_id']);
            unset($_POST['grade_ids']);

            if (empty($activaiton_code) && !empty($paid_school_id)) {

                $response['errors'][] = 'Invalid Activation Code.';

                echo( json_encode($response));
                exit;
            } elseif (!empty($activaiton_code) && !empty($paid_school_id)) {

                $this->db->dbprefix = '';

                $this->db->select('*');
                $this->db->from('student_activation_codes');
                $this->db->where('is_active', 1);
                $this->db->where('student_id', 0);
                $this->db->where('code', $activaiton_code);
                $this->db->where('school_id', $paid_school_id);
                $query = $this->db->get();

                if ($query->num_rows() <= 0) {

                    $response['errors'][] = 'Invalid Activation Code.';

                    echo( json_encode($response));
                    exit;
                }

                $this->db->dbprefix = 'tds_';
            }

            /* Additional Information */
            $additional_info = json_encode($this->input->post());
            /* Additional Information */

            $this->load->config('user_register');
            $b_need_approval = $this->config->config['join_user_approval'][$user_type];
            $b_mulit_school_join = $this->config->config['multi_school_join'];

            $user_school = new User_school();

            $user_school_data = ($b_mulit_school_join) ? $user_school->get_user_school($user_id, $school_id) : $user_school->get_user_school($user_id);

            if ($user_school_data === FALSE) {

                $User_school = new User_school;
                $User_school->user_id = $user_id;
                $User_school->school_id = $school_id;
                $User_school->grade = implode(',', $grade);
                $User_school->type = $user_type;
                $User_school->is_approved = ( $b_need_approval ) ? '0' : '1';
                $User_school->information = $additional_info;

                if ($User_school->save()) {

                    $ar_email['sender_full_name'] = 'Champs21';
                    $ar_email['sender_email'] = 'info@champs21.com';
                    $ar_email['to_name'] = get_free_user_session('full_name');
                    $ar_email['to_email'] = get_free_user_session('email');
                    $ar_email['html'] = true;

                    $ar_email['subject'] = 'Join to school';
                    $ar_email['message'] = $this->get_welcome_message($ar_email['to_name'], false, true);
                    send_mail($ar_email);

                    $response = array(
                        'saved' => true,
                        'is_approved' => $User_school->is_approved,
                        'activaiton_code' => $activaiton_code
                    );
                } else {
                    $response['errors'] = $User_school->error->all;
                }
            } else {
                $response['errors'][] = 'You cannot join more than one school. Please leave the previous school to join new school.';
//                $response['errors'][] = 'You are already a member of this school.';
            }

            echo( json_encode($response));
            exit;
        }
    }

    function leave_school() {

        if ($this->input->is_ajax_request()) {

            $b_need_approval = TRUE;
            $additional_info = array();
            $response = array(
                'saved' => false,
                'left' => false
            );

            $user_id = get_free_user_session('id');
            $school_id = $this->input->post('school_id');

            unset($_POST['school_id']);

            $this->db->where('user_id', $user_id);
            $this->db->where('school_id', $school_id);
            $this->db->delete('user_school');

            if ($this->db->affected_rows() > 0) {
                $response = array(
                    'saved' => true,
                    'left' => true
                );
            } else {
                $response['errors'] = $user_school->error->all;
            }


            echo( json_encode($response));
            exit;
        }
    }

    function redirect_to_paid_school() {
        if (get_free_user_session('paid_id') && get_free_user_session('paid_school_code')) {
            $paid_id = get_free_user_session('paid_id');
            $paid_username = get_free_user_session('paid_username');
            $paid_password = get_free_user_session('paid_password');

            $user_rand = $this->cache->file->get("auth_" . $paid_id);
            if ($user_rand) {
                $random = $user_rand;
            } else {
                $random = md5(rand());

                $insert['auth_id'] = $random;
                $insert['user_id'] = $paid_id;
                $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));

                $this->db->insert("user_auth", $insert);

                $this->cache->file->save("auth_" . $paid_id, $random, 82800);
            }

            $params = "?username=" . $paid_username . "&password=" . $paid_password . "&auth_id=" . $random . "&user_id=" . $paid_id;
            $school_dn = get_paid_domain();
            
            $url = "http://{$school_dn}" . $params;
            header("Location: " . $url);
            exit;
        }
    }

    function schools() {
        $b_frame = $_GET['iframe'];

        $ar_segmens = $this->uri->segment_array();
        if ($b_frame == '1') {

            $data['ci_key'] = 'schools';

            $school_name = $ar_segmens[2];
            $data['school_name'] = $school_name;

//            $body = get_school_page($school_name);
//            $body = str_replace('"bootstrap/css/', '"http://schoolpage.champs21.com/'.$school_name.'/bootstrap/css/', $body);
//            $body = str_replace('"css/', '"http://schoolpage.champs21.com/'.$school_name.'/css/', $body);
//            $body = str_replace('"js/', '"http://schoolpage.champs21.com/'.$school_name.'/js/', $body);
//            $data['school_page_body'] = $body;

            $s_content = $this->load->view('schools_frame', $data, true);

            // User Data
            $data['join_user_types'] = $this->get_school_join_user_types();
            // User Data

            $s_right_view = '';

            $str_title = "Schools";
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';
            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;
            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "title" => $str_title,
                "description" => $meta_description,
                "keywords" => $keywords,
                "side_bar" => $s_right_view,
                "target" => "schools",
                "fb_contents" => NULL,
                "content" => $s_content
            );

            $this->extra_params = $ar_params;
        } else if (count($ar_segmens) < 2) {
            //$this->show_404_custom();
            $joined_school = get_user_school_joined();

            if ($joined_school) {
                redirect("schools/" . sanitize($joined_school));
            }
            $this->db->select('*');
            $this->db->from('tds_school');

            ($this->input->post('name') != "") ? $this->db->like('name', $this->input->post('name'), 'after') : '';
            ($this->input->post('district') != "") ? $this->db->or_like('division', $this->input->post('division'), 'after') : '';
            ($this->input->post('level') != "") ? $this->db->or_like('level', $this->input->post('level'), 'after') : '';
            ($this->input->get('str') != "") ? $this->db->like('name', $this->input->get('str'), 'after') : '';
            
            $this->db->where("is_visible",1);

            $this->db->order_by("is_paid DESC, name ASC");

            $query = $this->db->get();

            $free_schools = $query->result_array();

            $data['schooldata'] = $free_schools;

            $data['ci_key'] = 'schools';

            // User Data
            $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

            $data['model'] = $this->get_free_user($user_id);

            $data['free_user_types'] = $this->get_free_user_types();
            $data['join_user_types'] = $this->get_school_join_user_types();

            $data['country'] = $this->get_country();
            $data['country']['id'] = $data['model']->tds_country_id;

            $data['grades'] = $this->get_grades();

            $data['medium'] = $this->get_medium();

            $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;

            $user_school = new User_school();
            $user_school_data = $user_school->get_user_school($user_id);

            if ($user_school_data) {
                foreach ($user_school_data as $row) {
                    $data['user_school_ids'][] = $row->school_id;
                    $data['user_school_status'][$row->school_id] = $row->is_approved;
                }
            }

            $obj_post = new Posts();
            $data['category_tree'] = $obj_post->user_preference_tree_for_pref();
            // User Data

            $s_content = $this->load->view('schools_all', $data, true);

            //has some work in right view
            $s_right_view = $this->load->view('right', $data, TRUE);
            //echo "<pre>";
            //print_r($data);

            $str_title = "Schools";
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';
            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;
            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "title" => $str_title,
                "description" => $meta_description,
                "keywords" => $keywords,
                "side_bar" => $s_right_view,
                "target" => "schools",
                "fb_contents" => NULL,
                "content" => $s_content
            );

            $this->extra_params = $ar_params;
        } else {
            $school_name = unsanitize($ar_segmens[2]);
            $school_obj = new schools();
            $school_menu_id = 0;
            if ($school_details = $school_obj->find_school_by_name($school_name)) {
                $userschool = get_user_school($school_details->id);
                if (!isset($ar_segmens[3])) {

                    if ($userschool) {
                        if ($userschool->is_approved == 1 || $userschool->is_approved == 0) {
                            redirect("schools/" . $ar_segmens[2] . "/feed");
                        } else {
                            $school_menu_id = 1;
                            $menu_details = $school_obj->find_default_school_menu($school_details->id);
                        }
                    } else {
                        $school_menu_id = 1;
                        $menu_details = $school_obj->find_default_school_menu($school_details->id);
                    }
                } else {
                    $menu_name = unsanitize($ar_segmens[3]);

                    if ($menu_details = $school_obj->find_menu_by_name($menu_name)) {
                        $school_menu_id = $menu_details->id;
                    } else if ($ar_segmens[3] == "activities" && isset($ar_segmens[4])) {
                        $activity_details = $school_obj->find_activity_details($ar_segmens[4]);
                    } else if ($ar_segmens[3] == "activities" && !isset($ar_segmens[4])) {

                        $activities = $school_obj->find_all_ativity($school_details->id);
                    } else if ($ar_segmens[3] == "feed") {
//                        if(!$userschool || $userschool->is_approved==0)
                        if (!$userschool) {

                            redirect("schools/" . $ar_segmens[2]);
                        } else {
                            $feed = true;
                        }
                    } else {
                        $this->show_404_custom();
                    }
                }

                if ((isset($menu_details) && count($menu_details) > 0) || (isset($activity_details) && count($activity_details) > 0) || (isset($activities) && count($activities) > 0 || isset($feed) )
                ) {
                    $this->load->helper('cookie');

                    $cookie_set = $this->input->cookie("school_views_" . $school_details->id, false);

                    if (!$cookie_set) {
                        $cookie = array(
                            'name' => "school_views_" . $school_details->id,
                            'value' => 'yes',
                            'expire' => 886500,
                            'secure' => false
                        );
                        $school_obj->increament_views($school_details->id);
                        $this->input->set_cookie($cookie);
                    }
                    $schools_pages = $school_obj->find_school_pages($school_details->id);

                    if (count($schools_pages) == 0) {
                        $this->show_404_custom();
                    } else {
                        if (isset($menu_details) && count($menu_details) > 0) {
                            $page_details = $school_obj->find_page_details($school_menu_id, $school_details->id);
                        } else if (isset($activity_details) && count($activity_details) > 0) {
                            $page_details = $activity_details;
                        } else {
                            $page_details = false;
                        }


                        $data['school_details'] = $school_details;
                        $data['school_page_details'] = $page_details;
                        $data['schools_pages'] = $schools_pages;
                        if (count($menu_details) > 0) {

                            $data['menu_details'] = $menu_details;
                            $data['gallery'] = $school_obj->find_page_gallery($page_details->id);
                        } else if (isset($activity_details) && count($activity_details) > 0) {
                            $menu_details->title = "Activity";
                            $data['activity_link'] = true;
                            $data['menu_details'] = $menu_details;
                            $data['gallery'] = $school_obj->find_activity_gallery($activity_details->id);
                        } else if (isset($feed)) {

                            $data['feeds'] = $feed;
                            $data['menu_details'] = $menu_details;
                        } else {
                            $menu_details->title = "Activity";
                            $data['menu_details'] = $menu_details;
                        }




                        if (isset($school_menu_id) && $school_menu_id == 1) {
                            $data['activities'] = $school_obj->getActivities($school_details->id);
                        } else if (isset($activities) && count($activities) > 0) {
                            $data['activities'] = $activities;
                        }

                        // User Data
                        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                        $data['model'] = $this->get_free_user($user_id);

                        $data['free_user_types'] = $this->get_free_user_types();
                        $data['join_user_types'] = $this->get_school_join_user_types();

                        $data['country'] = $this->get_country();
                        $data['country']['id'] = $data['model']->tds_country_id;

                        $data['grades'] = $this->get_grades();

                        $data['medium'] = $this->get_medium();

                        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;

                        $user_school = new User_school();
                        $user_school_data = $user_school->get_user_school($user_id);

                        if ($user_school_data) {
                            foreach ($user_school_data as $row) {
                                $data['user_school_ids'][] = $row->school_id;
                                $data['user_school_status'][$row->school_id] = $row->is_approved;
                            }
                        }
                        // User Data

                        $data['ci_key'] = $school_details->name;
                        $data['ci_key_for_cover'] = $school_details->name;


                        $s_content = $this->load->view('school', $data, true);

                        $obj_post = new Posts();
                        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();

                        //has some work in right view
                        $s_right_view = $this->load->view('right', $data, TRUE);

                        $str_title = $school_details->name . " > " . $menu_details->title;
                        $ar_js = array();
                        $ar_css = array();
                        $extra_js = '';
                        $meta_description = $school_details->name . " > " . $menu_details->title;
                        $keywords = $school_details->name . " > " . $menu_details->title;
                        $fb_contents['description'] = $school_details->name . " > " . $menu_details->title;

                        $logo_image = base_url() . "images/backgrounds/bg_content.png";
                        if ($school_details->logo) {

                            $logo_image_url = base_url() . $school_details->logo;
                            list($width, $height, $type, $attr) = @getimagesize($logo_image_url);
                            if (isset($width)) {
                                $logo_image = $logo_image_url;
                            }
                        }


                        $fb_contents['image'] = $logo_image;
                        $ar_params = array(
                            "javascripts" => $ar_js,
                            "css" => $ar_css,
                            "extra_head" => $extra_js,
                            "title" => $str_title,
                            "description" => $meta_description,
                            "keywords" => $keywords,
                            "side_bar" => $s_right_view,
                            "target" => "index",
                            "fb_contents" => $fb_contents,
                            "content" => $s_content
                        );

                        $this->extra_params = $ar_params;
                    }
                } else {
                    $this->show_404_custom();
                }
            } else {
                $this->show_404_custom();
            }
        }
    }

    function show_404_custom() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $data = array();
        $data['ci_key'] = "error_page";
        $data['ci_key_for_cover'] = "error_page";
        $s_content = $this->load->view('error_page', $data, true);

        // User Data
        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

        $data['model'] = $this->get_free_user($user_id);

        $data['free_user_types'] = $this->get_free_user_types();

        $data['country'] = $this->get_country();
        $data['country']['id'] = $data2['model']->tds_country_id;

        $data['grades'] = $this->get_grades();

        $data['medium'] = $this->get_medium();

        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
        // User Data
        $obj_post = new Posts();

        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();

        $s_right_view = $this->load->view('right', $data, TRUE);

        $str_title = getCommonTitle();

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "index",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    function download() {
        $str_f_path = filter_var($_GET['f_path'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_SPECIAL_CHARS);

//        $finfo = new finfo(FILEINFO_MIME);
//        $type = $finfo->file($str_f_path);
        $type = getFileType($str_f_path);
        $ar_str_f_path = end(explode('/', $str_f_path));

        header("Content-Disposition: attachment; filename=" . sanitize($ar_str_f_path));
        header("Content-Type: {$type}");
        header("Content-Length: " . filesize($str_f_path));
        readfile($str_f_path);
    }

    function index() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "index";
        $data['ci_key_for_cover'] = "index";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('home', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }



        $str_title = getCommonTitle();


        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;

        $fb_contents['title'] = META_DESCRIPTION;
        $fb_contents['site_name'] = "Champs21";
        $fb_contents['description'] = META_DESCRIPTION;
        $fb_contents['type'] = "website";
        $fb_contents['url'] = "http://www.champs21.com/";
        $fb_contents['image'] = base_url() . "styles/layouts/tdsfront/images/c-21.jpg";

        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "index",
            "fb_contents" => $fb_contents,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    function good_read($s_folder_name = "") {
        if (free_user_logged_in()) {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();
            $this->load->model("user_folder");
            $i_user_id = get_free_user_session("id");
            $s_folder_name = ($s_folder_name == "") ? "Unread" : $s_folder_name;
            $ar_folder_id_data = $this->user_folder->get_folder_id($i_user_id, $s_folder_name, FALSE);
            $i_folder_id = $ar_folder_id_data->id;
            $data['folder_visible'] = $ar_folder_id_data->visible;
            $ar_user_folder = $this->user_folder->get_user_good_read_folder($i_user_id, 0, 30);

            $arFdata = array();
            if ($ar_folder_id_data->visible == 1) {
                $i = 1;
                foreach ($ar_user_folder['data'] as $folder) {
                    if ($i_folder_id == $folder->id) {
                        $selected_fdata = $folder;
                    } else {
                        $arFdata[$i] = $folder;
                    }

                    $i++;
                }

                array_unshift($arFdata, $selected_fdata);
            } else {
                $arFdata = $ar_user_folder['data'];
            }
            $data['i_user_folder_count'] = $ar_user_folder['total'];
            $data['ar_user_folder'] = $arFdata;
            //$obj_post_data->good_read_single = $this->load->view( 'good_read_single', $data, TRUE );


            if ($i_folder_id != 0) {
                $folder_data = $this->user_folder->get_user_good_read_post_count($i_user_id, $i_folder_id, $data['folder_visible'], $s_folder_name);
                $data['totalpost'] = $folder_data['totalpost'];
                $data['selected_folder_id'] = $i_folder_id;
                $data['selected_folder_name'] = $s_folder_name;
            } else {
                $data['totalpost'] = 0;
                $data['selected_folder_id'] = 1;
                $data['selected_folder_name'] = $s_folder_name;
            }


            $data['ci_key'] = "index";
            $data['ci_key_for_cover'] = "index";
            $data['s_category_ids'] = "0";

            $this->load->config("user_register");
            $data['dfolders'] = $this->config->config['free_user_folders'];

            $s_content = $this->load->view('good_read', $data, true);

            $s_right_view = "";
            $cache_name = "common/right_view";
            if (!$s_widgets = $this->cache->file->get($cache_name)) {


                $this->db->where('is_enabled', 1);
                $query = $this->db->get('widget');

                $obj_widgets = $query->result();

                if ($obj_widgets) {
                    $data2['post_details'] = 0;
                    $data2['widgets'] = $obj_widgets;
                    $data2['cartoon'] = true;

                    // User Data
                    $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                    $data2['model'] = $this->get_free_user($user_id);

                    $data2['free_user_types'] = $this->get_free_user_types();

                    $data2['country'] = $this->get_country();
                    $data2['country']['id'] = $data2['model']->tds_country_id;

                    $data2['grades'] = $this->get_grades();

                    $data2['medium'] = $this->get_medium();

                    $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                    // User Data

                    $obj_post = new Posts();
                    $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();

                    $s_right_view = $this->load->view('right', $data2, TRUE);
                    $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
                }
            } else {
                $s_right_view = $s_widgets;
            }



            $str_title = getCommonTitle();


            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;
            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "title" => $str_title,
                "description" => $meta_description,
                "keywords" => $keywords,
                "side_bar" => $s_right_view,
                "target" => "index",
                "fb_contents" => NULL,
                "content" => $s_content
            );

            $this->extra_params = $ar_params;
        } else {
            redirect("/");
        }
    }

    function __inner($i_category_id, $s_category_name, $b_popular = FALSE) {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $data = array();


        /**
         * Get Category and Sub-category first for a single post
         */
        $obj_parent_category = get_parent_category($i_category_id);

        if ($obj_parent_category->id != $i_category_id) {
            $data['a_category_ids'] = array($i_category_id);
        }

        if (!$obj_parent_category) {
            $this->show_404_custom();
        }

        $data['name'] = $obj_parent_category->name;

        $data['display_name'] = $obj_parent_category->display_name;

        $data['has_categories'] = FALSE;

        $data['category_name'] = $s_category_name;


        $obj_category = new Category_model($i_category_id);

        $this->load->config("huffas");
        if (($category_config = $this->config->config[sanitize($s_category_name)])) {
            if (isset($category_config['hide_category'])) {
                $obj_child_categories = $obj_category->where("parent_id", $obj_parent_category->id)->where("status", 1)->where("id NOT IN (" . $category_config['hide_category'] . ")")->order_by("priority", "asc")->get();
                $data['obj_child_categories'] = $obj_child_categories;
            } else {
                $obj_child_categories = $obj_category->where("parent_id", $obj_parent_category->id)->where("status", 1)->order_by("priority", "asc")->get();
                $data['obj_child_categories'] = $obj_child_categories;
            }
        } else {
            $obj_child_categories = $obj_category->where("parent_id", $obj_parent_category->id)->where("status", 1)->order_by("priority", "asc")->get();
            $data['obj_child_categories'] = $obj_child_categories;
        }
        if (count($obj_child_categories->all) > 0) {
            $data['has_categories'] = TRUE;
        }

        if ($b_popular) {
            $data['ci_key'] = "inner-popular";
            $data['ci_key_for_cover'] = sanitize($s_category_name) . "-popular";
        } else {
            $data['ci_key'] = "inner";
            $data['ci_key_for_cover'] = sanitize($s_category_name);
        }
        $data['popular'] = $b_popular;
        $data['s_category_ids'] = $i_category_id;
        $data['hide_top_breadcrumb'] = (isset($this->config->config['hide-top-breadcrumb'][sanitize($s_category_name)])) ? $this->config->config['hide-top-breadcrumb'][sanitize($s_category_name)] : FALSE;

        if ($obj_parent_category->game_type == 1) {
            $s_content = $this->load->view('inner_page_game', $data, true);
        } elseif ($obj_parent_category->game_type == 2) {
            $s_content = $this->load->view('inner_page_video', $data, true);
        } else {
            $s_content = $this->load->view('inner_page_new', $data, true);
        }

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }



        $str_title = getCommonTitle();


        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "index",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    function get_external_scripts($type = "js") {
        $s_script_content = file_get_contents($_GET['script']);
        $headers['Content-Length'] = strlen($s_script_content);
        $headers['Expires'] = gmdate("D, d M Y H:i:s", time() + 86400 * 30) . " GMT";
        $headers['Cache-control'] = "max-age=2592000";
        if ($type == "js") {
            $headers['Content-Type'] = 'application/x-javascript; charset=utf-8';
        }
        if ($type == "css") {
            $headers['Content-Type'] = 'text/css; charset=utf-8';
        }
        //$headers['Content-Encoding'] = "gzip";
        $headers['Vary'] = 'Accept-Encoding';
        $headers['ETag'] = time() + 86400 * 30;
        foreach ($headers as $name => $val) {
            header($name . ': ' . $val);
        }
        echo $s_script_content;
    }

    function process_post_view($i_category_id, $obj_post_data, $b_layout = true) {
        $ar_js = array("scripts/post/jquery.social.share.2.0.js", "scripts/post/scripts.js", "scripts/post/jquery.social.share.2.0.js");
        $ar_css = array(
            "styles/layouts/tdsfront/css/post/social.css" => "screen"
        );

        $cache_name = "POST" . '_' . $obj_post_data->post_id;
        $s_content = $this->cache->get($cache_name);
        
        $s_content = false;
        if ($s_content !== false) {
            $s_content = $s_content;
        } else {

            $this->load->config("huffas");
            $obj_post = new Post_model();

            //TAKE THE GOOD READ FOLDER IN CASE USER LOGIN
            $obj_post_data->good_read_single = "";
            $obj_post_data->attempt = FALSE;
            $user_played_levels = array();

            if (free_user_logged_in()) {
                $this->load->model("user_folder");
                $i_user_id = get_free_user_session("id");
                $ar_user_folder = $this->user_folder->get_user_good_read_folder($i_user_id);
                $folder_name = "Unread";
                $folder_data = $this->user_folder->get_folder_id($i_user_id, $folder_name);
                $folder_id = $folder_data->id;
                $this->user_folder->set_unread_post_to_read($i_user_id, $obj_post_data->post_id, $folder_id);
                $data['i_user_folder_count'] = $ar_user_folder['total'];
                $data['ar_user_folder'] = $ar_user_folder['data'];
                $obj_post_data->good_read_single = $this->load->view('good_read_single', $data, TRUE);


                //IN CASE POST_TYPE = 4
                if ($obj_post_data->post_type == 4) {
                    $this->db->where('user_id', $i_user_id, FALSE);
                    $this->db->where('post_id', $obj_post_data->post_id);

                    $this->db->from("user_gk_answers");
                    $query = $this->db->get();

                    if ($query->num_rows() > 0) {
                        $row = $query->row();
                        $obj_post_data->attempt = TRUE;
                        $obj_post_data->is_correct = $row->is_correct;
                        $obj_post_data->user_answer = $row->user_answer;
                    }
                }

                /* User Assessment Data */
                $obj_post_data->assess_user_mark = $obj_post->get_user_assessment_marks($i_user_id, $obj_post_data->assessment_id);

                foreach ($obj_post_data->assess_user_mark as $user_marks) {
                    $user_played_levels[] = $user_marks->level;
                }

                /* User Assessment Data */
            }
            /**
             * Get Category and Sub-category first for a single post
             */
            $obj_parent_category = get_parent_category($i_category_id);

            if (!$obj_parent_category) {
                $this->show_404_custom();
            }

            $obj_post_data->name = $obj_parent_category->name;

            $obj_post_data->display_name = $obj_parent_category->display_name;
            $obj_post_data->parent_category_id = $obj_parent_category->id;

            $obj_post_data->has_categories = FALSE;
            $i_category_id = $obj_parent_category->id;


            $obj_category = new Category_model($i_category_id);
            $obj_child_categories = $obj_category->where("parent_id", $obj_category->id)->where("status", 1)->order_by("priority", "asc")->get();
            $obj_post_data->obj_child_categories = $obj_child_categories;
            if (count($obj_child_categories->all) > 0) {
                $obj_post_data->has_categories = TRUE;
            }


            $this->load->model('post', 'model');
            //NOW GET ALL CATEGORY FOR THE POST
            if (isset($obj_post_data->post_id)) {
                $categories_for_the_post = $obj_post->get_category_by_post($obj_post_data->post_id);
                $a_category_ids = array();
                if (count($categories_for_the_post) > 0)
                    foreach ($categories_for_the_post as $cate) {
                        array_push($a_category_ids, $cate->category_id);
                    }
                $obj_post_data->a_category_ids = $a_category_ids;
            }
            if (!$b_layout) {
                $this->layout_front = false;
            }
            error_reporting(0);

            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;

            $obj_post_data->ci_key = "post";
            $obj_post_data->ci_key_for_cover = "post";

            $extra_js = '';

            //$ar_fb = NULL;
            $b_show_post = TRUE;
            $s_target = "inner";


            $obj_post_data->post_show_publish_date = $this->config->config['post_show_publish_date'];
            $obj_post_data->post_show_updated_date = $this->config->config['post_show_updated_date'];
            $obj_post_data->has_outbrain = $this->config->config['has_outbrain'];
            $obj_post_data->has_disqus = $this->config->config['has_disqus'];

            if (strlen($obj_post_data->lead_material) == 0) {
                $s_image = getImageForFacebook($obj_post_data);
            } else {
                $s_image = $obj_post_data->lead_material;
            }


            $strContent = preg_replace('/<div (.*?)>Source:(.*?)<\/div>/', '', $obj_post_data->content);
            $strContent = preg_replace('/<div class="img_caption" (.*?)>(.*?)<\/div>/', '', $strContent);
            $strContent = strip_tags($strContent);
            $s_content = ( strlen($strContent) > 200 ) ? substr($strContent, 0, 200) . "..." : $strContent;


            $i_pos = stripos($s_image, "gallery/");
            if ($i_pos !== FALSE) {
                $s_img_first = substr($s_image, 0, $i_pos + strlen("gallery/"));
                $s_img_last = substr($s_image, $i_pos + strlen("gallery/"), strlen($s_image));
                $s_image = $s_img_first . "facebook/" . $s_img_last;
                $s_image = str_replace("http://bd.", "http://www.", $s_image);
            }

            $url_main = create_link_url(NULL, $obj_post_data->headline, $obj_post_data->post_id);

            $only_link = str_replace(base_url(), "", $url_main);

            $only_link_encoded = urlencode($only_link);

            if ($obj_post_data->referance_id > 0) {
                $url_segment = $this->uri->segment(1);
                $only_link_encoded = $url_segment;
                //$only_link_encoded = urlencode($only_link);
                $only_link_encoded = $only_link_encoded . "/" . $obj_post_data->language;
            }

            $encoded_url = base_url() . $only_link_encoded;
            $ar_fb = array(
                "type" => "website",
                "site_name" => WEBSITE_NAME,
                "title" => $obj_post_data->headline,
                "image" => $s_image,
                "url" => $encoded_url,
                "description" => trim($s_content)
            );

            $obj_post_data->fb_desc = trim($s_content);

            $obj_post_data->discus_short_name = $this->config->config['disqus_short_name'];

            $str_title = (!empty($obj_post_data)) ? getCustomTitle($obj_post_data) : getCommonTitle();

            $obj_post->updateCount($obj_post_data->post_id);

            $data['related_tags'] = $obj_post->get_related_tags($obj_post_data->post_id);

            $related_news = $obj_post->get_related_news($obj_post_data->post_id);

            $assessment = $obj_post->get_related_assessment($obj_post_data->assessment_id);
            $assessment_levels = $obj_post->get_assessment_levels($obj_post_data->assessment_id);

            $ar_assessment_levels = explode(',', $assessment_levels);
            $ar_playable_levels = array();

            $obj_post_data->assessment_has_levels = FALSE;

            if ($assessment_levels > 0) {
                $obj_post_data->assessment_has_levels = TRUE;
                $obj_post_data->next_level = min(array_diff($ar_assessment_levels, $user_played_levels));
            }

            $obj_post_data->has_assessment = FALSE;

            $data['all_attachment'] = $obj_post->get_related_attach($obj_post_data->post_id);

            $obj_post_data->has_related = FALSE;

            if (is_array($related_news)) {
                $obj_post_data->has_related = TRUE;
            }

            if ($assessment) {
                $obj_post_data->has_assessment = TRUE;
                $obj_post_data->assessment = $assessment;
                $obj_post_data->assessment_levels = $assessment_levels;
                $obj_post_data->go_to_assessment = $this->config->config['go_to_assessment'];
            }

            foreach ($related_news as &$r_news) {
                if (!isset($r_news->content)) {
                    $link = $r_news->new_link;
                    $headline = $r_news->title;
                    
                    
                    //$related_news_id = str_replace(base_url() . sanitize($headline) . "-", "", $link);
                    $ar_text = explode('-',$link);
                    $related_news_id = end($ar_text);
                    
                    $obj_related_news = $obj_post->get_by_id($related_news_id);
                    $ar_news_data = getFormatedContentAll($obj_related_news, 150); 
                    
                    $r_news->lead_material = $ar_news_data['lead_material'];
                    $r_news->content = $ar_news_data['content'];
                    $r_news->image = $ar_news_data['image'];
                }
            }

            //Get Language for the post
            $i_lang_post_id = ( $obj_post_data->referance_id > 0 ) ? $obj_post_data->referance_id : $obj_post_data->post_id;
            $s_lang = $obj_post->get_available_language($i_lang_post_id, $obj_post_data->other_language, $obj_post_data->referance_id, $obj_post_data->language);
            $this->db->set_dbprefix('tds_');
            $data['s_lang'] = $s_lang;


            if ($obj_post_data->referance_id > 0) {
                $this->db->select("id, headline, referance_id");
                $this->db->from("post");
                $this->db->where("id", $obj_post_data->referance_id, FALSE);
                $post_data = $this->db->get()->row();
                $data['main_post_id'] = $post_data->id;
                $data['main_headline'] = $post_data->headline;
                $data['main_referance_id'] = $post_data->referance_id;
            } else {
                $data['main_post_id'] = $obj_post_data->post_id;
                $data['main_headline'] = $obj_post_data->headline;
                $data['main_referance_id'] = $obj_post_data->referance_id;
            }
            
            $data['related_news'] = $related_news;

            $data['post_images'] = $obj_post->get_related_gallery($obj_post_data->post_id, array(1, 5));

            $data['post_videos'] = $obj_post->get_post_videos($obj_post_data->post_id);

            if (!empty($obj_post_data->attach) && file_exists($obj_post_data->attach)) {
                $data['attachment'] = $obj_post_data->attach;
            }

            $meta_description = $obj_post_data->meta_description;

            $keywords = $obj_post->get_keywords($obj_post_data->post_id);

            $obj_post_data->b_layout = $b_layout;

            /* $data['related_doc'] = $obj_post->get_related_gallery(); */

            //$obj_post_data->related_news_content = $this->load->view( 'related_news', $data, TRUE );
            $obj_post_data->disqus_content = $this->load->view('disqus', $obj_post_data, TRUE);

            $data1['outbrain_url'] = $this->config->config['outbrain_url'];
            $obj_post_data->outbrain_content = $this->load->view('outbrain', $data1, TRUE);

            $obj_post_data->resource = $obj_post->get_related_gallery($obj_post_data->post_id);

            if ($obj_post_data->post_type == 4) {
                $obj_post_data->has_previous = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'previous', $obj_post_data->published_date);
                $obj_post_data->has_next_news = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'next', $obj_post_data->published_date);
            } else {
                $obj_post_data->has_previous = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'previous');
                $obj_post_data->has_next_news = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'next');
            }

            if ($obj_post_data->has_previous) {
                $obj_post_data->previous_news_link = $obj_post->news_link($obj_post_data->post_id, $a_category_ids, 'previous');
            }

            if ($obj_post_data->has_next_news) {
                $obj_post_data->next_news_link = $obj_post->news_link($obj_post_data->post_id, $a_category_ids, 'next');
            }
            if ($obj_post_data->post_type == 4) {
                $obj_post_data->has_more = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'none', $obj_post_data->published_date);
            } else {
                $obj_post_data->has_more = $obj_post->has_news($obj_post_data->post_id, $a_category_ids);
            }


            $dom = new DOMDocument;
            $dom->loadHTML($obj_post_data->content);
            $xp = new DOMXpath($dom);

            $b_found = false;
            foreach ($xp->query('//*[@style]') as $node) {

                if ($node->getAttribute('class') == "related_news_on_post") {
                    $related = (string) $dom->saveXML($node);
                    $style = $node->getAttribute('style');
                    $b_found = true;
                    break;
                }
            }

            $ar_ad_images = $this->config->config['post-ads'];

            $ar_post_banner = array('category_id' => $i_category_id, 'post_id' => $obj_post_data->post_id);
            $s_ad_image = get_single_post_custom_banner($ar_post_banner);

            if (!$s_ad_image) {

                $ar_images = $ar_ad_images['add'];
                list($i_first_image, $i_second_image) = get_rand_images($ar_images);

                $i_first_image_text = '<a href="' . base_url() . $ar_ad_images['link'][$i_first_image] . '">';
                $i_first_image_text .= '<img id="' . $ar_ad_images['id'][$i_first_image] . '" class="ads ads-image ' . $ar_ad_images['class'][$i_first_image] . ' ';
                if ($ar_ad_images['check_login'][$i_first_image] == "1") {
                    $i_first_image_text .= 'check_login"';
                } else {
                    $i_first_image_text .= '"';
                }
                $i_first_image_text .= 'src="' . base_url() . $ar_images[$i_first_image] . '" /></a>';

                $i_second_image_text = '<a href="' . base_url() . $ar_ad_images['link'][$i_second_image] . '">';
                $i_second_image_text .= '<img id="' . $ar_ad_images['id'][$i_second_image] . '" class="ads ads-image ' . $ar_ad_images['class'][$i_second_image] . ' ';
                if ($ar_ad_images['check_login'][$i_second_image] == "1") {
                    $i_second_image_text .= 'check_login"';
                } else {
                    $i_second_image_text .= '"';
                }
                $i_second_image_text .= 'src="' . base_url() . $ar_images[$i_second_image] . '" /></a>';

                $s_ad_image = "<p>" . $i_first_image_text . "" . $i_second_image_text . "</p>";
            }

            $obj_post_data->s_ad_image = $s_ad_image;

            $s_related_news = "";
            $s_related_news_content = $this->load->view('related_news', $data, TRUE);
            if (!$b_found && is_array($data['related_news'])) {
                $style = "width: 220px; height: 200px; float:right;";
            }
            if (is_array($data['related_news'])) {
                $s_related_news = '<div class="related_news" style="' . $style . '">' . $s_related_news_content . '</div><p>';
            }

            if (!$b_found && is_array($data['related_news'])) {
                $obj_post_data->related_news_append = '<div id="related_news_1" class="related_news_parent">' . $s_related_news_content . '</div>';
            }

            if (isset($obj_post_data->video_file) && $obj_post_data->video_file != "" && $obj_post_data->video_file != null) {
                $video_file = trim($obj_post_data->video_file);
                if ($video_file != "") {
                    $s_content = $this->load->view('post_videos', $obj_post_data, TRUE);
                } else {
                    $s_content = $this->load->view('post', $obj_post_data, TRUE);
                }
            } else {
                $s_content = $this->load->view('post', $obj_post_data, TRUE);
            }
        }


        if ($b_found) {
            $s_content = str_replace($related, $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace("/>", " />", $related), $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace(" />", "/>", $related), $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace("/>", "", $related), $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace(" />", "", $related), $s_related_news, $s_content);
        }

        if ($b_layout) {
            $s_right_view = "";
            $cache_name = "common/right_view";
            if (!$s_widgets = $this->cache->file->get($cache_name)) {


                $this->db->where('is_enabled', 1);
                $query = $this->db->get('widget');

                $obj_widgets = $query->result();

                if ($obj_widgets) {
                    $data2['post_details'] = 0;
                    $data2['widgets'] = $obj_widgets;
                    $data2['cartoon'] = true;

                    // User Data
                    $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                    $data2['model'] = $this->get_free_user($user_id);

                    $data2['free_user_types'] = $this->get_free_user_types();

                    $data2['country'] = $this->get_country();
                    $data2['country']['id'] = $data2['model']->tds_country_id;

                    $data2['grades'] = $this->get_grades();

                    $data2['medium'] = $this->get_medium();

                    $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                    // User Data

                    $obj_post = new Posts();
                    $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();

                    $s_right_view = $this->load->view('right', $data2, TRUE);
                    $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
                }
            } else {
                $s_right_view = $s_widgets;
            }

            if (!isset($str_title) && $ci_key != "index" && $ci_key != "") {
                $str_title = ucfirst($ci_key) . " News | " . WEBSITE_NAME;
            } else {
                $str_title = (isset($str_title)) ? $str_title : getCommonTitle();
            }

            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "description" => $meta_description,
                "keywords" => $keywords,
                "title" => $str_title,
                "side_bar" => $s_right_view,
                "target" => $s_target,
                "fb_contents" => $ar_fb,
                "ci_key" => sanitize($obj_post_data->name),
                "content" => $s_content
            );
            $this->extra_params = $ar_params;
        } else {
            print $s_content;
        }
    }

    function delete_cache() {
        if (isset($_GET['cache'])) {
            $cache_prefix = strtoupper(str_ireplace("*", "", $_GET['cache']));
            print "<h1>" . $cache_prefix . "</h1><br /><Br />";
            $ar_cache = $this->cache->cache_info();
            foreach ($ar_cache['cache_list'] as $cache_list) {
                if (strpos($cache_list['info'], $cache_prefix) !== FALSE) {
                    print "Deleting cache ..." . $cache_list['info'] . "<Br />";
                    $this->cache->delete($cache_list['info']);
                }
            }

            $cache_prefix = strtolower(str_ireplace("*", "", $_GET['cache']));
            print "<h1>" . $cache_prefix . "</h1><br /><Br />";
            $ar_cache = $this->cache->cache_info();
            foreach ($ar_cache['cache_list'] as $cache_list) {
                if (strpos($cache_list['info'], $cache_prefix) !== FALSE) {
                    print "Deleting cache ..." . $cache_list['info'] . "<Br />";
                    $this->cache->delete($cache_list['info']);
                }
            }

            $cache_prefix = str_ireplace("*", "", $_GET['cache']);
            $cache_prefix = strtoupper(substr($cache_prefix, 0, 1)) . substr($cache_prefix, 1, strlen($cache_prefix));
            print "<h1>" . $cache_prefix . "</h1><br /><Br />";
            $ar_cache = $this->cache->cache_info();
            foreach ($ar_cache['cache_list'] as $cache_list) {
                if (strpos($cache_list['info'], $cache_prefix) !== FALSE) {
                    print "Deleting cache ..." . $cache_list['info'] . "<Br />";
                    $this->cache->delete($cache_list['info']);
                }
            }
        } else {
            return false;
        }
        $this->layout_front = false;
    }

    function socialPage() {


        $ar_segmens = $this->uri->segment_array();
        $image_url = $ar_segmens[2];

        $this->layout_front = false;
        $this->db->where("material_url", str_replace("-d-", "/", $image_url));
        $this->db->limit(1);
        $data = $this->db->get("materials")->row();

        $this->load->view("social_page_like", $data);
    }

    function good_read_add_folder() {
        if (free_user_logged_in()) {
            $this->layout_front = FALSE;

            $data['user_id'] = get_free_user_session("id");

            $this->load->view("add_folder", $data);
        } else {
            redirect("/");
        }
    }

    function print_post() {

        $b_news_link = false;
        $b_category_found = false;
        $ar_segmens = $this->uri->segment_array();
        $b_already_cached = false;
        $ar_ids = explode("-", $ar_segmens[2]);
        $ar_segmens[2] = $ar_ids[count($ar_ids) - 1];
        if (isset($ar_segmens[2])) {

            $i_post_id_md5 = $ar_segmens[2];

            $obj_post = new Post_model();
            $b_is_md5 = ( is_numeric($i_post_id_md5) ) ? FALSE : TRUE;
            if (($obj_post_data = $obj_post->has_post($i_post_id_md5, $b_is_md5))) {
                $b_news_link = TRUE;
            }
        }

        if ($b_news_link) {
            $this->process_post_view('', $obj_post_data, false);
        }
    }

    function _remap($method, $params = array()) {
        $ar_segments_to_execute = array('good-read');
        $s_current_module = $this->load->get_current_module();

        $s_controller_name = $this->router->fetch_class();

        $ar_mod_controllers = array($s_current_module, $s_controller_name);

        $ar_mod = array('admin', 'ad');
        $funcs = get_class_methods($this);

        $ar_segmens = $this->uri->segment_array();
        
        $i_count_segments = count($ar_segmens);

        if ($i_count_segments > 0) {
            if ($i_count_segments == 1) {
                $s_controller = $this->uri->segment(1);
                if ($s_controller == $s_current_module) {
                    redirect("/");
                } else if ($s_controller == $s_controller_name) {
                    redirect("/");
                }
            } else {
                $s_controller = $this->uri->segment($i_count_segments);
            }

            if (in_array($s_controller, $ar_mod)) {
                $this->show_404_custom();
            }
            $method = strtolower($s_controller) ;
        }
        if (in_array($method, $funcs)) {
            // We are trying to go to a method in this class
            return call_user_func_array(array($this, $method), $params);
        } else if (in_array($this->uri->segment(1), $funcs)) {
            return call_user_func_array(array($this, $this->uri->segment(1)), $params);
        } else {
            $method = str_ireplace("-", "_", $method);
            if (in_array($method, $funcs)) {
                // We are trying to go to a method in this class
                return call_user_func_array(array($this, $method), $params);
            }

            if (count($ar_segmens) > 1) {
                $ar_segments_to_check = $ar_segmens[1];
                if (in_array($ar_segments_to_check, $ar_segments_to_execute)) {
                    $params = array("s_folder_name" => $ar_segmens[2]);
                    $method = str_ireplace("-", "_", $ar_segments_to_check);
                    if (in_array($method, $funcs)) {
                        // We are trying to go to a method in this class
                        return call_user_func_array(array($this, $method), $params);
                    }
                }
            }

            $this->load->model('post');


            $obj_category = new Category_model();
            $i_parent_category_id = 0;
            if ($i_count_segments > 1) {
                //Now Come on to the multiple segments, let say we have only categories and news will be passed through the remap
                //Give me hell Yeah: Huffas
                $i_count = count($ar_segmens) - 1;
                $s_category = array_pop($ar_segmens);
                
                $i_parent_category_id = check_categories_recursive($ar_segmens);
                
                if (!$i_parent_category_id) {
                    //TRY FOR NEWS POST
                    $s_news_category = array_pop($ar_segmens);

                    $s_data = $s_category;
                    
                    if (!empty($ar_segmens)) {

                        $i_parent_category_id = check_categories_recursive($ar_segmens);
                        if (!$i_parent_category_id) {

                            $this->show_404_custom();
                        }
                    }
                    
                    $news_title = explode("-", $s_news_category);
                    $i_post_id = $news_title[count($news_title) - 1];

                    $lang = "";
                    if (strlen($s_data) > 0) {
                        $lang = $s_data;
                    }
                    
                    $a_post_id_pop = array_pop($news_title);
                    $s_headline_sanitize = implode("-", $news_title);
                    $s_headline = ucwords(unsanitize($s_headline_sanitize));
                    
                    if ($i_parent_category_id == 0) {
                        $a_post_params = array("tds_post.referance_id" => $i_post_id, "tds_post.language" => $lang, "ignore_post_type" => true);
                    } else {
                        $a_post_params = array(
                            "tds_post.id" => $i_post_id,
                            "category.id" => $i_parent_category_id
                        );
                    }
                    
                    $a_post = $this->post->gePostNews($a_post_params);
                    
                    if (!is_array($a_post)) {
                        if ($i_parent_category_id == 0) {
                            $a_post_params = array(
                                "tds_post.id" => $i_post_id
                            );
                            $a_post = $this->post->gePostNews($a_post_params);
                        }
                        if (!is_array($a_post)) {
                            $this->show_404_custom();
                        } else {
                            $obj_post_data = $a_post['data'][0];
                            $this->process_post_view($obj_post_data->id, $obj_post_data);
                            return;
                        }
                    } else {
                        $obj_post_data = $a_post['data'][0];
                        $this->process_post_view($obj_post_data->id, $obj_post_data);
                        return;
                    }
                }
            } else {
                $s_category = $ar_segmens[1];
            }
            
            $b_popular = FALSE;
            if ($s_category == "popular") {
                $b_popular = TRUE;
                $ar_segmens = $this->uri->segment_array();
                $s_category = $ar_segmens[count($ar_segmens) - 1];
                if (!$i_parent_category_id) {
                    if (strlen($s_category) < 4) {
                        $s_category .= ".";
                        $this->db->where('name', $s_category);
                        $this->db->where("status", 1);
                        $query = $this->db->get('categories');
                        $obj_cate = $query->row();
                        $i_parent_category_id = $obj_cate->id;
                    }
                } else {
                    $this->db->where('id', $i_parent_category_id);
                    $this->db->where("status", 1);
                    $query = $this->db->get('categories');
                    $obj_cate = $query->row();
                }
                $this->__inner($i_parent_category_id, $obj_cate->name, TRUE);
                return;
            }
            $b_category_found = false;
            //First Let see if it is a Category

            $cate_name = ucwords(unsanitize($s_category));
            if (strlen(ucwords(unsanitize($s_category))) < 4) {
                $cate_name .= ".";
            }

            $this->db->where('name', $cate_name);
            $this->db->where("status", 1);
            if ($i_parent_category_id == 0) {
                $this->db->where('(parent_id = 0 OR parent_id IS NULL)');
            } else {
                $this->db->where("parent_id", $i_parent_category_id, FALSE);
            }
            $query = $this->db->get('categories');

            $s_category_name = unsanitize($s_category, '-');
            if ($query->num_rows()) {
                $obj_cate = $query->row();
                $b_category_found = true;
            }

            if (!$b_category_found) {

                //TRY FOR NEWS POST
                $news_title = explode("-", $s_category);
                $i_post_id = $news_title[count($news_title) - 1];

                $a_post_id_pop = array_pop($news_title);
                $s_headline_sanitize = implode("-", $news_title);
                $s_headline = ucwords(unsanitize($s_headline_sanitize));



                if ($i_parent_category_id == 0) {
                    $a_post_params = array("tds_post.id" => $i_post_id);
                } else {
                    $a_post_params = array(
                        "tds_post.id" => $i_post_id,
                        "category.id" => $i_parent_category_id
                    );
                }

                $a_post = $this->post->gePostNews($a_post_params, "Single");
                if (!is_array($a_post)) {
                    //print urldecode($s_headline_sanitize) . "   " . sanitize($obj_post_data->headline);
                    $this->show_404_custom();
                } else {
                    $obj_post_data = $a_post['data'][0];

                    $b_layout = TRUE;
                    $ar_segmens = $this->uri->segment_array();
                    foreach ($ar_segmens as $segment) {
                        if ($segment == "print_post") {
                            $b_layout = FALSE;
                            break;
                        }
                    }
                    $this->process_post_view($obj_post_data->id, $obj_post_data, $b_layout);
                }
            } else {
                $this->__inner($obj_cate->id, $obj_cate->name);
            }
        }
    }

    function register_user() {

        $api_registration = FALSE;

        if ($this->input->is_ajax_request() && free_user_logged_in()) {
            $data['logged_in'] = free_user_logged_in();
            echo json_encode($data);
            exit;
        }

        $this->load->helper('form');

        $free_user = new Free_users();

        if ($this->input->is_ajax_request()) {

            $_POST['nick_name'] = filter_var($_POST['data']['nick_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $_POST['first_name'] = filter_var($_POST['data']['first_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $_POST['last_name'] = filter_var($_POST['data']['last_name'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (isset($_POST['data'])) {
                $_POST['email'] = filter_var($_POST['data']['email'], FILTER_SANITIZE_EMAIL);
            } else {
                $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            }

            if (isset($_POST['data'])) {
                $_POST['school_code'] = $_POST['data']['school_code'];
            } else {
                $_POST['school_code'] = $_POST['school_code'];
            }
            
            if (isset($_POST['data'])) {
                $_POST['paid_school_id'] = $_POST['data']['paid_school_id'];
            } else {
                $_POST['paid_school_id'] = $_POST['paid_school_id'];
            }

            if (isset($_POST['data']['location']) && !empty($_POST['data']['location'])) {
                $_POST['district'] = filter_var($_POST['data']['district'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['country'] = filter_var($_POST['data']['country'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['location'] = filter_var($_POST['data']['location'], FILTER_SANITIZE_SPECIAL_CHARS);
            }

            if (isset($_POST['data']['gender']) && !empty($_POST['data']['gender'])) {
                $_POST['gender'] = filter_var($_POST['data']['gender'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['gender'] = ($_POST['gender'] == 'female') ? '0' : '1';
            }

            if (isset($_POST['data']['dob']) && !empty($_POST['data']['dob'])) {
                $_POST['dob'] = filter_var($_POST['data']['dob'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['dob'] = date('Y-m-d', strtotime($_POST['dob']));
            }

            if (isset($_POST['data']['profile_image']) && !empty($_POST['data']['profile_image'])) {
                $_POST['profile_image'] = filter_var($_POST['data']['profile_image'], FILTER_VALIDATE_URL);
            }

            // Google Registration Starts
            if ($_POST['data']['source'] == 'g') {
                $_POST['gl_profile_id'] = $_POST['data']['id'];
                $_POST['nick_name'] = 1;
                $_POST['google_profile_url'] = filter_var($_POST['data']['profile_url'], FILTER_VALIDATE_URL);
            }
            // Google Registration Ends
            // Facebook Registration Starts
            if ($_POST['data']['source'] == 'f') {
                $_POST['fb_profile_id'] = $_POST['data']['id'];
                $_POST['nick_name'] = 1;
                $_POST['fb_profile_url'] = filter_var($_POST['data']['profile_url'], FILTER_VALIDATE_URL);
            }
            // Facebook Registration Ends

            if ($_POST['data']['source'] == 'g' || $_POST['data']['source'] == 'f') {
                $api_registration = TRUE;
            }

            unset($_POST['data']);
        }

        if (isset($_POST) && !empty($_POST)) {

            foreach ($_POST as $key => $value) {
                if($key!="school_code")
                $free_user->$key = $value;
            }

            $free_user->grade_ids = implode(',', $free_user->grade_ids);

            if ($api_registration) {

                $free_user->skip_validation();

                if (!$free_user->_email_unique()) {

                    $data['errors'][] = 'The Email Address you supplied is already taken.';

                    $data['registered'] = FALSE;
                    $data['logged_in'] = FALSE;

                    echo json_encode($data);
                    exit;
                }
                  
            }
            
            if($_POST['paid_school_id'])
            {
                if(!$_POST['school_code'])
                {
                    $data['errors'][] = 'You have to give school code if you select premium school';

                    $data['registered'] = FALSE;
                    $data['logged_in'] = FALSE;

                    echo json_encode($data);
                    exit;
                }
                else if(!check_school_code_paid($_POST['paid_school_id'],$_POST['school_code']))
                {
                    $data['errors'][] = 'Invalid School Code';

                    $data['registered'] = FALSE;
                    $data['logged_in'] = FALSE;

                    echo json_encode($data);
                    exit;
                }    
            }  

            if ($free_user->save()) {

                $ar_email['sender_full_name'] = 'Russell T. Ahmed';
                $ar_email['sender_email'] = 'info@champs21.com';
                $ar_email['to_name'] = $free_user->first_name . ' ' . $free_user->last_name;
                $ar_email['to_email'] = $free_user->email;
                $ar_email['html'] = true;

                $ar_email['subject'] = 'Welcome to Champs21.com';
                $ar_email['message'] = $this->get_welcome_message($ar_email['to_name'], true);
                send_mail($ar_email);

                ($api_registration) ? $free_user->api_login() : $free_user->login();

                $this->set_user_session($free_user, $this->input->post('password'), false, true);

                $this->create_free_user_folders();

                $data['registered'] = true;
            } else {

                $data['errors'] = $free_user->error->all;

                $this->session->sess_destroy();

                $data['registered'] = false;
            }
        }
        $data['logged_in'] = free_user_logged_in();

        echo json_encode($data);
        exit;
    }

    function login_user() {


        if ($this->input->is_ajax_request() && free_user_logged_in()) {
            $user_info['logged_in'] = free_user_logged_in();
            echo json_encode($user_info);
            exit;
        }

        $this->load->helper('form');

        $api_login = FALSE;

        $free_user = new Free_users();

        $this->load->config("tds");

        if ($this->input->is_ajax_request()) {

            $source = '';
            if (isset($_POST['data']['source']) && !empty($_POST['data']['source'])) {

                $_POST['email'] = filter_var($_POST['data']['email'], FILTER_SANITIZE_EMAIL);

                if ($_POST['data']['source'] == 'g') {
                    $source = 'g';
                    $_POST['gl_profile_id'] = filter_var($_POST['data']['id'], FILTER_SANITIZE_NUMBER_INT);
                }

                if ($_POST['data']['source'] == 'f') {
                    $source = 'f';
                    $_POST['fb_profile_id'] = filter_var($_POST['data']['id'], FILTER_SANITIZE_NUMBER_INT);
                }

                $api_login = TRUE;
                unset($_POST['data']);
            }
        }

        if (isset($_POST) && !empty($_POST)) {

            $free_user->email = $this->input->post('email');
            $remember_me = $this->input->post('remember_me');

            if ($api_login) {

                if (isset($_POST['fb_profile_id']) && !empty($_POST['fb_profile_id'])) {
                    $free_user->fb_profile_id = $this->input->post('fb_profile_id');
                }

                if (isset($_POST['gl_profile_id']) && !empty($_POST['gl_profile_id'])) {
                    $free_user->gl_profile_id = $this->input->post('gl_profile_id');
                }

                if ($obj_free_user = $free_user->api_login($source)) {

                    $this->set_user_session($obj_free_user, NULL, $remember_me, true);
                } else {

                    $data['errors'] = $free_user->error->all;

                    $this->session->unset_userdata($array_items);
                    $this->session->sess_destroy();
                }

            } else {


                $free_user->password = $this->input->post('password');

                if ($free_user->login()) {

                    $this->set_user_session($free_user, $this->input->post('password'), $remember_me, true);
                } else {

                    $data['errors'] = $free_user->error->all;

                    $this->session->unset_userdata($array_items);
                    $this->session->sess_destroy();
                }
            }
        }

        $data['logged_in'] = free_user_logged_in();

        echo json_encode($data);
        exit;
    }

    function update_profile() {

        $user_id = '0';

        if (free_user_logged_in()) {
            $user_id = get_free_user_session('id');
        } else {
            $data['logged_in'] = FALSE;
            $data['registered'] = FALSE;
            echo json_encode($data);
            exit;
        }

        if ($this->input->is_ajax_request()) {

            $this->load->helper('form');

            $free_user = new Free_users($user_id);

            if (isset($_POST) && !empty($_POST)) {

                foreach ($_POST as $key => $value) {
                    if (!empty($value)) {
                        $free_user->$key = $value;
                    }
                }

                $day = $free_user->dob_day;
                if (strlen($day) < 2 && !empty($day)) {
                    $day = '0' . $day;
                }

                $month = $free_user->dob_month;
                if (strlen($month) < 2 && !empty($month)) {
                    $month = '0' . $month;
                }

                $year = $free_user->dob_year;

                $dob = NULL;
                if (!empty($free_user->dob_day) && !empty($free_user->dob_month) && !empty($free_user->dob_year)) {
                    $dob = $year . '-' . $month . '-' . $day;
                }

                if (!empty($dob)) {
                    $free_user->dob = $dob;
                }

                unset($free_user->dob_day);
                unset($free_user->dob_month);
                unset($free_user->dob_year);

                if ($_POST['gender'] == '0') {
                    $free_user->gender = '0';
                }

                if ($_POST['gender'] == '1') {
                    $free_user->gender = '1';
                }
                if ($_POST['division'] && $_POST['mobile_no'] && $_POST['school_name']) {
                    $free_user->is_joined_spellbee = '1';
                }
                $free_user->grade_ids = implode(',', $free_user->grade_ids);

                $free_user->skip_validation();

                if ($free_user->save()) {
                    $this->set_user_session($free_user);
                    $data['success'] = TRUE;
                } else {

                    $errors = $free_user->error->all;

                    foreach ($errors as $error) {
                        $data['errors'][] = $error;
                    }
                }
            }
        }

        echo json_encode($data);
        exit;
    }

    function update_spellingbee_profile() {

        $user_id = '0';

        if (free_user_logged_in()) {
            $user_id = get_free_user_session('id');
        } else {
            $data['logged_in'] = free_user_logged_in();
            $data['registered'] = false;
            echo json_encode($data);
            exit;
        }

        if ($this->input->is_ajax_request()) {

            $this->load->helper('form');

            $free_user = new Free_users($user_id);

            if (isset($_POST) && !empty($_POST)) {

                foreach ($_POST as $key => $value) {
                    $free_user->$key = $value;
                }

                if (empty($free_user->school_name)) {
                    $data['errors']['school_name'] = 'School name cannot be blank.';
                }
                if (empty($free_user->division)) {
                    $data['errors']['division'] = 'Division cannot be blank.';
                }
                if (empty($free_user->mobile_no)) {
                    $data['errors']['mobile_no'] = 'Mobile No cannot be blank.';
                }
                if (empty($free_user->first_name)) {
                    $data['errors']['first_name'] = 'First Name cannot be blank.';
                }

                if (strlen($free_user->mobile_no) < 11) {
                    $free_user->mobile_no = '0' . $free_user->mobile_no;
                }

                if (strlen($free_user->mobile_no) == 10 && substr($free_user->mobile_no, 1, 1) != 1) {
                    $data['errors']['mobile_no'] = 'Invalid mobile no format';
                }

                if (strlen($free_user->mobile_no) < 10 || strlen($free_user->mobile_no) > 11) {
                    $data['errors']['mobile_no'] = 'Invalid mobile no format';
                }

                if (strlen($free_user->mobile_no) == 11 && substr($free_user->mobile_no, 0, 1) != 0) {
                    $data['errors']['mobile_no'] = 'Invalid mobile no format';
                }

                if (empty($data['errors'])) {
                    $free_user->is_joined_spellbee = '1';
                    if ($free_user->save()) {
                        $this->set_user_session($free_user);
                        $data['success'] = TRUE;
                    }
                }
            }
        }

        echo json_encode($data);
        exit;
    }

    function schoolsearch() {
        //echo $this->input->get('str');
        //echo $this->input->post('name');
        //echo $this->input->post('division');
        //echo $this->input->post('level');

        $this->db->select('*');
        $this->db->from('tds_school');
        ($this->input->post('name') != "") ? $this->db->like('name', $this->input->post('name'), 'after') : '';
        ($this->input->post('district') != "") ? $this->db->or_like('division', $this->input->post('division'), 'after') : '';
        ($this->input->post('level') != "") ? $this->db->or_like('level', $this->input->post('level'), 'after') : '';
        ($this->input->get('str') != "") ? $this->db->like('name', $this->input->get('str'), 'after') : '';
        $this->db->where("is_visible",1);
        $query = $this->db->get();
        $data['schooldata'] = $query->result_array();


        $data['ci_key'] = 'schoolsearch';
        $s_content = $this->load->view('schoolsearch', $data, true);

        // User Data
        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

        $data['model'] = $this->get_free_user($user_id);

        $data['free_user_types'] = $this->get_free_user_types();

        $data['country'] = $this->get_country();
        $data['country']['id'] = $data2['model']->tds_country_id;

        $data['grades'] = $this->get_grades();

        $data['medium'] = $this->get_medium();

        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
        // User Data

        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();

        //has some work in right view
        $s_right_view = $this->load->view('right', $data, TRUE);
        //echo "<pre>";
        //print_r($data);

        $str_title = "School Search";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schoolsearch",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    function search() {
        $purify_config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purify_config);

        $q = '';

        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $q = $this->input->get('s');
        }

        $q = $purifier->purify($q);

        if (empty($q)) {
            redirect(base_url());
        }

        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "search";
        $data['ci_key_for_cover'] = "search";
        $data['s_category_ids'] = "0";
        $data['q'] = $q;

        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('newssearch', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = getCommonTitle();

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "search",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    function logout_user() {
        $array_items = array('free_user' => array());
        $this->session->unset_userdata($array_items);
        $this->session->sess_destroy();
        unset($_COOKIE['champs_session']);
        setcookie('champs_session', NULL, time() - 100, '/', str_replace('www.', '', $_SERVER['SERVER_NAME']));
        set_type_cookie(1);
        redirect(base_url());
    }

    private function set_user_session($obj_user, $pwd = NULL, $remember = false, $b_refresh_cookie = false) {

        set_user_sessions($obj_user, $pwd, $remember, $b_refresh_cookie);
    }

    function upload_profile_image() {

        $this->load->library('upload');

        if (!empty($_FILES['profile_image']['name'])) {

            $user_data = get_free_user_session();

            $free_user = new Free_users($user_data['id']);

            $profile_image_ext = end(explode('.', $_FILES['profile_image']['name']));

            $config_profile['upload_path'] = 'upload/free_user_profile_images/';
            $config_profile['allowed_types'] = 'jpg|jpeg|JPG|JPEG|png|PNG';
            $config_profile['max_size'] = '512';
            $config_profile['max_width'] = '2000';
            $config_profile['max_height'] = '1600';
            $config_profile['is_image'] = TRUE;
            $config_profile['file_name'] = time() . '_' . $free_user->id;
            $config_profile['overwrite'] = TRUE;

            $this->upload->initialize($config_profile);

            if ($this->upload->do_upload('profile_image')) {

                $file_path = base_url($config_profile['upload_path'] . $config_profile['file_name'] . '.' . $profile_image_ext);
                $free_user->profile_image = $file_path;

                $free_user->skip_validation();
                $free_user->save();

                unset($_FILES['profile_image']['name']);

                $this->set_user_session($free_user);

                echo $free_user->profile_image;
                exit;
            } else {
                echo 0;
                exit;
            }
        } else {
            echo 0;
            exit;
        }
    }

    function set_preference() {

        $user_id = '0';

        if (free_user_logged_in()) {
            $user_id = get_free_user_session('id');
        } else {
            echo -1;
            exit;
        }

        if ($this->input->is_ajax_request()) {

            if (isset($_POST) && !empty($_POST['category']) && count($_POST['category']) > 0) {
                $obj_category = new Category();
                $array = array('status' => 1, 'show' => 1);
                $obj_category->where($array)->order_by('name', 'asc')->get();
                $all_category_selected = true;
                if (count($obj_category) > 0) {
                    foreach ($obj_category as $value) {
                        if (!in_array($value->id, $_POST['category'])) {
                            $all_category_selected = false;
                            break;
                        }
                    }
                }

                if ($all_category_selected === false) {
                    $str_categories = implode(',', $this->input->post('category'));

                    $user_pref_mod = new Free_user_preference;
                    $user_pref = $user_pref_mod->get_by_user_id($user_id);

                    if (!$user_pref) {
                        $user_pref_mod->free_user_id = $user_id;
                        $user_pref_mod->category_ids = $str_categories;

                        if ($user_pref_mod->save()) {
                            echo 1;
                        } else {
                            echo 0;
                        }
                    } else {
                        $user_pref->category_ids = $str_categories;

                        if ($user_pref->save()) {
                            echo 1;
                        } else {
                            echo 0;
                        }
                    }
                } else {
                    $user_pref_mod = new Free_user_preference;
                    $user_pref = $user_pref_mod->get_by_user_id($user_id);
                    if ($user_pref) {
                        $user_pref_delete = new Free_user_preference($user_pref->id);
                        $user_pref_delete->delete();
                    }
                    echo 1;
                }
            }
        }
        exit;
    }

    function testbar() {

        $s_content = $this->load->view('sidebar');
        $data['category_tree'] = array();

        //has some work in right view
        $s_right_view = "";
        $s_content = $this->load->view('sidebar', $data, TRUE);
        //echo "<pre>";
        //print_r($data);

        $str_title = "School Search";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schoolsearch",
            "fb_contents" => NULL,
            "content" => $s_content
        );
        $this->extra_params = $ar_params;
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

    private function get_free_user($id = null) {
        return (!empty($id)) ? new Free_users($id) : new Free_users();
    }

    private function get_medium() {
        $this->load->config("user_register");
        return $this->config->config['medium'];
    }

    private function get_free_user_types() {
        $this->load->config("user_register");
        return $this->config->config['free_user_types'];
    }

    private function get_school_join_user_types() {
        $this->load->config("user_register");
        return $this->config->config['join_user_types'];
    }

    private function create_free_user_folders() {

        $this->load->config("user_register");

        $ar_data['folders'] = $this->config->config['free_user_folders'];
        $ar_data['user_id'] = get_free_user_session('id');

        $this->load->model("user_folder", 'ur_mod');

        return $this->ur_mod->created_good_read_folders($ar_data);
    }

    public function translate_tts() {
        $this->layout_front = false;
        $s_music_file = $this->input->get("q");
        $s_music_file = base64_decode($s_music_file);

        $str_music_dir = FCPATH . 'games-old/var/upload/spellingbee/' . $s_music_file . '.mp3';

        $b_file_exist = false;
        if (file_exists($str_music_dir) && is_file($str_music_dir) && is_readable($str_music_dir)) {
            $b_file_exist = true;
        } else {
            $str_music_dir = FCPATH . 'upload/spellingbee/' . $s_music_file . '.mp3';
            if (file_exists($str_music_dir) && is_file($str_music_dir) && is_readable($str_music_dir)) {
                $b_file_exist = true;
            }
        }
        if ($b_file_exist) {
            header("Content-Length: " . filesize($str_music_dir));
            ob_clean();
            flush();
            @readfile($str_music_dir);
        }
    }
    public function sciencerocks() {
        
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "sciencerocks";
        $data['ci_key_for_cover'] = "sciencerocks";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;
        
        //GET EXPERIMENT
        $this->db->select('tds_post.*');
        $this->db->from('post');
        $this->db->join("post_category as pcat", "tds_post.id=pcat.post_id", 'INNER');
        $this->db->where("tds_post.status",5);
        $this->db->where("pcat.category_id", 82);    
        $this->db->order_by("tds_post.id", "desc");  
        $this->db->limit(11);
        $mostpopuler_news = $this->db->get()->result();

        $ar_news_data = array();
        $i = 0;
        foreach ($mostpopuler_news as $r_news) {

                if (isset($r_news->content)) {				

                        $ar_news_id['id'] = $r_news->id;				
                        $ar_news = getFormatedContentAll($r_news, 150,'index');
                        $ar_news_data[$i] = $ar_news_id + $ar_news;
                }
                $i++;
        }
        $data['experiment'] = $ar_news_data;
        //END GET EXPERIMENT
        //GET DOZ
         $today = date('Y-m-d');  
        $this->db->select('*');
        $this->db->from('dailydose');        
        $this->db->where("status",1);
        $this->db->where("date <=",$today);
        $this->db->order_by("id", "desc"); 
        $daily_doz = $this->db->get()->result();
        
        $data['daily_doz'] = $daily_doz;
        //END GET DOZ

        $s_content = $this->load->view('sciencerocks', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Science Rocks";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $fb_contents['image'] = "http://www.champs21.com/swf/spellingbee_2015/sbee.png";

        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "sciencerocks",
            "fb_contents" => $fb_contents,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }
    public function sciencerocks2() {
        
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "sciencerocks";
        $data['ci_key_for_cover'] = "sciencerocks";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;
        
        //GET EXPERIMENT
        $this->db->select('tds_post.*');
        $this->db->from('post');
        $this->db->join("post_category as pcat", "tds_post.id=pcat.post_id", 'INNER');
        $this->db->where("tds_post.status",5);
        $this->db->where("pcat.category_id", 82);    
        $this->db->order_by("tds_post.id", "desc");  
        $this->db->limit(11);
        $mostpopuler_news = $this->db->get()->result();

        $ar_news_data = array();
        $i = 0;
        foreach ($mostpopuler_news as $r_news) {

                if (isset($r_news->content)) {				

                        $ar_news_id['id'] = $r_news->id;				
                        $ar_news = getFormatedContentAll($r_news, 150,'index');
                        $ar_news_data[$i] = $ar_news_id + $ar_news;
                }
                $i++;
        }
        $data['experiment'] = $ar_news_data;
        //END GET EXPERIMENT
        //GET DOZ
         $today = date('Y-m-d');  
        $this->db->select('*');
        $this->db->from('dailydose');        
        $this->db->where("status",1);
        $this->db->where("date <=",$today);
        $this->db->order_by("id", "desc"); 
        $daily_doz = $this->db->get()->result();
        
        $data['daily_doz'] = $daily_doz;
        //END GET DOZ

        $s_content = $this->load->view('sciencerocks2', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Science Rocks";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $fb_contents['image'] = "http://www.champs21.com/swf/spellingbee_2015/sbee.png";

        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "sciencerocks",
            "fb_contents" => $fb_contents,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }
    public function spellingbee() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "spellingbee";
        $data['ci_key_for_cover'] = "spellingbee";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('spellingbee', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Spelling Bee";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $fb_contents['image'] = "http://www.champs21.com/swf/spellingbee_2015/sbee.png";

        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "spellingbee",
            "fb_contents" => $fb_contents,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function spellingbee_play() {
        $this->layout_front = false;

        $url = "http://www.champs21.com/swf/spellingbee_2015/index.html";

        header('Content-Encoding: none;');
        //echo '<object
        //        type="application/x-shockwave-flash"
        //        data="'.$fileName.'"
        //        width="100%" height="100%">
        //            <param name="movie" value="'.$fileName.'" />
        //    </object>';
        ob_flush();
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle compressed
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        eval($content);
        echo $content;
        ob_end_flush();
    }

    public function leaderboard() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
        $user_division = (free_user_logged_in()) ? get_free_user_session('division') : NULL;

        $obj_post = new Post_model();
        $user_score = $obj_post->get_user_score($user_id);
        $user_rank = $obj_post->get_user_rank($user_score[0]->score, $user_score[0]->test_time, $user_score[0]->country_id, strtolower($user_division));

        /* old leaderboard */
//        $obj_post_data = $obj_post->get_leader_board();
//        
//        if($obj_post_data != 0)
//        {
        $rank = 1;
        $shtml = "";
//        foreach ($obj_post_data as $srow){
//                   
//		$shtml .= "<tr>";
//                $shtml .= "<td>".$rank."</td>";
//		$shtml .= "<td>".ucfirst($srow->first_name)." ".ucfirst($srow->middle_name)." ".ucfirst($srow->last_name)." "."(".ucfirst($srow->school_name).")"."</td>";
//		$shtml .= "<td>".$srow->score."</td>";
//		$shtml .= "</tr>";
//            $rank++;
//        }
//        }else{$shtml = "<p>No Data Found.</p>";}

        /* new leaderboard from excel */

        $file = 'score_board_16_7.xlsx';

        $this->load->library('EXcel');

        $inputFileType = PHPExcel_IOFactory::identify($file);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);

        $objExcel = $objReader->load($file);

        $division = strtolower('dhaka');

        $division_name_a = '';
        $division_name_b = '';
        if ($division == 'dhaka') {
            $division_name_a = $division . 'a';
            $division_name_b = $division . 'b';
        }

        if (!empty($division_name_a) && !empty($division_name_b)) {
            $obj_post_data_a = $objSheet = $objExcel->getSheetByName($division_name_a);
            $obj_post_data_b = $objSheet = $objExcel->getSheetByName($division_name_b);
        } else {
            $obj_post_data_a = $objSheet = $objExcel->getSheetByName($division);
        }

        if (!empty($obj_post_data_a) || !empty($obj_post_data_b)) {
            $highestRow_a = $obj_post_data_a->getHighestRow();

            if (!empty($obj_post_data_b)) {
                $highestRow_b = $obj_post_data_b->getHighestRow();
            }

            /* old leaderboard */
//                foreach ($obj_post_data as $value)
//                {
//                    echo "<tr>";
//                    echo "<td>".$rank."</td>";
//                    echo "<td>".ucfirst($value->first_name)." ".ucfirst($value->middle_name)." ".ucfirst($value->last_name)." "."(".ucfirst($value->school_name).")"."</td>";
//                    echo "<td>".$value->score."</td></tr>";
//                    $rank++;
//                }

            /* new leaderboard from excel */
            if (!empty($obj_post_data_b)) {
                $shtml .= "<tr>";
                $shtml .= "<td colspan=\"3\" style=\"text-align: center; background-color: #aaaaaa; color: #ffffff; font-size: 18px;\">Dhaka A</td>";
                $shtml .= "</tr>";
            }

            for ($i = 2; $i <= $highestRow_a; $i++) {
                $shtml .= "<tr>";
                $shtml .= "<td>" . $obj_post_data_a->getCell('A' . $i)->getValue() . "</td>";
                $shtml .= "<td>" . $obj_post_data_a->getCell('B' . $i)->getValue() . " " . "(" . $obj_post_data_a->getCell('C' . $i)->getValue() . ")" . "</td>";
                $shtml .= "<td>" . $obj_post_data_a->getCell('D' . $i)->getValue() . "</td>";
                $shtml .= "</tr>";
            }

            /* new leaderboard from excel */
            if (!empty($obj_post_data_b)) {
                $shtml .= "<tr>";
                $shtml .= "<td colspan=\"3\" style=\"text-align: center; background-color: #aaaaaa; color: #ffffff; font-size: 18px;\">Dhaka B</td>";
                $shtml .= "</tr>";

                for ($i = 2; $i <= $highestRow_b; $i++) {
                    $shtml .= "<tr>";
                    $shtml .= "<td>" . $obj_post_data_b->getCell('A' . $i)->getValue() . "</td>";
                    $shtml .= "<td>" . $obj_post_data_b->getCell('B' . $i)->getValue() . " " . "(" . $obj_post_data_b->getCell('C' . $i)->getValue() . ")" . "</td>";
                    $shtml .= "<td>" . $obj_post_data_b->getCell('D' . $i)->getValue() . "</td>";
                    $shtml .= "</tr>";
                }
            }
        }

        /* new leaderboard from excel */

        $data['spellbee_user_score'] = $user_score;
        $data['spellbee_user_rank'] = $user_rank;
        $data['spellbee_data'] = $shtml;

        $data['ci_key'] = "leaderboard";
        $data['ci_key_for_cover'] = "leaderboard";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('leaderboard', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Leaderboard";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "leaderboard",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function gamerules() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "gamerules";
        $data['ci_key_for_cover'] = "gamerules";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('gamerules', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Game Rules";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "gamerules",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function archive() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $s_st = null;
        $s_st = $this->input->get("st");

        if ($s_st == null) {
            $s_st = "season3";
        }
        $data = array();

        $data['ci_key'] = "archive";
        $data['ci_key_for_cover'] = "archive";
        $data['s_category_ids'] = "0";
        $data['active_tab'] = $s_st;


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('spellingbee/archive', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Spellingbee Archive";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "archive",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function top_spellers() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $s_st = null;
        $s_st = $this->input->get("st");

        if ($s_st == null) {
            $s_st = "season3";
        }
        $data = array();

        $data['ci_key'] = "top_spellers";
        $data['ci_key_for_cover'] = "top_spellers";
        $data['s_category_ids'] = "0";
        $data['active_tab'] = $s_st;


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('spellingbee/top_spellers', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Spellingbee Top Spellers";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "top_spellers",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function about_us() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "aboutus";
        $data['ci_key_for_cover'] = "aboutus";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('aboutus', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | About Us";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "aboutus",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function terms() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "terms";
        $data['ci_key_for_cover'] = "terms";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('terms', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | Terms";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "terms",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function privacy_policy() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "privacypolicy";
        $data['ci_key_for_cover'] = "privacypolicy";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('privacypolicy', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }



        $str_title = WEBSITE_NAME . " | Privacy Policy";


        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "privacypolicy",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function copyright() {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "copyright";
        $data['ci_key_for_cover'] = "copyright";
        $data['s_category_ids'] = "0";


        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;


        $s_content = $this->load->view('copyright', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {


            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['post_details'] = 0;
                $data2['widgets'] = $obj_widgets;
                $data2['cartoon'] = true;

                // User Data
                $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                $data2['model'] = $this->get_free_user($user_id);

                $data2['free_user_types'] = $this->get_free_user_types();

                $data2['country'] = $this->get_country();
                $data2['country']['id'] = $data2['model']->tds_country_id;

                $data2['grades'] = $this->get_grades();

                $data2['medium'] = $this->get_medium();

                $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                // User Data

                $obj_post = new Posts();
                $data2['category_tree'] = $obj_post->user_preference_tree();

                $s_right_view = $this->load->view('right', $data2, TRUE);
                $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        } else {
            $s_right_view = $s_widgets;
        }



        $str_title = WEBSITE_NAME . " | Copyright";


        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "copyright",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function contact_us() {

        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "contact_us";
        $data['ci_key_for_cover'] = "contact_us";
        $data['s_category_ids'] = "0";

        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;

        if (isset($_POST) && !empty($_POST)) {

            $this->load->config('champs21');

            $email_config = $this->config->config['contact_email_addr'];

            $contact_model = new Contact_us();

            $contact_model->full_name = $this->input->post('full_name');
            $contact_model->email = $this->input->post('email');
            $contact_model->contact_type = $this->input->post('contact_type');
            $contact_model->description = $this->input->post('ques_description');
            $contact_model->created_date = date('Y-m-d H:i:s', time());

            $ar_email['sender_full_name'] = $contact_model->full_name;
            $ar_email['sender_email'] = $contact_model->email;
            $ar_email['to_name'] = $email_config[$contact_model->contact_type]['to']['full_name'];
            $ar_email['to_email'] = $email_config[$contact_model->contact_type]['to']['email'];
            $ar_email['cc_name'] = $email_config[$contact_model->contact_type]['cc']['full_name'];
            $ar_email['cc_email'] = $email_config[$contact_model->contact_type]['cc']['email'];
            $ar_email['bcc_name'] = $email_config[$contact_model->contact_type]['bcc']['full_name'];
            $ar_email['bcc_email'] = $email_config[$contact_model->contact_type]['bcc']['email'];
            $ar_email['html'] = true;

            $ar_email['subject'] = $email_config[$contact_model->contact_type]['subject'];
            $ar_email['message'] = $contact_model->description;

            if ($contact_model->validate()) {

                if (send_mail($ar_email)) {

                    if ($contact_model->save()) {
                        $data['saved'] = TRUE;
                        $data['errors'][] = 'We appreciate that you have taken the time to write us. We’ll get back to you very soon. Please come back and see us often.';
                    } else {
                        $data['saved'] = FALSE;
                        $data['errors'] = $contact_model->error->all;
                    }
                } else {
                    $data['saved'] = FALSE;
                    $data['errors'][] = 'Something bad happend. Your message could not be sent at the moment. Please try again after sometime.';
                }
            }

            echo json_encode($data);
            exit;
        }

        $s_content = $this->load->view('contact_us', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['free_user_types'] = $this->get_free_user_types();
            }
        }

        $str_title = WEBSITE_NAME . " | Contact Us";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "contact-us",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function createpage() {

        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "createpage";
        $data['ci_key_for_cover'] = "createpage";
        $data['s_category_ids'] = "0";

        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $data['layout'] = $layout_settings->value;

        if (isset($_POST) && !empty($_POST)) {

            $this->load->config('champs21');

            $email_config = $this->config->config['contact_email_addr'];

            $contact_model = new Contact_us();

            $contact_model->full_name = $this->input->post('full_name');
            $contact_model->email = $this->input->post('email');
            $contact_model->contact_type = $this->input->post('contact_type');
            $contact_model->description = $this->input->post('ques_description');
            $contact_model->created_date = date('Y-m-d H:i:s', time());

            $ar_email['sender_full_name'] = $contact_model->full_name;
            $ar_email['sender_email'] = $contact_model->email;
            $ar_email['to_name'] = $email_config[$contact_model->contact_type]['to']['full_name'];
            $ar_email['to_email'] = $email_config[$contact_model->contact_type]['to']['email'];
            $ar_email['cc_name'] = $email_config[$contact_model->contact_type]['cc']['full_name'];
            $ar_email['cc_email'] = $email_config[$contact_model->contact_type]['cc']['email'];
            $ar_email['bcc_name'] = $email_config[$contact_model->contact_type]['bcc']['full_name'];
            $ar_email['bcc_email'] = $email_config[$contact_model->contact_type]['bcc']['email'];

            $ar_email['subject'] = $email_config[$contact_model->contact_type]['subject'];
            $ar_email['message'] = $contact_model->description;

            if ($contact_model->validate()) {

                if (send_mail($ar_email)) {

                    if ($contact_model->save()) {
                        $data['saved'] = TRUE;
                        $data['errors'][] = 'Seccessfully Saved.';
                    } else {
                        $data['saved'] = FALSE;
                        $data['errors'] = $contact_model->error->all;
                    }
                }
            }

            echo json_encode($data);
            exit;
        }

        $s_content = $this->load->view('createpage', $data, true);

        $s_right_view = "";
        $cache_name = "common/right_view";
        if (!$s_widgets = $this->cache->file->get($cache_name)) {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');

            $obj_widgets = $query->result();

            if ($obj_widgets) {
                $data2['free_user_types'] = $this->get_free_user_types();
            }
        }

        $str_title = WEBSITE_NAME . " | Create Page";

        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "contact-us",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    private function get_welcome_message($full_name = '', $b_image_mail = false, $b_join_school = false) {

        $message = '<!DOCTYPE HTML>';

        $message .= '<head>';
        $message .= '<meta http-equiv="content-type" content="text/html">';

        if ($b_join_school) {
            $message .= '<title>Welcome to Champs21.com</title>';
        } else {
            $message .= '<title>Join to school</title>';
        }
        $message .= '<body>';

        if (!$b_image_mail) {

            if ($b_join_school) {

                if (!empty($full_name)) {
                    $message .= '<p>Hi ' . $full_name . ',</p>';
                }

                $message .= '<p>Your request for joining to the school has been accepted and under processing.</p>';
                $message .= '<p> We&#39;ll inform you as soon as your request is approved.</p>';

                $message .= '<p>Thank you once again for your time and patience.</p>';
                $message .= '<p>Best Regards,</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>Champs21.com</p>';
            } else {

                $message .= '<div id="header" style="width: 50%; height: 60px; margin: 0 auto; padding: 10px; color: #fff; text-align: center; background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">';
                $message .= '<img height="50" width="220" style="border-width:0" src="' . base_url('styles/layouts/tdsfront/images/logo-new.png') . '" alt="Champs21.com" title="Champs21.com">';
                $message .= '</div>';

                if (!empty($full_name)) {
                    $message .= '<p>Hi ' . $full_name . ',</p>';
                }

                $message .= '<p>Thank you for joining Champs21.com and welcome to country&#39;s largest portal for Students | Teachers | Parents. I&#39;m writing this mail to Thank You and giving you a little brief on our services and features.</p>';
                $message .= '<p>
                    Champs21.com, the pioneer eLearning program of Bangladesh, has been dedicatedly and very
                    humbly working with the objectives to better prepare our students as the Champions of 21st Century. 
                    The portal offers various educational and non-educational contents on daily basis for every family 
                    that has a school going student.</p>';

                $message .= '<p>
                    <a href="' . base_url('resource-centre') . '" style="color:#000000; text-decoration: underline; font-weight: bold; ">Resource Centre</a> is the most important section where you will find education content not for students 
                    but also teaching and learning resources for teachers and parents on various subjects. All the 
                    education contents are developed by professional pool of teachers from Champs21.com. Please feel 
                    free and <a href="' . base_url() . '" style="color:#000000; text-decoration: underline; ">apply</a>, if you want to join us as a teacher. Education resources uploaded by others are 
                    carefully checked and modified before it is uploaded for our respected users. Please <a href="' . base_url() . '" style="color:#000000; text-decoration: underline; font-weight: bold; ">Candle</a> now if 
                    you want to share any resources with our education community.</p>';

                $message .= '<p>
                    Our non-education contents i.e. Tech News, Sports News, Entertainment, Health & Nutrition, 
                    Literature, Travel, Games and Videos are also very popular among our family members. Our 
                    continued efforts are always there to research and develop contents in order to make them truly 
                    useful for you.</p>';

                $message .= '<p>
                    <a href="' . base_url('schools') . '" style="color:#000000; text-decoration: underline; font-weight: bold; ">Schools</a> section offers and extensive database of schools in the country. This makes your life simpler 
                    to collect information about any particular school. If you are a teacher, create your <a href="' . base_url('schools') . '" style="color:#000000; text-decoration: underline; ">School</a> if it is not 
                    already there.</p>';

                $message .= '<p>
                    <strong>Good Read</strong> allows you to save the articles and create your own library of resources. You can save 
                    your favourite articles and read them again and again at later dates at your convenience.</p>';

                $message .= '<p>
                    Do you think you can contribute to our Students | Teachers | Parents community? <a href="' . base_url() . '" style="color:#000000; text-decoration: underline; font-weight: bold; ">Candle</a> us your 
                    article now and spread light. Other than only education, you can write and Candle on any available 
                    sections of Champs21.com.</p>';

                $message .= '<p>
                    As a registered user, you can now make <strong>preference settings</strong> and get only favourite content feeding 
                    on your home page.</p>';

                $message .= '<p>
                    You are very important to us. So is our every other student, teacher and parent of our beloved 
                    country. If you like our resources, please do <span style="text-decoration: underline; ">spread</span> this message among your near and dear ones.</p>';

                $message .= '<p>Thank you once again for your time and patience.</p>';
                $message .= '<p>Best Regards,</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>Russell T. Ahmed</p>';
                $message .= '<p>Founder &amp; CEO</p>';
            }
        } else {
            $message .= '<img src="' . base_url('/styles/layouts/tdsfront/image/welcome-email.png') . '">';
        }

        $message .= '</body>';
        $message .= '</head>';

        return $message;
    }

    public function plus_api($param) {


        $this->load->library('plus_api');
        $ar_user_dt = $this->plus_api->get_data_login();
        $ar_params = array(
            'school_code' => $ar_user_dt['school_code'],
            "username" => $ar_user_dt['username'],
            "password" => $ar_user_dt['password']
        );
        $int_response = $this->plus_api->init($ar_params, false);


        if ($int_response != FALSE) {
            //
            //$ar_params = array("username"=>"nbs-ST0001","password"=>"123456"); 
            //echo $res = $this->plus_api->login($ar_params, 'users/loginhook');

            $res = $this->plus_api->call__("get", 'reminders', 'get_data_reminder');
            echo "<pre>";
            print_r($res);
        }
        exit;
    }

    public function plus_api3() {

        $CI = &get_instance();

        $CI->load->library('plus_api');

        $ar_params = array(
            'username' => get_free_user_session('paid_username'),
            'password' => get_free_user_session('paid_password'),
            'school_code' => get_free_user_session('paid_school_code')
        );

        $int_response = $CI->plus_api->init($ar_params, false);
        if ($int_response != FALSE) {
            //
            //$ar_params = array("username"=>"nbs-ST0001","password"=>"123456"); 
            //echo $res = $this->plus_api->login($ar_params, 'users/loginhook');

            $res = $CI->plus_api->call__("get", 'timetables', 'get_data_timetables');

            echo "<pre>";
            print_r($res);
        }
        exit;
    }
    
    /// USER CREATION FOR PAID ///
     
    
    
    
    /// USER CREATION FOR PAID ///
    
    
    

    public function paid_regiser() {

        $user_data = get_free_user_session();

        $activation_code = $this->uri->segment(2);
        $school_code = $this->uri->segment(3);

        $user_rand = $this->cache->file->get("auth_" . $activation_code);
        if ($user_rand) {
            $random = $user_rand;
        } else {
            $random = md5(rand());

            $insert['auth_id'] = $random;
            $insert['activation_code'] = $activation_code;
            $insert['user_id'] = $user_data['id'];
            $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));

            $this->db->insert("user_auth", $insert);

            $this->cache->file->save("auth_" . $activation_code, $random, 82800);
        }

        $params = '?user_id=' . $user_data['id'] . '&auth_id=' . $random . '&activation_code=' . $activation_code . '&first_name=' . $user_data['first_name'] . '&middle_name=' . $user_data['middle_name'] . '&last_name=' . $user_data['last_name'] . '&dob=' . $user_data['dob'] . '&bng=' . $user_data['bng_pwd'] . '&country_id=' . $user_data['country_id'] . '&gender=' . $user_data['gender'] . '&email=' . $user_data['email'];

        $url = "http://" . $school_code . ".champs21.com/user/new_student_registration" . $params;

        header("Location: " . $url);
        exit;
    }

    public function school_feed_for_paid() {

        $ar_js = array();
        $ar_css = array();
        $extra_js = '';

        $data = array();

        $data['ci_key'] = "index";
        $data['ci_key_for_cover'] = "index";
        $data['s_category_ids'] = "0";

        $data['school_id'] = 0;

        if ($_GET['paid_school_id']) {
            $school_id = $this->input->get('paid_school_id');

            $this->db->select("id");
            $this->db->from("school");
            $this->db->where("paid_school_id", $school_id);
            $free_school_id = $this->db->get()->row();

            if ($free_school_id) {
                $data['school_id'] = $free_school_id->id;
            }
        }

        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();

        $s_content = $this->load->view('school_feed_for_paid', $data, true);

        $str_title = getCommonTitle();


        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;

        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "index",
            "full_template" => FALSE,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    public function quiz() {

        $this->load->config("huffas");
        $assessment_config = $this->config->config['assessment'];

        $ar_js = array();
        $ar_css = array(
            "styles/layouts/tdsfront/css/post/social.css" => "screen"
        );
        $extra_js = '';

        $uri_segments = $this->uri->segment_array();
        $str_assesment = $uri_segments[2];

        if (count($uri_segments) < 4 && is_numeric($uri_segments[3])) {
            $assessment_level = $uri_segments[3];
            $str_post = NULL;
        } elseif (count($uri_segments) == 4 && is_numeric($uri_segments[4])) {
            $assessment_level = $uri_segments[4];
            $str_post = $uri_segments[3];
        } else {
            $assessment_level = 0;
            $str_post = $uri_segments[3];
        }

        $ar_assessment = explode('-', $str_assesment);
        $assessment_type = $ar_assessment[count($ar_assessment) - 2];
        $assesment_id = end($ar_assessment);
        $user_id = 0;

        if (free_user_logged_in()) {
            $user_id = get_free_user_session('id');
        } else {
            $assessment_level = 0;
        }

        if ($assessment_config['update_played']['before_start']) {
            assessment_update_played($assesment_id);
        }

        $data['post_uri'] = $str_post;
        $assessment = get_assessment($assesment_id, $user_id, 1, $assessment_level, $assessment_type);

        if (!property_exists($assessment->assesment, 'id')) {

            $this->show_404_custom();
        } else {

            if ((!$assessment)) {
                $data['assessment'] = array();
                $data['school_score_board'] = array();
                $data['score_board'] = array();
                $data['can_play'] = false;
                $data['last_played'] = false;
            } else {

                $obj_post = new Post_model();

                $assessment->assesment->assess_user_mark = $obj_post->get_user_assessment_marks($user_id, $assesment_id);

                foreach ($assessment->assesment->assess_user_mark as $user_marks) {
                    $user_played_levels[] = $user_marks->level;
                }

                $ar_assessment_levels = explode(',', $assessment->assesment->levels);

                $assessment->assesment->assessment_has_levels = FALSE;

                if ($assessment->assesment->levels > 0) {
                    $assessment->assesment->assessment_has_levels = TRUE;
                    $assessment->assesment->next_level = min(array_diff($ar_assessment_levels, $user_played_levels));
                }

                $data['assessment'] = $assessment->assesment;
                $data['school_score_board'] = $assessment->school_score_board;
                $data['score_board'] = $assessment->score_board;
                $data['can_play'] = $assessment->can_play;
                $data['last_played'] = $assessment->last_played;
            }

            $s_content = $this->load->view('assessment', $data, true);

            $str_title = WEBSITE_NAME . " | Quiz";

            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;

            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "title" => $str_title,
                "description" => $meta_description,
                "keywords" => $keywords,
                "side_bar" => '',
                "target" => "index",
                "content" => $s_content
            );

            $this->extra_params = $ar_params;
        }
    }

    public function cricaddict() {

        $this->load->config("huffas");
        $assessment_config = $this->config->config['assessment'];

        $ar_js = array();
        $ar_css = array(
            "styles/layouts/tdsfront/css/post/social.css" => "screen"
        );
        $extra_js = '';

        $uri_segments = $this->uri->segment_array();
        $str_assesment = $uri_segments[2];

        if (count($uri_segments) < 4 && is_numeric($uri_segments[3])) {
            $assessment_level = $uri_segments[3];
            $str_post = NULL;
        } elseif (count($uri_segments) == 4 && is_numeric($uri_segments[4])) {
            $assessment_level = $uri_segments[4];
            $str_post = $uri_segments[3];
        } else {
            $assessment_level = 0;
            $str_post = $uri_segments[3];
        }

        $ar_assessment = explode('-', $str_assesment);
        $assessment_type = $ar_assessment[count($ar_assessment) - 2];
        $assesment_id = end($ar_assessment);
        $user_id = 0;

        if (free_user_logged_in()) {
            $user_id = get_free_user_session('id');
        } else {
            $assessment_level = 1;
        }

        if ($assessment_config['update_played']['before_start']) {
            assessment_update_played($assesment_id);
        }

        $data['post_uri'] = $str_post;
        $assessment = get_assessment($assesment_id, $user_id, 1, $assessment_level, $assessment_type);

        if (!property_exists($assessment->assesment, 'id')) {

            $this->show_404_custom();
        } else {

            if ((!$assessment)) {
                $data['assessment'] = array();
                $data['school_score_board'] = array();
                $data['score_board'] = array();
                $data['can_play'] = false;
                $data['last_played'] = false;
            } else {

                $obj_post = new Post_model();

                $assessment->assesment->assess_user_mark = $obj_post->get_user_assessment_marks($user_id, $assesment_id);

                $user_played_levels = array();
                foreach ($assessment->assesment->assess_user_mark as $user_marks) {
                    $user_played_levels[] = $user_marks->level;
                }

                $ar_assessment_levels = explode(',', $assessment->assesment->levels);

                $assessment->assesment->assessment_has_levels = FALSE;

                if ($assessment->assesment->levels > 0) {
                    $assessment->assesment->assessment_has_levels = TRUE;
                    $unplayed_levels = array_diff($ar_assessment_levels, $user_played_levels);
                    $assessment->assesment->next_level = min($unplayed_levels);
                }

                $assessment->assesment->unplayed_levels = $unplayed_levels;
                $assessment->assesment->ar_assessment_levels = $ar_assessment_levels;
                $data['assessment'] = $assessment->assesment;
                $data['school_score_board'] = $assessment->school_score_board;
                $data['score_board'] = $assessment->score_board;
                $data['can_play'] = $assessment->can_play;
                $data['last_played'] = $assessment->last_played;
            }

            $s_content = $this->load->view('assessment', $data, true);

            $str_title = WEBSITE_NAME . " | Quiz";

            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;

            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "title" => $str_title,
                "description" => $meta_description,
                "keywords" => $keywords,
                "side_bar" => '',
                "target" => "index",
                "content" => $s_content
            );

            $this->extra_params = $ar_params;
        }
//        var_dump();
//        exit;
    }

    public function save_assessment() {

        $this->load->config("huffas");
        $this->load->config('user_register');

        $b_mulit_school_join = $this->config->config['multi_school_join'];
        $assessment_config = $this->config->config['assessment'];

        $response = array();
        if (!$this->input->is_ajax_request()) {
            $response['saved'] = false;
            $response['error'] = 'BAD_REQUEST';
            echo json_encode($response);
            exit;
        }

        $data = trim($_POST['data'], ',');

        $add_to_school = $this->input->post('add_to_school');
        $cur_level = $this->input->post('cur_level');

        if ($cur_level > 0) {
            $cur_level = $cur_level;
        } else {
            $cur_level = 0;
        }

        $ar_data = explode('_', $data);

        $assessment_id = $ar_data[0];

        $user_id = 0;

        if (free_user_logged_in()) {
            $user_id = get_free_user_session('id');

            $obj_assessment_mark = new Assesment_mark();
            $obj_assessment_school_mark = new Assessment_school_mark();
            $user_school = new User_school();
            $score_add_to_school = TRUE;

            if ($add_to_school == 'false') {

                $user_school_data = ($b_mulit_school_join) ? $user_school->get_user_school($user_id, $school_id) : $user_school->get_user_school($user_id);
                $assessment_school_mark = $obj_assessment_school_mark->find_assessment_school_mark($user_id, $assessment_id, 1, $user_school_data[0]->school_id);

                if (!$assessment_school_mark) {
                    $score_add_to_school = FALSE;
                }
            }
        }

        if ($assessment_config['update_played']['after_finish']) {
            assessment_update_played($assesment_id);
        }

        $assessment = get_assessment($assessment_id, $user_id);

        $total_mark = 0;
        $user_mark = 0;
        $ar_q_a = explode(',', $ar_data[2]);

        $i = 0;
        foreach ($assessment->assesment->question as $question) {
            $total_mark += $question->mark;
            $i++;

            foreach ($ar_q_a as $str_q_a) {
                $q_a = explode('-', $str_q_a);

                $q = $q_a[0];
                $a = $q_a[1];

                if ($question->id == $q) {

                    foreach ($question->option as $option) {

                        if ($option->id == $a) {

                            if ($option->correct == 1) {
                                $user_mark += $question->mark;
                            }
                        }
                    }
                }
            }
        }

        $avg_time = $ar_data[1] / $i;
        $ar_asses_levels = explode(',', $assessment->assesment->levels);

        if (free_user_logged_in()) {

            $user_id = get_free_user_session('id');

            $assessment_mark = $obj_assessment_mark->find_assessment_mark($user_id, $assessment_id, $cur_level);
            $user_school_data = ($b_mulit_school_join) ? $user_school->get_user_school($user_id, $school_id) : $user_school->get_user_school($user_id);

            if ($user_school_data != FALSE) {
                $assessment_school_mark = $obj_assessment_school_mark->find_assessment_school_mark($user_id, $assessment_id, $cur_level, $user_school_data[0]->school_id);
                $response['has_school'] = TRUE;
            } else {
                $response['has_school'] = FALSE;
            }

            $next_play_time = strtotime(date('Y-m-d H:i:s', strtotime($assessment_mark->created_date . "+1 Day")));
            $now = date('Y-m-d H:i:s');
            $now_time = strtotime($now);

            $can_play = TRUE;

            if (($now_time < $next_play_time) && $assessment_mark !== FALSE) {
                $can_play = FALSE;
            }
            $can_play = TRUE;
            if ($can_play) {

                if (($assessment_mark != false)) {

                    $ar_lb = array(
                        'created_date' => $now,
                        'no_played' => $assessment_mark->no_played + 1,
                    );

                    if ($user_mark > $assessment_mark->mark) {
                        $ar_lb['mark'] = $user_mark;
                    }

                    if ((empty($assessment_mark->time_taken)) || ($ar_data[1] < $assessment_mark->time_taken)) {
                        $ar_lb['time_taken'] = $ar_data[1];
                    }

                    if ((empty($assessment_mark->avg_time_per_ques)) || ($avg_time < $assessment_mark->avg_time_per_ques)) {
                        $ar_lb['avg_time_per_ques'] = number_format($avg_time, 2);
                    }

                    $this->db->update('assesment_mark', $ar_lb, array('user_id' => $user_id, 'assessment_id' => $assessment_id, 'level' => $cur_level));
                    $response['highest_score'] = $user_mark;
                } else {

                    $obj_assessment_mark->user_id = $user_id;
                    $obj_assessment_mark->assessment_id = $assessment_id;
                    $obj_assessment_mark->mark = $user_mark;
                    $obj_assessment_mark->level = $cur_level;
                    $obj_assessment_mark->created_date = $now;
                    $obj_assessment_mark->time_taken = $ar_data[1];
                    $obj_assessment_mark->avg_time_per_ques = number_format($avg_time, 2);
                    $obj_assessment_mark->no_played = 1;

                    $obj_assessment_mark->save();

                    $response['highest_score'] = $user_mark;
                }

                if (($assessment_school_mark != false)) {

                    $this->db->where('user_id', $user_id);
                    $this->db->where('assessment_id', $assessment_id);
                    $this->db->delete('assessment_school_mark');
                }

                if ($score_add_to_school && $response['has_school']) {

                    $assessment_mark = $obj_assessment_mark->find_assessment_mark_all($user_id, $assessment_id);

                    if ($assessment_mark !== FALSE) {

                        $assessment_school_mark = array();
                        foreach ($assessment_mark as $am) {

                            $assessment_school_mark_data['user_id'] = $user_id;
                            $assessment_school_mark_data['assessment_id'] = $assessment_id;
                            $assessment_school_mark_data['mark'] = $am->mark;
                            $assessment_school_mark_data['level'] = $am->level;
                            $assessment_school_mark_data['school_id'] = $user_school_data[0]->school_id;
                            $assessment_school_mark_data['created_date'] = $am->created_date;
                            $assessment_school_mark_data['time_taken'] = $am->time_taken;
                            $assessment_school_mark_data['avg_time_per_ques'] = $am->avg_time_per_ques;
                            $assessment_school_mark_data['no_played'] = $am->no_played;

                            $assessment_school_mark[] = $assessment_school_mark_data;
                        }

                        $this->db->insert_batch('assessment_school_mark', $assessment_school_mark);
                    }
                }

                $response['saved'] = true;
            }

            $assessment_user_total_mark = $obj_assessment_mark->find_user_assessment_total_mark($user_id, $assessment_id);
        }

        $next_level = 0;

        if (count($ar_asses_levels) > $cur_level) {
            $next_level = $cur_level + 1;
        }

        $response['cur_level'] = $cur_level;
        $response['assessment_id'] = sanitize($assessment->assesment->id);
        $response['assessment_title'] = sanitize($assessment->assesment->title);
        $response['assessment_type'] = $assessment->assesment->type;
        $response['assessment_levels'] = $assessment->assesment->levels;
        $response['next_level'] = $next_level;
        $response['score'] = $user_mark;
        $response['total_score'] = $total_mark;
        $response['user_total_score'] = $assessment_user_total_mark->mark;
        echo json_encode($response);
        exit;
    }

    public function assessment_leader_board() {

        $response = array();
        if (!$this->input->is_ajax_request()) {
            $response['saved'] = false;
            $response['error'] = 'Bad Request';
            echo json_encode($response);
            exit;
        }
        $assessment_id = $this->input->post('assessment_id');
        $assessment_type = $this->input->post('type');

        $assessment_leader_board = get_assessment_leader_board($assessment_id, 100, $assessment_type);

        $response['leader_board'] = $assessment_leader_board;

        echo json_encode($response);
        exit;
    }

    public function invite_friend_by_email() {

        $response = array();
        if (!$this->input->is_ajax_request()) {
            $response['invitation_sent'] = false;
            $response['error'] = 'Bad Request';
            echo json_encode($response);
            exit;
        }

        if (free_user_logged_in()) {
            $user_data = get_free_user_session();
        }

        $to_name = $this->input->post('friend_name');
        $to_email = $this->input->post('friend_email');

        $ar_email['sender_full_name'] = $user_data['full_name'];
        $ar_email['sender_email'] = $user_data['email'];
        $ar_email['to_name'] = $to_name;
        $ar_email['to_email'] = $to_email;
        $ar_email['html'] = true;

        $ar_email['subject'] = 'ICC World Cup 2015 Quiz at Champs21.com';
        $ar_email['message'] = $this->invite_friend_by_email_body($ar_email);

        if (send_mail($ar_email)) {
            $response['invitation_sent'] = TRUE;
            $response['error'] = 'NO_ERROR';
        } else {
            $response['invitation_sent'] = FALSE;
            $response['error'] = 'invitation not sent. Please try later.';
        }

        echo json_encode($response);
        exit;
    }

    public function successfully_school_information_send() {

        $data['all_ar_templates'] = $this->config->config['school_templates'];

        if (!isset($_GET['id']) || !isset($data['all_ar_templates'][$_GET['id']])) {
            redirect('/create-school-website');
        }

        $id = $_GET['id'];
        $data['ci_key'] = 'new_school';

        $data['ar_templates'] = $data['all_ar_templates'][$id];

        $s_content = $this->load->view('successfully_school_information_send', $data, true);

        // User Data
        $data['join_user_types'] = $this->get_school_join_user_types();
        // User Data

        $s_right_view = '';

        $str_title = "New School Information Send";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schools",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }

    private function validate_cookie($cookie) {

        $free_user = new Free_users();

        $free_user->cookie_token = $cookie;
        $user_data = $free_user->cookie_login();

        if ($user_data !== false) {
            $this->set_user_session($user_data, NULL, FALSE, TRUE);
            return free_user_logged_in();
        }
    }

    private function invite_friend_by_email_body($param) {

        $message = '<!DOCTYPE HTML>';

        $message .= '<head>';
        $message .= '<meta http-equiv="content-type" content="text/html">';

        $message .= '<title>ICC World Cup 2015 Quiz at Champs21.com</title>';

        $message .= '<body>';

        if (!empty($param['full_name'])) {
            $message .= '<p>Hi ' . $param['full_name'] . ',</p>';
        } else {
            $message .= '<p>Hi,</p>';
        }

        $message .= '<p>I have played ICC World Cup 2015 Quiz at Champs21.com. It&#39;s really an amazing experience. You should play too.</p>';
        $message .= '<p>They are giving very exciting prizes to the winners.</p>';

        $message .= '<p>Best Regards,</p>';
        $message .= '<p>&nbsp;</p>';
        $message .= '<p>&nbsp;</p>';
        $message .= '<p>' . $ar_email['sender_full_name'] . '</p>';

        $message .= '</body>';
        $message .= '</head>';

        return $message;
    }

    public function doUpload($field_name) {
        $config['upload_path'] = 'upload/school_imformation/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';

        $config['allowed_types'] = 'gif|jpg|jpeg|png|docx|doc|zip';

        $config['file_name'] = 'info_' . time();

        $config['max_size'] = '5000';

        $config['max_width'] = '3920';

        $config['max_height'] = '4280';

        if (!is_dir($config['upload_path'])) { //create the folder if it's not already exists
            mkdir($config['upload_path'], 0755, TRUE);
        }



        $this->load->library('upload', $config);





        if (!$this->upload->do_upload($field_name)) {
            $error = array('error' => $this->upload->display_errors());
        } else {
            $fInfo = $this->upload->data();

            //$this->_createThumbnail($fInfo['file_name']);

            $data['uploadInfo'] = $fInfo;

            $data['thumbnail_name'] = $fInfo['file_name'];

            return $config['upload_path'] . $fInfo['file_name'];
        }
    }

}
