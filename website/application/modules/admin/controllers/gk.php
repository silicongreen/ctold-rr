<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class gk extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI = & $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id for byline
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Question', 'Ans 1', 'Ans 2', 'Ans 3', 'Ans 4', 'Correct', 'Date', 'Action');
        $this->render('admin/gk/index');
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for byline callback function
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
        $this->datatables->set_controller_name("gk");
        $this->datatables->set_primary_key("id");

        $this->datatables->set_custom_string(6, array(1 => 'Answer 1', 2 => 'Answer 2', 3 => 'Answer 3', 4 => 'Answer 4'));

        $this->datatables->select('id,question,ans1,ans2,ans3,ans4,correct,post_date')
                ->unset_column('id')
                ->from('general_knowladge');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    private function insert_post($obj_gk,$post_id = 0)
    {
        if($post_id==0)
        {
            $post_obj = new Posts();
        }
        else
        {
            $post_obj = new Posts($post_id);
        }    

        $post_obj->headline = $obj_gk->question;


        $content_array = array();
        if ($obj_gk->correct == 1)
        {
            $content_array[] = $obj_gk->ans1 . "|1";
        }
        else
        {
            $content_array[] = $obj_gk->ans1 . "|0";
        }
        if ($obj_gk->correct == 2)
        {
            $content_array[] = $obj_gk->ans2 . "|1";
        }
        else
        {
            $content_array[] = $obj_gk->ans2 . "|0";
        }
        if ($obj_gk->correct == 3)
        {
            $content_array[] = $obj_gk->ans3 . "|1";
        }
        else
        {
            $content_array[] = $obj_gk->ans3 . "|0";
        }
        if ($obj_gk->correct == 4)
        {
            $content_array[] = $obj_gk->ans4 . "|1";
        }
        else
        {
            $content_array[] = $obj_gk->ans4 . "|0";
        }

        $post_obj->content = implode(",", $content_array);

        $post_obj->published_date = date("Y-m-d H:i:s", strtotime($obj_gk->post_date));
        $post_obj->show = 0;
        
        $post_obj->status = 5;
        $post_obj->type = "Print";
        $post_obj->post_type = 4;
        
        $post_obj->layout = $obj_gk->layout;
        $post_obj->layout_color = $obj_gk->layout_color;
      
        
        $post_obj->save();
  
        
        if($post_id == 0)
        {
            $this->insert_releted_data($post_obj->id,$obj_gk->post_date);
        }
        return $post_obj->id;
    }
    private function insert_releted_data($post_id,$post_date)
    {
        //category insert
        $this->db->select("id");
        $this->db->where("name","gk");
        $category_id = $this->db->get("categories")->row()->id;
        
        $array['post_id'] = $post_id;
        $array['category_id'] = $category_id;
        
        $this->db->insert("post_category",$array);
        
        //Insert Type
        $reletad_type = array();
        for($i = 0; $i<4; $i++)
        {
            $reletad_type[$i]['type_id'] = $i+1;
            $reletad_type[$i]['post_id'] = $post_id;
        }
        $this->db->insert_batch('post_type', $reletad_type);
        
        //Insert Class
        $reletad_class = array();
        for($i = -1; $i<=10; $i++)
        {
            $reletad_class[$i+1]['class_id'] = $i;
            $reletad_class[$i+1]['post_id'] = $post_id;
        }
        $this->db->insert_batch('post_class', $reletad_class);
        
        //insert into home page
        $type_array = array(1,2,3,4);
        foreach($type_array as $value)
        {
            $date =  $post_date;
            $this->db->where("date",$date);
            $this->db->where("post_id",$post_id);
            $this->db->where("post_type",$value);
            $data = $this->db->get("homepage_data")->result();
            if(count($data) == 0)
            {
                $this->db->select("MAX(priority) as mp");
                $this->db->where("date",$date);
                $this->db->where("post_type",$value);
                $mdata = $this->db->get("homepage_data")->row();      
                $insert['post_id']  = $post_id;
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
                $insert['priority'] = $date."-".$priority;
                
                $this->db->select("MAX(priority) as mp");
                $this->db->where("date",$date);
                $this->db->where("post_type",$value);
                $mdata = $this->db->get("homepage_data")->row();      
                $insert['post_id']  = $post_id;
                $insert['post_type']  = $value;
                $insert['priority'] = $mdata->mp+1;
                $insert['date'] = $date;
                $this->db->insert("homepage_data",$insert);
            } 
        }
        
    }        

    function add()
    {
        $obj_gk = new gks();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gk->$key = $value;
            }
        }



        $data['model'] = $obj_gk;
        if (!$obj_gk->save())
        {

           
            $this->render('admin/gk/insert', $data);
        }
        else
        {
            $post_id = $this->insert_post($obj_gk);
            
            $updateObjGk = new gks($obj_gk->id);
            $obj_gk->post_id = $post_id;
            $obj_gk->save();
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update Byline
     * @author Fahim
     */
    function edit($id)
    {
        $obj_gk = new gks($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gk->$key = $value;
            }
        }
        if(!$obj_gk->post_id)
        {
            $obj_gk->delete();
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }

        $data['model'] = $obj_gk;
        if (!$obj_gk->save() || !$_POST)
        {
            
            $this->render('admin/gk/insert', $data);
        }
        else
        {
            $this->insert_post($obj_gk,$obj_gk->post_id);
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a byline
     * @author Fahim
     */
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_gk = new gks($this->input->post('primary_id'));
        if($obj_gk->post_id)
        $this->delnews($obj_gk->post_id);
        $obj_gk->delete();
        echo 1;
    }

    private function delnews($news_id)
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $post_model = new Posts($news_id);


        $this->db->where("post_id", $news_id);
        $this->db->delete("post_user_activity");
        $this->db->where("post_id", $news_id);
        $this->db->delete("post_category");
        $this->db->where("post_id", $news_id);
        $this->db->delete("post_gallery");
        $this->db->where("post_id", $news_id);
        $this->db->delete("post_keyword");
        $this->db->where("post_id", $news_id);
        $this->db->delete("post_tags");
        $this->db->where("post_id", $news_id);
        $this->db->delete("related_news");
        $this->db->where("post_id", $news_id);
        $this->db->delete("post_class");
        $this->db->where("post_id", $news_id);
        $this->db->delete("post_type");
        $this->db->where("id", $news_id);
        $this->db->delete("post");

        echo 1;
    }

}

?>
