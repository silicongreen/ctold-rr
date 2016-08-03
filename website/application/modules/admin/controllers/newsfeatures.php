<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class newsfeatures extends MX_Controller
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

        $this->table->set_heading('Published Date','Title','Categories', 'Status','Seen','Wow','User', 'Action');

        $obj_category = new Category();
        $obj_category->order_by('name');
        $obj_category->where('status', 1);
        $obj_category->get();


        $select_categoryMenu[NULL] = "Select";
        foreach ($obj_category as $value)
        {
            $select_categoryMenu[$value->name] = $value->name;
        }

        $obj_users = $this->db->get('users')->result();

        $select_users[NULL] = "Select";
        foreach ($obj_users as $value)
        {
            $select_users[$value->name] = $value->name;
        }

        $obj_tags = $this->db->get('tags')->result();

        $select_tags[NULL] = "Select";
        foreach ($obj_tags as $value)
        {
            if ($value->tags_name)
                $select_tags[$value->tags_name] = $value->tags_name;
        }
        
        $this->load->config("champs21");
        
        $language[NULL] = "Select";
        foreach($this->config->config["language_codes"] as $key=>$value)
        {
            $language[$key] = $value;
        }
        if($school_id>0)
        {    
            $schoolobj = new schools($school_id);
            if(isset($schoolobj->name))
            {
                $data['school_name'] = $schoolobj->name;
            }
        
        }
        $data['language'] = $language;
        
        $data['school_id'] = $school_id;

        $data['categoryMenu'] = $select_categoryMenu;
        $data['users'] = $select_users;
        $data['tags'] = $select_tags;
        $data['has_daterange'] = true;

        $this->render('admin/newsfeature/index', $data);
    }
    
            
    public function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("pin_post","model2");
        $this->datatables->set_buttons("editor_picks","model2");
        $this->datatables->set_buttons("feature_post","model2");
        
        
        $this->load->config("champs21");
        
       
        
        $this->datatables->set_controller_name("newsfeatures");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_use_found_rows(true);
        $this->datatables->set_date_string(1);
        $this->datatables->set_custom_string(4, array(1 => 'Draft', 2 => 'Created', 5 => 'Published'));
        $this->datatables->set_custom_string(7, array(1=>'Admin',2=>"Web User"));
        
        
        
        $this->datatables->select('SQL_CALC_FOUND_ROWS tds_post.id as primary_id,post.published_date,post.headline,GROUP_CONCAT(DISTINCT pre_cat.name)
            ,post.status,post.view_count,post.wow_count,post.user_type', false)
                ->unset_column('primary_id')
                ->from('post')->join("post_category as pre_post_category", "post.id=pre_post_category.post_id", 'LEFT')
                ->join("categories as pre_cat", "pre_post_category.category_id=pre_cat.id", 'LEFT')
                ->join("post as pre_post", "pre_post.id=post.referance_id", 'LEFT')
                ->join("post_tags as pre_post_tags", "post.id=pre_post_tags.post_id", 'LEFT')
                ->join("tags as pre_tag", "pre_post_tags.tag_id=pre_tag.id", 'LEFT')
                ->join("post_user_activity as pre_post_user_activity", "post.id=pre_post_user_activity.post_id", 'LEFT')
                ->join("users as pre_user", "pre_post_user_activity.user_id=pre_user.id", 'LEFT')
                ->where("tds_post.status!=", "6", false)
                ->where("tds_post.show",1);
                
                $this->datatables->where("tds_post.school_id",0); 
                $this->datatables->where("tds_post.referance_id",0); 
                
                $this->datatables->where("tds_post.teacher_id",0);
                $this->datatables->group_by("post.id");


        echo $this->datatables->generate();
    }
    
    private function sort_change_update($category_id=0)
    {
        $type_array = array(1,2,3,4);
        if($category_id)
        {
            $this->db->where("category_id",$category_id);
            $this->db->where("post_type",0);
            $this->db->delete("sorting_change");

            $data['post_type'] = 0;

            $data['updated_date'] = date("Y-m-d H:i:s");
            $data['category_id'] =$category_id;
            $this->db->insert("sorting_change",$data);
        }
        else
        {
            foreach($type_array as $post_type)
            {
                $this->db->where("category_id",0);
                $this->db->where("post_type",$post_type);
                $this->db->delete("sorting_change");

                $data['post_type'] = $post_type;

                $data['updated_date'] = date("Y-m-d H:i:s");
                $data['category_id'] =0;
                $this->db->insert("sorting_change",$data);
            }    
        }    
        
    }
    
    public function feature_post($id)
    {
        
        if ($_POST && $_POST['position'] && $_POST['post_id'] )
        {
            $this->db->where("post_id",$this->input->post("post_id"));
            $this->db->where("category_id",$this->input->post("category_id"));
            $this->db->delete("selected_post");
            
            $this->db->where("position",$this->input->post("position"));
            $this->db->where("category_id",$this->input->post("category_id"));
            $this->db->delete("selected_post");
            
            $insert['post_id']  = $this->input->post("post_id");
            $insert['category_id']  = $this->input->post("category_id");
            $insert['position']  = $this->input->post("position");
            $this->db->insert("selected_post",$insert);
            
            $this->sort_change_update($this->input->post("category_id"));
            
            
        }
         
        $obj_category = new Category();
        $obj_category->order_by('name');
        $obj_category->where('status', 1);
        $obj_category->get();

        foreach ($obj_category as $value)
        {
            $select_categoryMenu[$value->id] = $value->name;
        }
        
        $position = array(1=>"1st",2=>"2nd",3=>"3rd");
        
        for($i=4;$i<=10;$i++)
        {
            $position[$i] = $i."th";
            
        }
        
        $data['position'] = $position;
        $data['select_categoryMenu'] = $select_categoryMenu;
        
        $post_model= new posts($id);
        $data['model'] = $post_model;
        
        if(!$_POST || !$_POST['position'] || !$_POST['post_id'] )
        {
            $this->render('admin/newsfeature/selected_post', $data);
        }
        else
        {
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    public function pin_post($id)
    {
        
        if ($_POST && $_POST['position'] && $_POST['post_id'] )
        {
            $this->db->where("post_id",$this->input->post("post_id"));
            $this->db->where("category_id",$this->input->post("category_id"));
            $this->db->delete("pin_post");
            
            $this->db->where("position",$this->input->post("position"));
            $this->db->where("category_id",$this->input->post("category_id"));
            $this->db->delete("pin_post");
            
            $insert['post_id']  = $this->input->post("post_id");
            $insert['category_id']  = $this->input->post("category_id");
            $insert['position']  = $this->input->post("position");
            $this->db->insert("pin_post",$insert);
            $this->sort_change_update($this->input->post("category_id"));
            
            
        }
         
        $obj_category = new Category();
        $obj_category->order_by('name');
        $obj_category->where('status', 1);
        $obj_category->get();


        $select_categoryMenu[0] = "Select";
        foreach ($obj_category as $value)
        {
            $select_categoryMenu[$value->id] = $value->name;
        }
        
        $position = array(1=>"1st",2=>"2nd",3=>"3rd");
        
        for($i=4;$i<=10;$i++)
        {
            $position[$i] = $i."th";
            
        }
        
        
        
        $data['position'] = $position;
        $data['select_categoryMenu'] = $select_categoryMenu;
        
        $post_model= new posts($id);
        $data['model'] = $post_model;
        
        if(!$_POST || !$_POST['position'] || !$_POST['post_id'] )
        {
            $this->render('admin/newsfeature/pin_post', $data);
        }
        else
        {
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    public function editor_picks($id)
    {
        
        if ($_POST && $_POST['position'] && $_POST['post_id'] )
        {
            $this->db->where("post_id",$this->input->post("post_id"));
            $this->db->delete("editor_picks");
            
            $this->db->where("position",$this->input->post("position"));
            $this->db->delete("editor_picks");
            
            $insert['post_id']  = $this->input->post("post_id");
            $insert['position']  = $this->input->post("position");
            $this->db->insert("editor_picks",$insert);
            
            
        }
        
        $position = array(1=>"1st",2=>"2nd",3=>"3rd");
        
        for($i=4;$i<=50;$i++)
        {
            $position[$i] = $i."th";
            
        } 
        
        $data['position'] = $position;
        
        $post_model= new posts($id);
        $data['model'] = $post_model;
        
        if(!$_POST || !$_POST['position'] || !$_POST['post_id'] )
        {
            $this->render('admin/newsfeature/editor_picks', $data);
        }
        else
        {
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    
}

?>
