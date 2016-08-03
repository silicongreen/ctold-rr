<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class voice_box extends MX_Controller
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
        $this->table->set_heading('Voice', 'Personality Name', 'Published Date', 'Status', 'Actions');
        
        # load tds config.
        $this->load->config('tds');
        
        $obj_personality = new Personality();
        $data['personality'] = $obj_personality->personalities_filter_dropdown();
        
        $this->render('admin/voice/index', $data);
    }
    
    public function datatable(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("voice_box");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(4, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('voice_box.id AS primary_id, voice_box.voice, pre_personality.name, voice_box.published_date, voice_box.is_active')
                ->unset_column('primary_id')
                ->from("voice_box")
                ->where("voice_box.is_active", 1)
                ->join("personality as pre_personality", "pre_personality.id = voice_box.personality_id", 'INNER')
                ;
        
        echo $this->datatables->generate();
        exit;
    }
    
    public function add(){
        $model = new Voice();
        
        $data['model'] = 'Voice';
        $data['edit'] = false;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['Voice'])){
            foreach($this->input->post('Voice') as $key => $value){
                $model->$key = $value;
            }
            
            if(empty($model->topic_id)){
                $topic = new Topic();
                $topic->topic = $this->input->post('topic_search');
                $topic->save();
                
                $model->topic_id = $topic->id;
            }
            
            $model->create_date = date('Y-m-d H:i:s');
            
            if($model->myValidation($_POST['Voice'])){
                if($model->save()){
                    echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                    garbage_collector_category(109);
                }else{
                    $data['values'] = $model;
                    $data['errors'] = $model->error;
                    $data['personality_search'] = $this->input->post('personality_search');
                }
            }else{
                $data['values'] = $model;
                $data['errors'] = $model->my_errors;
                $data['personality_search'] = $this->input->post('personality_search');
            }
        }
        $this->render('admin/voice/_form',$data);
    }
    
    public function edit($id){
        $model = new Voice($id);
        
        $data['model'] = 'Voice';
        $data['values'] = $model;
        $data['edit'] = true;
        $data['fields'] = $model->get_fields();
        
        $mod_personality = new Personality();
        $personality = $mod_personality->get_personality_by_id($model->personality_id);

        $mod_topic = new Topic();
        $topic = $mod_topic->get_topic_by_id($model->topic_id);
        
        $data['personality_search'] = $personality->name;
        $data['topic_search'] = $topic->topic;
        
        if(isset($_POST['Voice'])){
            foreach($this->input->post('Voice') as $key => $value){
                $model->$key = $value;
            }
            
            if(empty($model->topic_id)){
                $topic = new Topic();
                $topic->topic = $this->input->post('topic_search');
                $topic->save();
                
                $model->topic_id = $topic->id;
            }
            
            if($model->myValidation($_POST['Voice'])){
                if($model->save()){
                    garbage_collector_category(109);
                    echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                }else{
                    $data['values'] = $model;
                    $data['errors'] = $model->error;
                    $data['personality_search'] = $this->input->post('personality_search');
                    $data['topic_search'] = $this->input->post('topic_search');
                }
            }else{
                $data['values'] = $model;
                $data['errors'] = $model->my_errors;
                $data['personality_search'] = $this->input->post('personality_search');
                $data['topic_search'] = $this->input->post('topic_search');
            }
        }
        $this->render('admin/voice/_form',$data);
    }
    
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        $model = new Voice($id);
        $model->is_active = '0';
        if($model->save()){
            echo 1;
        }else{
            echo 0;
        }
        exit;
    }
            
    public function topic_search(){
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $term = $this->input->get('term');
        $mod_topic = new Topic();
        $topic = $mod_topic->get_topic_by_name($term, true);
        
        $a_data = array();
        foreach($topic as $values)
        {
            $a_topic['id'] = $values->id;
            $a_topic['topic'] = $values->topic;
            $a_data[] = $a_topic;
        }
        
        echo json_encode(array('topic' => $a_data));
    }
}
?>