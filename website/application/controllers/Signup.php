<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function index() {
        $data['user_type'] = $this->input->get("user_type");
        $data['back_url'] = base_url().'login';
        $this->load_view('signup',$data);
    }
    
    public function guardian()
    {
        $this->load_view_inner('guardian');
    }  
    public function student()
    {
        $this->load_view_inner('student');
    }
    public function teacher()
    {
        $this->load_view_inner('teacher');
    }
    public function admin()
    {
        $this->load_view_inner('admin');
    }
    
    public function help_demo()
    {
        $this->load_view_inner('help_demo');
    }
    
    //PRIVATE FUNCTION
    private function load_view($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_other');
    }
    private function load_view_inner($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_inner');
    }

    

}
