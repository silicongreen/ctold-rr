<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class quote extends MX_Controller
{
    private $view;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->form_validation->CI = &$this;
        $this->view = $this->router->fetch_class();
    }
    
    public function datatable(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name($this->view);
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(4, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('quotes.id AS primary_id, quotes.quote, pre_personality.name, DATE(`tds_quotes`.`published_date`) AS published_date, quotes.is_active')
                ->unset_column('primary_id')
                ->from("quotes")
                ->join("personality as pre_personality", "pre_personality.id = quotes.personality_id", 'INNER')
                //->where('quotes.is_active',1)
                ;
        
        echo $this->datatables->generate();
        exit;
    }
    
    public function index(){
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Quote', 'Personality', 'Published Date', 'Status', 'Actions');
        
        # load tds config.
        $this->load->config('tds');
        
        $obj_personality = new Personality();
        $data['personality'] = $obj_personality->personalities_filter_dropdown();
        
        $this->render('admin/'.$this->view.'/index',$data);
    }
    
    public function add(){
        $model = new Quotes();
        
        $data['model'] = 'Quotes';
        $data['edit'] = false;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['Quotes'])){
            foreach($this->input->post('Quotes') as $key => $value){
                $model->$key = $value;
            }
            
            $model->create_date = date('Y-m-d H:i:s');
            
            if($model->myValidation($_POST['Quotes'])){
                if($model->save()){
                    echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
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
        $this->render('admin/'.$this->view.'/_form',$data);
    }
    
    public function edit($id){
        $model = new Quotes($id);
        
        $data['model'] = 'Quotes';
        $data['values'] = $model;
        $data['edit'] = true;
        $data['fields'] = $model->get_fields();
        
        $mod_personality = new Personality();
        $personality = $mod_personality->get_personality_by_id($model->personality_id);
        
        $data['personality_search'] = $personality->name;
        
        if(isset($_POST['Quotes'])){
            foreach($this->input->post('Quotes') as $key => $value){
                $model->$key = $value;
            }
            
            if($model->myValidation($_POST['Quotes'])){
                if($model->save()){
                    echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
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
        $this->render('admin/'.$this->view.'/_form',$data);
    }
    
    public function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        $model = new Quotes($id);
        $model->is_active = '0';
        if($model->save()){
            echo 1;
        }else{
            echo 0;
        }
        exit;
    }
    
    public function personality_search(){
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $term = $this->input->get('term');
        $mod_channels = new Personality();
        $personality = $mod_channels->get_personality_by_name($term, true);
        
        $a_data = array();
        foreach($personality as $values)
        {
            $a_personality['id'] = $values->id;
            $a_personality['name'] = $values->name;
            $a_data[] = $a_personality;
        }
        
        echo json_encode(array('personalities' => $a_data));
    }

}
?>