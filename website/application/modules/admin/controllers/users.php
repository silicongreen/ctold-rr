<?php

/*
 * Users Controller
 * Admin User Management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class users extends MX_Controller {

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
        
        $this->table->set_heading('Name','Email','Full Name','Group','Action');
        
        $obj_group = new Group();
        $group = $obj_group->get();
        
        $select_group[Null]="Select";
        foreach($group as $value)
        {
            $select_group[$value->name] = $value->name;  
        } 
        
        
        $data['group'] = $select_group;
        
        $this->render('admin/users/index',$data);
        
    }
    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for User callback function
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
        $this->datatables->set_controller_name("users");
        $this->datatables->set_primary_key("id");
     
        $this->datatables->select('users.id,users.username,users.email,users.name,groups.name as group_name')
        ->unset_column('users.id')
        ->from('users')->join("groups_users","users.id=groups_users.user_id","left")->join("groups","groups_users.group_id=groups.id","left");
        
        echo $this->datatables->generate();
    }
    /**
     * add function
     * @param none
     * @defination use for insert admin user
     * @author Fahim
     */
    function add()
    {
       
        $obj_user = new User();
        $obj_group = new Group();
        $group = $obj_group->get();
        
        $group_relation = "";

        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
              
               $obj_user->$key= $value; 
            } 
            $group_relation = new Group($this->input->post('group_id'));
          
        }
        foreach($group as $value)
        {
            $select_group[$value->id] = $value->name;  
        }    
        
        $data['group'] = $select_group;
        
        $data['model'] = $obj_user;
        
        if (!$obj_user->save($group_relation))
        {
            $this->render('admin/users/insert',$data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
       
    }
    
    /**
     * edit function
     * @param none
     * @defination use for Update User if password set then also update password
     * @author Fahim
     */
    function edit($id)
    {
       
        $obj_user= new User($id);
        $obj_user->group->get();
        $obj_user->group_id = $obj_user->group->id;
        $obj_group = new Group();
        $group = $obj_group->get();
        
        $group_relation = "";

        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
               if($key != 'password' && $value != ""){
                    $obj_user->$key= $value;
               }else{
                    $obj_user->password = $this->input->post('password');
               }
            } 
            $group_relation = new Group($this->input->post('group_id'));
          
        }
        foreach($group as $value)
        {
            $select_group[$value->id] = $value->name;  
        }    
       
        $data['group'] = $select_group;
        
        $data['model'] = $obj_user;
         
        if (!$obj_user->save($group_relation) || !$_POST)
        {
            $this->render('admin/users/insert',$data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
       
    }
    
    
    /**
     * delete function
     * @param none
     * @defination use for delete a admin user
     * @author Fahim
     */
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
