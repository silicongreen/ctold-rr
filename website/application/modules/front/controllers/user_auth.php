<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_auth extends MX_Controller {

    public function __construct()
    {
        parent::__construct();		
		
		$this->load->database();
        $this->load->library('datamapper');
		$this->load->library('form_validation');
		$this->layout_front = false;
    }
	
	public function index() 
	{
            if(isset($_GET["back_url"])) {
                $this->input->get('back_url', TRUE);
            }
            else
            {
                $back_url = "";
            }
            $back_url = "";
            $newdata = array('back_url'  => $back_url);
            $this->session->set_userdata($newdata);		

            if(free_user_logged_in())
            {
                $this->load_view('user_auth/already_done');
            } 

            $data['back_url'] = $back_url;
            $this->form_validation->set_rules('first_name', 'First Name', 'required|min_length[3]');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
            $this->form_validation->set_rules('confirm_email', 'Confirm Email', 'required|valid_email|matches[email]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|min_length[6]|matches[password]');

            if ($this->form_validation->run() == FALSE)
            {
                $this->load_view('user_auth/user_register',$data);
            }
            else
            {			
                $p = $this->generate_passowrd_and_salt($this->input->post('password'));

                $user_data['nick_name'] = $this->input->post('first_name');
                $user_data['password'] = $p['password'];
                $user_data['salt'] = $p['salt'];

                $user_data['email'] = $this->input->post('email');
                $user_data['cnf_email'] = $this->input->post('email');
                $user_data['cnf_password'] = $p['password'];
                $user_data['first_name'] = $this->input->post('first_name');
                $user_data['last_name'] = $this->input->post('last_name');
                $user_data['user_type'] = 1;

                $free_user = new Free_users();

                foreach ($user_data as $key => $value) {

                    $free_user->$key = $value;
                }

                if ($free_user->save()) {

                    $free_user->login();
                    $this->set_user_session($free_user, $this->input->post('password'), false, true);
                    $this->create_free_user_folders();

                    if($back_url)
                    {
                            $this->redirect_parent_url($back_url);
                    }
                    else
                    {					
                            $this->load->view('user_auth/success_message');
                    }

                }
                else 
                {
                        $data['error'] = "Something went wrong please try again later or contact with champs21";
                        $this->load_view('user_auth/user_register',$data);

                }

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
	public function success() 
	{
		$school_type = $this->session->userdata('school_type');
		$user_register = $this->session->userdata('user_register');
		if(!$school_type || ( $school_type!="paid" && $school_type!="free" ))
		{
			redirect("createschool/type");
		} 
		else if(!$user_register)
		{
			redirect("createschool/userregister/".$school_type);
		} 
		$data['school_type'] = $school_type;
	   
		$this->load_view('success',$data);
		
	}
	public function newschool() 
	{
		$school_type = $this->session->userdata('school_type');
		$user_register = $this->session->userdata('user_register');
		if(!$school_type || ( $school_type!="paid" && $school_type!="free" ))
		{
			redirect("createschool/type");
		} 
		else if(!$user_register)
		{
			redirect("createschool/userregister/".$school_type);
		} 
	   
		$data['school_type'] = $school_type;
		
		$this->form_validation->set_rules('name', 'School Name', 'required|min_length[5]');
		if($school_type=="paid")
		{
			$this->form_validation->set_rules('number_of_student', 'Number Of student', 'required|is_natural_no_zero');
		}
		$this->form_validation->set_rules('institution_address', 'Institution Address', 'required|min_length[8]');
		$this->form_validation->set_rules('institution_phone_no', 'Institution Phone Number', 'required|min_length[5]');
		$this->form_validation->set_rules('code', 'Sub domain', 'required|alpha_numeric|min_length[3]|max_length[8]');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load_view('createschool',$data);
		}
		else
		{
			redirect("createschool/success");
			
		}
	}
	public function type() 
	{
		$this->load_view('school_type');
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

	//PRIVATE FUNCTION
	private function set_user_session($obj_user, $pwd = NULL, $remember = false, $b_refresh_cookie = false) {

        set_user_sessions($obj_user, $pwd, $remember, $b_refresh_cookie);
    }
	private function create_free_user_folders() {

        $this->load->config("user_register");

        $ar_data['folders'] = $this->config->config['free_user_folders'];
        $ar_data['user_id'] = get_free_user_session('id');

        $this->load->model("user_folder", 'ur_mod');

        return $this->ur_mod->created_good_read_folders($ar_data);
    }
	private function redirect_parent_url($url) {
       $return = "<script>window.top.location.href = '" . $url . "'</script>";
       return $return;
	}
	private function load_view($view_name,$data=array()) {
		//$this->load->view('layout/header');
		$this->load->view($view_name,$data);
		
		//$this->load->view('layout/footer');
	}
	private function generate_passowrd_and_salt($password) {
		// Don't encrypt an empty string
	   
			$p['salt'] = md5(uniqid(rand(), true));
			$p['password'] = hash('sha512', $p['salt'] . $password);
			return $p;
	}

}
