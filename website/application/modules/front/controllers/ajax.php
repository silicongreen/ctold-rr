<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ajax extends MX_Controller {

    public $obj_post;

    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('datamapper');
        $this->load->helper('form');

        $this->layout_front = false;
        $this->obj_post = new Post_model();
    }

    public function send_email_to_user_api() {
        $data = $this->input->post("data");
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'host.champs21.com';   //examples: ssl://smtp.googlemail.com, myhost.com
        $config['smtp_user'] = 'info@champs21.com';
        $config['smtp_pass'] = '174097@hM&^256';
        $config['smtp_port'] = '465';
        $config['charset'] = 'utf-8';  // Default should be utf-8 (this should be a text field)
        $config['newline'] = "\r\n"; //"\r\n" or "\n" or "\r". DEFAULT should be "\r\n"
        $config['crlf'] = "\r\n"; //"\r\n" or "\n" or "\r" DEFAULT should be "\r\n"

        $this->load->library('email');

        $this->email->set_mailtype("html");
        $this->email->from("info@classtune.com", "Classtune");
        $this->email->subject('Classtune Signup Success');
        $this->email->to($data['email'], $data['email_name']);

        $mail_html = $this->load->view('email_template/singup', $data, true);

        $this->email->message($mail_html);

        $this->email->send();
    }

    public function gotoplaystore() {
        $this->load->config('huffas');
        $app_url = $this->config->config['android_app_dl_url'];
        redirect($app_url);
    }

    public function getExclusiveNews() {
        echo "getExclusiveNews";
    }
    public function send_paid_notification_eddozz() {
        $user_id = $this->input->post("user_id");

        $user_ids = explode(",", $user_id);

        $notification_id = $this->input->post("notification_id");

        $notification_ids = explode(",", $notification_id);
        if (count($user_ids) > 0 && count($user_ids) == count($notification_ids)) {
            foreach ($user_ids as $key => $value) {
                $notification_status[] = send_notification_paid_eddozz($notification_ids[$key], $value);
            }
            $response['status']['noti_status'] = $notification_status;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
            echo json_encode($response);
        }
    }

    public function send_paid_notification() {
        $user_id = $this->input->post("user_id");

        $user_ids = explode(",", $user_id);

        $notification_id = $this->input->post("notification_id");

        $notification_ids = explode(",", $notification_id);
        if (count($user_ids) > 0 && count($user_ids) == count($notification_ids)) {
            foreach ($user_ids as $key => $value) {
                $notification_status[] = send_notification_paid($notification_ids[$key], $value);
            }
            $response['status']['noti_status'] = $notification_status;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
            echo json_encode($response);
        }
    }

    public function set_type_cookie($user_type) {
        set_type_cookie($user_type);
        redirect(base_url());
    }

    public function unset_type_cookie() {
        set_type_cookie(1);
        redirect(base_url());
    }

    public function sharepop($id) {
        $data['post_id'] = $id;
        $data['post'] = new Posts($id);
        $this->load->view("sharepop", $data);
    }

    public function showsharepost($id) {
        $data['post_id'] = $id;
        $data['post'] = new Posts($id);
        $this->load->view("schoolsharepop", $data);
    }

    public function sharepost($post_id) {
        if (free_user_logged_in() || wow_login() == false) {

            $user_id = get_free_user_session("id");

            if ($post_id && $user_id) {
                $url = get_curl_url("shareschoolfeed");
                $fields = array(
                    'user_id' => $user_id,
                    'id' => $post_id
                );

                $fields_string = "";

                foreach ($fields as $key => $value) {
                    $fields_string .= $key . '=' . $value . '&';
                }

                rtrim($fields_string, '&');
                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POST, count($fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Content-Length: ' . strlen($fields_string)
                        )
                );

                $result = curl_exec($ch);

                curl_close($ch);

                $a_data = json_decode($result);
            }
        }
        echo "<script>alert('Post shared to your school');var element = parent.document.getElementById('school_share_" . $post_id . "');element.parentNode.removeChild(element);parent.document.getElementById('addthisbutton_" . $post_id . "').style.display = 'block'; parent.$.fancybox.close();</script>";
    }

    function minify_css() {
        $s_css_key = $this->uri->segment(2);

        $cache_name = "css/" . $s_css_key;

        if ($s_content = $this->cache->file->get($cache_name)) {
            ob_start("ob_gzhandler");

            $headers['Content-Length'] = strlen($s_content);
            $headers['Expires'] = gmdate("D, d M Y H:i:s", time() + 86400 * 30) . " GMT";
            $headers['Cache-control'] = "max-age=2592000";
            $headers['Content-Type'] = 'text/css; charset=utf-8';
            //$headers['Content-Encoding'] = "gzip";
            $headers['Vary'] = 'Accept-Encoding';
            //$headers['ETag'] = time() + 86400 * 30;

            foreach ($headers as $name => $val) {
                header($name . ': ' . $val);
            }

            echo $s_content;

            ob_flush();


            exit;
        }
        $this->load->config("huffas");

        $this->layout_front = false;

        ob_start("ob_gzhandler");

        $s_css_key = $this->uri->segment(2);

        $ar_css = $this->config->config['css_champs21'][$s_css_key];

        foreach ($ar_css as $css) {
            $s_css .= $this->compress(file_get_contents(base_url($css)));
        }

        $headers['Content-Length'] = strlen($s_css);
        $headers['Expires'] = gmdate("D, d M Y H:i:s", time() + 86400 * 30) . " GMT";
        $headers['Cache-control'] = "max-age=2592000";
        $headers['Content-Type'] = 'text/css; charset=utf-8';
        //$headers['Content-Encoding'] = "gzip";
        $headers['Vary'] = 'Accept-Encoding';
        $headers['ETag'] = time() + 86400 * 30;

        foreach ($headers as $name => $val) {
            header($name . ': ' . $val);
        }

        $this->cache->file->save($cache_name, $s_css, 86400 * 30 * 12);
        echo $s_css;
    }

    function compress($minify) {
        $minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify);

        /* remove tabs, spaces, newlines, etc. */
        $minify = str_replace(array("\r\n", "\r", "\n", "\t"), '', $minify);

        return $minify;
    }

    public function delete_user_folder() {
        $return = 0;
        if (free_user_logged_in()) {
            $folder_name = $this->input->get('name');
            $folder = explode("_", $folder_name);


            $i_user_id = get_free_user_session("id");
            $folder_id = $folder[count($folder) - 1];

            $this->load->config("user_register");
            $folders = $this->config->config['free_user_folders'];

            $this->db->where("id", $folder_id);
            $this->db->where("user_id", $i_user_id);
            foreach ($folders as $value) {
                $this->db->where("title !=", rtrim($value));
            }
            $this->db->delete("user_folder");

            $return = "folderli_" . $folder_id;
        }
        return $return;
    }

    public function setGKAnswer() {
        $CI = & get_instance();
        $b_free_user_login = free_user_logged_in();

        //$b_free_user_login = TRUE;
        if ($b_free_user_login) {
            $this->load->model('post');

            $data['user_id'] = get_free_user_session("id");

            $data['post_id'] = $this->input->post("post_id");
            $data['user_answer'] = $this->input->post("answer");

            $a_post_params = array(
                "tds_post.id" => $data['post_id']
            );

            $a_post = $CI->post->gePostNews($a_post_params);
            $obj_post_data = $a_post['data'][0];

            $data['question'] = $obj_post_data->headline;

            $content = $obj_post_data->content;

            $s_gk_answers = $content;
            $a_gk_answers = explode(",", $s_gk_answers);
            $correct_answer = "";
            foreach ($a_gk_answers as $answer) {
                $a_answer = explode("|", $answer);
                if ($a_answer[1] == 1) {
                    $correct_answer = $a_answer[0];
                }
            }
            $data['is_correct'] = 0;
            if ($correct_answer == $data['user_answer']) {
                $data['is_correct'] = 1;
            }
            $data['correct_answer'] = $correct_answer;
            $data['date'] = date("Y-m-d", strtotime($obj_post_data->published_date));

            $CI->db->insert("user_gk_answers", $data);

            echo '1' . '+' . $data['is_correct'];
        } else {
            echo '0' . '+' . $data['is_correct'];
        }
    }

    public function getGKAnswer() {
        $CI = & get_instance();
        $b_free_user_login = free_user_logged_in();

        //$b_free_user_login = TRUE;
        if ($b_free_user_login) {
            $this->load->model('user_gk_answers');

            $user_id = get_free_user_session("id");

            $current_page = $this->input->post("current_page");
            $_page = $current_page;

            $current_page--;
            $a_data = $CI->user_gk_answers->get_user_gk_answers($user_id, $current_page);

            $data['total'] = $a_data['total'];
            $data['answers'] = $a_data['data'];

            $default_limit = 1;
            if ($current_page == 0) {
                $data['has_previous'] = FALSE;
            } else {
                $data['has_previous'] = TRUE;
            }

            if ($data['total'] > $_page) {
                $data['has_next'] = TRUE;
            } else {
                $data['has_next'] = FALSE;
            }

            $data['current_page'] = $current_page;

            $this->load->view("gk_answers", $data);
        } else {
            echo "0";
        }
    }

    public function get_category_main() {
        if (free_user_logged_in()) {
            $candle_candle_category_id = $this->input->get('candle_category_id');
            $parent_category_id = NULL;

            $this->db->select('categories.*')
                    ->from('categories')
                    ->where("tds_categories.status", 1)
                    ->where("tds_categories.show", 1)
                    ->order_by("id", "asc");

            if (!empty($candle_candle_category_id)) {
                $this->db->where("tds_categories.id", $candle_candle_category_id);
            } else {
                $this->db->where("tds_categories.parent_id", $parent_category_id);
            }

            $news_query = $this->db->get();
            $data = array();
            foreach ($news_query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            $class = 'class="f5"';
            echo '<div class="select-style">
                    ' . form_dropdown('category', $data, '', $class) . '
                </div>';
        } else {
            echo 0;
        }
        //echo form_dropdown('category', $data);
    }

    private function generate_byline_id($byline_string) {
        $this->db->select("id");
        $this->db->where("title", trim($byline_string));
        $bylines_row = $this->db->get("bylines")->row();
        if (count($bylines_row) > 0) {
            $byline_id = $bylines_row->id;
        } else {
            $a_byline_insert['title'] = trim($byline_string);
            $this->db->insert('bylines', $a_byline_insert);
            $byline_id = $this->db->insert_id();
        }
        return $byline_id;
    }

    public function add_school() {
        $user_id = 0;
        if (free_user_logged_in()) {
            $school_obj = new userschools();
            $school_obj->school_name = $this->input->post("school_name");
            $school_obj->freeuser_id = get_free_user_session("id");
            $school_obj->contact = $this->input->post("contact");
            $school_obj->address = $this->input->post("address");
            $school_obj->zip_code = $this->input->post("zip_code");
            $school_obj->about = $this->input->post("about");

            $this->load->library('upload');
            if (!empty($_FILES['picture']['name'])) {
                $config_cover['upload_path'] = 'upload/user_submitted_image/';
                $config_cover['allowed_types'] = 'jpg|png|gif|jpeg';
                $config_cover['max_size'] = '1024';
                $config_cover['is_image'] = false;
                $config_cover['file_name'] = "image_" . time() . "_" . $_FILES['picture']['name'];
                $config_cover['overwrite'] = TRUE;

                $this->upload->initialize($config_cover);

//                if ($this->upload->do_upload('picture')) {
//                    $school_obj->picture = $config_cover['upload_path'] . $this->upload->file_name;
//                }
            }
            if (!empty($_FILES['logo']['name'])) {
                $config_image['upload_path'] = 'upload/user_submitted_image/';
                $config_image['allowed_types'] = 'jpg|png|gif|jpeg';
                $config_image['max_size'] = '1024';
                $config_image['max_width'] = '2000';
                $config_image['max_height'] = '1600';
                $config_image['is_image'] = TRUE;
                $config_image['file_name'] = "image_" . time() . "_" . $_FILES['logo']['name'];
                $config_image['overwrite'] = TRUE;

                $this->upload->initialize($config_image);

//                if ($this->upload->do_upload('logo')) {
//                    $school_obj->logo = $config_image['upload_path'] . $this->upload->file_name;
//                }
            }

            if ($school_obj->save()) {

                $this->load->config("huffas");
                $assessment_config = $this->config->config['assessment'];

                $data = trim($this->input->post('icc_quiz_cookie'), ',');
                $add_to_school = $this->input->post('add_to_school');
                $cur_level = $this->input->post('icc_quiz_level');

                if ($cur_level > 0) {
                    $cur_level = $cur_level;
                } else {
                    $cur_level = 0;
                }

                $ar_data = explode('_', $data);

                $assessment_id = $ar_data[0];

                $user_id = get_free_user_session('id');

                $obj_assessment_mark = new Assesment_mark();
                $obj_assessment_school_mark_temp = new Assessment_school_mark_temp();

                $score_add_to_school = TRUE;

                if ($add_to_school == 'false') {

                    $assessment_school_mark_temp = $obj_assessment_school_mark_temp->find_assessment_school_mark($user_id, $assessment_id, 1, $school_obj->id);

                    if (!$assessment_school_mark_temp) {
                        $score_add_to_school = FALSE;
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

                $assessment_mark = $obj_assessment_mark->find_assessment_mark($user_id, $assessment_id, $cur_level);

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

                    if (($assessment_school_mark_temp != false)) {

                        $this->db->where('user_id', $user_id);
                        $this->db->where('assessment_id', $assessment_id);
                        $this->db->delete('assessment_school_mark_temp');
                    }

                    $assessment_mark = $obj_assessment_mark->find_assessment_mark_all($user_id, $assessment_id);

                    if ($assessment_mark !== FALSE) {

                        $assessment_school_mark_temp = array();
                        foreach ($assessment_mark as $am) {

                            $assessment_school_mark_temp_data['user_id'] = $user_id;
                            $assessment_school_mark_temp_data['assessment_id'] = $assessment_id;
                            $assessment_school_mark_temp_data['mark'] = $am->mark;
                            $assessment_school_mark_temp_data['level'] = $am->level;
                            $assessment_school_mark_temp_data['temp_school_id'] = $school_obj->id;
                            $assessment_school_mark_temp_data['created_date'] = $am->created_date;
                            $assessment_school_mark_temp_data['time_taken'] = $am->time_taken;
                            $assessment_school_mark_temp_data['avg_time_per_ques'] = $am->avg_time_per_ques;
                            $assessment_school_mark_temp_data['no_played'] = $am->no_played;

                            $assessment_school_mark_temp[] = $assessment_school_mark_temp_data;
                        }

                        $this->db->insert_batch('assessment_school_mark_temp', $assessment_school_mark_temp);
                    }
                }
            }

            if ($school_obj->id) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    public function createteacherpage() {
        if (free_user_logged_in()) {

            $school_obj = new createpage();
            $school_obj->user_id = get_free_user_session("id");

            $information['school_name'] = $this->input->post("school_name");
            $information['contact'] = $this->input->post("contact");
            $information['address'] = $this->input->post("address");
            $information['zip_code'] = $this->input->post("zip_code");
            $information['about'] = $this->input->post("about");


            $this->load->library('upload');
            if (!empty($_FILES['national_card']['name'])) {
                $config_cover['upload_path'] = 'upload/user_submitted_image/';
                $config_cover['allowed_types'] = 'jpg|png|gif|jpeg';
                $config_cover['max_size'] = '1024';
                $config_cover['is_image'] = false;
                $config_cover['file_name'] = "image_" . time() . "_" . $_FILES['picture']['name'];
                $config_cover['overwrite'] = TRUE;

                $this->upload->initialize($config_cover);

//                if ($this->upload->do_upload('national_card')) {
//                    $information['national_card'] = $config_cover['upload_path'] . $this->upload->file_name;
//                }
            }
            if (!empty($_FILES['school_card']['name'])) {
                $config_image['upload_path'] = 'upload/user_submitted_image/';
                $config_image['allowed_types'] = 'jpg|png|gif|jpeg';
                $config_image['max_size'] = '1024';
                $config_image['max_width'] = '2000';
                $config_image['max_height'] = '1600';
                $config_image['is_image'] = TRUE;
                $config_image['file_name'] = "image_" . time() . "_" . $_FILES['logo']['name'];
                $config_image['overwrite'] = TRUE;

                $this->upload->initialize($config_image);

//                if ($this->upload->do_upload('school_card')) {
//                    $information['school_card'] = $config_image['upload_path'] . $this->upload->file_name;
//                }
            }
            $jsonData = json_encode($information);

            $school_obj->information = $jsonData;

            //echo "<pre>";
            //print_r($jsonData);
            $school_obj->save();



            if ($school_obj->id) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    public function add_post() {
        if (free_user_logged_in()) {
            $related_attached_file = "";
            $byline_string = get_free_user_session("full_name");

            $user_id = get_free_user_session("id");

            $user_mobile_number = get_free_user_session("mobile_no");

            $post_obj = new posts();
            $post_obj->headline = $this->input->post("headline");
            $post_obj->content = $this->input->post("content");
            $post_obj->mobile_num = $this->input->post("mobile_num");
            $post_obj->published_date = date("Y-m-d H:i:s");
            $post_obj->status = 1;
            $post_obj->type = "Print";
            $post_obj->user_type = 2;
            $post_obj->language = "en";
            $post_obj->byline_id = $this->generate_byline_id($byline_string);

            $i_school_id = $this->input->post("school_id");
            $post_obj->school_id = (!empty($i_school_id)) ? $i_school_id : 0;

            $post_obj->can_comment = $this->input->post("can_comment");
            $post_obj->show_comment_to_all = $this->input->post("show_comment_to_all");
            $post_obj->user_id = get_free_user_session("id");
            $post_obj->candle_type = $this->input->post("candle_type");


            $this->load->library('upload');
            if (!empty($_FILES['attach_file']['name'])) {
                $config_cover['upload_path'] = 'upload/user_submitted_image/';
                $config_cover['allowed_types'] = 'pdf|doc|docx|docs';
                $config_cover['max_size'] = '1024';
                $config_cover['is_image'] = false;
                $config_cover['file_name'] = "file_" . time() . "_" . $_FILES['attach_file']['name'];
                $config_cover['overwrite'] = TRUE;

                $this->upload->initialize($config_cover);

//                if ($this->upload->do_upload('attach_file')) {
//                    $post_obj->attach = $config_cover['upload_path'] . $this->upload->file_name;
//                    $related_attached_file = $config_cover['upload_path'] . $this->upload->file_name;
//                }
            }
            if (!empty($_FILES['leadimage']['name'])) {
                $config_image['upload_path'] = 'upload/user_submitted_image/';
                $config_image['allowed_types'] = 'jpg|png|gif|jpeg';
                $config_image['max_size'] = '1024';
                $config_image['max_width'] = '2000';
                $config_image['max_height'] = '1600';
                $config_image['is_image'] = TRUE;
                $config_image['file_name'] = "image_" . time() . "_" . $_FILES['leadimage']['name'];
                $config_image['overwrite'] = TRUE;

                $this->upload->initialize($config_image);

//                if ($this->upload->do_upload('leadimage')) {
//                    $post_obj->lead_material = $config_image['upload_path'] . $this->upload->file_name;
//                }
            }

            $post_obj->save();



            if ($post_obj->id) {
                $array['post_id'] = $post_obj->id;
                $array['category_id'] = 1;
                $this->db->insert("post_category", $array);

                if (!$user_mobile_number) {

                    $free_user = new Free_users($user_id);
                    $free_user->mobile_no = $this->input->post("mobile_num");
                    $free_user->skip_validation();
                    $free_user->save();
                }

                $data_post_class = array(
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 1
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 2
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 3
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 4
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 5
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 6
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 7
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 8
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 9
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'class_id' => 10
                    )
                );

                $this->db->insert_batch('post_class', $data_post_class);


                $data_post_type = array(
                    array(
                        'post_id' => $post_obj->id,
                        'type_id' => 1
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'type_id' => 2
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'type_id' => 3
                    ),
                    array(
                        'post_id' => $post_obj->id,
                        'type_id' => 4
                    )
                );

                $this->db->insert_batch('post_type', $data_post_type);

                if ($related_attached_file != "") {
                    $data_post_attached = array(
                        array(
                            'post_id' => $post_obj->id,
                            'file_name' => $related_attached_file,
                            'show' => 1,
                            'caption' => "Download Content"
                        )
                    );

                    $this->db->insert_batch('post_attachment', $data_post_attached);
                }


                //sending email
                $category_model = new Category($array['category_id']);

                $from_email = "candleInfo@champs21.com";
                if (trim($category_model->responsible)) {
                    $this->load->library('email');

                    $this->email->from($from_email, 'Champs21 Candle');
                    $this->email->to($category_model->responsible);

                    if (trim($category_model->backup))
                        $this->email->cc($category_model->backup);

                    $this->email->subject('New Candle Added');

                    $mailconfig['mailtype'] = 'html';

                    $this->email->initialize($mailconfig);

                    $candle_admin_link = base_url() . "admin/news/edit/" . $post_obj->id;
                    $messege = "<p>Dear Admin,</p><p>A new candle \"" . $this->input->post("headline") . "\" added by <b>" . $byline_string . "</b> in <b>" . $category_model->name . "<b> category.</p>"
                            . "<p>Please check <a href='" . $candle_admin_link . "'>Admin Candle Link</a> </p>"
                            . "<p>Thanks</p>";
                    $this->email->message($messege);

                    $this->email->send();

                    echo $this->email->print_debugger();
                }

                //end of sending email




                $reletad_type = array();
                $i = 0;
                if ($_POST['type_post']) {
                    foreach ($this->input->post("type_post") as $value) {
                        $reletad_type[$i]['type_id'] = $value;
                        $reletad_type[$i]['post_id'] = $post_obj->id;
                        $i;
                    }
                    $this->db->insert_batch('post_type', $reletad_type);
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    public function getPostsGame($s_category_ids = "", $game_type = 1, $current_page) {

        $CI = & get_instance();

        $CI->load->config("huffas");

        $b_featured = FALSE;
        $i_featured_position = 0;
        $a_post_params = array(
            "tds_post.referance_id" => 0,
            "CUSTOM" => "( tds_post.game_type = " . $game_type . ")"
        );
        $this->load->model('post');

        $ar_post_news = $this->post->gePostNews($a_post_params, "inner", "smaller", "DATE(tds_post.published_date),desc+postCategories.inner_priority,asc", $s_category_ids, 6, $current_page, $b_featured, $i_featured_position);


        $data['target'] = "inner";

        $data['page'] = "index";

        $data['category'] = $s_category_ids;

        $data['obj_post_news'] = $ar_post_news["data"];
        $data['total_data'] = $ar_post_news['total'];
        $data['page_size'] = 6;
        $data['current_page'] = $current_page;
        $data['game_type'] = $game_type;

        $data['featured'] = $b_featured;

        $this->load->view("post_datas_game", $data);
    }

    public function getPosts($s_category_ids = "", $target = "inner", $page = "index", $i_limit = 9, $current_page = 0, $lang = '') {
        $CI = & get_instance();
        $CI->load->config("huffas");

        $b_featured = FALSE;
        $i_featured_position = 0;


        if ($target == "index" && !$b_featured) {
            $CI->db->where('key', 'layout');
            $query = $CI->db->get('settings');
            $layout_settings = $query->row();

            $layout = $layout_settings->value;
            if ($layout == "3-block-default" || $layout == "3-block-with-featured-in-two-block") {
                $b_featured = 2;
                $i_featured_position = "1,2,3,4";
            } else if ($layout == "3-block-with-featured-with-two-block") {
                $b_featured = 3;
                $i_featured_position = "2,3,4";
            }
        }

        $this->load->model('post');
        $content_showed = $_GET['content_showed'];

        $a_post_params = array();
        if ($target == "index") {

            if ($content_showed) {
                $content_array = explode("|", $content_showed);
                unset($content_array[count($content_array) - 1]);
                if (isset($content_array[0]) && strlen(trim($content_array[0])) > 0) {
                    $a_post_params = array(
                        "NOT_IN" => array("tds_post.id", $content_array)
                    );
                }
            }
            if (!empty($lang)) {
                $a_post_params['lang'] = $lang;
            }
//            print_r($a_post_params);
//            exit;
            $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", "MAX(t.priority),DESC", $s_category_ids, $i_limit, $current_page, $b_featured, $i_featured_position);
        } else if ($target == "inner") {

            $a_post_params = array(
                "tds_post.referance_id" => 0
            );
            if (!empty($lang)) {
                $a_post_params['lang'] = $lang;
            }

            $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", "DATE(tds_post.published_date),desc+postCategories.inner_priority,asc", $s_category_ids, $i_limit, $current_page, $b_featured, $i_featured_position);
        } else if ($target == "search") {
            $q = '';
            if (isset($_GET['s']) && !empty($_GET['s'])) {
                $q = $this->input->get('s');
            }

            $a_post_params['q'] = $q;
            if (!empty($lang)) {
                $a_post_params['lang'] = $lang;
            }
            $s_priority = "DATE(tds_post.published_date), DESC";
            $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", $s_priority, $s_category_ids, $i_limit, $current_page, $b_featured, $i_featured_position);
        } else {
            $s_priority = "tds_post.user_view_count,desc";
            $a_post_params = array(
                "tds_post.referance_id" => 0
            );
            if (!empty($lang)) {
                $a_post_params['lang'] = $lang;
            }
            $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", $s_priority, $s_category_ids, $i_limit, $current_page, $b_featured, $i_featured_position);
        }


        $data['target'] = $target;
        if ($target == "inner") {
            $data['page'] = $page;
        }
        $data['category'] = $s_category_ids;

        $data['obj_post_news'] = $ar_post_news["data"];
        $data['total_data'] = $ar_post_news['total'];
        $data['page_size'] = $i_limit;
        $data['current_page'] = $current_page;

        $data['ecl'] = FALSE;
        if (isset($CI->config->config['education-changes-life']['ecl_ids'])) {
            $data['ecl'] = in_array($data['category'], $CI->config->config['education-changes-life']['ecl_ids']) ? TRUE : FALSE;
        }

        if (isset($CI->config->config['nation-builder']['ecl_ids'])) {
            $data['ecl'] = in_array($data['category'], $CI->config->config['nation-builder']['ecl_ids']) ? TRUE : FALSE;
        }

        $data['opinion'] = FALSE;
        if (isset($CI->config->config['opinion']['op_ids'])) {
            $data['opinion'] = in_array($data['category'], $CI->config->config['opinion']['op_ids']) ? TRUE : FALSE;
        }

        $data['featured'] = $b_featured;

        $this->load->view("post_datas", $data);
    }

    public function addWow() {
        if (free_user_logged_in() || wow_login() == false) {

            $user_id = get_free_user_session("id");
            $post_id = $this->input->post("post_id");
            $single = $this->input->post("single");

            if ($post_id && ($user_id || wow_login() == false)) {
                $url = get_curl_url("addwow");
                $fields = array(
                    'user_id' => $user_id,
                    'post_id' => $post_id
                );

                $fields_string = "";

                foreach ($fields as $key => $value) {
                    $fields_string .= $key . '=' . $value . '&';
                }

                rtrim($fields_string, '&');
                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POST, count($fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Content-Length: ' . strlen($fields_string)
                        )
                );

                $result = curl_exec($ch);

                curl_close($ch);

                $a_data = json_decode($result);

                if (isset($a_data->data->wow_count)) {
                    $count = $a_data->data->wow_count;
                    if ($count < 1000) {
                        $count_string = $count;
                    } else if ($count >= 1000 && $count < 1000000) {
                        $new_count = round($count / 1000);
                        $count_string = $new_count . "k";
                    } else if ($count >= 100000) {
                        $new_count = round($count / 100000);
                        $count_string = $new_count . "M";
                    }
                    if ($single) {
                        echo $a_data->data->wow_count;
                    } else {
                        echo "WoW " . $count_string . "";
                    }
                } else {
                    echo "0";
                }
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
    }

    public function addPostToGoodRead() {
        if (free_user_logged_in()) {
            $this->load->model("user_folder");
            $ar_user_good_read['user_id'] = get_free_user_session("id");

            $ar_user_good_read['folder_id'] = $this->input->post("folder_id");
            if ($ar_user_good_read['folder_id'] == 0) {
                $folder_name = "Unread";
                $folder_data = $this->user_folder->get_folder_id($ar_user_good_read['user_id'], $folder_name);
                $ar_user_good_read['folder_id'] = $folder_data->id;
            }
            $ar_user_good_read['post_id'] = $this->input->post("post_id");
            $ar_user_good_read['is_read'] = $this->input->post("is_read");

            $s_message = $this->user_folder->save_post_to_user_good_read_folder($ar_user_good_read);
            echo $s_message;
        } else {
            echo "-1";
        }
    }

    public function deletePostFromGoodRead() {
        if (free_user_logged_in()) {
            $this->load->model("user_folder");
            $ar_user_good_read['user_id'] = get_free_user_session("id");

            $ar_user_good_read['folder_id'] = $this->input->post("folder_id");
            if ($ar_user_good_read['folder_id'] == 0) {
                $folder_name = "Unread";
                $folder_data = $this->user_folder->get_folder_id($ar_user_good_read['user_id'], $folder_name);
                $ar_user_good_read['folder_id'] = $folder_data->id;
            }
            $ar_user_good_read['post_id'] = $this->input->post("post_id");

            $s_message = $this->user_folder->delete_post_from_user_good_read_folder($ar_user_good_read);
            echo $s_message;
        } else {
            echo "-1";
        }
    }

    public function addUserGoodReadFolder() {
        if (free_user_logged_in()) {
            $ar_user_good_read['title'] = $this->input->post("title");
            $ar_user_good_read['status'] = 1;
            $ar_user_good_read['visible'] = 1;
            $this->load->model("user_folder");
            $ar_user_good_read['user_id'] = get_free_user_session("id");
            $s_message = $this->user_folder->save_user_good_read_folder($ar_user_good_read);
            echo $s_message;
        }
    }

    public function removeFolder() {
        if (free_user_logged_in()) {
            $i_folder_id = $this->input->post("folder_id");
            $this->load->model("user_folder");
            $i_user_id = get_free_user_session("id");
            $ar_user_folder = $this->user_folder->remove_user_good_read_folder($i_user_id, $i_folder_id);
        }
    }

    private function get_category_id_by_ci_key($ci_key = 'index') {
        $ci_key = ( strlen($ci_key) > 0 && $ci_key != 'index') ? $ci_key : NULL;

        $this->load->model('menus');
        $obj_menu = $this->menus->get_menu_by_ci_key($ci_key);
        $obj_category = (($obj_menu) && !empty($obj_menu->category_id) && ($obj_menu->category_id > 0)) ? $obj_menu : FALSE;

        if (!($obj_category) || ((int) $obj_category->category_id <= 0)) {
            $category_name = ucwords(unsanitize($ci_key));
            $obj_category = new Category_model();
            $obj_category = $obj_category->getCategoryInfoByName($category_name);
        }
        return $obj_category;
        exit;
    }

    private function change_image_url($image_url) {
        return str_replace("bd.", "www.", $image_url);
    }

    public function getmedia() {
        $data['ci_key'] = $_GET["ci_key"];
        $data['gallery_name'] = $_GET["gallery_name"];
        $data['ad_plan_id'] = $_GET["ad_plan_id"];
        $data['s_date'] = $_GET["s_date"];
        $data['show_ad'] = $_GET["show_ad"];

        $this->load->view("media", $data);
    }

    function clear_cache() {
        if (isset($_GET['cache'])) {
            $this->cache->delete($_GET['cache']);
        }
    }

    public function show_image_news() {
        $image_name = $this->uri->segment(5);
        if (strlen($image_name) == 0) {
            $image_name = "news_image";
        } else {
            $i_pos = strrpos($image_name, "-");
            $image_name[$i_pos] = ".";
        }
        $news_image = $this->uri->segment(4);
        $i_pos = strrpos($news_image, "-");
        $news_image[$i_pos] = ".";
        $news_image = str_ireplace("-", "/", $news_image);
        $image_path = FCPATH . $news_image;
        $ar_paths = pathinfo($image_path);
        $this->load->helper('file');
        $data = read_file($image_path);
        header("Content-Disposition: filename=" . $image_name . ";");
        $stuff = get_mime_by_extension($image_path);
        header("Content-Type: {$stuff}");
        header("Content-Length:" . filesize($image_path));
        header("Expires:" . gmdate("D, d M Y H:i:s", time() * 86400 * 30) . " GMT");
        header("Cache-control: max-age=2592000");
        header("Vary: Accept-Encoding");
        header("ETag:" . time() * 86400 * 30);
        header('Content-Transfer-Encoding: binary');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        echo $data;
    }

    public function cache_info() {
        print '<pre>';
        print_r($this->cache->file->cache_info());
    }

    public function clear_cache_all() {
        $this->cache->clean();
    }

    public function getpositiondropudown() {
        $category_id = $this->input->post("category_id");
        $school_id = $this->input->post("school_id");
        $dropdown = get_paid_employee_position_droupdown($school_id, $category_id);
        echo $dropdown;
    }

    public function cat_home() {


        $cache_name = 'HOME_CATEGORY';

        if (!$obj_cat = $this->cache->get($cache_name)) {
            print "Sfsdf";
            $this->db->where('category_type_id', 1);
            $this->db->where('status', 1);
            $this->db->where('parent_id IS NULL');
            $this->db->where('priority IS NOT NULL');
            $this->db->order_by("priority", "asc");
            $this->db->limit(11);
            $query = $this->db->get('categories');

            $obj_cat = $query->result();

            $this->cache->save($cache_name, $obj_cat, 86400 * 30 * 12);
        }
        print '<pre>';
        print_r($obj_cat);
    }

    public function getSupplimentLink() {
        $this->db->where('status', 1);
        $this->db->where('parent_id', 148);
        $this->db->order_by("id", "DESC");
        $query = $this->db->get('categories');
        $obj_cat = $query->result();
        echo "<ul>";
        foreach ($obj_cat as $value) {
            echo "<li><a href='" . base_url(sanitize($value->name)) . "'  >" . $value->name . "</a></li>";
        }
        echo "</ul>";
    }

    function getschoolname() {
        if (!empty($_POST["keyword"])) {
            $this->db->like('school_name', $_POST["keyword"], 'after');
            $this->db->order_by("school_name", "ASC");
            $this->db->limit(6, 0);
            $query = $this->db->get('school_list_spellingbee');
            $number = $query->num_rows();
            $obj_cat = $query->result();
            if ($number > 0) {
                echo "<ul id='country-list'>";
                foreach ($obj_cat as $value) {
                    echo "<li onClick='selectCountry(&#39" . $value->school_name . "&#39);'>" . $value->school_name . "</li>";
                }
                echo "</ul>";
            }
        }
    }

    function getleaderboarddata() {
        if (!empty($_POST["stdivision"])) {
            $division = $_POST["stdivision"];

            /* old leaderboard */
//            $obj_post = new Post_model();
//            $obj_post_data = $obj_post->get_leader_board($division);

            /* new leaderboard from excel */

            //load our new PHPExcel library
            $file = 'score_board_16_7.xlsx';

            $this->load->library('EXcel');

            $inputFileType = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);

            $objExcel = $objReader->load($file);

            $division = strtolower($division);
            $division = str_replace(' ', '', $division);

//            var_dump($division);exit;
//            $division_name_a = '';
//            $division_name_b = '';
//            if($division == 'dhaka') {
//                $division_name_a = $division.'a';
//                $division_name_b = $division.'b';
//            }
//            if(!empty($division_name_a) && !empty($division_name_b)) {
//                $obj_post_data_a = $objSheet = $objExcel->getSheetByName($division_name_a);
//                $obj_post_data_b = $objSheet = $objExcel->getSheetByName($division_name_b);
//            } else {
            $obj_post_data_a = $objSheet = $objExcel->getSheetByName($division);
//            }

            if (!empty($obj_post_data_a) || !empty($obj_post_data_b)) {
                $highestRow_a = $obj_post_data_a->getHighestRow();

                if (!empty($obj_post_data_b)) {
                    $highestRow_b = $obj_post_data_b->getHighestRow();
                }

                $rank = 1;
                echo "<table cellspacing='0'>                       
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name & School</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>";

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
                    echo "<tr>";
                    echo "<td colspan=\"3\" style=\"text-align: center; background-color: #aaaaaa; color: #ffffff; font-size: 18px;\">Dhaka A</td>";
                    echo "</tr>";
                }

                for ($i = 2; $i <= $highestRow_a; $i++) {
                    echo "<tr>";
                    echo "<td>" . $obj_post_data_a->getCell('A' . $i)->getValue() . "</td>";
                    echo "<td>" . $obj_post_data_a->getCell('B' . $i)->getValue() . " " . "(" . $obj_post_data_a->getCell('C' . $i)->getValue() . ")" . "</td>";
                    echo "<td>" . $obj_post_data_a->getCell('D' . $i)->getValue() . "</td>";
                    echo "</tr>";
                }

                /* new leaderboard from excel */
                if (!empty($obj_post_data_b)) {
                    echo "<tr>";
                    echo "<td colspan=\"3\" style=\"text-align: center; background-color: #aaaaaa; color: #ffffff; font-size: 18px;\">Dhaka B</td>";
                    echo "</tr>";

                    for ($i = 2; $i <= $highestRow_b; $i++) {
                        echo "<tr>";
                        echo "<td>" . $obj_post_data_b->getCell('A' . $i)->getValue() . "</td>";
                        echo "<td>" . $obj_post_data_b->getCell('B' . $i)->getValue() . " " . "(" . $obj_post_data_b->getCell('C' . $i)->getValue() . ")" . "</td>";
                        echo "<td>" . $obj_post_data_b->getCell('D' . $i)->getValue() . "</td>";
                        echo "</tr>";
                    }
                }


                echo "</tbody>
                    </table>";
            } else {
                echo "<p>No Data Found.</p>";
            }
        }
    }

    public function createArchiveCache() {

        $dt = "-1 day";
        $date_to_cahce = date("Y-m-d", strtotime($dt));

        $content = file_get_contents(base_url() . "newspaper?date=" . $date_to_cahce);
    }

    function logout_user() {
        $array_items = array('free_user' => array());
        $this->session->unset_userdata($array_items);
        $this->session->sess_destroy();

        $arr['name'] = $data;
        echo $_GET['callback'] . "(" . json_encode($arr) . ");";
    }
    
    function get_paid_school_domain() {
        $school_dn = get_paid_domain();
        echo $school_dn;
        exit;
    }

}

?>
