<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
class twitter extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->form_validation->CI =& $this;
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->view = $this->router->fetch_class();
    }
    
    public function index(){
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Menu', 'Twitter Name', 'Widget ID', 'Action');
        $model = new Menu();
        $data['menus'] = $model->get_menus_for_dropdown();
        
        $this->render('admin/'.$this->view.'/index',$data);
    }
    
    public function add(){
        $model = new Menu();
        
        $data['model'] = 'menu';
        $data['edit'] = false;
        $data['fields'] = $model->get_fields();
        $data['menus'] = $model->get_menus_for_dropdown();
        
        if(isset($_POST['menu'])){
            $inputs = $this->input->post('menu');
            foreach($this->input->post('menu') as $key => $value){
                $model->$key = $value;
            }
            
            if($model->myValidation($inputs)){
                $model->where('id',$model->id)->update(array('twitter_name' => $model->twitter_name, 'widget_id' => $model->widget_id));
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
            }else{
                $data['values'] = $model;
                $data['errors'] = $model->my_errors;
                $data['personality_search'] = $this->input->post('personality_search');
            }
        }
        
        $this->render('admin/'.$this->view.'/_form',$data);
    }
    
    public function edit($id){
        $model = new Menu($id);
        
        $data['model'] = 'menu';
        $data['edit'] = true;
        $data['fields'] = $model->get_fields();
        $data['menus'] = $model->get_menus_for_dropdown();
        $data['values'] = $model;
        
        if(isset($_POST['menu'])){
            $inputs = $this->input->post('menu');
            foreach($this->input->post('menu') as $key => $value){
                $model->$key = $value;
            }
            
            if($model->myValidation($inputs)){
                $model->where('id',$model->id)->update(array('twitter_name' => $model->twitter_name, 'widget_id' => $model->widget_id));
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
            }else{
                $data['values'] = $model;
                $data['errors'] = $model->my_errors;
                $data['personality_search'] = $this->input->post('personality_search');
            }
        }
        
        $this->render('admin/'.$this->view.'/_form',$data);
    }
    
    public function datatable(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        # load tds config.
        $this->load->config('tds');
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_controller_name("twitter");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->select('menu.id AS primary_id, menu.title, menu.twitter_name, menu.widget_id')
                ->unset_column('primary_id')
                ->from("menu")
                ->where("twitter_name IS NOT NULL")
                ->where("twitter_name != ",'')
        ;
        
        echo $this->datatables->generate();
    }
}