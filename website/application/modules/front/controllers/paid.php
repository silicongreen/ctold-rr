<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class paid extends MX_Controller {


    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('datamapper');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->CI = & $this;
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>'); 

        $this->layout_front = false;
        $this->obj_post = new Post_model();
        $this->country = new country();
    }
    public function test_view() {
        $this->load->view('test_view');
    }
    public function forget_password_link() 
    {
        $token = $this->input->get("token");
        
        if($token && check_token_forget_password($token))
        {
            $data['success'] = "";
            if((isset($_POST) && !empty($_POST)) )
            {
                
                $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|min_length[6]|matches[password]');

                if ($this->form_validation->run() == TRUE) 
                {
                    password_change_request($this->input->post("password"), $token);
                    $data['success'] = "Your Password Has Been Change Successfully";
                }
            } 
            $this->load->view('forget_password_link',$data);
        }
        else
        {
            $back = $this->redirect_parent_url("http://www.classtune.com");
            echo $back;
        }    
    }
    public function forget_password() 
    {
        $data['success'] = "";
        if((isset($_POST) && !empty($_POST)) )
        {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_user_forget');
            if ($this->form_validation->run() == TRUE) 
            {
                forget_password_request($this->input->post("username"), $this->input->post("email"));
                $data['success'] = "A link to change your password is send to your mail";
            }
        } 
        $this->load->view('forget_password',$data);
    }
    public function check_user_forget($email) {
        $username = $this->input->post("username");
            
        if (!$username || !check_user_forget_password($username,$email)) 
        {
            $this->form_validation->set_message('check_user_forget', 'Invalid Username and email combination');
            return FALSE;
        } 
        else 
        {
            return TRUE;
        }
        
               
    }

    public function select_school() {
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $user_type = array(1, 2, 3, 4);
        $user_type_check = array(2, 3, 4);
        
        if (!$user_type || !$back_url || !in_array($user_type_send, $user_type_check)) {
            
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }else if ($user_type_send){
            if((isset($_POST) && !empty($_POST)) )
            {    
                
                $this->form_validation->set_rules('paid_school_id', 'School', 'required|callback_check_student_limit');
                $this->form_validation->set_rules('school_code', 'School Code', 'required|callback_school_code_check');
                
                $this->form_validation->set_rules('first_name', 'First Name', 'required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'required');
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
                $this->form_validation->set_rules('confirm_email', 'Confirm Email', 'required|valid_email|matches[email]');
                $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|min_length[6]|matches[password]');

                
                if ($this->form_validation->run() == TRUE) {
                    
                    $form_data=serialize($_POST); 
                    $encoded=htmlentities($form_data);
                    $data['form1_data'] = $encoded;
                    $data['paid_school_id'] = $_POST['paid_school_id'];
                    $data['back_url'] = $back_url;
                    $data['user_type'] = $user_type_send;
                    
					$arCountry = $this->country->get_country();
					$data['countryList'] = $arCountry;
										
                    if($user_type_send == 2) {
                        $this->load->view('apply_for_student_admission',$data);
                        //redirect('front/paid/apply_for_student_admission?back_url=' . $back_url);
                    } else if ($user_type_send == 3) {
                        $this->load->view('apply_for_teacher_admission',$data);
                        //redirect('front/paid/apply_for_teacher_admission?back_url=' . $back_url);
                    } else if ($user_type_send == 4) {
                        $this->load->view('apply_for_parent_admission',$data);
                        //redirect('front/paid/apply_for_parent_admission?back_url=' . $back_url);
                    }
                }
                else
                {
                    $data['back_url'] = $back_url;
                    $data['user_type'] = $user_type_send;
                    $this->load->view('select_school',$data); 
                }    
            }
            else
            {
                $data['back_url'] = $back_url;
                $data['user_type'] = $user_type_send;
                $this->load->view('select_school',$data);
            }
            
            
        } else {
            
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }
    }
    
    public function check_student_limit($school_id) {
        $user_type_send = $this->input->get("user_type");
            
            if (student_limit_excessed($school_id) && $user_type_send == 2 ) {
                $this->form_validation->set_message('check_student_limit', 'Student limit excessed for your school. Please Contact with school administrator. ');
                return FALSE;
                } else {
                return TRUE;
            }
               
    }

    public function school_code_check($str = "") {
        if($str == "")
        {
            $str = $this->input->post('school_code');
            if ($data = check_school_code_paid($str)) {
                $data->error_message = '';
                echo json_encode($data);
                exit;
            } else {
                $data['error_message'] = 'School code does not match.';
                echo json_encode($data);
                exit;
            }
        }else
        {   
            if (!check_school_code_paid($str)) {
                $this->form_validation->set_message('school_code_check', 'Invalid School Code');
                return FALSE;
            } else {
                return TRUE;
            }
        }        
    }
    
    public function email_unique() 
    {
            $free_user = new Free_users();
            $requestedEmail  = $_REQUEST['email'];
            $free_user->email = $requestedEmail;

            if (!$free_user->_email_unique()) {
                    echo 'false';
            }
            else{
                    echo 'true';
            }
    }
    public function email_check($str)
    {
            $this->db->where("email",$str);
            $user_data = $this->db->get("free_users")->row();
            if ($user_data)
            {
                $this->form_validation->set_message('email_check', '{field} Address is already taken');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
    }
    
    public function apply_for_parent_addmission() {
              
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $user_type = array(1, 2, 3, 4);
        $user_type_check = array(2, 3, 4);		
		
        if (!$user_type || !$back_url || !in_array($user_type_send, $user_type_check)) {
            
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }else if ($user_type_send){
            if((isset($_POST) && !empty($_POST)) )
            {                     
                
                $this->form_validation->set_rules('date_of_birth', 'Birth date', 'required');
                $this->form_validation->set_rules('phoneNumber', 'Phone Number', 'required');
                $this->form_validation->set_rules('city', 'City', 'required');
                $this->form_validation->set_rules('address', 'Address', 'required');
                
                if ($this->form_validation->run() == TRUE) {
                    
                    $form1_data=$_POST['form1_data']; 
                    unset($_POST['form1_data']);
                    $ar_form1_data = unserialize($form1_data);
                    $form_data_1 = $ar_form1_data;
                    //echo "<pre>";
                    //print_r($_POST);
                    //exit;
                                        
                    $form_data['form_data'] = $form_data_1 + $_POST;                    
                    $form_data_serialize = serialize($form_data); 
                    $encoded=htmlentities($form_data_serialize);
                    $data['form_data'] = $encoded;
                    
                    $data['back_url'] = $back_url;
                    $data['user_type'] = $user_type_send;
                    $data['paid_school_id'] = $ar_form1_data['paid_school_id'];
                    $data['student_no'] = 1;
                    
                   if ($user_type_send == 4) {
                        $this->load->view('apply_for_parent_admission_2',$data);
                        //redirect('front/paid/apply_for_parent_admission?back_url=' . $back_url);
                    }
                }
                else {
                    $form1_data=$_POST['form1_data'];
                    $data['form_data'] = $form1_data;
                    $data['back_url'] = $back_url;
                    $data['user_type'] = $user_type_send;
                    $data['error'] = "Something went wrong please try again later or contact with ClassTune";
                    $this->load->view('apply_for_parent_addmission',$data);
                }
            }
            else
            {
                $form_data=serialize($_POST['form1_data']); 
                $encoded=htmlentities($form_data);
                $data['form1_data'] = $encoded;
                $data['back_url'] = $back_url;
                $data['user_type'] = $user_type_send;
                $this->load->view('apply_for_parent_admission',$data);
            }
            
            
        } else {
            
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }
    }
    public function apply_for_parent_addmission_2() {
              
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $user_type = array(1, 2, 3, 4);
        $user_type_check = array(2, 3, 4);
        
        if (!$user_type || !$back_url || !in_array($user_type_send, $user_type_check)) {
            
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }else if ($user_type_send){
            if((isset($_POST) && !empty($_POST)) )
            {                     
                
                
                $choose_guardian = $this->input->post("choose_guardian");
                if($choose_guardian != "choose")
                {
                    $this->form_validation->set_rules('s_admission_no', 'Admission No', 'required');
                    $this->form_validation->set_rules('s_admission_date', 'Admission Date', 'required');
                    $this->form_validation->set_rules('s_first_name', 'First Name', 'required');
                    $this->form_validation->set_rules('s_last_name', 'Last Name', 'required');
                    $this->form_validation->set_rules('batch_id', 'Class Name', 'required');
                    $this->form_validation->set_rules('date_of_birth', 'Birth date', 'required');
                }                
                $this->form_validation->set_rules('s_relation', 'Relation', 'required');
                
                if ($this->form_validation->run() == TRUE) {
                    
                    $form_data=$_POST['form_data']; 
                    $ar_form_data = unserialize($form_data);
                    unset($_POST['form_data']);                                      
                                        
                    $data['paid_school_id'] = $_POST['paid_school_id'];
                    $old_student_no = $_POST['student_no'];
                    $data['student_no'] = $old_student_no + 1;
                    unset($_POST['student_no']);                    
                    
                    for($i=1;$i<=$old_student_no;$i++)
                    {
                        if($i==$old_student_no)
                        {   
                            $ar_form_data['student_data'][$i] = $_POST;
                        }
                    }
                    
                    $form_data_serialize = serialize($ar_form_data); 
                    $encoded=htmlentities($form_data_serialize);
                    
                    $data['form_data'] = $encoded;                
                    $data['back_url'] = $back_url;
                    $data['user_type'] = $user_type_send;
                    
                    if($user_type_send == 2) {
                        $this->load->view('apply_for_student_admission_2',$data);
                        //redirect('front/paid/apply_for_student_admission?back_url=' . $back_url);
                    } else if ($user_type_send == 3) {
                        $this->load->view('apply_for_teacher_admission_2',$data);
                        //redirect('front/paid/apply_for_teacher_admission?back_url=' . $back_url);
                    } else if ($user_type_send == 4) {
                        $this->load->view('apply_for_parent_admission_2',$data);
                        //redirect('front/paid/apply_for_parent_admission?back_url=' . $back_url);
                    }
                }
                else {
                    $form_data=$_POST['form_data'];                                         
                    $data['form_data'] = $form_data;
                    
                    $data['paid_school_id'] = $_POST['paid_school_id'];
                    $data['student_no'] = $_POST['student_no'];                    
                    
                    $data['back_url'] = $back_url;
                    $data['user_type'] = $user_type_send;
                    $data['error'] = "Something went wrong please try again later or contact with ClassTune";
                    $this->load->view('apply_for_parent_admission_2',$data);
                }
            }
            else
            {
                $form_data=serialize($_POST['form_data']); 
                $encoded=htmlentities($form_data);
                $data['form_data'] = $encoded;
                $data['back_url'] = $back_url;
                $data['user_type'] = $user_type_send;
                $this->load->view('apply_for_parent_admission',$data);
            }
            
            
        } else {
            
            $back = $this->redirect_parent_url(base_url());
            echo $back;
        }
    }
	public function prepairSingleStudentdata($Data, $choose_guardian)
	{
		$_POST = $Data;		
		
		if($choose_guardian != "choose")
		{
			$this->form_validation->set_rules('s_admission_no', 'Admission No', 'required');
			$this->form_validation->set_rules('s_admission_date', 'Admission Date', 'required');
			$this->form_validation->set_rules('s_first_name', 'First Name', 'required');
			$this->form_validation->set_rules('s_last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('batch_id', 'Class Name', 'required');
			$this->form_validation->set_rules('date_of_birth', 'Birth date', 'required');
		}                
		$this->form_validation->set_rules('s_relation', 'Relation', 'required');
		
		if ($this->form_validation->run() == TRUE) {
			
			$form_data=$_POST['form_data']; 
			$ar_form_data = unserialize($form_data);
			unset($_POST['form_data']);                                      
								
			$data['paid_school_id'] = $_POST['paid_school_id'];
			$old_student_no = $_POST['student_no'];
			$data['student_no'] = $old_student_no + 1;
			unset($_POST['student_no']);                    
			
			for($i=1;$i<=$old_student_no;$i++)
			{
				if($i==$old_student_no)
				{   
					$ar_form_data['student_data'][$i] = $_POST;
				}
			}
			
			$encoded = serialize($ar_form_data); 
			
			return $encoded;               
			
		}
	}
    public function apply_for_parent_admission_final() {
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $student_no = $_POST['student_no'];
        $choose_guardian = $this->input->post("choose_guardian");
		
		if($student_no == 1)
		{			
			$form_data = $this->prepairSingleStudentdata($_POST,$choose_guardian);
		}
		else
		{
			$form_data=$_POST['form_data']; 
		}

        $ar_form_data = unserialize($form_data);             
        $ar_form_data['form_data']['user_type'] = $user_type_send;
		//echo "<pre>";
        //print_r($ar_form_data);
        //exit;
        $success = false;
        
        for($i=1;$i<=$student_no;$i++)
        {
            $existing_student = $ar_form_data['student_data'][$i]['choose_guardian'];
            if($existing_student == "choose")
            {
                $su_id = $ar_form_data['student_data'][$i]['s_id'];
                $paid_st_data = $this->get_student_data_by_user_id($su_id);
                $std_id = $paid_st_data['sid'];
                $user_data = new stdClass();
                $user_data->paid_school_id = $ar_form_data['form_data']['paid_school_id'];
                $admission_no = $ar_form_data['student_data'][$i]['s_username'];
                
                if ($user_data->paid_school_id < 10) {
                    $idchange = "0" . $user_data->paid_school_id;
                } else {
                    $idchange = $user_data->paid_school_id;
                }
                $school_code = $idchange . "-";
                $length = strlen($school_code);
                $postdata["admission_no"] = substr($admission_no, $length);
                
                $data['students'][$i]['fulname'] = $paid_st_data["first_name"]." ".$paid_st_data["middle_name"]." ".$paid_st_data["last_name"];
                $data['students'][$i]['username'] = $admission_no;
                $data['students'][$i]['admission_no'] = $paid_st_data["admission_no"];
            }
            else
            {
                //STUDENT FREE USER CREATION//
                $student_data['user_type'] = 2;
                $student_data['first_name'] = $ar_form_data['student_data'][$i]['s_first_name']." ".$ar_form_data['student_data'][$i]['s_middle_name'];
                $student_data['password'] = $ar_form_data['student_data'][$i]['s_password'];
                $student_data['last_name'] = $ar_form_data['student_data'][$i]['s_last_name'];
                $student_data['email'] = $ar_form_data['form_data']['email'];
                $student_data['gender'] = $ar_form_data['student_data'][$i]['s_gender'];
                $student_data['date_of_birth'] = $ar_form_data['student_data'][$i]['date_of_birth'];
                $student_data['paid_school_id'] = $ar_form_data['student_data'][$i]['paid_school_id'];
                if($user_data = $this->createFreeUser($student_data))
                {                
                    unset($postdata);
                    $postdata["admission_no"] = $ar_form_data['student_data'][$i]['s_admission_no'];
                    $postdata["admission_date"] = $ar_form_data['student_data'][$i]['s_admission_date'];
                    $postdata["class_roll_no"] = $ar_form_data['student_data'][$i]['s_class_roll_no'];
                    $postdata["batch_id"] = $ar_form_data['student_data'][$i]['batch_id'];
                    $postdata["password"] = $ar_form_data['student_data'][$i]['s_password'];
                    $postdata["first_name"] = $ar_form_data['student_data'][$i]['s_first_name'];//." ".$ar_form_data['student_data'][$i]['s_middle_name']
                    $postdata["last_name"] = $ar_form_data['student_data'][$i]['s_last_name'];
                    $postdata['paid_school_id'] = $ar_form_data['student_data'][$i]['paid_school_id'];
                    $postdata['email'] = $ar_form_data['form_data']['email'];

                    $u_id = $this->create_paid_user_for_all($user_data, $postdata,2);  

                    if($u_id[0])
                    {
                        $data['students'][$i]['fulname'] = $postdata["first_name"]." ".$postdata["last_name"];
                        $data['students'][$i]['username'] = $u_id[1];
                        $data['students'][$i]['admission_no'] = $postdata["admission_no"];
                        
                        $postdata["middle_name"] = $ar_form_data['student_data'][$i]['s_middle_name'];                    
                        $postdata["city"] = $ar_form_data['form_data']['city'];
                        $postdata["date_of_birth"] = $ar_form_data['student_data'][$i]['date_of_birth'];
                        $postdata["gender"] = $ar_form_data['student_data'][$i]['s_gender'];
                        $postdata["mobile_phone"] = $ar_form_data['form_data']['phoneNumber'];

                        $this->update_user_before_apply($user_data, $postdata, $u_id[1],$u_id[0]);
                        $this->db->dbprefix = '';
                        $st = $this->get_user_default_data($user_data, $u_id[0]);
                        $st['admission_no'] = $postdata["admission_no"];
                        $st['admission_date'] = $postdata["admission_date"];
                        $st['class_roll_no'] = $postdata["class_roll_no"];
                        $st['batch_id'] = $postdata["batch_id"];

                        $st['first_name'] = $postdata["first_name"];
                        $st['middle_name'] = $postdata["middle_name"];
                        $st['last_name'] = $postdata["last_name"];

                        $st['date_of_birth'] = $postdata["date_of_birth"];
                        $st['city'] = $postdata["city"];
                        $st['gender'] = $postdata["gender"];
                        $st['phone2'] = $postdata["mobile_phone"];
                        $st['address_line2'] = $ar_form_data['form_data']['address'];

                        $this->db->insert('students', $st);
                        $std_id = $this->db->insert_id();

                        $sb['sibling_id'] = $std_id;                        
                        $this->db->where('id', $std_id);
                        $this->db->update('students', $sb);
                        
                        $success = true;
                    }
                    else
                    {
                        $success = false;
                    }
                }
                else
                {
                    $success = false;
                }
                
            }
            
            if($std_id)
            {
                $n_g = 1;
                $postdata["gfirst_name"] = $ar_form_data['form_data']['first_name'];
                $postdata["glast_name"] = $ar_form_data['form_data']['last_name'];
                $postdata["gpassword"] = $ar_form_data['form_data']['password'];
                $postdata["gdate_of_birth"] = $ar_form_data['form_data']['date_of_birth'];
                $postdata["gmobile_phone"] = $ar_form_data['form_data']['phoneNumber'];
                $postdata["gemail"] = $ar_form_data['form_data']['email'];
                $postdata["gaddress"] = $ar_form_data['form_data']['address'];
                $postdata["relation"] = $ar_form_data['student_data'][$i]['s_relation'];
                $postdata["country_id"] = $ar_form_data['form_data']['country_id'];
                if($i==1)
                {
                    $gu_id = $this->createGuardianStudent($postdata, $n_g, $std_id, $user_data);
                    if($gu_id)
                    {
                        $data['guardian']['fulname'] = $postdata["gfirst_name"]." ".$postdata["glast_name"];
                        //$data['guardian']['username'] = make_paid_username($user_data, $postdata["admission_no"], true, true);
                        $data['guardian']['username'] = $this->get_guardian_userdata_by_userid($gu_id);
                        $data['email'] = $postdata['gemail'];
                        $data['email_name'] = $postdata["gfirst_name"]." ".$postdata["glast_name"];
                        $success = true;
                    }
                    else
                    {
                        $success = false;
                    }
                }
                else
                {
                    if($this->existing_guardian_add_to_student($gu_id,$std_id,$postdata["relation"]))
                    {
                        $success = true;
                    }
                    else
                    {
                        $success = false;
                    }
                }
            }
                                
             
        }
        if($success)
        {
            $data['error'] = 0;
            $this->send_email_to_user($data);
            $this->load->view('apply_for_parent_admission_final',$data);
        }
        else
        {
            $data['error'] = 1;//
            $this->load->view('apply_for_parent_admission_final',$data);
        }
    }

    public function apply_for_student_admission() {
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $user_type = array(1, 2, 3, 4);
        $user_type_check = array(2, 3, 4);
        $success = false;
        
        if ($user_type_send==2) {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();

            $data['ci_key'] = "apply_for_student_admission";
            $data['ci_key_for_cover'] = "apply_for_student_admission";
            $data['s_category_ids'] = "0";   

            if (isset($_POST) && !empty($_POST)) {
                $data['post_data'] = $_POST;
                $paid_school_id = $data['paid_school_id'] = $_POST['paid_school_id'];
                $user_type_send = $this->input->get("user_type");
                $back_url = $this->input->get("back_url");
                $data['back_url'] = $back_url;
                $data['user_type'] = $user_type_send;
                
                
                $this->form_validation->set_rules('paid_school_id', 'School', 'required|callback_check_student_limit');
                $this->form_validation->set_rules('admission_no', 'Admission No', 'required');
                $this->form_validation->set_rules('admission_date', 'Admission Date', 'required');
                $this->form_validation->set_rules('batch_id', 'Class Name', 'required');
                $this->form_validation->set_rules('date_of_birth', 'Birth date', 'required');
                $this->form_validation->set_rules('city', 'City', 'required');

                $add_guardian = $this->input->post("add_guardian");
                $choose_guardian = $this->input->post("choose_guardian");
                $choose_guardian2 = $this->input->post("choose_guardian2");
                
                if ($add_guardian == "one") {
                    if($choose_guardian == "choose")
                    {
                        $guardian1_id = $this->input->post("g_id");
                    }
                    else
                    {
                        $this->guardian_one_validation_rule();
                    }
                }
                if ($add_guardian == "two") 
                {
                    if($choose_guardian == "choose")
                    {
                        $guardian1_id = $this->input->post("g_id");
                    }
                    else
                    {                        
                        $this->guardian_one_validation_rule();
                    }

                    if($choose_guardian2 == "choose")
                    {
                        $guardian2_id = $this->input->post("g_id2");
                    }
                    else
                    {
                        $this->guardian_two_validation_rule();
                    }
                }                        
                
                if ($this->form_validation->run() == TRUE) {
                    $form1_data=$_POST['form1_data']; 
                    $data['form1_data'] = $form1_data;
                    unset($_POST['form1_data']);
                    $ar_form1_data = unserialize($form1_data);
                    $post_data = $_POST + $ar_form1_data;
                    $post_data['user_type'] = $user_type_send ;       
                    $post_data['country_id'] = 14 ;     
					
                    //insert user data
                    if($user_data = $this->createFreeUser($post_data))
                    {                        
						//insert user data
                        $u_id = $this->create_paid_user_for_all($user_data, $post_data,$user_type_send);      
						
                        //insert student data
                        if ($u_id[0]) {
                            unset($data);
                            $data['student']['fulname'] = $post_data["first_name"]." ".$post_data["middle_name"]." ".$post_data["last_name"];
                            $data['student']['username'] = $u_id[1];
                            $data['student']['admission_no'] = $post_data["admission_no"];
                            $data['email'] = $post_data['email'];
                            $data['email_name'] = $post_data["first_name"]." ".$post_data["middle_name"]." ".$post_data["last_name"];
                            
                            $this->update_user_before_apply($user_data, $post_data, $u_id[1],$u_id[0]);
                            $this->db->dbprefix = '';
                            $st = $this->get_user_default_data($user_data, $u_id[0]);
                            $st['admission_no'] = $post_data["admission_no"];
                            $st['admission_date'] = $post_data["admission_date"];
                            $st['class_roll_no'] = $post_data["class_roll_no"];
                            $st['batch_id'] = $post_data["batch_id"];
                            
                            $st['first_name'] = $post_data["first_name"];
                            $st['middle_name'] = $post_data["middle_name"];
                            $st['last_name'] = $post_data["last_name"];
                            
                            $st['date_of_birth'] = $post_data["date_of_birth"];
                            $st['city'] = $post_data["city"];
                            $st['gender'] = $post_data["gender"];

                            $this->db->insert('students', $st);

                            $std_id = $this->db->insert_id();

                            $sb['sibling_id'] = $std_id;                        
                            $this->db->where('id', $std_id);
                            $this->db->update('students', $sb);
                            update_subscription_current_count($paid_school_id);
							
                            //create guardian
                            //$this->db->dbprefix = 'tds_';
                            if ($add_guardian == "one") {
                                if($guardian1_id > 0)
                                {
                                    if($this->existing_guardian_add_to_student($guardian1_id,$std_id,$post_data["relation"]))
                                    {
                                        $data['guardians'][1]['username'] = $post_data['g_username'];
                                        $data['guardians'][1]['fulname'] = '';
                                        $success = true;
                                    }
                                    else
                                    {
                                        $success = false;
                                    }
                                }
                                else
                                {   
                                    $n_g = 1;
                                    $gu_id1 = $this->createGuardianStudent($post_data, $n_g, $std_id, $user_data);									
                                    
                                    if($gu_id1)
                                    {
                                        $g1_username = $this->get_guardian_userdata_by_userid($gu_id1);
                                        //$this->update_user_before_apply($user_data, $post_data, $g1_username,$gu_id1);
                                        $data['guardians'][1]['username'] = $g1_username;
                                        $data['guardians'][1]['fulname'] = $post_data["gfirst_name"]." ".$post_data["glast_name"];
                                        $success = true;
                                    }
                                    else
                                    {
                                        $success = false;
                                    }
                                }								
                            }

                            if ($add_guardian == "two") {
                                if($guardian1_id > 0)
                                {
                                    if($this->existing_guardian_add_to_student($guardian1_id,$std_id,$post_data["relation"]))
                                    {
                                        $data['guardians'][1]['username'] = $post_data['g_username'];
                                        $data['guardians'][1]['fulname'] = '';
                                        $success = true;
                                    }
                                    else
                                    {
                                        $success = false;
                                    }
                                }
                                else
                                {
                                    $n_g = 1;
                                    $gu_id2 = $this->createGuardianStudent($post_data, $n_g, $std_id, $user_data);
                                    if($gu_id2)
                                    {
                                        $g2_username = $this->get_guardian_userdata_by_userid($gu_id2);  
                                        //$this->update_user_before_apply($user_data, $post_data, $g2_username,$gu_id2);
                                        $data['guardians'][1]['username'] = $g2_username;                                 
                                        $data['guardians'][1]['fulname'] = $post_data["gfirst_name"]." ".$post_data["glast_name"];
                                        $success = true;
                                    }
                                    else
                                    {
                                        $success = false;
                                    }
                                }

                                if($guardian2_id > 0)
                                {                                                                
                                    if($this->existing_guardian_add_to_student($guardian2_id,$std_id,$post_data["relation2"]))
                                    {
                                        $data['guardians'][2]['username'] = $post_data['g_username2'];
                                        $data['guardians'][2]['fulname'] = '';
                                        $success = true;
                                    }
                                    else
                                    {
                                        $success = false;
                                    }
                                }
                                else
                                {
                                    $n_g = 2;
                                    $gu_id2 = $this->createGuardianStudent($post_data, $n_g, $std_id, $user_data);
                                    if($gu_id2)
                                    {
                                        //$data['guardians'][2]['username'] = make_paid_username($user_data, $post_data["admission_no"], true, true);
                                        $data['guardians'][2]['username'] = $this->get_guardian_userdata_by_userid($gu_id2);
                                        $data['guardians'][2]['fulname'] = $post_data["gfirst_name2"]." ".$post_data["glast_name2"];
                                        
                                        $success = true;
                                    }
                                    else
                                    {
                                        $success = false;
                                    }
                                }
                            }                        
                        }
                                               
                        if($success)
                        {
                            $data['error'] = 0;
                            $this->send_email_to_user($data);
                            $this->load->view('apply_for_student_admission_final',$data);
                        }
                        else
                        {
                            $data['error'] = 1;//
                            $this->load->view('apply_for_student_admission_final',$data);
                        }
                        //$back = $this->redirect_parent_url($back_url);
                        //echo $back;
                        //exit;
                    }
                    else
                    { 
						$data['error'] = "Something went wrong please try again later or contact with ClassTune";
                        $this->load->view('apply_for_student_admission',$data);
                    }
                }
                else
                {
                    $this->load->view('apply_for_student_admission', $data);
                }
            }
            else
            {
                $this->load->view('apply_for_student_admission', $data);
            }
            
            
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

    public function apply_for_teacher_admission() {
        $this->layout_front = false;
        $user_type_send = $this->input->get("user_type");
        $back_url = $this->input->get("back_url");
        $user_type = array(1, 2, 3, 4);
        $user_type_check = array(2, 3, 4);
        $success = false;
        
        if ($user_type_send==3) {
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
                $form1_data=$_POST['form1_data']; 
                $data['form1_data'] = $form1_data;
                unset($_POST['form1_data']);
                $ar_form1_data = unserialize($form1_data);
                $post_data = $_POST + $ar_form1_data;
                $post_data['user_type'] = $user_type_send ;     
                
                
                $this->form_validation->set_rules('admission_no', 'Employee NO', 'required');                
                $this->form_validation->set_rules('employee_department_id', 'Employee Department', 'required');
                $this->form_validation->set_rules('employee_category', 'Employee Category', 'required');
                $this->form_validation->set_rules('employee_position_id', 'Employee Position', 'required');
                $this->form_validation->set_rules('date_of_birth', 'Birth date', 'required');
                $this->form_validation->set_rules('joining_date', 'Joining date', 'required');


                if ($this->form_validation->run() == TRUE) {


                    if($user_data = $this->createFreeUser($post_data))
                    {

                        $u_id = $this->create_paid_user_for_all($user_data, $post_data,3);


                        if ($u_id[0]) {
                            unset($data);
                            $data['teacher']['fulname'] = $post_data["first_name"]." ".$post_data["middle_name"]." ".$post_data["last_name"];
                            $data['teacher']['username'] = $u_id[1];
                            $data['teacher']['admission_no'] = $post_data["admission_no"];
                            $data['email'] = $post_data['email'];
                            $data['email_name'] = $post_data["first_name"]." ".$post_data["middle_name"]." ".$post_data["last_name"];
                            
                            $this->update_user_before_apply($user_data, $post_data, $u_id[1],$u_id[0]);
                            $this->db->dbprefix = '';
                            $st = $this->get_user_default_data($user_data, $u_id[0]);
                            $st['employee_number'] = make_paid_username($user_data, $post_data['admission_no']);
                            $st['employee_category_id'] = $post_data["employee_category"];
                            $st['employee_position_id'] = $post_data["employee_position_id"];
                            $st['employee_department_id'] = $post_data["employee_department_id"];
                            if($post_data["employee_grade_id"])
                            $st['employee_grade_id'] = $post_data["employee_grade_id"];
                            $st['first_name'] = $post_data["first_name"];
                            $st['last_name'] = $post_data["last_name"];
                            $st['date_of_birth'] = $post_data["date_of_birth"];
                            $st['joining_date'] = $post_data["joining_date"];
                            $st['gender'] = $post_data["gender"];
                            
                            if($post_data["job_title"])
                            $st['job_title'] = $post_data["job_title"];
                            if($post_data["mobile_phone"])
                            $st['mobile_phone'] = $post_data["mobile_phone"];
                            $st['office_country_id'] = $post_data["country_id"];

                            $this->db->insert('employees', $st);

                            $std_id = $this->db->insert_id();
                            $success = true;
                            if (isset($_POST['batch_id']) && $_POST['batch_id']) {
                                $eb['employee_id'] = $std_id;
                                $eb['batch_id'] = $post_data["batch_id"];
                                $this->db->insert('batch_tutors', $eb);
                            }

                            $this->db->dbprefix = 'tds_';
                        }
                        
                        if($success)
                        {
                            $data['error'] = 0;
                            $this->send_email_to_user($data);
                            $this->load->view('apply_for_teacher_admission_final',$data);
                        }
                        else
                        {
                            $data['error'] = 1;//
                            $this->load->view('apply_for_teacher_admission_final',$data);
                        }

                        //$back = $this->redirect_parent_url($back_url);
                        //echo $back;
                        //exit;
                    }
                    else
                    {
                        $data['error'] = "Something went wrong please try again later or contact with ClassTune";
                        $this->load_view('apply_for_teacher_admission',$data);
                    }
                }
                else
                {
                    $this->load->view('apply_for_teacher_admission', $data);
                }
            }
            else
            {
                $this->load->view('apply_for_teacher_admission', $data);
            }

            
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
    
    public function existing_guardian_add_to_student($gu_id,$std_id,$relation) {
        $g_data = $this->get_guardian_data_by_user_id($gu_id);
        if($this->isnot_already_guardian_added_to_student($std_id,$g_data['gid']))
        {
            if($this->update_student_immediate_contact_id($std_id,$g_data['gid']))
            {
                if($this->new_guardian_student_relation($std_id,$g_data['gid'],$relation))
                {
                    return 1;
                }else {return 2;}
            }else {return 3;}
        }else {return 4;}
    }
    public function is_guardian_exist() 
    {
        $requestedUsername  = $_REQUEST['g_username'];
        $this->db->dbprefix = '';
        $this->db->select("*");
        $this->db->where("username", $requestedUsername);
        $this->db->where("parent", 1);
        $parents = $this->db->get("users")->result();

        if ($parents) {
            foreach ($parents as $value) {
                $data['id'] = $value->id;
                $data['first_name'] = $value->first_name;
                $data['last_name'] = $value->last_name;
            }
            
            echo json_encode($data);
        }
        else
        {
            echo 'false';
        }
    }
    public function get_guardian_userdata_by_userid($user_id) 
    {        
        $this->db->dbprefix = '';
        $this->db->select("*");        
        $this->db->where("parent", 1);
        $this->db->where("id", $user_id);
        $parents = $this->db->get("users")->result();

        if ($parents) {
            foreach ($parents as $value) {                
                $gusername = $value->username;
            }
            
           return $gusername;
        }
        else
        {
            return false;
        }
    }
    public function is_student_exist_username() 
    {
        $requestedUsername  = $_REQUEST['s_username'];
        $this->db->dbprefix = '';
        $this->db->select("*");
        $this->db->where("username", $requestedUsername);
        $this->db->where("student", 1);
        $students = $this->db->get("users")->result();
        
        if ($students) {
            foreach ($students as $value) {
                $data['id'] = $value->id;
                $data['first_name'] = $value->first_name;
                $data['last_name'] = $value->last_name;
            }
            $data['success'] = 1;
            echo json_encode($data);
        }
        else
        {
            $data['success'] = 0;
			echo json_encode($data);
        }
    }
    public function is_student_username_exist() 
    {        
        $admission_no  = $_REQUEST['admission_no'];
        $paid_school_id  = $_REQUEST['paid_school_id'];
        $s_admission_no  = $_REQUEST['s_admission_no'];
		if(preg_match("/^[a-zA-Z0-9\-]+$/i", $s_admission_no))
		{
			$this->db->dbprefix = '';
			$this->db->select('id');
			$this->db->from('users');
			$this->db->where("student", 1);
			$this->db->where('username', trim($admission_no));
			$this->db->where('school_id',$paid_school_id);                 
			$std = $this->db->get()->row();
			$this->db->dbprefix = 'tds_';

			if($std)
			{
				$data['success'] = 0;
				echo json_encode($data);			
			}
			else
			{
				$data['success'] = 1;
				echo json_encode($data);
			}   
		}
		else
		{
			$data['success'] = 0;
			echo json_encode($data);
		}   
    }
    public function is_teacher_username_exist() 
    {        
        $admission_no  = $_REQUEST['admission_no'];
        $paid_school_id  = $_REQUEST['paid_school_id'];
                
        $this->db->dbprefix = '';
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where("employee", 1);
        $this->db->where('username', trim($admission_no));
        $this->db->where('school_id',$paid_school_id);                 
        $std = $this->db->get()->row();
        $this->db->dbprefix = 'tds_';

        if($std)
        {
            echo 'false';
        }
        else
        {
            echo 'true';
        }     
    }
	public function getCountryid() 
    {        
        $countryCode  = $_REQUEST['countryCode'];
        $countryDialCode  = $_REQUEST['countryDialCode'];
                
        $this->db->dbprefix = '';
        $this->db->select('id');
        $this->db->from('countries');
        $this->db->where("code", trim($countryCode));
        $this->db->where('phone_code', trim($countryDialCode));        
        $std = $this->db->get()->row()->id;
        $this->db->dbprefix = 'tds_';
		
		if($std)
        {
            echo $std;
        }
        else
        {
            echo 'false';
        }  
    }
    /****PRIVATE FUNCTIONS****/
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

    private function create_paid_user_for_all($user_data, $postdata,$user_type = 0) {

        $user['username'] = make_paid_username($user_data, $postdata["admission_no"]);
        $user['salt'] = $this->generateRandomString();

        $user['hashed_password'] = sha1($user['salt'] . $postdata["password"]);

        $user['admin'] = 0;
        $user['student'] = ($user_type == 2)?1:0;        
        $user['employee'] = ($user_type == 3)?1:0;
        $user['parent'] = ($user_type == 4)?1:0;

        $user['free_user_id'] = $user_data->id;

        $user['is_approved'] = 1;

        $user['first_name'] = $postdata["first_name"];
        $user['last_name'] = $postdata["last_name"];
        
        if (isset($postdata['email']) && $postdata['email']) {
            $user['email'] = $postdata['email'];
        }
        $user['is_first_login'] = 0;

        $user['is_deleted'] = 0;
        $user['school_id'] = $user_data->paid_school_id;
        $user['created_at'] = date("Y-m-d H:i:s");
        $user['updated_at'] = date("Y-m-d H:i:s");
        $this->db->dbprefix = '';
        $this->db->insert('users', $user);
        $u_id = $this->db->insert_id();
        $this->db->dbprefix = 'tds_';
        return array($u_id, $user['username']);
    }

    private function update_user_before_apply($user_data, $u_array, $u_id,$paid_id=0) {
        $f_user['paid_password'] = $u_array['password'];
        $f_user['paid_username'] = $u_id;
        $f_user['email'] = $u_id;
        $f_user['paid_id'] = $paid_id;

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
        $f_user['nick_name'] = $u_array['first_name'];
        $f_user['last_name'] = $u_array['last_name'];
        $f_user['applied_paid'] = 1;
        $this->db->where('id', $user_data->id);
        $this->db->update('tds_free_users', $f_user);
    }

    private function get_user_default_data($user_data, $u_id) {

        if ($user_data->user_type == 2 || $user_data->user_type == 3) {
            $data['nationality_id'] = 14;        }

        if ($user_data->user_type == 2 || $user_data->user_type == 4) {
            $data['country_id'] = 14;
        }
        if ($user_data->user_type == 2) {
            $data['is_active'] = 1;
            $data['is_deleted'] = 0;
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

        $f_user_data = new Free_users();
        $extra = "";
        if ($n_g > 1) {
            $extra = $n_g;
        }
        
        $f_user_data->nick_name = $s_array['gfirst_name' . $extra];
        $f_user_data->first_name = $s_array['gfirst_name' . $extra];
        $f_user_data->last_name = $s_array['glast_name' . $extra];
        $f_user_data->email = $s_array['admission_no'] . "@classtune.com";
        $f_user_data->cnf_email = $s_array['admission_no'] . "@classtune.com";
        $f_user_data->password = $s_array['gpassword' . $extra];
        $f_user_data->cnf_password = $s_array['gpassword' . $extra];        
        $f_user_data->paid_school_id = $user_data->paid_school_id;
        if (isset($s_array['gdate_of_birth']) && $s_array['gdate_of_birth']) {
            $f_user_data->dob = $s_array['gdate_of_birth'];
        }
        if (isset($s_array['gmobile_phone']) && $s_array['gmobile_phone']) {
            $f_user_data->mobile_no = $s_array['gmobile_phone'];
        }
        
        
        $f_user_data->tds_country_id = $s_array['country_id'];
        $f_user_data->city = $s_array['city'];
        
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
            $user['is_deleted'] = 0;
            $user['is_approved'] = 1;
            if (isset($s_array['gemail']) && $s_array['gemail']) {
                $f_user['email'] = $s_array['gemail'];
            }
            $user['school_id'] = $user_data->paid_school_id;
            $user['created_at'] = date("Y-m-d H:i:s");
            $user['updated_at'] = date("Y-m-d H:i:s");
            $this->db->dbprefix = '';
            
            $this->db->insert('users', $user);
            $u_id = $this->db->insert_id();

            $ward = get_parent_children($s_array["admission_no"], $user_data);
            $data['user_id'] = $u_id;
            $data['country_id'] = $s_array['country_id'];
            $data['school_id'] = $user_data->paid_school_id;
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $data['first_name'] = $s_array['gfirst_name' . $extra];
            $data['last_name'] = $s_array['glast_name' . $extra];
            $data['city'] = $s_array['city'];
            $data['relation'] = $s_array['relation' . $extra];
            $data['ward_id'] = $sid;
            if (isset($s_array['gemail']) && $s_array['gemail']) {
                $data['email'] = $s_array['gemail'];
            }
            if (isset($s_array['gmobile_phone']) && $s_array['gmobile_phone']) {
                $data['mobile_phone'] = $s_array['gmobile_phone'];
            }
            if (isset($s_array['gaddress']) && $s_array['gaddress']) {
                $data['office_address_line1'] = $s_array['gaddress'];
            }
            if (isset($s_array['gdate_of_birth']) && $s_array['gdate_of_birth']) {
                $data['dob'] = $s_array['gdate_of_birth'];
            }
            
            $this->db->dbprefix = '';
            $this->db->insert('guardians', $data);

            $gid_id = $this->db->insert_id();

            if (!$ward->immediate_contact_id) {
                $this->db->dbprefix = '';
                $sb['immediate_contact_id'] = $gid_id;
                $this->db->where('id', $ward->id);
                $this->db->update('students', $sb);
            }

            $this->new_guardian_student_relation($sid,$gid_id,$s_array['relation' . $extra]) ;

            $this->update_freeuser_applied_status($user['username'], $u_id,$f_user_data->id);

        }

        return $u_id;
    }
    private function createFreeUser($s_array) {

        $p = $this->generate_passowrd_and_salt($s_array['password']);

        $user_data['nick_name'] = $s_array['first_name'];
        $user_data['password'] = $p['password'];
        $user_data['salt'] = $p['salt'];

        $user_data['email'] = $s_array['email'];
        $user_data['first_name'] = $s_array['first_name'];
        $user_data['last_name'] = $s_array['last_name'];
        $user_data['gender'] = ($s_array['gender']=='m' || $s_array['gender']==1)?1:0;
        $user_data['dob'] = $s_array['date_of_birth'];
        $user_data['user_type'] = $s_array['user_type'];
        $user_data['paid_school_id'] = $s_array['paid_school_id'];
        $user_data['password'] = $p['password'];
        $user_data['tds_country_id'] = 14;
        $this->db->dbprefix = 'tds_';      
        $this->db->insert('free_users', $user_data);
        $sftd_id = $this->db->insert_id();
        
        
        $free_user = new Free_users($sftd_id);       
       
        if ($free_user) {
            return $free_user;
        }else
        {
            return false;
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
    private function generate_passowrd_and_salt($password) {
        // Don't encrypt an empty string

        $p['salt'] = md5(uniqid(rand(), true));
        $p['password'] = hash('sha512', $p['salt'] . $password);
        return $p;
    }
    private function get_guardian_data_by_user_id($user_id) 
    {        
        $this->db->dbprefix = '';
        $this->db->select("*");
        $this->db->where("user_id", $user_id);
        $parents = $this->db->get("guardians")->result();

        if ($parents) {
            foreach ($parents as $value) {
                $data['gid'] = $value->id;
            }
            return $data;
        }
        else
        {
            return false;
        }
    }
    private function get_student_data_by_user_id($user_id) 
    {        
        $this->db->dbprefix = '';
        $this->db->select("*");
        $this->db->where("user_id", $user_id);
        $parents = $this->db->get("students")->result();

        if ($parents) {
            foreach ($parents as $value) {
                $data['sid'] = $value->id;
                $data['first_name'] = $value->first_name;
                $data['last_name'] = $value->last_name;
                $data['middle_name'] = $value->middle_name;
                $data['admission_no'] = $value->admission_no;
            }
            return $data;
        }
        else
        {
            return false;
        }
    }
    private function isnot_already_guardian_added_to_student($s_id,$g_id) 
    {        
        $this->db->dbprefix = '';
        $this->db->where('guardian_id	',$g_id);
        $this->db->where('student_id',$s_id);
        $query = $this->db->get('guardian_students');
        if ($query->num_rows() > 0){
                $data = $query->row_array();
                return false;
        }
        else{
                return true;
        }
    }
    private function update_student_immediate_contact_id($s_id,$g_id) 
    {        
        $this->db->dbprefix = '';
        $data['immediate_contact_id'] = $g_id;
        
        $this->db->where('id',$s_id);
        $this->db->update('students', $data);
        
        $this->db->trans_status();
        if ($this->db->affected_rows() == '1') {
                return TRUE;
        } else {		
                return false;
        }        
    }
    private function update_freeuser_applied_status($username, $paid_id, $fu_id) 
    {   
        //update freeuser data
        $this->db->dbprefix = 'tds_';
        $f_user['email'] = $username;
        $f_user['paid_username'] = $username;
        $f_user['paid_id'] = $paid_id;
        $f_user['applied_paid'] = 1;
        $f_user['dob'] = NULL;

        $this->db->where('id', $fu_id);
        $this->db->update('free_users', $f_user);
        
        $this->db->trans_status();
        if ($this->db->affected_rows() == '1') {
                return TRUE;
        } else {		
                return false;
        }        
    }
    private function new_guardian_student_relation($s_id,$g_id,$reation) 
    {        
        $this->db->dbprefix = '';
        $data['student_id'] = $s_id;
        $data['guardian_id'] = $g_id;
        $data['relation'] = $reation;
        
        $this->db->insert('guardian_students', $data);
        
        $this->db->trans_status();
        if($insert_id > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }
    private function guardian_one_validation_rule()
    {
        $this->form_validation->set_rules('gfirst_name', 'Guardian First Name', 'required');
        $this->form_validation->set_rules('glast_name', 'Guardian Last Name', 'required');
        $this->form_validation->set_rules('gpassword', 'Guardian Password', 'required');
        $this->form_validation->set_rules('relation', 'Relation', 'required');
    }
    private function guardian_two_validation_rule()
    {
        $this->form_validation->set_rules('gfirst_name2', 'Guardian First Name (2nd)', 'required');
        $this->form_validation->set_rules('glast_name2', 'Guardian Last Name (2nd)', 'required');
        $this->form_validation->set_rules('gpassword2', 'Guardian Password (2nd)', 'required');
        $this->form_validation->set_rules('relation2', 'Relation (2nd)', 'required');
    }
    
    private function send_email_to_user($data)
    {
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
       
       $mail_html = $this->load->view('email_template/singup',$data, true);

       $this->email->message($mail_html);

       $this->email->send();
   }
}
