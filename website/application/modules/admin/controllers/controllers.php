<?php

/*
 * Menu Controller
 * Admin Menu Management (controller and function)
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class controllers extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI = & $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id for admin Controller
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Name', 'Controller', 'Allow From All', 'Action');
        
        $this->render('admin/controller/index', $data);
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for Controller callback function
     * @author Fahim
     */
    function datatable()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("controllers");
        $this->datatables->set_primary_key("id");
        $this->datatables->set_custom_string(3);
        
        $this->datatables->select('id,name,controller,allow_from_all')
                ->unset_column('id')
                ->from('controllers');
     

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert admin controller
     * @author Fahim
     */
    function add()
    {
        $obj_controller = new Controller();
        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
                $obj_controller->$key= $value; 
            }  
        }

        $data['model'] = $obj_controller;
        if (!$obj_controller->save())
        {
            $this->render('admin/controller/insert',$data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update admin controller
     * @author Fahim
     */
    function edit($id)
    {
        $obj_controller = new Controller($id);
        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
                $obj_controller->$key= $value; 
            }  
        }

        $data['model'] = $obj_controller;
        if (!$obj_controller->save() || !$_POST)
        {
            $this->render('admin/controller/insert',$data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a admin controller
     * @author Fahim
     */
    function delete()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        $obj_controller = new Controller($this->input->post('primary_id') );
        $obj_controller->delete(); 
        echo 1;
    }

    

}

?>
