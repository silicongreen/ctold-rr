<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class paid extends MX_Controller {


    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('datamapper');
        $this->load->helper('form');

        $this->layout_front = false;
        $this->obj_post = new Post_model();
    }

    public function apply_for_parent_admission() {
        $user_type = array(4);
        if ($user_data = $this->check_user_valid($user_type)) {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();

            $data['ci_key'] = "apply_for_parent_admission";
            $data['ci_key_for_cover'] = "apply_for_parent_admission";
            $data['s_category_ids'] = "0";
            $data['user_data'] = $user_data;

            $data['post_data'] = $this->process_post_data($user_data);

            if (isset($_POST) && !empty($_POST)) {

                $data['post_data'] = $_POST;
                $this->load->library('form_validation');
                $this->form_validation->set_rules('admission_no', 'Admission No', 'required|ci_check_admission_no_parent');
                $this->form_validation->set_rules('password', 'Password', 'required|ci_check_password');
                $this->form_validation->set_rules('first_name', 'First Name', 'required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'required');
                $this->form_validation->set_rules('relation', 'Relation', 'required');
                $this->form_validation->set_rules('dob', 'Birth date', 'required|ci_validate_date');
                $this->form_validation->set_rules('city', 'City', 'required');

                if ($this->form_validation->run() == TRUE) {


                    //update free user
                    //insert user data

                    $u_id = $this->create_paid_user_for_all($user_data, $this->input->post());
                    if ($u_id[0]) {
                        //update free user
                        $this->update_user_before_apply($user_data, $this->input->post(), $u_id[1]);

                        $ward = get_user_default_data($this->input->post("admission_no"), $user_data);
                        $this->db->dbprefix = '';
                        $st = $this->get_parent_default_data($user_data, $u_id[0]);

                        $st['first_name'] = $this->input->post("first_name");
                        $st['last_name'] = $this->input->post("last_name");
                        $st['dob'] = $this->input->post("dob");
                        $st['city'] = $this->input->post("city");
                        $st['relation'] = $this->input->post("relation");
                        $st['ward_id'] = $ward->id;


                        $st['occupation'] = $this->input->post("occupation");
                        $st['mobile_phone'] = $this->input->post("mobile_phone");

                        $this->db->insert('guardians', $st);

                        $gid_id = $this->db->insert_id();

                        if (!$ward->immediate_contact_id) {
                            $sb['immediate_contact_id'] = $gid_id;
                            $this->db->where('id', $ward->id);
                            $this->db->update('students', $sb);
                        }

                        $this->db->dbprefix = 'tds_';
                    }




                    redirect(base_url("user-apply-success"));
                }
            }

            $s_content = $this->load->view('apply_for_parrent_admission', $data, true);

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
        } else {
            redirect(base_url());
        }
    }

    public function apply_for_student_admission() {
        $user_type = array(1, 2, 3, 4);
        $back_url = $this->input->get("back_url");
        if ($back_url && $user_data = $this->check_user_valid($user_type)) {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();

            $data['ci_key'] = "apply_for_student_admission";
            $data['ci_key_for_cover'] = "apply_for_student_admission";
            $data['s_category_ids'] = "0";
            $data['user_data'] = $user_data;
            $data['post_data'] = $this->process_post_data($user_data);


            if (isset($_POST) && !empty($_POST)) {
                $data['post_data'] = $_POST;
                $this->load->library('form_validation');
                $this->form_validation->set_rules('admission_no', 'Admission No', 'required|ci_check_admission_no');
                $this->form_validation->set_rules('admission_date', 'Admission Date', 'required');
                $this->form_validation->set_rules('password', 'Password', 'required|ci_check_password');
                $this->form_validation->set_rules('first_name', 'First Name', 'required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'required');
                $this->form_validation->set_rules('batch_id', 'Class Name', 'required');
                $this->form_validation->set_rules('date_of_birth', 'Birth date', 'required|ci_validate_date');
                $this->form_validation->set_rules('city', 'City', 'required');



                $add_guardian = $this->input->post("add_guardian");

                if ($add_guardian == "one" || $add_guardian == "two") {
                    $this->form_validation->set_rules('gfirst_name', 'Guardian First Name', 'required');
                    $this->form_validation->set_rules('glast_name', 'Guardian Last Name', 'required');
                    $this->form_validation->set_rules('gpassword', 'Guardian Password', 'required');
                    $this->form_validation->set_rules('relation', 'Relation', 'required');
                }
                if ($add_guardian == "two") {
                    $this->form_validation->set_rules('gfirst_name2', 'Guardian First Name (2nd)', 'required');
                    $this->form_validation->set_rules('glast_name2', 'Guardian Last Name (2nd)', 'required');
                    $this->form_validation->set_rules('gpassword2', 'Guardian Password (2nd)', 'required');
                    $this->form_validation->set_rules('relation2', 'Relation (2nd)', 'required');
                }

                if ($this->form_validation->run() == TRUE) {



                    //insert user data

                    $u_id = $this->create_paid_user_for_all($user_data, $this->input->post());


                    //insert student data

                    if ($u_id[0]) {
                        $this->update_user_before_apply($user_data, $this->input->post(), $u_id[1]);
                        $this->db->dbprefix = '';
                        $st = $this->get_user_default_data($user_data, $u_id[0]);
                        $st['admission_no'] = $this->input->post("admission_no");
                        $st['first_name'] = $this->input->post("first_name");
                        $st['last_name'] = $this->input->post("last_name");
                        $st['admission_date'] = $this->input->post("admission_date");
                        
                        $st['batch_id'] = $this->input->post("batch_id");
                        $st['date_of_birth'] = $this->input->post("date_of_birth");
                        $st['city'] = $this->input->post("city");

                        $st['class_roll_no'] = $this->input->post("class_roll_no");
                        $st['middle_name'] = $this->input->post("middle_name");
                        
                        $st['gender'] = $this->input->post("gender");

                        $this->db->insert('students', $st);

                        $std_id = $this->db->insert_id();

                        $sb['sibling_id'] = $std_id;
                        $this->db->where('id', $std_id);
                        $this->db->update('students', $sb);

                        $n_g = 1;
                        if ($add_guardian == "two") {
                            $n_g = 2;
                        }
                        //create guardian
                        $this->db->dbprefix = 'tds_';
                        if ($add_guardian == "one" || $add_guardian == "two") {
                            $this->createGuardianStudent($this->input->post(), $n_g, $std_id, $user_data);
                        }
                    }



                    $back = $this->redirect_parent_url($back_url);
                    echo $back;
                    exit;
                }
            }

            $this->load->view('apply_for_student_admission', $data);

            
        } else {
            $back = $this->redirect_parent_url(base_url());
            echo $back;
            exit;
        }
    }

    private function redirect_parent_url($url) {
        $return = "<script>window.top.location.href = '" . $url . "'</script>";
        return $return;
    }

    public function test_view() {
        $this->load->view('test_view');
    }

    public function select_school() {
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $user_type = array(1, 2, 3, 4);
        $user_type_check = array(2, 3, 4);
        if (!$user_type || !$back_url || !in_array($user_type_send, $user_type_check)) {
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        } else if ($user_data = $this->check_user_valid($user_type)) {
           
            if ($user_type_send == 2) {
                redirect('front/paid/apply_for_student_admission?back_url=' . $back_url);
            } else if ($user_type_send == 3) {
                redirect('front/paid/apply_for_teacher_admission?back_url=' . $back_url);
            } else if ($user_type_send == 4) {
                redirect('front/paid/apply_for_parent_admission?back_url=' . $back_url);
            }
        } else if ($user_data = $this->check_user_valid($user_type, false)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('paid_school_id', 'School', 'required');
            $this->form_validation->set_rules('school_code', 'School Code', 'required|ci_school_code_check');
            
            
            if ($this->form_validation->run() == TRUE) {
                
                $free_users = new Free_users($user_data->id);
                $free_users->paid_school_id = $this->input->post("paid_school_id");
                $free_users->save();
                        
                if($user_type_send == 2) {
                    redirect('front/paid/apply_for_student_admission?back_url=' . $back_url);
                } else if ($user_type_send == 3) {
                    redirect('front/paid/apply_for_teacher_admission?back_url=' . $back_url);
                } else if ($user_type_send == 4) {
                    redirect('front/paid/apply_for_parent_admission?back_url=' . $back_url);
                }
            }
            $this->load->view('select_school');
        } else {
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }
    }

    public function school_code_check($str) {
        $paid_school_id = $this->input->post('paid_school_id');

        if (!check_school_code_paid($paid_school_id, $str)) {
            $this->form_validation->set_message('school_code_check', 'Invalid School Code');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function apply_for_teacher_admission() {
        $this->layout_front = false;
        $user_type = array(1, 2, 3, 4);
        $back_url = $this->input->get("back_url");
        if ($back_url && $user_data = $this->check_user_valid($user_type)) {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();

            $data['ci_key'] = "apply_for_teacher_admission";
            $data['ci_key_for_cover'] = "apply_for_teacher_admission";
            $data['s_category_ids'] = "0";
            $data['user_data'] = $user_data;
            $data['post_data'] = $this->process_post_data($user_data);


            if (isset($_POST) && !empty($_POST)) {
                $data['post_data'] = $_POST;
                $this->load->library('form_validation');
                $this->form_validation->set_rules('admission_no', 'Employee NO', 'required|ci_check_admission_no');
                $this->form_validation->set_rules('password', 'Password', 'required|ci_check_password');
                $this->form_validation->set_rules('first_name', 'First Name', 'required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'required');
                $this->form_validation->set_rules('employee_department_id', 'Employee Department', 'required');
                $this->form_validation->set_rules('employee_category', 'Employee Category', 'required');
                $this->form_validation->set_rules('employee_position_id', 'Employee Position', 'required');
                $this->form_validation->set_rules('date_of_birth', 'Birth date', 'required|ci_validate_date');
                $this->form_validation->set_rules('joining_date', 'Joining date', 'required');


                if ($this->form_validation->run() == TRUE) {




                    $u_id = $this->create_paid_user_for_all($user_data, $this->input->post());


                    if ($u_id[0]) {
                        $this->update_user_before_apply($user_data, $this->input->post(), $u_id[1]);
                        $this->db->dbprefix = '';
                        $st = $this->get_user_default_data($user_data, $u_id[0]);
                        $st['employee_number'] = make_paid_username($user_data, $this->input->post('admission_no'));
                        $st['employee_category_id'] = $this->input->post("employee_category");
                        $st['employee_position_id'] = $this->input->post("employee_position_id");
                        $st['employee_department_id'] = $this->input->post("employee_department_id");
                        $st['employee_grade_id'] = $this->input->post("employee_grade_id");
                        $st['first_name'] = $this->input->post("first_name");
                        $st['last_name'] = $this->input->post("last_name");
                        $st['date_of_birth'] = $this->input->post("date_of_birth");
                        $st['joining_date'] = $this->input->post("joining_date");

                        $this->db->insert('employees', $st);

                        $std_id = $this->db->insert_id();

                        if (isset($_POST['batch_id']) && $_POST['batch_id']) {
                            $eb['employee_id'] = $std_id;
                            $eb['batch_id'] = $this->input->post("batch_id");
                            $this->db->insert('batch_tutors', $eb);
                        }

                        $this->db->dbprefix = 'tds_';
                    }


                    $back = $this->redirect_parent_url($back_url);
                    echo $back;
                    exit;
                }
            }

            $this->load->view('apply_for_teacher_admission', $data);
        } else {
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }
    }

    public function user_apply_success() {
        $user_type = array(2, 3, 4);
        if ($user_data = $this->check_success_user($user_type)) {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();

            $data['ci_key'] = "user_apply_success";
            $data['ci_key_for_cover'] = "user_apply_success";
            $data['s_category_ids'] = "0";
            $data['user_data'] = $user_data;

            if ($user_data->user_type == 2) {
                $data['parents'] = $this->get_student_parents($user_data->id);
            }




            $s_content = $this->load->view('user_apply_success', $data, true);

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
        } else {
            redirect(base_url());
        }
    }

    private function check_success_user($user_type) {
        if (free_user_logged_in()) {
            $user_data = get_user_data();
            if ($user_data) {
                if ($user_data->applied_paid && $user_data->user_type != 1 && $user_data->paid_school_id) {
                    if (in_array($user_data->user_type, $user_type)) {

                        return $user_data;
                    }
                }
            }
        }
        return false;
    }

    private function check_user_valid($user_type, $check_school_selcted = true) {
        if (free_user_logged_in()) {
            $user_data = get_user_data();
            if ($user_data) {
                if (!$user_data->applied_paid && !$user_data->paid_id && ($user_data->paid_school_id || !$check_school_selcted)) {
                    if (in_array($user_data->user_type, $user_type)) {
                        return $user_data;
                    }
                }
            }
        }
        return false;
    }

    private function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function create_paid_user_for_all($user_data, $postdata) {

        $user['username'] = make_paid_username($user_data, $postdata["admission_no"]);
        $user['salt'] = $this->generateRandomString();

        $user['hashed_password'] = sha1($user['salt'] . $postdata["password"]);

        $user['admin'] = 0;
        $user['student'] = 0;
        $user['parent'] = 0;
        $user['employee'] = 0;

        if ($user_data->user_type == 2) {
            $user['student'] = 1;
        } else if ($user_data->user_type == 3) {
            $user['employee'] = 1;
        } else if ($user_data->user_type == 4) {
            $user['parent'] = 1;
        }

        $user['free_user_id'] = $user_data->id;

        $user['is_approved'] = 0;

        $user['first_name'] = $postdata["first_name"];
        $user['last_name'] = $postdata["last_name"];

        $user['is_first_login'] = 0;

        $user['is_deleted'] = 1;
        $user['school_id'] = $user_data->paid_school_id;
        $user['created_at'] = date("Y-m-d H:i:s");
        $user['updated_at'] = date("Y-m-d H:i:s");
        $this->db->dbprefix = '';
        $this->db->insert('users', $user);
        $u_id = $this->db->insert_id();
        $this->db->dbprefix = 'tds_';
        return array($u_id, $user['username']);
    }

    private function update_user_before_apply($user_data, $u_array, $u_id) {
        $f_user['paid_password'] = $u_array['password'];
        $f_user['email'] = $u_id;

        if (isset($u_array['first_name']) && $u_array['first_name']) {
            $f_user['first_name'] = $u_array['first_name'];
        }
        if (isset($u_array['middle_name']) && $u_array['middle_name']) {
            $f_user['middle_name'] = $u_array['middle_name'];
        }
        if (isset($u_array['last_name']) && $u_array['last_name']) {
            $f_user['last_name'] = $u_array['last_name'];
        }
        if (isset($u_array['city']) && $u_array['city']) {
            $f_user['district'] = $u_array['city'];
        }
        if (isset($u_array['dob']) && $u_array['dob']) {
            $f_user['dob'] = $u_array['dob'];
        }
        if (isset($u_array['date_of_birth']) && $u_array['date_of_birth']) {
            $f_user['dob'] = $u_array['date_of_birth'];
        }
        if (isset($u_array['gender']) && $u_array['gender']) {
            if ($u_array['gender'] == "m") {
                $f_user['gender'] = 1;
            } else {
                $f_user['gender'] = 0;
            }
        }

        if (isset($u_array['mobile_phone']) && $u_array['mobile_phone']) {
            $f_user['mobile_no'] = $u_array['mobile_phone'];
        }
        if (isset($u_array['phone1']) && $u_array['phone1']) {
            $f_user['mobile_no'] = $u_array['phone1'];
        }

        $f_user['tds_country_id'] = 14;
        $f_user['nick_name'] = 1;
        $f_user['last_name'] = $u_array['last_name'];
        $f_user['applied_paid'] = 1;
        $this->db->where('id', $user_data->id);
        $this->db->update('free_users', $f_user);
    }

    private function get_user_default_data($user_data, $u_id) {


        if ($user_data->user_type == 2 or $user_data->user_type == 3) {
            $data['nationality_id'] = 14;
        }

        if ($user_data->user_type == 2 or $user_data->user_type == 4) {
            $data['country_id'] = 14;
        }
        if ($user_data->user_type == 2) {
            $data['is_active'] = 0;
            $data['is_deleted'] = 1;
        }
        $data['user_id'] = $u_id;
        $data['school_id'] = $user_data->paid_school_id;
        $data['email'] = $user_data->email;
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['updated_at'] = date("Y-m-d H:i:s");

        return $data;
    }

    private function process_post_data($user_data) {
        $datas = array();
        $datas['first_name'] = $user_data->first_name;
        $datas['last_name'] = $user_data->last_name;
        $datas['middle_name'] = $user_data->middle_name;
        $datas['city'] = $user_data->district;
        $datas['dob'] = $user_data->dob;
        $datas['date_of_birth'] = $user_data->dob;
        $datas['mobile_phone'] = $user_data->mobile_no;
        $datas['phone1'] = $user_data->mobile_no;

        if (isset($user_data->gender) && $user_data->gender) {
            if ($user_data->gender == "1") {
                $datas['gender'] = "m";
            } else {
                $datas['gender'] = "f";
            }
        }
        return $datas;
    }

    private function createGuardianStudent($s_array, $n_g, $sid, $user_data) {

        for ($i = 1; $i <= $n_g; $i++) {
            $f_user_data = new Free_users();
            $extra = "";
            if ($i > 1) {
                $extra = $i;
            }

            $f_user_data->nick_name = 1;
            $f_user_data->first_name = $s_array['gfirst_name' . $extra];
            $f_user_data->email = $s_array['admission_no'] . "@champs21.com";
            $f_user_data->cnf_email = $s_array['admission_no'] . "@champs21.com";
            $f_user_data->password = $s_array['gpassword' . $extra];
            $f_user_data->cnf_password = $s_array['gpassword' . $extra];
            $f_user_data->dob = date("Y-m-d");
            $f_user_data->paid_school_id = $user_data->paid_school_id;

            $f_user_data->tds_country_id = 14;
            $f_user_data->division = $s_array['city'];

            $f_user_data->last_name = $s_array['glast_name' . $extra];
            $f_user_data->user_type = 4;
            $f_user_data->paid_password = $s_array['gpassword' . $extra];

            if ($f_user_data->save()) {
                $user['username'] = make_paid_username($user_data, $s_array["admission_no"], true, true);
                $user['salt'] = $this->generateRandomString();
                $user['hashed_password'] = sha1($user['salt'] . $s_array['gpassword' . $extra]);
                $user['admin'] = 0;
                $user['student'] = 0;
                $user['parent'] = 1;
                $user['employee'] = 0;
                $user['free_user_id'] = $f_user_data->id;
                $user['is_approved'] = 0;
                $user['first_name'] = $s_array['gfirst_name' . $extra];
                $user['last_name'] = $s_array['glast_name' . $extra];
                $user['is_first_login'] = 0;
                $user['is_deleted'] = 1;
                $user['school_id'] = $user_data->paid_school_id;
                $user['created_at'] = date("Y-m-d H:i:s");
                $user['updated_at'] = date("Y-m-d H:i:s");
                $this->db->dbprefix = '';
                $this->db->insert('users', $user);
                $u_id = $this->db->insert_id();




                $ward = get_parent_children($s_array["admission_no"], $user_data);
                $data['user_id'] = $u_id;
                $data['country_id'] = 14;
                $data['school_id'] = $user_data->paid_school_id;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['updated_at'] = date("Y-m-d H:i:s");
                $data['first_name'] = $s_array['gfirst_name' . $extra];
                $data['last_name'] = $s_array['glast_name' . $extra];
                $data['city'] = $s_array['city'];
                $data['relation'] = $s_array['relation' . $extra];
                $data['ward_id'] = $sid;

                $this->db->dbprefix = '';
                $this->db->insert('guardians', $data);

                $gid_id = $this->db->insert_id();

                if (!$ward->immediate_contact_id) {
                    $this->db->dbprefix = '';
                    $sb['immediate_contact_id'] = $gid_id;
                    $this->db->where('id', $ward->id);
                    $this->db->update('students', $sb);
                }

                $this->db->dbprefix = 'tds_';

                //update user data
                $f_user['email'] = $user['username'];
                $f_user['applied_paid'] = 1;
                $f_user['dob'] = NULL;

                $this->db->where('id', $f_user_data->id);
                $this->db->update('free_users', $f_user);
            }
        }
    }

    private function get_student_parents($user_id) {
        $this->db->dbprefix = '';

        $this->db->select("id");
        $this->db->where("free_user_id", $user_id);
        $users = $this->db->get("users")->row();

        $this->db->select("id");
        $this->db->where("user_id", $users->id);
        $student = $this->db->get("students")->row();

        $this->db->select("*");
        $this->db->where("ward_id", $student->id);
        $parents = $this->db->get("guardians")->result();

        $all_guardian = array();

        if ($parents) {
            foreach ($parents as $value) {
                $this->db->select("*");
                $this->db->where("id", $value->user_id);
                $users_g = $this->db->get("users")->row();
                $all_guardian[] = $users_g;
            }
        }

        $this->db->dbprefix = 'tds_';

        return $all_guardian;
    }

}
