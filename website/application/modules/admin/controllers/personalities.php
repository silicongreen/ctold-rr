<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class personalities extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->form_validation->CI = &$this;
    }
    
    public function index(){
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Name', 'Description', 'Status', 'Actions');
        
        # load tds config.
        $this->load->config('tds');
        
        $this->render('admin/personalities/index',$data);
    }
    
    public function datatable(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("personalities");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(3, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('personality.id AS primary_id, personality.name, personality.description, personality.is_active')
                ->unset_column('primary_id')
                ->from("personality");
        
        echo $this->datatables->generate();
        exit;
    }
    
    public function add(){
        $model = new Personality();
        
        $data['model'] = 'Personality';
        $data['edit'] = false;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['Personality'])){
            foreach($this->input->post('Personality') as $Personality_key => $Personality_value){
                $model->$Personality_key = $Personality_value;
            }
            
            if($model->save()){
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                exit;
            }else{
                $data['values'] = $model;
                $data['errors'] = 'Personality could not be saved.';
            }
        }
        
        $this->render('admin/personalities/_form',$data);
    }
    
    public function edit($id){
        $model = new Personality($id);
        
        $data['model'] = 'Personality';
        $data['edit'] = true;
        $data['values'] = $model;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['Personality'])){
            foreach($this->input->post('Personality') as $Personality_key => $Personality_value){
                $model->$Personality_key = $Personality_value;
            }
            
            if($model->save()){
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                exit;
            }else{
                $data['values'] = $model;
                $data['errors'] = 'Personality could not be saved.';
            }
        }
        
        $this->render('admin/personalities/_form',$data);
    }
    
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        $model = new Personality($id);
        $model->is_active = '0';
        if($model->save()){
            echo 1;
        }else{
            echo 0;
        }
        exit;
    }
}
?>