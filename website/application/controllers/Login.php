<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $data['error'] = "";
        if ($this->form_validation->run() == FALSE) 
        {
            $this->load_view('login',$data);
        } 
        else 
        {
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $this->db->set_dbprefix('');
            $where = "(username='".$username."' AND is_approved=1) and (is_deleted=0 OR parent=1)";
            $this->db->where($where);
            $users = $this->db->get("users")->row();
            if($users)
            {
                $hashed_password = sha1($users->salt.$password);
                if($hashed_password == $users->hashed_password)
                {
                    $this->db->where("linkable_id",$users->school_id);
                    $domain = $this->db->get("school_domains")->row();
                    if($domain)
                    {
                        $random = md5(rand());
                        $insert['auth_id'] = $random;
                        $insert['user_id'] = $users->id;
                        $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));
                        $this->db->insert("tds_user_auth", $insert);
                        
                        
                        $params = "?username=" . $username . "&password=" . $password . "&auth_id=" . $random . "&user_id=" . $users->id;
                        $url = "http://" .$domain->domain . $params;
                        header("Location: " . $url);
                    }
                    else
                    {
                        $data['error'] = "Wrong Username or Password";
                        $this->load_view('login',$data);
                    }    
                }
                else
                {
                    $data['error'] = "Wrong Username or Password";
                    $this->load_view('login',$data);
                }    
            }
            else
            {
                
                $data['error'] = "Wrong Username or Password"; 
                $this->load_view('login',$data);
            } 
            
            $this->db->set_dbprefix('');
            
            
        }
    }
    
    //PRIVATE FUNCTION
    private function load_view($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_other');
    }

    

}
