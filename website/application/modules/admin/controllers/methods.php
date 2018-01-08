<?php

/*
 * Menu Controller
 * Admin Menu Management (controller and function)
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class methods extends MX_Controller
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
     * @defination use for showing table header and setting table id for admin menu
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Name', 'Function', 'Allow From All','Controller', 'Action');
        
        
        $obj_controller = new Controller();
        $controller = $obj_controller->get();
        
        $select_controller[Null]="Select";
        foreach($controller as $value)
        {
            $select_controller[$value->name] = $value->name;  
        } 
        
        
        $data['controller'] = $select_controller;
        
        $this->render('admin/methods/index', $data);
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for Methods callback function
     * @author Fahim
     */
    function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("methods");
        $this->datatables->set_primary_key("id");
        $this->datatables->set_custom_string(3);
         $this->datatables->select('functions.id,functions.name,functions.function,functions.allow_from_all,controllers.name as controller_name')
        ->unset_column('functions.id')
        ->from('functions')->join("controllers","functions.controller_id=controllers.id","left");
 
        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert admin Methods
     * @author Fahim
     */
    function add()
    {
        $obj_methods = new Functions();
        
        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
                $obj_methods->$key= $value; 
            }  
           
        }
        
        $obj_controller = new Controller();
        $controller = $obj_controller->get();
        
        $select_controller[Null]="Select";
        foreach($controller as $value)
        {
            $select_controller[$value->id] = $value->name;  
        } 
        
        
        $data['controller'] = $select_controller;

        $data['model'] = $obj_methods;
        if (!$obj_methods->save())
        {
            $this->render('admin/methods/insert',$data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update admin Methods
     * @author Fahim
     */
    function edit($id)
    {
        
        $obj_methods = new Functions($id);
        $controller_relation = "";
        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
                $obj_methods->$key= $value; 
            }  
           
        }
        
        $obj_controller = new Controller();
        $controller = $obj_controller->get();
        
        $select_controller[Null]="Select";
        foreach($controller as $value)
        {
            $select_controller[$value->id] = $value->name;  
        } 
        
        
        $data['controller'] = $select_controller;

        $data['model'] = $obj_methods;
        if (!$obj_methods->save() || !$_POST)
        {
            $this->render('admin/methods/insert',$data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
        
       
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a admin Methods
     * @author Fahim
     */
    function delete()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        $obj_methods = new Functions($this->input->post('primary_id') );
        $obj_methods->delete(); 
        echo 1;
    }

    

}

?>
