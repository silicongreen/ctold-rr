<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class channel extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->form_validation->CI = &$this;
    }
    
    public function datatable(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("channel");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(2, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('channels.id AS primary_id, channels.name, channels.is_active')
                ->unset_column('primary_id')
                ->from("channels");
        
        echo $this->datatables->generate();
        exit;
    }
    
    public function index(){
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Name', 'Status', 'Actions');
        
        # load tds config.
        $this->load->config('tds');
        
        $this->render('admin/channel/index',$data);
    }
    
    public function add(){
        $model = new Channels();
        
        $data['model'] = 'Channels';
        $data['edit'] = false;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['Channels'])){
            foreach($this->input->post('Channels') as $Channels_key => $Channels_value){
                $model->$Channels_key = $Channels_value;
            }
            
            if($model->save()){
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                exit;
            }else{
                $data['values'] = $model;
                $data['errors'] = 'Channel could not be saved.';
            }
        }
        $this->render('admin/channel/_form',$data);
    }
    
    public function edit($id){
        $model = new Channels($id);
        
        $data['model'] = 'Channels';
        $data['edit'] = true;
        $data['values'] = $model;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['Channels'])){
            foreach($this->input->post('Channels') as $Channels_key => $Channels_value){
                $model->$Channels_key = $Channels_value;
            }
            
            if($model->save()){
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                exit;
            }else{
                $data['values'] = $model;
                $data['errors'] = 'Channel could not be saved.';
            }
        }
        
        $this->render('admin/channel/_form',$data);
    }
    
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        $model = new Channels($id);
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