<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
class setting extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->form_validation->CI =& $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }
   
    
    public function index(){
        $obj_layout = new Settings(1);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_layout->$key = $value;
            }
        }

        $data['model'] = $obj_layout;
        if (!$obj_layout->save() || !$_POST)
        {
            $this->render('admin/setting/insert', $data);
        }
        else
        {
            $this->render('admin/setting/insert', $data);
            $data['update'] = true;
        }
    }
    
   
    
    
}