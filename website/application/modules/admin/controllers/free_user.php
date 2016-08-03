<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class free_user extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->form_validation->CI =& $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }
    
    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id for user
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('ID', 'Email', 'Full Name', 'Gender', 'Mobile NO.', 'Action');
        
        $data['datatableSortBy'] = 0;
        $data['datatableSortDirection'] = 'DESC';
        
        $data['gender'] = array(NULL => '', 1 => 'Male', 0 => 'Female');
        
        $this->render('admin/free_user/index', $data);
    }
    
    function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->datatables->set_buttons("view");
        
        $this->datatables->set_controller_name("free_user");
        $this->datatables->set_primary_key("id");
        $this->datatables->set_custom_string(3, array(NULL => '', 1 => 'Male', 0 => 'Female'));
        
        $this->datatables->select('free_users.id, free_users.email, CONCAT_WS(" ", tds_free_users.first_name, tds_free_users.middle_name, tds_free_users.last_name) AS full_name, free_users.gender, free_users.mobile_no', FALSE)
        ->from('free_users');
        
        echo $this->datatables->generate();
    }
    
    function view($id)
    {
        $obj_free_user = new Free_users();
        $obj_free_user = $obj_free_user->get_free_user_by_id($id);
        
        $this->load->config('user_register');
        
        $data['model'] = $obj_free_user['data'];
        $data['_attributes'] = $obj_free_user['_attributes'];
        $data['medium'] = $this->config->config['medium'];
        $data['user_type'] = $this->config->config['free_user_types'];
         
        $this->render('admin/free_user/view', $data);        
    }
    
    function delete()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        $obj_user = new User($this->input->post('primary_id') );
        $obj_user->delete();
        
        echo 1;
    }
   
}
?>
