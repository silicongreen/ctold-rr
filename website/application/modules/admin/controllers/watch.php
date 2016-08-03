<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class watch extends MX_Controller
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
        $this->datatables->set_buttons("change_status","ajax");
        $this->datatables->set_controller_name("watch");
        $this->datatables->set_primary_key("primary_id");
        
        $ar_program_type = array(
            '1' => 'TV Program',
            '3' => "Thank God It's Friday",
            '4' => 'Showbiz Event',
            '2' => 'Other Program',
        );
        $this->datatables->set_custom_string(2, $ar_program_type);
        $this->datatables->set_custom_string(6, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('whats_on.id AS primary_id, pre_channels.name AS channel_name, whats_on.program_type, whats_on.program_details, pre_categories.name AS category_name, whats_on.show_date, whats_on.is_active')
                ->unset_column('primary_id')
                ->from("whats_on")
                ->join("channels as pre_channels", "pre_channels.id = whats_on.channel_id", 'INNER')
                ->join("categories as pre_categories", "pre_categories.id = whats_on.category_id", 'INNER')
                //->where('whats_on.is_active',1)
                ;
        
        echo $this->datatables->generate();
        exit;
    }
    
    public function index(){
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Channel', 'Type', 'Details', 'Category', 'Show Date', 'Status','Actions');
        
        # load tds config.
        $this->load->config('tds');
        
        $obj_category = new Category();
        $data['categories'] = $obj_category->categories_filter_dropdown();
        
        $this->render('admin/watch/index',$data);
    }
    
    public function add(){
        $model = new WhatsOn();
        
        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->category_tree();
        
        $data['model'] = 'WhatsOn';
        $data['edit'] = false;
        $data['fields'] = $model->get_fields();
        
        if(isset($_POST['WhatsOn'])){
            foreach($this->input->post('WhatsOn') as $WhatsOn_key => $WhatsOn_value){
                $model->$WhatsOn_key = $WhatsOn_value;
            }
            if($model->myValidation($_POST['WhatsOn'])){
                if($model->save()){
                    garbage_collector_category($model->category_id);
                    echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                }else{
                    $data['values'] = $model;
                    $data['errors'] = $model->error;
                    $data['channel_search'] = $this->input->post('channel_search');
                }
            }else{
                $data['values'] = $model;
                $data['errors'] = $model->my_errors;
                $data['channel_search'] = $this->input->post('channel_search');
            }
        }
        $this->render('admin/watch/_form',$data);
    }
    
    public function edit($id){
        $model = new WhatsOn($id);
        
        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->category_tree();
        
        $mod_channels = new Channels();
        $channel = $mod_channels->get_channel_by_id($model->channel_id);
        
        $data['model'] = 'WhatsOn';
        $data['edit'] = true;
        $data['fields'] = $model->get_fields();
        $data['values'] = $model;
        $data['channel_search'] = $channel->name;
        
        if(isset($_POST['WhatsOn'])){
            foreach($this->input->post('WhatsOn') as $WhatsOn_key => $WhatsOn_value){
                $model->$WhatsOn_key = $WhatsOn_value;
            }
            
            if($model->myValidation($_POST['WhatsOn'])){
                if($model->save()){
                    garbage_collector_category($model->category_id);
                    echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                }else{
                    $data['values'] = $model;
                    $data['errors'] = $model->error;
                    $data['channel_search'] = $this->input->post('channel_search');
                }
            }else{
                $data['values'] = $model;
                $data['errors'] = $model->my_errors;
                $data['channel_search'] = $this->input->post('channel_search');
            }
        }
        $this->render('admin/watch/_form',$data);
    }
    
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        $model = new WhatsOn($id);
        
        $model->delete();
//        $model->is_active = '0';
//        if($model->save()){
          echo 1;
//        }else{
//            echo 0;
//        }
//        exit;
    }
    
    function channel_search()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $term = $this->input->get('term');
        $mod_channels = new Channels();
        $channels = $mod_channels->get_channel_by_name($term, true);
        
        $a_data = array();
        foreach($channels as $values)
        {
            $a_channels['id'] = $values->id;
            $a_channels['name'] = $values->name;
            $a_data[] = $a_channels;
        }
        
        echo json_encode(array('channels' => $a_data));
    }
    
    function change_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_watch = new WhatsOn($this->input->post('primary_id'));
       
        if($obj_watch->is_active)
        {
            $is_active = 0;
        }    
        else
        {
            $is_active = 1;
        }    
        
        $data  = array('is_active' =>$is_active);
        $where = "id = ".$this->input->post('primary_id');

        $str   = $this->db->update_string('tds_whats_on', $data, $where);
        $this->db->query($str);  
        echo 1;
    }
    
    
}
?>