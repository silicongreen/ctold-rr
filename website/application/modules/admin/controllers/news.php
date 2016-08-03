<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class news extends MX_Controller
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
    public function index($school_id=0)
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Published Date', 'Title', 'Author', 'Categories', 'Status','Reference','Language','User Type', 'Action');

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

        $this->render('admin/news/index', $data);
    }
    
    public function attach_video()
    {
       $this->load->library('upload');
      
        if (!empty($_FILES['videoUpload']['name']))
        {
           
            $config_cover['upload_path'] = 'upload/attach_video/';
            $config_cover['allowed_types'] = 'mp4';
            $config_cover['max_size'] = '6000000';
            $config_cover['is_image'] = false;
            $config_cover['file_name'] = "video_" . time() . "_" . $_FILES['videoUpload']['name'];
            $config_cover['overwrite'] = TRUE;

            $this->upload->initialize($config_cover);

            if ($this->upload->do_upload('videoUpload'))
            {
                echo $this->upload->file_name;
            }
            else
            {
                echo 0;
            }    
            
        }
        else
        {
            echo 0;
        }
      
    }  

    /**
     * datatable function
     * @param none
     * @defination use for showing datatable of News
     * @author Fahim
     */
    public function attach_file()
    {
       $this->load->library('upload');
      
        if (!empty($_FILES['fileUpload']['name']))
        {
           
            $config_cover['upload_path'] = 'upload/attach_file/';
            $config_cover['allowed_types'] = 'pdf|doc|docx|docs';
            $config_cover['max_size'] = '8192';
            $config_cover['is_image'] = false;
            $config_cover['file_name'] = "file_" . time() . "_" . $_FILES['fileUpload']['name'];
            $config_cover['overwrite'] = TRUE;

            $this->upload->initialize($config_cover);

            if ($this->upload->do_upload('fileUpload'))
            {
                echo $this->upload->file_name;
            }
            else
            {
                echo 0;
            }    
            
        }
        else
        {
            echo 0;
        }
      
    }        
    public function datatable($school_id=0)
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit", false,true);
        $this->datatables->set_buttons("delete");
        
        $this->datatables->set_buttons("statictis");
        $this->load->config("champs21");
        
        if($school_id == 0)
        {
            $this->datatables->set_buttons("set_home_today","ajax",false,array("field"=>"reference","value"=>false));
        
            $this->datatables->set_buttons("set_home_date","model2",false,array("field"=>"reference","value"=>false));
        }
        
        $this->datatables->set_controller_name("news");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_use_found_rows(true);
        $this->datatables->set_date_string(1);
        $this->datatables->set_custom_string(5, array(1 => 'Draft', 2 => 'Created', 5 => 'Published'));
        $this->datatables->set_custom_string(7, $this->config->config["language_codes"]);
        $this->datatables->set_custom_string(8, array(1=>'Admin',2=>"Web User"));
        
        
        
        $this->datatables->select('SQL_CALC_FOUND_ROWS tds_post.id as primary_id,post.published_date,post.headline,
            pre_user.name as author,GROUP_CONCAT(DISTINCT pre_cat.name)
            ,post.status,pre_post.headline as reference,post.language,post.user_type', false)
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
                if($school_id>0)
                {
                   $this->datatables->where("tds_post.school_id",$school_id); 
                }   
                else
                {
                    $this->datatables->where("tds_post.school_id",0); 
                }
                $this->datatables->where("tds_post.teacher_id",0);
                $this->datatables->group_by("post.id");


        echo $this->datatables->generate();
    }

    /**
     * @param None
     * @defination use for showing table header and setting table id and filtering for trash news
     * @author Fahim
     */
    private function sort_change_update($value,$target="home")
    {
        if($target=="home")
        {
            $post_type = $value;
            $this->db->where("category_id",0);
            $this->db->where("post_type",$post_type);
            $this->db->delete("sorting_change");
            
            $data['post_type'] = $value;
            
            $data['updated_date'] = date("Y-m-d H:i:s");
            $data['category_id'] =0;
            $this->db->insert("sorting_change",$data);
               
        } 
        else
        {
            $category_id = $value;
            $this->db->where("category_id",$category_id);
            $this->db->where("post_type",0);
            $this->db->delete("sorting_change");
            
            $data['category_id'] = $value;
            $data['updated_date'] = date("Y-m-d H:i:s");
            $data['post_type'] =0;
            $this->db->insert("sorting_change",$data);
        }   
        
    }
    
    public function set_home_date($id)
    {
        
        if ($_POST)
        {
            $type_array = array(1,2,3,4);
            
                foreach($type_array as $value)
                {
                    if(isset($_POST['type_post']) && in_array($value, $_POST['type_post']))
                    {
                        $date = $this->input->post("date");
                        $this->db->where("date",$date);
                        $this->db->where("post_id",$id);
                        $this->db->where("post_type",$value);
                        $data = $this->db->get("homepage_data")->result();
                        if(count($data) == 0)
                        {
                            $this->db->select("MAX(priority) as mp");
                            $this->db->where("date",$date);
                            $this->db->where("post_type",$value);
                            $mdata = $this->db->get("homepage_data")->row();      
                            $insert['post_id']  = $id;
                            $insert['post_type']  = $value;
                            if(isset($mdata->mp))
                            {
                                $max_priority_array = explode("-",$mdata->mp);
                                $priority = (int)$max_priority_array[count($max_priority_array)-1]+1;
                            }
                            else
                            {
                                $priority = 1;
                            }
                            if($priority<10)
                            {
                                $priority = "0".$priority;
                            } 
                            $insert['priority'] = $date."-".$priority;
                            
                            $insert['date'] = $date;
                            $this->db->insert("homepage_data",$insert);
                            
                            $this->sort_change_update($value);
                        } 
                    }
                    else
                    {
                        $date = $this->input->post("date");
                        $this->db->where("date",$date);
                        $this->db->where("post_id",$id);
                        $this->db->where("post_type",$value);
                        $this->db->delete("homepage_data");
                        $this->sort_change_update($value);
                    }    
                }
           
        }
        $data['today'] = date("Y-m-d");
        
        $post_model= new posts();
        $data['post_type'] = $post_model->type_tree_homepage($id);
        
        if(!$_POST)
        {
            $this->render('admin/news/set_home_date', $data);
        }
        else
        {
            garbage_collector(); 
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    public function set_home_today()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        
        
        $type_array = array(1,2,3,4);
            
        foreach($type_array as $value)
        {
            $has_category = $this->db->get_where('post_type', array('type_id' => $value, 'post_id' => $this->input->post("primary_id")))->row();
            if(isset($has_category) && count($has_category)>0)
            {
                //$date =  date("Y-m-d", strtotime("tomorrow"));
                $date =  date("Y-m-d");
                $this->db->where("date",$date);
                $this->db->where("post_id",$this->input->post("primary_id"));
                $this->db->where("post_type",$value);
                $data = $this->db->get("homepage_data")->result();
                if(count($data) == 0)
                {
                    $this->db->select("MAX(priority) as mp");
                    $this->db->where("date",$date);
                    $this->db->where("post_type",$value);
                    $mdata = $this->db->get("homepage_data")->row();      
                    $insert['post_id']  = $this->input->post("primary_id");
                    $insert['post_type']  = $value;
                    
                    if(isset($mdata->mp))
                    {
                        $max_priority_array = explode("-",$mdata->mp);
                        $priority = (int)$max_priority_array[count($max_priority_array)-1]+1;
                    }
                    else
                    {
                        $priority = 1;
                    }
                    if($priority<10)
                    {
                        $priority = "0".$priority;
                    } 
                    $insert['priority'] = $date."-".$priority;
                    $insert['date'] = $date;
                    $this->db->insert("homepage_data",$insert);
                    $this->sort_change_update($value);
                } 
            }
            else
            {
                //$date =  date("Y-m-d", strtotime("tomorrow"));
                $date =  date("Y-m-d");
                $this->db->where("date",$date);
                $this->db->where("post_id",$this->input->post("primary_id"));
                $this->db->where("post_type",$value);
                $this->db->delete("homepage_data");
                $this->sort_change_update($value);
            }    
        }
        garbage_collector();   
        echo 1;
    }    
    public function trash()
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="trash_table">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Published Date', 'Title', 'Author', 'Categories', 'Action');

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
            $select_tags[$value->tags_name] = $value->tags_name;
        }


        $data['categoryMenu'] = $select_categoryMenu;
        $data['users'] = $select_users;
        $data['tags'] = $select_tags;
        $data['has_daterange'] = true;

        $this->render('admin/news/trash', $data);
    }

    /**
     * @param none
     * @defination use for showing datatable of trash news
     * @author Fahim
     */
    public function datatable_trash()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        $this->datatables->set_buttons("restore");
        $this->datatables->set_buttons("del");
        $this->datatables->set_controller_name("news");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_use_found_rows(true);
        
        $this->datatables->set_date_string(1);
       
        $this->datatables->select('SQL_CALC_FOUND_ROWS tds_post.id as primary_id,post.published_date,post.headline,
            pre_user.name as author,GROUP_CONCAT(DISTINCT pre_cat.name)
            ', false)
                ->unset_column('primary_id')
                ->from('post')->join("post_category as pre_post_category", "post.id=pre_post_category.post_id", 'LEFT')
                ->join("categories as pre_cat", "pre_post_category.category_id=pre_cat.id", 'LEFT')
                ->join("post_tags as pre_post_tags", "post.id=pre_post_tags.post_id", 'LEFT')
                ->join("tags as pre_tag", "pre_post_tags.tag_id=pre_tag.id", 'LEFT')
                ->join("post_user_activity as pre_post_user_activity", "post.id=pre_post_user_activity.post_id", 'LEFT')
                ->join("users as pre_user", "pre_post_user_activity.user_id=pre_user.id", 'LEFT')
                ->where("tds_post.status", "6", false)
                ->where("tds_post.show",1)
                ->group_by("post.id");

        


        echo $this->datatables->generate();
    }
    
    function del()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $post_model = new Posts($this->input->post('primary_id'));
        
        if($post_model->referance_id)
        {     
            $this->reference_language_reset($post_model->referance_id);
        }
        else
        {
            $this->remove_home_page_post($this->input->post('primary_id'));
        } 
        
        //post category
        $this->db->where("post_id",$this->input->post('primary_id'));
        $category_data = $this->db->get("post_category")->result();
        
        if(count($category_data)>0)
        {
            foreach($category_data as $value)
            {    
                $this->sort_change_update($value->category_id,"category");
            }
        }    
        
        
        
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_user_activity");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_category");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_gallery");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_keyword");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_tags");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("related_news");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_class");
        $this->db->where("post_id",$this->input->post('primary_id'));
        $this->db->delete("post_type");
        $this->db->where("id",$this->input->post('primary_id'));
        $this->db->delete("post");
        garbage_collector(); 
       
        echo 1;
    }

    /**
     * @param none
     * @defination use for restore news as a draft
     * @author Fahim
     */
    function restore()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $post_model = new Posts($this->input->post('primary_id'));
        
        if($post_model->referance_id)
        {     
            $this->reference_language_reset($post_model->referance_id);
        }
        
        $data = array('status' => 1);
        $where = "id = " . $this->input->post('primary_id');

        $str = $this->db->update_string('tds_post', $data, $where);
        $this->db->query($str);
        garbage_collector();
        echo 1;
    }
    
    private function getAllassessment($id = 0)
    {
        $sql = "select id,title from tds_assessment where id NOT IN (select DISTINCT assessment_id from tds_post where assessment_id IS NOT NULL "
                . " AND assessment_id!=0 AND id!=".$id.") ";
        $res = $this->db->query($sql)->result();
        $select[0] = "Select";
        if (count($res) > 0)
        {
            foreach ($res as $value)
            {
                $select[$value->id] = $value->title;
            }
        }
        return $select;
    }        
    /**
     * add function
     * @param none
     * @defination use for show insert News form
     * @author Fahim
     */
    function getsubcategory($parent_id)
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        if($parent_id)
        {
            $obj_post = new Posts();
            $category_array = $obj_post->category_array($parent_id);
       
        }
        else
        {
            $category_array[0] = "Select"; 
        }
        
        echo form_dropdown('subcategory_id_to_use', $category_array);  
        
    }
    public function add($school_id = 0)
    {
        $obj_post = new Posts();

        $obj_post->published_date = date("Y-m-d H:i:s");
    
        
        $data['model'] = $obj_post;
        
        

        $obj_user = new User();

        $obj_user->select("id,name");

        $obj_user->get();

        $select_user = array();

        foreach ($obj_user as $value)
        {
            $select_user[$value->id] = $value->name;
        }

        $user_data = $this->session->userdata("admin");

        $data['user_id'] = $user_data['id'];
        
        $data['school_id'] = $school_id;

        $data['users'] = $select_user;
        
        $this->load->config("champs21");
        
        $data['all_language'] = $this->config->config["language_codes"];
        
        $data['category_array'] = $obj_post->category_array();
        
        $select_category[0] = "Select";
        
        $data['subcategory_array'] = $select_category;
        
        $data['assessment_array'] = $this->getAllassessment();

        $data['category_tree'] = $obj_post->category_tree_news();
        $data['class_tree'] = $obj_post->class_tree_news();
        $data['type_tree'] = $obj_post->type_tree_news();
        
        //$data['category_tree'] = $obj_post->category_tree();

        $data['tag_string'] = "";
        $data['keyword_string'] = "";
        $data['related_news'] = array();
        $data['related_gallery'] = array();

        $this->render('admin/news/insert', $data);
    }
    
    private function getLanguage($id,$reference=false)
    {
        $obj_post = new Posts($id);
        if($obj_post->referance_id)
        {
            $referance_id = $obj_post->referance_id;
        }   
        else
        {
            $referance_id = $id;
        }
        
        $this->db->select("id,language");
        
        $this->db->where("referance_id",$referance_id);
        
        $this->db->where("status !=",6);
        if($reference == true)
        {
            $this->db->or_where("id",$id);
        }
        else
        {
            if($referance_id!=$id)
            {
                $this->db->where("id",$referance_id);
                $this->db->where("id !=",$id);
            }
           
            
        }    
        $post_obj = $this->db->get("post")->result();
        
        $lan = array();
        
        //language update
        foreach($post_obj as $value)
        {
            $lan[] = $value->language;
        }  
        
        $this->load->config("champs21");
        $all_language = $this->config->config["language_codes"];
        
        $language = array();
        foreach($all_language as $key=>$value)
        {
            if(!in_array($key, $lan))
            {
               $language[$key] = $value;
            }
        }   
        
        return $language;
        
        
    }        

    /**
     * edit function
     * @param none
     * @defination use for show edit news from
     * @author Fahim
     */
    public function edit($id,$edited=false)
    {
        $obj_post = new Posts($id);
        
        if($obj_post->referance_id)
        {
            $obj_referance_post = new Posts($obj_post->referance_id);
            $data['ref_headline'] = $obj_referance_post->headline;
        }    

        $obj_post->byline_id = $obj_post->get_byline_by_id($obj_post->byline_id);

        $data['model'] = $obj_post;

        $obj_user = new User();

        $obj_user->select("id,name");

        $obj_user->get();

        $select_user = array();

        foreach ($obj_user as $value)
        {
            $select_user[$value->id] = $value->name;
        }

        $user_data = $this->session->userdata("admin");

        $data['user_id'] = $user_data['id'];

        $data['users'] = $select_user;

        $data['tag_string'] = $obj_post->get_tag_string($id);
        $data['country_string'] = $obj_post->get_country_string($id);
        
        if($obj_post->category_id)
        {
            $data['subcategory_array'] = $obj_post->category_array($obj_post->category_id);
        }
        else
        {
            $select_category[0] = "Select";
            $data['subcategory_array'] = $select_category;
        }    
        

        $data['category_array'] = $obj_post->category_array();
        $data['assessment_array'] = $this->getAllassessment($id);
        $data['keyword_string'] = $obj_post->get_keyword_string($id);

        $data['related_news']   = $obj_post->get_related_news($id);
        $data['related_attach'] = $obj_post->get_related_attach($id);

        $data['related_gallery'] = $obj_post->get_gallery($id);
        $data['related_gallery_mobile'] = $obj_post->get_gallery($id,2);
        $data['all_language'] = $this->getLanguage($id);
        
        if($edited)
        {
           $data['edited'] = "Yes"; 
        }


        $data['category_tree'] = $obj_post->category_tree_news($id);
        $data['class_tree'] = $obj_post->class_tree_news($id);
        $data['type_tree'] = $obj_post->type_tree_news($id);
        //$data['category_tree'] = $obj_post->category_tree($id);

        $this->render('admin/news/insert', $data);
    }
    
    public function statictis($id)
    {
        
        
       
       $dt = "-7 day";
       $week_last = date("Y-m-d", strtotime($dt));
       $dt = "-1 month";
       $month_last = date("Y-m-d", strtotime($dt));
       
       
       $sql_home = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=1";
       
       $data['home'] = $this->db->query($sql_home)->row()->view;
       
       $sql_aboard = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0";
       
       $data['aboard'] = $this->db->query($sql_aboard)->row()->view;
       
       
       if($data['aboard']>0)
       {
           $sql_aboard_country = "select count(id) as view,country from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0 group by (country) order by view DESC";
           
           $data['aboard_country'] = $this->db->query($sql_aboard_country)->result();
       }    
       
       
       
       $sql_daily_home = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=1 AND date='".date("Y-m-d")."'";
       
       $data['daily_home'] = $this->db->query($sql_daily_home)->row()->view;
       
       $sql_daily_aboard = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0 AND date='".date("Y-m-d")."'";
       
       $data['daily_aboard'] = $this->db->query($sql_daily_aboard)->row()->view;
       
       
       if($data['daily_aboard']>0)
       {
           $sql_daily_aboard_country = "select count(id) as view,country from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0  AND date='".date("Y-m-d")."' group by (country) order by view DESC";
           
           $data['daily_aboard_country'] = $this->db->query($sql_daily_aboard_country)->result();
       }
       
       $sql_weekly_home = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=1 AND date>'".$week_last."'";
       
       $data['weekly_home'] = $this->db->query($sql_weekly_home)->row()->view;
       
       $sql_weekly_aboard = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0 AND date>'".$week_last."'";
       
       $data['weekly_aboard'] = $this->db->query($sql_weekly_aboard)->row()->view;
       
       if($data['weekly_aboard']>0)
       {
           $sql_weekly_aboard_country = "select count(id) as view,country from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0  AND  date>'".$week_last."' group by (country) order by view DESC";
           
           $data['weekly_aboard_country'] = $this->db->query($sql_weekly_aboard_country)->result();
       }
       
       $sql_monthly_home = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=1 AND date>'".$month_last."'";
       
       $data['monthly_home'] = $this->db->query($sql_monthly_home)->row()->view;
       
       $sql_monthly_aboard = "select count(id) as view from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0 AND date>'".$month_last."'";
       
       $data['monthly_aboard'] = $this->db->query($sql_monthly_aboard)->row()->view;
       
       if($data['monthly_aboard']>0)
       {
           $sql_monthly_aboard_country = "select count(id) as view,country from tds_post_statistic where news_id=".$id." 
           AND home_or_abroad=0  AND  date>'".$month_last."' group by (country) order by view DESC";
           
           $data['monthly_aboard_country'] = $this->db->query($sql_monthly_aboard_country)->result();
       }
       
       
       $this->render('admin/news/statictis', $data);
       
    } 

    /**
     * publishNews function
     * @param none
     * @defination use for  news publish ajax call
     * @author Fahim
     */
    function publishNews()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_post = $this->buildObject($this->input->post());

        $obj_post->validate();
        if (count($obj_post->error->all) == 0)
        {
            if (!isset($obj_post->id) || $obj_post->id == 0)
            {

                $obj_post->status = 5;
                $obj_post->priority = $this->priority($obj_post->published_date);
            }
            else
            {

                $post_row = $this->db->get_where('post', array('id' => $obj_post->id), 1)->row();



                if (isset($obj_post->status) && $obj_post->status == 5)
                {
                    $obj_post->status = 1;
                }
                else
                {
                    $obj_post->status = 5;
                }
            }


            $this->saveNewsWithAllRelatedData($obj_post, $this->input->post(), 'publish');
        }
        else
        {
            $validation_string = create_validation($obj_post);
            echo $validation_string;
        }
    }

    /**
     * newsSave function
     * @param none
     * @defination use for  news save ajax call
     * @author Fahim
     */
    function newsSave()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        $obj_post = $this->buildObject($this->input->post());

        $obj_post->validate();

        if (count($obj_post->error->all) == 0)
        {
            if (!isset($obj_post->id) || $obj_post->id == 0)
            {
                $obj_post->status = 1;
                $obj_post->priority = $this->priority($obj_post->published_date);
            }
        
            $this->saveNewsWithAllRelatedData($obj_post, $this->input->post());
        }
        else
        {
            $validation_string = create_validation($obj_post);
            echo $validation_string;
        }
    }

    
    function delete_home_page()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->db->where('post_id', $this->input->post('primary_id'));
        $this->db->where('post_type', $this->input->post('post_type'));
        $this->db->where('date', $this->input->post('date_running'));
        $this->db->delete('homepage_data'); 
        $this->sort_change_update($this->input->post('post_type'));
        garbage_collector(); 
        echo 1;
    }
    /**
     * delete function
     * @param None
     * @defination use for delete news into trash Ajax
     * @author Fahim
     */
    private function remove_home_page_post($id)
    {
            $this->load->config("champs21");
            $type_array = $this->config->config['user_type_value'];
           
            $releted_type_array = array();
            $this->db->where("post_id",$id);
            $releted_type = $this->db->get("post_type")->result();
            
            foreach($releted_type as $value)
            {
               
                $this->db->where('post_id', $id);
                $this->db->where('post_type', $value->type_id);
                $this->db->delete('homepage_data');
                $this->sort_change_update($value->type_id);
                
            }
       
    }        
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $post_model = new Posts($this->input->post('primary_id'));
        
        if($post_model->referance_id)
        {     
            $this->reference_language_reset($post_model->referance_id);
        }
        else
        {
            $this->remove_home_page_post($this->input->post('primary_id'));
           
        }
        $data = array('status' => 6);
        $where = "id = " . $this->input->post('primary_id');

        $str = $this->db->update_string('tds_post', $data, $where);
        $this->db->query($str);

        $user_data = $this->session->userdata("admin");

        $user_id = $user_data['id'];
        $user_agent = $this->input->post('user_agent');

        $this->saveUserActivity($user_id, $this->input->post('primary_id'), 5, $user_agent);
        garbage_collector(); 

        echo 1;
    }

    /**

     * @defination use for delete news into trash Ajax
     * @author Fahim
     */
    private function buildObject($post)
    {
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];
        $attach = false;
        $i = 0;
        foreach ($ar_data_keys as $keys)
        {
            if ($keys == "id")
            {
                if ($ar_data_vals[$i] > 0)
                {
                    $obj_post = new Posts($ar_data_vals[$i]);
                    $obj_post->lead_material = "";
                }
                else
                {
                    $obj_post = new Posts();
                    $obj_post->lead_material = "";
                }
            }
            else
            {
                if (!isset($obj_post))
                {
                    $obj_post = new Posts();
                    $obj_post->lead_material = "";
                }

                $obj_post->$keys = $ar_data_vals[$i];
            }
            
            if ($keys == "attach")
            {
                $attach = true;
            }

            $i++;
        }
        
        if(!$attach)
        {
           $obj_post->attach = null; 
        }    

        if (!isset($obj_post->priority_type) || $obj_post->priority_type == "")
        {
            $obj_post->priority_type = 5;
        }
        if (!isset($obj_post->news_expire_date) || $obj_post->news_expire_date == "")
        {
            $obj_post->news_expire_date = $obj_post->published_date;
        }
        $obj_post->content = $post['content'];
        $obj_post->mobile_content = $post['mobile_content'];

        return $obj_post;
    }

    private function priority($publish_datetime)
    {
        $a_publish_date = explode(" ", $publish_datetime);
        $publish_date = $a_publish_date[0];
        $this->db->like('published_date', $publish_date);
        $this->db->from('post');
        return $this->db->count_all_results() + 1;
    }

    private function google_short_url($id)
    {
        $obj_post = new Posts();
        $url = base_url() . $obj_post->single_page_controller . "/" . $id;
        $short_url = $this->google_url_api->shorten($url);
        return $short_url->id;
    }

    private function insert_related_keywords($keyword_string, $id)
    {
        $reletad_keyword = array();
        $a_keyword = explode(",", $keyword_string);
        $this->db->where("post_id", $id);
        $this->db->delete("post_keyword");
        $i_loop = 0;
        foreach ($a_keyword as $value)
        {
            if (trim($value) != "")
            {
                $this->db->select("id");
                $this->db->where("value", trim($value));
                $keyword_row = $this->db->get("keywords")->row();
                if (count($keyword_row) > 0)
                {
                    $keyword_id = $keyword_row->id;
                }
                else
                {
                    $a_keyword_insert['value'] = trim($value);
                    $this->db->insert('keywords', $a_keyword_insert);
                    $keyword_id = $this->db->insert_id();
                }

                $reletad_keyword[$i_loop]['keyword_id'] = $keyword_id;
                $reletad_keyword[$i_loop]['post_id'] = $id;
                $i_loop++;
            }
        }

        if ($reletad_keyword)
            $this->db->insert_batch('post_keyword', $reletad_keyword);
    }
    
     private function insert_related_country($country_string, $id)
    {
        $reletad_country = array();
        $a_country = explode(",", $country_string);
        $this->db->where("post_id", $id);
        $this->db->delete("post_country");
        $i_loop = 0;
        foreach ($a_country as $value)
        {
            if (trim($value) != "")
            {
                $sql = "select id from countries where name like '".trim($value)."%' limit 1";
                $country = $this->db->query($sql)->row();
                
                if (count($country) > 0)
                {
                    $country_id = $country->id;
                    $reletad_country[$i_loop]['country_id'] = $country_id;
                    $reletad_country[$i_loop]['post_id'] = $id;
                    $i_loop++;
                }
                
            }
        }

        if ($reletad_country)
        {
            $this->db->insert_batch('post_country', $reletad_country);
            return true;
        }
        return false;
            
    }
    
    

    private function insert_related_tags($tags_string, $id)
    {
        $reletad_tags = array();
        $a_tags = explode(",", $tags_string);
        $this->db->where("post_id", $id);
        $this->db->delete("post_tags");
        $i_loop = 0;
        foreach ($a_tags as $value)
        {
            if (trim($value) != "")
            {
                $this->db->select("id");
                $this->db->where("tags_name", trim($value));
                $tags_row = $this->db->get("tags")->row();
                if (count($tags_row) > 0)
                {
                    $tag_id = $tags_row->id;
                }
                else
                {
                    $a_tag_insert['tags_name'] = trim($value);
                    $this->db->insert('tags', $a_tag_insert);
                    $tag_id = $this->db->insert_id();
                }

                $reletad_tags[$i_loop]['tag_id'] = $tag_id;
                $reletad_tags[$i_loop]['post_id'] = $id;
                $i_loop++;
            }
        }

        if ($reletad_tags)
            $this->db->insert_batch('post_tags', $reletad_tags);
    }
    private function getcategory_subcategory_from_link($link)
    {
        $category_sub = array("category"=>0,"subcategory"=>0);
        if(strpos($link,"http://www.champs21.com/")!==FALSE)
        {
            $category_name = str_replace("http://www.champs21.com/", "", $link);
            if($category_name)
            {
                $category_name_clean = trim($category_name);
                if($category_name_clean)
                {
                    $category_all = explode("/", $category_name_clean);
                    $category_name_main = str_replace("-"," ",$category_all[0]);
                    if(count($category_all)>1)
                    {
                        if($category_all[1])
                        {
                            $category_name_main = str_replace("-"," ",$category_all[1]);
                        }
                    }    
                    $this->db->where("name",$category_name_main);
                    $category = $this->db->get("categories")->row();
                    if($category)
                    {
                        if($category->parent_id)
                        {
                            $category_sub['category'] = $category->parent_id;
                            $category_sub['subcategory'] = $category->id;
                        }
                        else
                        {
                            $category_sub['category'] = $category->id;
                        }    
                    }
                }
            }
            
        } 
        return $category_sub;
    }        
    private function insert_gallery_mobile($post, $id)
    {
        $reletad_gallery = array();
        $xml_field = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        $i_loop = 0;
        $i_loop2 = 0;
        $i_loop3 = 0;

        $this->db->where("post_id", $id);
        $this->db->where("type", 2);
        $this->db->delete("post_gallery");

        foreach ($ar_data_keys as $keys => $values)
        {
            if ($values == "related_img_mobile[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    
                    $this->db->where("material_url", str_replace(base_url(), "", $ar_data_vals[$keys]));
                    $meterials = $this->db->get("materials")->row();

                    if (count($meterials) > 0)
                    {
                        $reletad_gallery[$i_loop]['material_id'] = $meterials->id;
                        $reletad_gallery[$i_loop]['post_id'] = $id;
                        $reletad_gallery[$i_loop]['type'] = 2;
                    }
                    
                }
                $i_loop++;
            }
            if ($values == "caption_mobile[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    
                    $reletad_gallery[$i_loop2]['caption'] = $ar_data_vals[$keys];
                }
                $i_loop2++;
            }
            if ($values == "source_mobile[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_gallery[$i_loop3]['source'] = $ar_data_vals[$keys];
                    $reletad_gallery[$i_loop3]['category_id'] = 0;
                    $reletad_gallery[$i_loop3]['subcategory_id'] = 0;
                    if($ar_data_vals[$keys])
                    {
                        $cat_sub = $this->getcategory_subcategory_from_link($ar_data_vals[$keys]);
                        $reletad_gallery[$i_loop3]['category_id'] = $cat_sub['category'];
                        $reletad_gallery[$i_loop3]['subcategory_id'] = $cat_sub['subcategory'];
                        
                    }
                }
                $i_loop3++;
            }
        }
        
        if ($reletad_gallery)
        {
            $this->db->insert_batch('post_gallery', $reletad_gallery);

        }
         
    }

    private function insert_attach_file($post, $id)
    {
        $reletad_attach = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];
        $i_loop = 0;
        $i_loop2 = 0;
        $i_loop3 = 0;
        $this->db->where("post_id", $id);
        $this->db->delete("post_attachment");

        foreach ($ar_data_keys as $keys => $values)
        {
            if ($values == "attach[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                   
                    $reletad_attach[$i_loop]['file_name'] = $ar_data_vals[$keys];
                    $reletad_attach[$i_loop]['post_id'] = $id;
                    
                    //$reletad_gallery[$i_loop]['material_url'] = str_replace(base_url()."/","",$ar_data_vals[$keys]);
                }
                $i_loop++;
            }
            if ($values == "attach_checked[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_attach[$i_loop2]['show'] = $ar_data_vals[$keys];
                }
                $i_loop2++;
            }
            if ($values == "attach_caption[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_attach[$i_loop3]['caption'] = $ar_data_vals[$keys];
                }
                $i_loop3++;
            }
            
        }
       
        if ($reletad_attach)
        {
            $this->db->insert_batch('post_attachment', $reletad_attach);       
        }
    }
    private function insert_gallery($post, $id)
    {
        $reletad_gallery = array();
        $xml_field = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        $i_loop = 0;
        $i_loop2 = 0;
        $i_loop3 = 0;

        $this->db->where("post_id", $id);
        $this->db->where("type", 1);
        $this->db->delete("post_gallery");

        foreach ($ar_data_keys as $keys => $values)
        {
            if ($values == "related_img[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    
                    $this->db->where("material_url", str_replace(base_url(), "", $ar_data_vals[$keys]));
                    $meterials = $this->db->get("materials")->row();

                    if (count($meterials) > 0)
                    {
                        $xml_field[$i_loop]['file'] = str_replace(base_url(), "", $ar_data_vals[$keys]);
                        if (isset($meterials->video_id) && $meterials->video_id != 0)
                        {
                            $this->db->where("id", $meterials->video_id);
                            $meterials_video = $this->db->get("materials_video")->row();
                            $xml_field[$i_loop]['vfile'] = $meterials_video->url;
                            $xml_field[$i_loop]['thumbnail'] = str_replace(base_url(), "", $ar_data_vals[$keys]);
                        }
                        else
                        {
                            $xml_field[$i_loop]['thumbnail'] = str_replace(base_url(), "", $ar_data_vals[$keys]);
                        }
                        $reletad_gallery[$i_loop]['material_id'] = $meterials->id;
                        $reletad_gallery[$i_loop]['post_id'] = $id;
                    }
                    //$reletad_gallery[$i_loop]['material_url'] = str_replace(base_url()."/","",$ar_data_vals[$keys]);
                }
                $i_loop++;
            }
            if ($values == "caption[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $xml_field[$i_loop2]['title'] = $ar_data_vals[$keys];
                    $reletad_gallery[$i_loop2]['caption'] = $ar_data_vals[$keys];
                }
                $i_loop2++;
            }
            if ($values == "source[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_gallery[$i_loop3]['source'] = $ar_data_vals[$keys];
                }
                $i_loop3++;
            }
        }
        
        $news_obj = new Posts($id);
        
        $news_obj->has_image = 0;
        $news_obj->has_video = 0;
        $news_obj->has_pdf = 0;
        $create_xml = false;
        @unlink("gallery/xml/post/post_" . $id . ".xml");
        if ($reletad_gallery)
        {
            $this->db->insert_batch('post_gallery', $reletad_gallery);

            $xml = new DOMDocument();
            $xml_playlist = $xml->createElement("playlist");
            $iLoopGallery = 0;
            foreach ($xml_field as $value)
            {
                if (isset($value['file']))
                {
                    if ($news_obj->has_video == 0)
                    {
                        $query_check_video = "select count(tds_materials.id) as countvalue from tds_materials inner join  tds_gallery
                   on (tds_materials.gallery_id = tds_gallery.id) where tds_gallery.gallery_type = 2 and tds_materials.material_url = '" . $value['file'] . "'";

                        $count_value_video = $this->db->query($query_check_video)->row();
                        if ($count_value_video->countvalue > 0)
                        {
                            $news_obj->has_video = 1;
                        }
                    }
                    if ($news_obj->has_image == 0)
                    {
                        $query_check_image = "select count(tds_materials.id) as countvalue from tds_materials inner join  tds_gallery
                   on (tds_materials.gallery_id = tds_gallery.id) where tds_gallery.gallery_type in(1,5) and tds_materials.material_url ='" . $value['file'] . "'";

                        $count_value_image = $this->db->query($query_check_image)->row();
                        if ($count_value_image->countvalue > 0)
                        {
                            $news_obj->has_image = 1;
                        }
                    }
                    if ($news_obj->has_pdf == 0)
                    {
                        $query_check_pdf = "select count(tds_materials.id) as countvalue from tds_materials inner join  tds_gallery
                   on (tds_materials.gallery_id = tds_gallery.id) where tds_gallery.gallery_type = 4 and tds_materials.material_url ='" . $value['file'] . "'";

                        $count_value_pdf = $this->db->query($query_check_pdf)->row();

                        if ($count_value_pdf->countvalue > 0)
                        {
                            $news_obj->has_pdf = 1;
                        }
                    }

                    $query_check_image_video = "select count(tds_materials.id) as countvalue from tds_materials inner join  tds_gallery
               on (tds_materials.gallery_id = tds_gallery.id) where tds_gallery.gallery_type in(2,1,5) and tds_materials.material_url = '" . $value['file'] . "'";


                    $count_value_image_video = $this->db->query($query_check_image_video)->row();
                   

                    if ($count_value_image_video->countvalue > 0 && isset($value['file']) && $value['file'] != "")
                    {
                        $iLoopGallery++;

                        if (!$create_xml)
                            $create_xml = true;


                        $xml_slide = $xml->createElement("slide");
                        $xml_file = $xml->createElement("file");
                        $xml_thumbnail = $xml->createElement("thumbnail");
                        $xml_title = $xml->createElement("title");

                        $xml_playlist->appendChild($xml_slide);

                        $xml_slide->appendChild($xml_file);

                        if (!isset($value['vfile']))
                        {
                            $xml_file->nodeValue = (strpos($value['file'], "/") === 0 && strpos($value['file'], "http") === false) ? $value['file'] : "/" . $value['file'];
                        }
                        else
                        {
                            $xml_file->nodeValue = $value['vfile'];
                        }

                        $xml_slide->appendChild($xml_thumbnail);

                        $xml_thumbnail->nodeValue = (strpos($value['thumbnail'], "/") === 0 && strpos($value['thumbnail'], "http") === false) ? $value['thumbnail'] : "/" . $value['thumbnail'];

                        $xml_slide->appendChild($xml_title);

                        $xml_title->nodeValue = $value['title'];
                    }
                }
            }
            if ($create_xml && $iLoopGallery > 1)
            {
                $xml->appendChild($xml_playlist);
                $xml->formatOutput = true;
                $xml->save("gallery/xml/post/post_" . $id . ".xml");
            }
            
            $data = array(
               'has_image' => $news_obj->has_image,
               'has_video' => $news_obj->has_video,
               'has_pdf' => $news_obj->has_pdf
            );

            $this->db->where('id', $id);
            $this->db->update('post', $data); 
            
           // $news_obj->save();
          
        }
    }

    private function insert_related_news($post, $id)
    {
        $reletad_news = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        $i_loop = 0;
        $i_loop2 = 0;
        $i_loop3 = 0;

        $this->db->where("post_id", $id);
        $this->db->delete("related_news");

        foreach ($ar_data_keys as $keys => $values)
        {            
            if ($values == "related_title[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_news[$i_loop]['title'] = $ar_data_vals[$keys];
                    $reletad_news[$i_loop]['post_id'] = $id;                    

                    $i_loop++;
                }
            }
			
            if ($values == "related_link[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_news[$i_loop2]['new_link '] = $ar_data_vals[$keys];

                    if (strpos($ar_data_vals[$keys], base_url()) === false)
                    {
                        $reletad_news[$i_loop2]['related_type'] = 0;
                    }
                    else
                    {
                        $reletad_news[$i_loop2]['related_type'] = 1;
                    }

                    $i_loop2++;
                }
            }
			if($values == "related_published_date[]")
			{
				$reletad_news[$i_loop3]['published_date'] = $ar_data_vals[$keys];
				$i_loop3++;
			}
        }
		
        if ($reletad_news)
        {
            $this->db->insert_batch('related_news', $reletad_news);
            
            $data = array(
               'has_related_news' => 1
            );

            $this->db->where('id', $id);
            $this->db->update('post', $data); 
            
        }
        else
        {
            $data = array(
               'has_related_news' => 0
            );

            $this->db->where('id', $id);
            $this->db->update('post', $data); 
           
        }
    }
    
    private function insert_related_type($post, $id, $obj_post)
    {
        
        $reletad_category = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        $i_loop = 0;
        $this->db->where("post_id", $id);
        $this->db->delete("post_type");
        
        foreach ($ar_data_keys as $keys => $values)
        {
            
            if ($values == "type_post[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_category[$i_loop]['type_id'] = $ar_data_vals[$keys];
                    $reletad_category[$i_loop]['post_id'] = $id;
                    $i_loop++;
                }
                  
            }
        }
    
        
        if ($reletad_category)
        {
            
            $this->db->insert_batch('post_type', $reletad_category);
            return $reletad_category;
        }
       
        return false;
    }
    
    private function insert_related_class($post, $id, $obj_post)
    {
        $reletad_category = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        $i_loop = 0;
        $this->db->where("post_id", $id);
        $this->db->delete("post_class");
        $publish_date_type = 0;
        $total = 10;
        
        

        foreach ($ar_data_keys as $keys => $values)
        {
            
            if ($values == "class[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_category[$i_loop]['class_id'] = $ar_data_vals[$keys];
                    $reletad_category[$i_loop]['post_id'] = $id;
                    $i_loop++;
                }
                  
            }
        }
    
        
        if ($reletad_category)
        {
            
            $this->db->insert_batch('post_class', $reletad_category);
            return $reletad_category;
        }
       
        return false;
    }

    private function insert_related_category($post, $id, $obj_post)
    {
        $reletad_category = array();
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        $i_loop = 0;

        $this->db->select('category_id, post_id, inner_priority');
        $this->db->where("post_id", $id);
        $obj_post_cats = $this->db->get("post_category")->result();
        
        
        $ar_old_post_cate = array();
        foreach($obj_post_cats as $obj_post_cat)
        {
            $ar_old_post_cate[$obj_post_cat->category_id."_".$obj_post_cat->post_id] = $obj_post_cat->inner_priority;
        }
        
        
      

        
        $publish_date_type = 0;

        foreach ($ar_data_keys as $keys => $values)
        {
            if ($values == "category[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $reletad_category[$i_loop]['category_id'] = $ar_data_vals[$keys];
                    $reletad_category[$i_loop]['post_id'] = $id;
                    
                    
                    if(isset($ar_old_post_cate[$ar_data_vals[$keys]."_".$id]))
                    {
                        $reletad_category[$i_loop]['inner_priority'] = $ar_old_post_cate[$ar_data_vals[$keys]."_".$id];
                    }
                    else
                    {
                        $reletad_category[$i_loop]['inner_priority'] = $this->get_inner_priority($obj_post,$ar_data_vals[$keys]); 
                    }
                    
                    
                    $category_object = new Category($ar_data_vals[$keys]);
                   
                    
                    if($category_object->category_type_id>1 && $publish_date_type==0)
                    {
                        $publish_date_type = 2;
                    }  
                    
                    $this->sort_change_update($ar_data_vals[$keys],"category");
                    
                    $i_loop++;
                }
            }
        }
        
        if($obj_post->type=="Print" && $publish_date_type==0)
        {
            $publish_date_type = 1;
        }    
        
        
        if ($reletad_category)
        {
            $this->db->where("post_id", $id);
            $this->db->delete("post_category");
            $this->db->insert_batch('post_category', $reletad_category);
        }
        
        return $publish_date_type;
    }

    private function generate_byline_id($byline_string)
    {
        $this->db->select("id");
        $this->db->where("title", trim($byline_string));
        $bylines_row = $this->db->get("bylines")->row();
        if (count($bylines_row) > 0)
        {
            $new = "false";
            $byline_id = $bylines_row->id;
        }
        else
        {
            $a_byline_insert['title'] = trim($byline_string);
            $this->db->insert('bylines', $a_byline_insert);
            $new = "true";
            $byline_id = $this->db->insert_id();
        }
        return $byline_id."-".$new;
    }

    private function saveUserActivity($user_id, $post_id, $type, $user_agent)
    {
        $insert_activity['user_id'] = $user_id;
        $insert_activity['post_id'] = $post_id;
        $insert_activity['operation_type'] = $type;
        $insert_activity['operation_date'] = date("Y-m-d H:i:s");
        $insert_activity['ip_address'] = $this->input->ip_address();
        $insert_activity['user_agent'] = $user_agent;
        $insert_activity['session_id '] = $this->session->userdata('session_id');
        $this->db->insert('post_user_activity', $insert_activity);
    }
    
    /**
    * Reset inner pagaes priority starts
    * @var $post
    */
    
    private function get_inner_priority($obj_post,$category_id)
    {
        $sql = "select MAX(inner_priority) as max_inner_priority from tds_post_category WHERE
	                           tds_post_category.post_id IN ( SELECT id FROM tds_post WHERE
		                          DATE(tds_post.published_date) = '".date('Y-m-d', strtotime($obj_post->published_date))."'
	                           )
                               AND tds_post_category.category_id = '".$category_id."'"; 
                               
        $max_inner_priority = $this->db->query($sql)->row()->max_inner_priority; 
        
        return (isset($max_inner_priority)) ? $max_inner_priority+1 : 0;
        
                             
    }
    private function reset_inner_priority($post, $obj_post){
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];

        foreach ($ar_data_keys as $keys => $values)
        {
            if ($values == "category[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    $sql = "UPDATE tds_post_category SET inner_priority = inner_priority + 1 WHERE
	                           tds_post_category.post_id IN ( SELECT id FROM tds_post WHERE
		                          DATE(tds_post.published_date) = '".date('Y-m-d', strtotime($obj_post->published_date))."'
	                           )
                               AND tds_post_category.category_id = '".$ar_data_vals[$keys]."'";
                    $this->db->query($sql);
                    
                    $this->sort_change_update($ar_data_vals[$keys],"category");
                }
            }
        }
        
        return true;
    }

    private function saveNewsWithAllRelatedData($obj_post, $post, $save_type = "save")
    {
        if($obj_post->byline_id!="")
        {
            $byline_array = explode("-", $this->generate_byline_id($obj_post->byline_id));
            $obj_post->byline_id = $byline_array[0];
        }
        else
        {
             $obj_post->byline_id = 0; 
        }  
           
        $tags = $obj_post->tags;
        $countries = $obj_post->country_filter;

        $keywords = $obj_post->keywords;
        
        $reference_id = $obj_post->referance_id;
        
        
        
        if ($obj_post->breaking_expire == "" && $obj_post->is_breaking == 1)
        {
            $obj_post->breaking_expire = createDatePlus($obj_post->published_date, 1);
        }
        else if($obj_post->is_breaking != 1)
        {
            unset($obj_post->breaking_expire);
        }
        if ($obj_post->exclusive_expired == "" && $obj_post->is_exclusive == 1)
        {
            $obj_post->exclusive_expired = createDatePlus($obj_post->published_date, 1);
        }
        else if($obj_post->is_exclusive != 1)
        {
            unset($obj_post->exclusive_expired);
        }

        if ($save_type == "publish")
        {
            $type = 4;
        }
        else if (isset($obj_post->id) && $obj_post->id > 0)
        {
            $type = 2;
            
        }
        else
        {
            $type = 1;
           
        }
        
        $s_update = 'insert';
        if(isset($obj_post->id) && $obj_post->id > 0)
        {
            $s_update = 'update';
            $post_id = $obj_post->id;
        }   
        else
        {
            $post_id = 0;
            
            //$this->reset_inner_priority($post, $obj_post);
        }
        
        $b_check_priority = true;
        if ( $post_id > 0 )
        {
            
            $this->db->where("post_id", $post_id);
            $query = $this->db->get("post_category");
            $obj_post_cat = $query->result();
            
            foreach( $obj_post_cat as $post_cat )
            {
                garbage_collector_category($post_cat->category_id);
                $cache_name = "INNER_CATEGORY_" . $post_cat->category_id . "_" . date("Y-m-d", strtotime($obj_post->published_date));
                $this->cache->delete($cache_name);
            }
            
            $new_priority_type = $obj_post->priority_type;
            $obj_post_new = new Posts( $post_id );
            
            $pre_priority_type = $obj_post_new->priority_type;
            $s_update = 'update_' . $pre_priority_type . "_" . $new_priority_type;
            if ( $new_priority_type == $pre_priority_type )
            {
                $b_check_priority = false;
            }
        }
        $send_notification = $obj_post->send_notification;
       
        //$obj_post->priority_type = $this->get_priority_type($post_id,$obj_post->priority_type,$obj_post->published_date);
        
        $obj_post->save();
      
        
        
        if($send_notification )
        {
           
            $messegefornotification = $obj_post->headline;
            $data = array("key" => "news", "post_id" => $obj_post->id);
           
         
            $return = send_notification($messegefornotification,$data);
            
        }
        
                
        
          
        
//        if ( $obj_post->priority_type < 4 && $b_check_priority )
//        {
//            $this->load->config("tds");
//            $ar_news_type = array(
//                1   =>  $this->config->config[ 'carrosel_news_count' ],
//                2   =>  $this->config->config[ 'main_news_count' ],
//                3   =>  $this->config->config[ 'other_box_news_count' ]
//            );
//            
//            $this->db->where('priority_type', $obj_post->priority_type);
//            $this->db->where('status != ', 6, false);
//            $this->db->where("published_date BETWEEN '" . date("Y-m-d 00:00:00", strtotime($obj_post->published_date)) . "' AND '" . date("Y-m-d 23:59:59", strtotime($obj_post->published_date)) . "'");
//            $query = $this->db->get('post');
//        
//            if ( $query->num_rows() > $ar_news_type[$obj_post->priority_type] )
//            {
//                $this->reset_priority($obj_post->id, $obj_post->priority_type, $obj_post->published_date, $s_update);
//            }
//        }

        $this->load->helper('string');

        /* $str_news_headline = sanitize($obj_post->headline);

          $data  = array('ci_key' =>$str_news_headline);
          $where = "news_id = ".$obj_post->id;

          $str   = $this->db->update_string('tds_menu', $data, $where);
          $this->db->query($str); */



        $this->insert_related_tags($tags, $obj_post->id);
        $related_country = $this->insert_related_country($countries, $obj_post->id);
        
        $this->insert_related_keywords($keywords, $obj_post->id);
        $this->insert_related_news($post, $obj_post->id);
        $this->insert_gallery($post, $obj_post->id);
        $this->insert_attach_file($post, $obj_post->id);
        $this->insert_gallery_mobile($post, $obj_post->id);
        
        if($reference_id!=0)
        {
            $this->update_reference_language($reference_id,$obj_post->id);
        }
        else if($reference_id==0)
        {
            $publish_date_type = $this->insert_related_category($post, $obj_post->id,$obj_post);
            $related_class = $this->insert_related_class($post, $obj_post->id,$obj_post);
            $related_type = $this->insert_related_type($post, $obj_post->id,$obj_post);
        }
        
        
      
//        print '<pre>';
//        print_r($_POST);
        
        //Publish date regenerate
        if($related_country)
        {
            $data = array(
               'all_country' => 0
            );

            $this->db->where('id', $obj_post->id);
            $this->db->update('post', $data); 
        }
        //// have to update here
        if($obj_post->priority_type==1 && $reference_id==0 && $s_update == "insert" && $related_type)
        {
            $insert_home = array();
            foreach($related_type as $value)
            {
                $this->db->select("MAX(priority) as mp");
                $this->db->where("date",date("Y-m-d",strtotime($obj_post->published_date)));
                $this->db->where("post_type",$value['type_id']);
                $mdata = $this->db->get("homepage_data")->row();      
               
                $max_priority_array = explode("-",$mdata->mp);
                $priority = (int)$max_priority_array[count($max_priority_array)-1]+1;
                
                if($priority<10)
                {
                    $priority = "0".$priority;
                } 
                
                $insert_home['priority'] = date("Y-m-d",strtotime($obj_post->published_date))."-".$priority;              
                $insert_home['post_id'] = $obj_post->id;
                $insert_home['date']    = date("Y-m-d",strtotime($obj_post->published_date));
                $insert_home['post_type']= $value['type_id'];

                $this->db->insert("homepage_data",$insert_home);
                
                $this->sort_change_update($value['type_id']);
            }
        } 
        
        if($reference_id==0 && $s_update != "insert" && $related_type)
        {
            $this->load->config("champs21");
            $type_array = $this->config->config['user_type_value'];
            $releted_type_array = array();
            foreach($related_type as $value)
            {
                $releted_type_array[] = $value['type_id'];
            }
            foreach($type_array as $value)
            {
                if(!in_array($value,$releted_type_array))
                {
                    $this->db->where('post_id', $obj_post->id);
                    $this->db->where('post_type', $value);
                    $this->db->delete('homepage_data');
                    
                    $this->sort_change_update($value);
                }
            }
        }

        
        $user_agent = $post['user_agent'];
        $this->saveUserActivity($obj_post->user_id, $obj_post->id, $type, $user_agent);
        
        
        
        $ar_home_news_priority = array(1,2,3);
        
        $arIssueDateFront['s_issue_date'] = date("Y-m-d",strtotime($obj_post->published_date));
        $arIssueDateFront['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDateFront['s_issue_date']));
        $arIssueDateFront['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDateFront['s_issue_date']));

        $arIssueDateFront['current_date'] = date("Y-m-d");
        

        
        //REMOVE FIRST CATEGORY AND SECOND CATEGORY NEWS FROM HOME PAGE
        $cache_name = 'HOME_CATEGORY';
        if ( ! $obj_cat = $this->cache->get($cache_name)  )
        {
            $this->db->where('category_type_id', 1);
            $this->db->where('status', 1);        
            $this->db->where('parent_id IS NULL');
            $this->db->where('priority IS NOT NULL');
            $this->db->order_by("priority", "asc");
            $this->db->limit(11);
            $query = $this->db->get('categories');
            
            $obj_cat = $query->result();
            
            $this->cache->save($cache_name, $obj_cat, 86400 * 30 * 12);
        }
       
        $cat = array();
        $cat_limit = array(7,7,3,3,3,3,3,3,3,3,3);
        
        $ar_latest_news_cat = array(181);
        
        foreach( $obj_cat as $category )
        {
            array_push($cat, $category->id);
        }
        
        
        $ar_data_keys = $post['data_keys'];
        $ar_data_vals = $post['data_values'];
        
        $obj_post_front = new Post_model();
        $b_delete_weekly_cache = FALSE;
        $b_delete_home_page_cache = FALSE;
        
        if ( $obj_post->priority_type < 4 )
        {
            $b_delete_home_page_cache = TRUE;
        }
        
        $l=0;foreach ($ar_data_keys as $keys => $values)
        {
            if ($values == "category[]")
            {
                if (isset($ar_data_vals[$keys]))
                {
                    if ( in_array($ar_data_vals[$keys], $cat) )
                    {
                        $b_delete_home_page_cache = TRUE;
                    }
                    else if ( in_array($ar_data_vals[$keys], $ar_latest_news_cat) )
                    {
                        $cache_name = "TICKER_NEWS_" . date("Y_m_d", strtotime($obj_post->published_date));
                        $this->cache->delete($cache_name);
                        $obj_post_data = $obj_post_front->get_posts(NULL, $arIssueDateFront, NULL, 0, $ar_data_vals[$keys], 'between', "post.published_date,asc", 25);
                        $this->cache->save($cache_name, $obj_post_data, 60 * 60 * 24);
                        $l++;
                    }
                    
                    $this->db->where('id', $ar_data_vals[$keys]);
                    $query = $this->db->get('categories');

                    $cat_data = $query->row();
                    
                    if ( $cat_data->category_type_id == 2 )
                    {
                        $b_delete_weekly_cache = TRUE;
                    }
                    garbage_collector_category($ar_data_vals[$keys]);
                    $cache_name = "INNER_CATEGORY_" . $ar_data_vals[$keys] . "_" . date("Y-m-d", strtotime($obj_post->published_date));
                    $this->cache->delete($cache_name);
                    
                    if ( $obj_post->priority_type == 4 )
                    {
                        $obj_category = ((int)$ar_data_vals[$keys] > 0) ? $this->getCiKeyByCategoryId((int)$ar_data_vals[$keys]) : FALSE;
                        $ci_key = (($obj_category) && !empty($obj_category->name)) ? sanitize($obj_category->name) : NULL;
                        
                        $i_category_id = ((int)$ar_data_vals[$keys] > 0) ? (int)$ar_data_vals[$keys] : 0;
                        $i_category_type_id = (($obj_category) && !empty($obj_category->category_type_id)) ? $obj_category->category_type_id : 0;
                        
                        $cache_name = "POST_MORE_NEWS_" . strtoupper($ci_key) . "_" . date("Y_m_d", strtotime($arIssueDateFront['s_issue_date']));
                        
                        $this->cache->delete($cache_name);
                        
                        $obj_post_front = new Post_model();
                        $obj_post_front_data = $obj_post_front->get_posts(NULL, $arIssueDateFront, array(4), $i_category_type_id, $i_category_id, 'between', 'post.priority,asc');
                        
                        $this->cache->save($cache_name, $obj_post_front_data, 86400 * 30 * 12);
                    }
                }
            }
        }
        
        if ( isset($obj_post->byline_id) && isset($byline_array[1]) && $byline_array[1]=="false" )
        {
            $cache_name = "COLUMINST_" . $obj_post->byline_id;
            $this->cache->delete($cache_name);
            $obj_post_data_byline = $obj_post_front->get_posts(NULL, NULL, NULL, 0, 0, 'between', 'post.priority,asc', 50, TRUE, "", "", 0, 0, TRUE,$obj_post->byline_id);
            $this->cache->save($cache_name, $obj_post_data_byline, 86400 * 30 * 30);
        }
        $cache_title_name = "POST_TITLE_" . $obj_post->id;
        
        $this->cache->delete($cache_title_name);
        
        $cache_post_meta = "POST_META_" . $obj_post->id;
        
        $this->cache->delete($cache_post_meta);
        
        $cache_post_meta = "POST_CONTENT_" . $obj_post->id;
        
        $this->cache->delete($cache_post_meta);
            
        $cache_post_keywords = "POST_KW_" . $obj_post->id;
        
        $this->cache->delete($cache_post_keywords);
        
        $this->cache->delete("POST_" . $obj_post->id);
        
        $this->cache->delete("POST_CAN_COMMENT_" . $obj_post->id);
        $this->cache->delete("POST_HEADLINE_" . $obj_post->id);
        
        
        $html_header_cache = "common/HEADER_MENU";
        if($this->cache->file->get($html_header_cache))
        {
            $this->cache->file->delete($html_header_cache);
        }
        
        $cache_wc_name = "worldcup/POST_CONTENT_" . $obj_post->id;
        if($this->cache->file->get($cache_wc_name))
        {
            $this->cache->file->delete($cache_wc_name);
        }
        garbage_collector_block( 0,true );
        
        create_cache_single($obj_post->id);
        
        garbage_collector();
      
        
        echo $obj_post->id; //."|||".$obj_post->priority_type;
    }
    
    private function reference_language_reset($reference_id)
    {
        $this->db->select("id,language");
        $this->db->where("referance_id",$reference_id);
        $this->db->where("status !=",6);
        
        $this->db->or_where("id",$reference_id);
        $post_obj = $this->db->get("post")->result();
        
        $lan_array = array();
        
        //language update
        foreach($post_obj as $value)
        {
            $lan_array[] = $value->language;
        }  
        foreach($post_obj as $value)
        {
            $lan_all_array = $lan_array;
            $lan = $value->language;
            if(($key = array_search($lan, $lan_all_array))!==false)
            {
                unset($lan_all_array[$key]);
               
            }    
            $other_language = implode(",",$lan_all_array);
            $data = array(
               'other_language' => $other_language
            );

            $this->db->where('id', $value->id);
            $this->db->update('post', $data); 
        }
    }        
    
    private function update_reference_language($reference_id,$id)
    {
        $this->db->select("id,language");
        $this->db->where("referance_id",$reference_id);
        $this->db->where("status !=",6);
        
        $this->db->or_where("id",$reference_id);
        $post_obj = $this->db->get("post")->result();
        
        $lan_array = array();
        
        //language update
        foreach($post_obj as $value)
        {
            $lan_array[] = $value->language;
        }  
        foreach($post_obj as $value)
        {
            $lan_all_array = $lan_array;
            $lan = $value->language;
            if(($key = array_search($lan, $lan_all_array))!==false)
            {
                unset($lan_all_array[$key]);
               
            }    
            $other_language = implode(",",$lan_all_array);
            $data = array(
               'other_language' => $other_language
            );

            $this->db->where('id', $value->id);
            $this->db->update('post', $data); 
        }
        
        //category update
        //$main_post = new Post_model($reference_id);
        
        $this->db->select("category_id,inner_priority");
        $this->db->where("post_id",$reference_id);
        $categories = $this->db->get("post_category")->result();
        $category_array = array();
        
        $this->db->where("post_id",$id);
        $this->db->delete("post_category");
           
        
        $i_loop = 0;
        foreach($categories as $value)
        {
            $category_array[$i_loop]['category_id'] = $value->category_id;
            $category_array[$i_loop]['post_id'] = $id;
            $category_array[$i_loop]['inner_priority'] = $value->inner_priority;
            $this->sort_change_update($value->category_id,"category");
            $i_loop++;
        } 
        
        if($categories)
        {
            $this->db->insert_batch('post_category', $category_array);
        }
        
        //class update
        
        $this->db->select("class_id");
        $this->db->where("post_id",$reference_id);
        $class = $this->db->get("post_class")->result();
        
        $this->db->where("post_id", $id);
        $this->db->delete("post_class");
       
        $i_loop = 0;
        $class_array = array();
        foreach($class as $value)
        {
            $class_array[$i_loop]['class_id'] = $value->class_id;
            $class_array[$i_loop]['post_id'] = $id;
            $i_loop++;
        } 
        
    
        
        if ($class_array)
        {
           $this->db->insert_batch('post_class', $class_array);
        }
        //publish date update
        
        $main_post = new Post_model($reference_id);
        
        $data = array(
               'published_date' => $main_post->published_date
        );

        $this->db->where('id', $id);
        $this->db->update('post', $data); 
        
        
        
    }
    
    private function getCiKeyByCategoryId($i_category_id = 0){
        $i_category_id = ( !empty($i_category_id) && (int)$i_category_id > 0 ) ? $i_category_id : 0;
        
        $obj_menu = new Menu();
        $obj_category = $obj_menu->get_ci_key_by_category_id((int)$i_category_id);
        
        if(!($obj_category) || empty($obj_category->ci_key)){
            $obj_category = new Category();
            $obj_category = $obj_category->get_category_name_by_id((int)$i_category_id);
        }
        
        return (($obj_category) && !empty($obj_category->category_id)) ? $obj_category : FALSE;
        exit;
    }
    
    private function reset_priority($post_id,$priority_type,$publish_date, $s_update)
    {
        $s_published_date_from = date("Y-m-d 00:00:00", strtotime($publish_date));
        $s_published_date_to = date("Y-m-d 23:59:59", strtotime($publish_date));
        $obj_post = new Posts();
        $news_date = $obj_post->get_posts_published_date($s_published_date_from, $s_published_date_to);
        
        
        $s_post_id = "";
        $b_now_update = FALSE;
        $b_update_current = FALSE;
        $i_current_priority = 1;
        $rolling_priority = $priority_type;
        
        $this->load->config("tds");
        $ar_news_type = array(
            1   =>  $this->config->config[ 'carrosel_news_count' ],
            2   =>  $this->config->config[ 'main_news_count' ],
            3   =>  $this->config->config[ 'other_box_news_count' ],
            4   =>  $this->config->config[ 'more_news_count' ]
        );
        
        $ar_news_type_count = array(
            1   =>  0,
            2   =>  0,
            3   =>  0,
            4   =>  0,
            5   =>  0
        );
        
        
        foreach( $news_date as $news )
        {
            if ( $priority_type == $news->priority_type )
            {
                if ( ! $b_update_current )
                {
                    $b_update_current = TRUE;
                    $b_now_update = TRUE;
                    $i_priority  = $i_current_priority;
                    $s_post_id .= $post_id . "_" . $priority_type . "_" . $i_priority . "+";
                    $obj_post->update_priority($i_priority, $priority_type, $post_id);
                    $ar_news_type_count[$rolling_priority]++;
                    $i_current_priority++;
                    if ( $ar_news_type_count[$rolling_priority] >= $ar_news_type[$rolling_priority] )
                    {
                        $rolling_priority++;
                       // $priority_type++;
                    }
                    if ( $news->id != $post_id )
                    {
                        $i_priority  = $i_current_priority;
                        $s_post_id .= $news->id . "_" . $priority_type . "_" . $i_priority . "+";
                        $obj_post->update_priority($i_priority, $rolling_priority, $news->id);
                        $ar_news_type_count[$rolling_priority]++;
                    }
                }
                else if ( $post_id != $news->id )
                {
                    if ( $b_now_update )
                    {
                        if ( isset( $ar_news_type[$rolling_priority] ) )
                        {
                            if ( $ar_news_type[$rolling_priority] >= $ar_news_type_count[$rolling_priority] )
                            {
                                 $i_current_priority_type = $rolling_priority;
                                 $s_post_id .= $news->id . "_" . $i_current_priority_type . "_" . $i_current_priority . "+";
                                 $obj_post->update_priority($i_current_priority, $i_current_priority_type, $news->id);
                                 $ar_news_type_count[$rolling_priority]++;
                                 if ( $ar_news_type_count[$rolling_priority] == $ar_news_type[$rolling_priority] )
                                 {
                                     $rolling_priority++;
                                 }
                            }
                        }
                        else
                        {
                            $i_priority  = $i_current_priority;
                            $s_post_id .= $news->id . "_" . $rolling_priority . "_" . $i_priority . "+";
                            $obj_post->update_priority($i_priority, $rolling_priority, $news->id);
                        }
                    }
                }
                $b_update_current = TRUE;
            }
            else
            {
                if ( $b_now_update )
                {
                    if ( isset( $ar_news_type[$rolling_priority] ) )
                    {
                        if ( $ar_news_type[$rolling_priority] >= $ar_news_type_count[$rolling_priority] )
                        {
                             $i_current_priority_type = $rolling_priority;
                             $s_post_id .= $news->id . "_" . $i_current_priority_type . "_" . $i_current_priority . "+";
                             $obj_post->update_priority($i_current_priority, $i_current_priority_type, $news->id);
                             $ar_news_type_count[$rolling_priority]++;
                             if ( $ar_news_type_count[$rolling_priority] == $ar_news_type[$rolling_priority] )
                             {
                                $rolling_priority++;
                             }
                        }
                    }
                    else
                    {
                        $i_priority  = $i_current_priority;
                        $s_post_id .= $news->id . "_" . $rolling_priority . "_" . $i_priority . "+";
                        $obj_post->update_priority($i_priority, $rolling_priority, $news->id);
                    }
                }
                else
                {
                    
                }
            }
            $i_current_priority++;
            
        }
        
        $ar_priority_log = array(
            "priority"          => $s_post_id,
            "current_post_id"   => $post_id,
            "operation"         => $s_update
        );
        
        $this->db->insert('priority_log', $ar_priority_log);
    }
    
    private function get_priority_type($post_id,$priority_type,$publish_date)
    {
        if($priority_type==5 || $priority_type=="")
        {
            return 5;
        } 
        else
        {
            $obj_post = new Posts();
            $news_count =  $obj_post->get_count_news_in_priority($post_id,$publish_date);
            $this->load->config("tds");
            
           
            
            $carrosel_news_count = $this->config->config[ 'carrosel_news_count' ];
            $main_news_count = $this->config->config[ 'main_news_count' ];
            $other_box_news_count = $this->config->config[ 'other_box_news_count' ];
            $more_news_count = $this->config->config[ 'more_news_count' ];
            
            if($priority_type==1)
            {
                if( !isset($news_count[1]) || $news_count[1]<$carrosel_news_count )
                {
                    return 1;
                } 
                else if( !isset($news_count[2]) || $news_count[2]<$main_news_count )
                {
                    return 2;
                }
                else if( !isset($news_count[3]) || $news_count[3]<$other_box_news_count )
                {
                    return 3;
                }
                else if( !isset($news_count[4]) || $news_count[4]<$more_news_count )
                {
                    return 4;
                }
                else
                {
                    return 5;
                }    
            }
            if($priority_type==2)
            {
                if( !isset($news_count[2]) || $news_count[2]<$main_news_count )
                {
                    return 2;
                }
                else if( !isset($news_count[3]) || $news_count[3]<$other_box_news_count )
                {
                    return 3;
                }
                else if( !isset($news_count[4]) || $news_count[4]<$more_news_count )
                {
                    return 4;
                }
                else
                {
                    return 5;
                }    
            } 
            if($priority_type==3)
            {
                if( !isset($news_count[3]) || $news_count[3]<$other_box_news_count )
                {
                    return 3;
                }
                else if( !isset($news_count[4]) || $news_count[4]<$more_news_count )
                {
                    return 4;
                }
                else
                {
                    return 5;
                }    
            }
            if($priority_type==4)
            {
                if( !isset($news_count[4]) || $news_count[4]<$more_news_count )
                {
                    return 4;
                }
                else
                {
                    return 5;
                }    
            }
        }
        
    }

    function byline()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->select("title");
        $this->db->like("title", $this->input->get("term"));
        $bylines = $this->db->get("bylines");
        $a_bylines = array();
        foreach ($bylines->result() as $values)
        {
            $a_bylines[] = $values->title;
        }
        echo json_encode($a_bylines);
    }
    
    function reference_news()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->select("headline");
        $this->db->where("referance_id",0);
        $this->db->like("headline", $this->input->get("term"));
        $this->db->order_by("published_date", "DESC");
        $this->db->limit(20);
        $posts = $this->db->get("post");
        $a_posts = array();
        foreach ($posts->result() as $values)
        {
            $a_posts[] = $values->headline;
        }
        echo json_encode($a_posts);
    }
    function getajaxlanguage()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_post     = new Posts($this->input->post("id"));
        
        $language = $this->getLanguage($this->input->post("id"),true);
        
        echo form_dropdown('language', $language,NULL,"id='post_language'");
        
        
    }
    function reference_add()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->select("id,published_date");
        $this->db->where("headline", trim($this->input->post("term")));
        $this->db->order_by("published_date", "DESC");
        $this->db->limit(1);
        $posts = $this->db->get("post")->row();


        $str_related_news = 0;
        if (count($posts) > 0)
        {
            $str_related_news = $posts->id;
        }
        echo $str_related_news;
    }

    function releated_news()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $related_post_type = $this->input->get("related_post_type");
        $this->db->select("headline");
        $this->db->like("headline", $this->input->get("term"));
        if(isset($related_post_type) && $related_post_type==2)
        {
           $st = " assessment_id IS NOT NULL AND assessment_id!=0 ";
           $this->db->where($st, NULL, FALSE);  
        }    
        $this->db->where("status", 5);
        
        $this->db->order_by("published_date", "DESC");
        $this->db->limit(10);
        $posts = $this->db->get("post");
        $a_posts = array();
        foreach ($posts->result() as $values)
        {
            $a_posts[] = $values->headline;
        }
        echo json_encode($a_posts);
    }

    function related_add()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->select("id,headline,published_date");
        $this->db->where("headline", trim($this->input->post("term")));
        $this->db->where("status", 5);
        $this->db->order_by("published_date", "DESC");
        $this->db->limit(1);
        $posts = $this->db->get("post")->row();


        $str_related_news = 0;
        if (count($posts) > 0)
        {

            $link = create_link_url(NULL,$posts->headline, $posts->id);
            $str_related_news = '<div class="text-button">
                 <input type="hidden" name="related_title[]" value="' . $posts->headline . '">
                 <input type="hidden" name="related_link[]" value="' . $link . '">
                 <input type="hidden" name="related_published_date[]" value="' . $posts->published_date . '">
                 <span class="text-label">' . limit_string($posts->headline) . '</span>
                <a class="text-remove"></a>
            </div>';
        }
        echo $str_related_news;
    }

    function keywords()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->select("value");
        $this->db->like("value", $this->input->get("term"));
        $keywords = $this->db->get("keywords");
        $a_keyword = array();
        foreach ($keywords->result() as $values)
        {
            $a_keyword[] = $values->value;
        }
        echo json_encode($a_keyword);
    }

    function tags()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->select("tags_name");
        $this->db->like("tags_name", $this->input->get("term"));
        $tags = $this->db->get("tags");
        $a_tags = array();
        foreach ($tags->result() as $values)
        {
            $a_tags[] = $values->tags_name;
        }
        echo json_encode($a_tags);
    }
    
    function country()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        mb_internal_encoding("UTF-8"); 
        $this->db->query("SET NAMES utf8");
        
        $sql = "select name from countries where name like '%".$this->input->get("term")."%'";
        $countries = $this->db->query($sql);
        
        $a_countries = array();
        foreach ($countries->result() as $values)
        {
            $country_array = explode("(",$values->name);
            $a_countries[] = $country_array[0];
        }
        
        echo json_encode($a_countries);
    }

    private function categoryList($id = "")
    {
        $obj_category = new Category();
        $array = array('id !=' => $id);

        $obj_category->order_by('parent_id');
        $obj_category->where($array)->get();




        $select_parentCategory[NULL] = "Select";


        foreach ($obj_category as $value)
        {
            $select_parentCategory[$value->id] = $value->name;
        }

        return $select_parentCategory;
    }

    public function category_news_arrangement()
    {
        $obj_category = new Category();
        $obj_category->order_by('priority');
        //$obj_category->where("category_type_id", "1");
        $obj_category->where("parent_id IS NULL");
        $data['categories'] = $obj_category->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/news/sort_news', $data);
    }
    
    public function news_arrangement($date="")
    {
        if($date == "")
        {
            $date = date("Y-m-d",strtotime("+1 day"));
        }        
        $homepage_news_sql = "select tp.* from tds_homepage_data as thd left join tds_post as tp on thd.post_id=tp.id where 
                thd.date='".$date."' and thd.status=1 and thd.post_type=1 and tp.status!=6 order by thd.priority DESC";
        $data['home_page_post'] = $this->db->query($homepage_news_sql)->result();
      
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $data['date_running'] = $date;
        $data['type_array'] = array(1=>"Visitor",2=>"Student",3=>"Teacher",4=>"Parent");
        $this->render('admin/news/sort', $data);
    }
    
    public function get_news_search()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $homepage_news_sql = "select tp.* from tds_homepage_data as thd left join tds_post as tp on thd.post_id=tp.id where 
                thd.date='".$this->input->post("date")."' and thd.post_type='".$this->input->post("post_type")."' and thd.status=1 and tp.status!=6 order by thd.priority DESC";
        $data['home_page_post'] = $this->db->query($homepage_news_sql)->result();
        
        $this->load->view("admin/news/_partialsort",$data);

    }        
    
    /**
     * inner page news sorting start
    */
    public function inner_news_arrangement()
    {
        $obj_category = new Category();
        $obj_category->where("status", "1");
        $obj_category->where("show",1);
        $obj_category->order_by("name", "asc");
        $data['categories'] = $obj_category->get();
        
        $data['obj_post_model'] = new Post_model();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/news/inner_sort', $data);
    }
    
    public function save_innerpage_priority(){
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $post_ids = explode(',', $this->input->post('post_ids'));
        $post_publish = explode(',', $this->input->post('published_dates'));
        $cate_id =$this->input->post('category_id');
        
        $post_category = new Post_category();
        $rows_num = $post_category->update_priority($post_ids, $cate_id, $post_publish);
        
        $this->sort_change_update($cate_id,"category");
        
        garbage_collector_category($cate_id);
        
        $html_header_cache = "common/HEADER_MENU";
        if($this->cache->file->get($html_header_cache))
        {
            $this->cache->file->delete($html_header_cache);
        }
        
        garbage_collector();
        
        echo ($rows_num > 0) ? 1 : 0;
    }
    /**
     * inner page news sorting ends
    */

    public function more_news_arrangement(){
        $obj_settings = new Settings();
        $obj_issue_date = $obj_settings->get_value("key", "issue_date");
        $s_issue_date = $obj_issue_date->value;

        $ar_types = array(
            "4" => "More News",
            "5" => "All Other News"
        );

        //Get top two priority news
        $obj_category = new Category();
        $obj_category->order_by('name' ,'asc');
        $obj_category->where("(enable_sort = 1 AND priority IS NOT NULL AND status = 1)");
        //$obj_category->limit(2);

        $data['categories'] = $obj_category->get();

        $obj_posts = new Posts();
        $obj_posts->order_by('priority');
        $data['obj_posts'] = $obj_posts;
        $data['types'] = $ar_types;
        $data['obj_post_model'] = new Post_model();
        
        $this->load->config("tds");
        $ar_count_news = array(
            4 => $this->config->config['more_news_count'],
            5 => 0
        );
        $data['categories_news_count'] = $this->config->config['categories_news_count'];
        $data['news_count'] = $ar_count_news;
        $data['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($s_issue_date));
        $data['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($s_issue_date));

        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        
        $this->render('admin/news/more_news_sort', $data);
    }

    public function news_by_category()
    {
        $obj_settings = new Settings();
        $obj_issue_date = $obj_settings->get_value("key", "issue_date");
        $s_issue_date = $obj_issue_date->value;
        
        $this->disable_layout = TRUE;
        $obj_posts = new Posts();
        $obj_posts->order_by('priority');
        $issue_date_from = date("Y-m-d 00:00:00", strtotime($s_issue_date));
        $issue_date_to = date("Y-m-d 23:59:59", strtotime($s_issue_date));
        $data['posts'] = $obj_posts->get_posts_by_category($this->input->post('category_id'), $issue_date_from, $issue_date_to);
        
        $data['category_id'] = $this->input->post('category_id');
        $s_post_list = $this->render('admin/news/post_lists', $data, TRUE);
        
        
        
        echo $s_post_list;
    }
    
    public function get_caption()
    {
        $obj_post = new Posts();
        $obj_caption_source = $obj_post->get_caption_source($this->input->post("url"));
        $this->disable_layout = TRUE;
        $caption = "";
        $source = "";
        
        if(isset($obj_caption_source->caption))
            $caption = $obj_caption_source->caption;
        if(isset($obj_caption_source->source))
            $source = $obj_caption_source->source;
        
        echo $caption."||".$source;
        
    }
    
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        $s_post_data = $this->input->post("post_ids");

        $ar_posts = explode(",", $s_post_data);
        
        
        $this->db->where("date",$this->input->post("date"));
        $this->db->where("post_type",$this->input->post("post_type"));
        $this->db->delete("homepage_data");
        $j = 0;
        $insert = array();
        $total_post = count($ar_posts);
        foreach ($ar_posts as $post)
        {
            $priority = $total_post-$j;
            if($priority<10)
            {
                $priority = "0".$priority;
            }    
                
            $insert[$j]['priority'] = $this->input->post("date")."-".$priority;
            $insert[$j]['date']     = $this->input->post("date");
            $insert[$j]['post_id']  = $post;
            $insert[$j]['post_type'] = $this->input->post("post_type");
            $j++;
           
        }
        if($insert)
        $this->db->insert_batch("homepage_data",$insert);
        
        $this->sort_change_update($this->input->post("post_type"));
        
        
        
        garbage_collector_block(0,true);
        
        garbage_collector();
        garbage_collector_category();
        echo "1";
    }
    
    public function weekly_arrangement(){
        $obj_category = new Category();
        $obj_category->order_by('weekly_priority');
        $obj_category->where(array("category_type_id" => 2, "status" => 1));
        $obj_category->where("parent_id IS NULL");
        
        $data['categories'] = $obj_category->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/news/sort_weekly', $data);
    }
    
    public function save_weekly_priorities(){
        if(!$this->input->is_ajax_request()){
            exit('No Direct Access Allowed');
        }
        
        $obj_category = new Category();
        $category_ids = explode(',', $this->input->post('category_ids'));
        
        $i = 1;
        foreach($category_ids as $category_id){
          $obj_category->where('id', (int)$category_id);
          $ar_fields = array( "weekly_priority" => $i);
          if($obj_category->update($ar_fields)){
            $i++;
          }
        }
        
        garbage_collector();
        
        echo (count($category_ids) == ($i-1)) ? 1 : 0;
        exit;
    }
}

?>
