<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Createschool extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('tmp');
        $this->load->model('error_logs');
    }

    public function initial_setup($school_id = 0) {
        $this->load->config('create_school');
        $data['setup_forms'] = $this->config->config['setup_forms'];

        $data['school_id'] = $school_id;
        $this->load_view('school_setup/wrapper', $data);
    }

    public function load_setup_form() {

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $data = array();
        $page_index = $notify = $this->input->post('form_id');
        $this->load->config('create_school');
        $config = $this->config->config['setup_forms'];
        $form_name = $config[$page_index];

        $school_id = $this->input->post('school_id');

        $this->load->model($form_name, 'model');

        if (method_exists($this->model, 'getCategories')) {
            $this->model->init($school_id);

            $categories_data = $this->model->getCategories();
            if ($categories_data !== FALSE) {
                $data['categories'] = $this->model->formatForDropdown($categories_data);
            }
        }

        if (method_exists($this->model, 'getCourses')) {
            $this->model->init($school_id);

            $course_data = $this->model->getCourses();
            if ($course_data !== FALSE) {
                $data['courses'] = $this->model->formatForDropdown($course_data);
            }
        }

        $this->load->model('defaults');
        $data['data'] = $this->defaults->getData(array(
            'key' => $form_name,
            'status' => 1
                ), TRUE);

        $data['form_name'] = $form_name;

        echo $this->load->view('school_setup/_' . $form_name, $data, true);
        exit;
    }

    public function save_initial_setup() {

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $school_id = $this->input->post('school_id');
        unset($_POST['school_id']);
        unset($_POST['checkbox']);

        if (!empty($_POST)) {

            $response = array();
            $model_name = array_keys($_POST);
            
            if ($model_name[0] == 'course') {
                
                $model_name[1] = 'course';
                $_POST[$model_name[1]] = $_POST[$model_name[0]];
                
                $model_name[0] = 'shift';
                $_POST[$model_name[0]][] = 'General';
            }
            
            if (count($model_name) > 1) {
               
                $this->load->model($model_name[1], 'model');
                $this->model->init($school_id);

                $data = $this->model->create($_POST[$model_name[1]]);

                if (!isset($data['error']) && ($data != FALSE)) {

                    $this->load->model($model_name[0], 'model_1');
                    $this->model_1->init($school_id);

                    $data = $this->model_1->create($_POST[$model_name[0]]);
                }
            } else {
                $this->load->model($model_name[0], 'model');
                $this->model->init($school_id);

                $data = $this->model->create($_POST[$model_name[0]]);

            }

            if (!isset($data['error']) && ($data != FALSE)) {
                $response['success'] = TRUE;
            } else {
                $response['success'] = FALSE;
                $response['message'] = $data['error'];
            }

            echo json_encode($response);
            exit;
        }
    }

    public function userregister($school_type) {
        
        if ($school_type != "paid" && $school_type != "free") {
            redirect("createschool/type");
        }
        
        if (!empty($_POST)) {

            if (isset($_POST['school_type']) && !empty($_POST['school_type'])) {
                $school_type = $this->input->post('school_type');
            }

            $this->form_validation->set_rules('first_name', 'First Name', 'required|min_length[3]');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
            $this->form_validation->set_rules('confirm_email', 'Confirm Email', 'required|valid_email|matches[email]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|min_length[6]|matches[password]');

            if ($this->form_validation->run() !== FALSE) {

                $rp = $this->input->post('password');
                $p = $this->generate_passowrd_and_salt($rp);

                $user_data['nick_name'] = 1;
                $user_data['password'] = $p['password'];
                $user_data['salt'] = $p['salt'];

                $user_data['email'] = $this->input->post('email');
                $user_data['first_name'] = $this->input->post('first_name');
                $user_data['last_name'] = $this->input->post('last_name');
                $user_data['user_type'] = 3;

                $this->db->insert("free_users", $user_data);

                $user_data['user_id'] = $user_id = $this->db->insert_id();
                $user_data['rp'] = $rp;

                $i_tmp_free_user_data_id = $this->tmp->create(array(
                    'key' => 'free_user_data',
                    'value' => json_encode(array('free_user_id' => $user_id, 'rp' => $rp))
                ));

                if ($user_id) {
                    redirect("createschool/newschool/" . $school_type . '/' . $i_tmp_free_user_data_id);
                } else {
                    $data['error'] = "Something went wrong please try again later or contact with classtune";
                }
            }
        }

        $data['school_type'] = $school_type;
        $this->load_view('user_register', $data);
    }

    public function email_check($str) {
        $this->db->where("email", $str);
        $user_data = $this->db->get("free_users")->row();
        if ($user_data) {
            $this->form_validation->set_message('email_check', '{field} Address is already taken');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function success($school_type = 'free', $i_tmp_school_created_data_id = 0, $i_free_user_id = 0) 
    {
        $data = $this->tmp->getData($i_tmp_school_created_data_id);
        if ($data !== FALSE) {

            $this->load->library('school');

            $data['i_tmp_school_created_data_id'] = $i_tmp_school_created_data_id;
            $data['i_free_user_id'] = $i_free_user_id;
            $data['ar_free_user_data'] = $this->school->getFreeUserDataById($i_free_user_id);
            
        } else {
            
            $_ar_errors['message'] = 'Temp school created data not found';
            $_ar_errors['code'] = 404;
            $_ar_errors['type'] = 'TempSchoolCreatedData';
            
            $this->error_logs->record($_ar_errors);
            $data['error'] = 'School not created';
        }
        
        $this->load_view('success', $data);
    }

    public function notify_user() {

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $this->load->config('create_school');
        $config = $this->config->config['create_school'];

        $notify = $this->input->post('notify');
        $i_free_user_id = $this->input->post('i_free_user_id');
        $i_tmp_school_created_data_id = $this->input->post('i_tmp_school_created_data_id');
        
        if($config['mode'] == 'live') {
            $this->sendMail($i_tmp_school_created_data_id, $i_free_user_id);
        }
        
        $response['success'] = 'done';

        echo json_encode($response);
        exit;
    }

    public function newschool($school_type = 'free', $i_tmp_free_user_data_id = 0) {
        
        if ($i_tmp_free_user_data_id <= 0 && empty($_POST)) {
            redirect("/createschool/userregister/" . $school_type);
        }

        $this->load->config('create_school');
        $config = $this->config->config['create_school'];
        $this->load->model('country');
        $country_call_code = 880;

        if (!empty($_POST)) {

            if (isset($_POST['school_type']) && !empty($_POST['school_type'])) {
                $school_type = $this->input->post('school_type');
            }

            if (isset($_POST['i_tmp_free_user_data_id']) && !empty($_POST['i_tmp_free_user_data_id'])) {
                $i_tmp_free_user_data_id = $this->input->post('i_tmp_free_user_data_id');
            }
            $country_call_code = $this->input->post('country_call_code');
            
            $this->form_validation->set_rules('name', 'School Name', 'required|min_length[5]');
            if ($school_type == "paid") {
                $this->form_validation->set_rules('number_of_student', 'Number Of student', 'required|is_natural_no_zero');
            }

            $this->form_validation->set_rules('institution_address', 'Institution Address', 'required|min_length[8]');
            $this->form_validation->set_rules('institution_phone_no', 'Institution Phone Number', 'required|min_length[5]');
            $this->form_validation->set_rules('code', 'Sub domain', 'required|alpha_numeric|min_length[3]|max_length[8]');
            $this->form_validation->set_rules('country_call_code', 'Country Code', 'required');
            
            if ($this->form_validation->run() !== FALSE) {

                if (isset($_POST['i_tmp_free_user_data_id']) && !empty($_POST['i_tmp_free_user_data_id'])) {
                    $i_tmp_free_user_data_id = $this->input->post('i_tmp_free_user_data_id');
                }

                if (isset($_POST['school_type']) && !empty($_POST['school_type'])) {
                    $school_type = $this->input->post('school_type');
                }

                $i_num_student = $this->input->post('number_of_student');
                $school_code = $this->input->post('code');
                $school_domain = $school_code . '.free.' . $config['main_domain'];
                $country_call_code = $this->input->post('country_call_code');
                $phone_number = $country_call_code . '-' . $this->input->post('institution_phone_no');

                $ar_data['institution']['institution_address'] = $this->input->post('institution_address');
                $ar_data['institution']['institution_phone_no'] = $phone_number;
                $ar_data['school']['code'] = $school_code;
                $ar_data['school']['inherit_smtp_settings'] = 0;
                $ar_data['school']['school_domains_attributes'][0]['domain'] = $school_domain;
                $ar_data['school']['name'] = $this->input->post('name');
                $ar_data['school']['inherit_sms_settings'] = 0;
                $ar_data['school']['number_of_student'] = $i_num_student;
                $ar_data['school']['import'] = $config['import'];
                $ar_data['assign_free_school']['create_new_n_assign'] = 0;
                $ar_data['free_feed']['free_feed_for_student'] = 1;
                $ar_data['palette_setting'] = 1;
                $ar_data['package'] = [2];
                
                $i_tmp_school_creation_data_id = $this->tmp->create(array(
                    'key' => 'school_creation_data',
                    'value' => json_encode($ar_data)
                ));

                $ar_tmp_free_user_data = $this->tmp->getData($i_tmp_free_user_data_id);
                $i_free_user_id = $ar_tmp_free_user_data['free_user_id'];

                if ($school_type == 'paid') {
                    redirect("checkout/payment/" . $i_tmp_school_creation_data_id . '/' . $i_tmp_free_user_data_id);
                } else {
                    $this->load->library('school');
                    $this->school->init($i_tmp_school_creation_data_id, $ar_tmp_free_user_data);
                    $data = $this->school->create();
                    
                    echo '<pre>';
                    var_dump($data);
                    exit;
                    
                    if (isset($data['success']) && $data['success'] === TRUE) {
                        $i_tmp_school_created_data_id = $this->tmp->create(array(
                            'key' => 'school_created_data',
                            'value' => json_encode($data)
                        ));

//                        $this->tmp->delete($i_tmp_free_user_data_id);

                        redirect('/createschool/success/' . $school_type . '/' . $i_tmp_school_created_data_id . '/' . $i_free_user_id);
                    }
                }
            }
        }

        $data['country_call_code'] = $country_call_code;
        $data['countries'] = $this->country->getAll();
        $data['school_type'] = $school_type;
        $data['i_tmp_free_user_data_id'] = $i_tmp_free_user_data_id;

        $this->load_view('createschool', $data);
    }

    public function finalize() {

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $this->load->config('create_school');
        $config = $this->config->config['create_school'];

        $code = $this->input->post('code');
        $type = $this->input->post('type');

        $this->load->library('school');
        $this->school->setCode($code);
        
        $b_subdomain_created = ($config['mode'] == 'live') ? $this->school->createSubdomains($type) : TRUE;
        
        if ($b_subdomain_created) {
            $response['success'] = 'done';
        } else {
            $response['error'] = 'error';
        }

        echo json_encode($response);
        exit;
    }

    public function type() {
        $this->load_view('school_type');
    }
    public function subscription() {
        $this->load->config('payment');        
        $PaymentPackages = $this->config->config['PaymentPackages'];
        
        $data['subscription'] = $PaymentPackages;
        $this->load_view('school_subscription', $data);
    }

    //PRIVATE FUNCTION
    private function load_view($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_other');
    }

    private function generate_passowrd_and_salt($password) {
        // Don't encrypt an empty string

        $p['salt'] = md5(uniqid(rand(), true));
        $p['password'] = hash('sha512', $p['salt'] . $password);
        return $p;
    }

    private function sendMail($i_tmp_school_created_data_id = 0, $i_free_user_id = 0) {
        
        $this->load->config('create_school');
        $this->load->library('school');

        $data['school_created_data'] = $this->tmp->getData($i_tmp_school_created_data_id);
//        $this->tmp->delete($i_tmp_school_created_data_id);
        $user_data = $this->school->getFreeUserDataById($i_free_user_id);

        $custom_urls = $this->config->config['custom_urls'];
        $data['activation_url'] = $custom_urls['activation'] . '?token=' . $this->school->getToken(64);
        $data['login_url'] = $custom_urls['login'];

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
        $this->email->from("info@champs21.com", "Champs21");
        $this->email->subject('School created successfully');
        $this->email->to($user_data['email'], $user_data['first_name'] . ' ' . $user_data['last_name']);

        $data['user_data'] = $user_data;
        $mail_html = $this->load->view('email/activate_user', $data, true);

        $this->email->message($mail_html);

        if ($this->email->send()) {
            $message['status'] = "success";
            $message['message'] = "Your feedback is sent.Thank you.";
            $message['error'] = Null;
        } else {
            $message['status'] = "error";
            $message['message'] = show_error($this->email->print_debugger());
            $message['error'] = "No_Data";
        }
    }

}
