<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class featurepost extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->load->library('image_lib');
        $this->form_validation->CI = & $this;
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id and filtering for News
     * @author Fahim
     */
    public function index()
    {

          
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Title','Categories','Position', 'Action');

        $obj_category = new Category();
        $obj_category->order_by('name');
        $obj_category->where('status', 1);
        $obj_category->get();


        $select_categoryMenu[NULL] = "Select";
        foreach ($obj_category as $value)
        {
            $select_categoryMenu[$value->name] = $value->name;
        }

        
        

        $data['categoryMenu'] = $select_categoryMenu;
       
        $this->render('admin/featurepost/index', $data);
    }
    
            
    public function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("delete");
        
        
        $this->load->config("champs21");
        
        $position_array = array(1=>"1st",2=>"2nd",3=>"3rd");
        
        for($i=4;$i<=10;$i++)
        {
            $position_array[$i] = $i."th";
            
        }
        
        $this->datatables->set_controller_name("featurepost");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_custom_string(3, $position_array);
        $this->datatables->set_use_found_rows(true);
        $this->datatables->select('SQL_CALC_FOUND_ROWS tds_selected_post.id as primary_id,post.headline,pre_cat.name,tds_selected_post.position', false)
                ->unset_column('primary_id')
                ->from('selected_post')
                ->join("categories as pre_cat", "selected_post.category_id=pre_cat.id", 'LEFT')
                ->join("post as post", "selected_post.post_id=post.id", 'LEFT');


        echo $this->datatables->generate();
    }
    
    public function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $post_model = new Posts($this->input->post('primary_id'));
        
        $this->db->where("id",$this->input->post("primary_id"));
        $this->db->delete("selected_post");
         

        echo 1;
    }
    
    

    
}

?>
