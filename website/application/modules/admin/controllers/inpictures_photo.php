<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class inpictures_photo extends MX_Controller
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

        $this->table->set_heading('Image','Theme Title','Author','Caption','Date Taken', 'Action');
        
        $obj_theme = $this->db->get_where('inpictures_theme', array('is_active' => 1))->result();        
        $select_theme[NULL] = "Select";
        foreach ($obj_theme as $value)
        {
            $select_theme[$value->name] = $value->name;
        }        
        $data['theme_list'] = $select_theme;
        
        $obj_author = $this->db->get_where('inpictures_author', array('is_active' => 1))->result();        
        $select_author[NULL] = "Select";
        foreach ($obj_author as $value)
        {
            $select_author[$value->name] = $value->name;
        }        
        $data['author_list'] = $select_author;
        
        
        $this->render('admin/inpictures_photo/index',$data);
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
        $this->datatables->set_controller_name("inpictures_photo");        
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_image_field_position(1);

        $this->datatables->select('inpictures_photos.id as primary_id,inpictures_photos.image,pre_theme.name as theme,pre_author.name as author,photo_caption,date_taken')
                ->unset_column('primary_id')
                ->from('inpictures_photos')
                ->join("inpictures_author as pre_author", "pre_author.id=inpictures_photos.author_id", 'LEFT')
                ->join("inpictures_theme as pre_theme", "pre_theme.id=inpictures_photos.theme_id", 'LEFT');

        echo $this->datatables->generate();
    } 
    /**
     * add function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    function add()
    {
        $obj_inpictures_photo = new inpictures_photos();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_inpictures_photo->$key = $value;
            }
        }
        
        $obj_theme = $this->db->get_where('inpictures_theme', array('is_active' => 1))->result();        
        $select_theme[NULL] = "Select";
        foreach ($obj_theme as $value)
        {
            $select_theme[$value->id] = $value->name;
        }        
        $data['theme_list'] = $select_theme;
        
        $obj_author = $this->db->get_where('inpictures_author', array('is_active' => 1))->result();        
        $select_author[NULL] = "Select";
        foreach ($obj_author as $value)
        {
            $select_author[$value->id] = $value->name;
        }        
        $data['author_list'] = $select_author;
        

        $data['model'] = $obj_inpictures_photo;
        
        if (!$obj_inpictures_photo->save())
        {
            $this->render('admin/inpictures_photo/insert', $data);
        }
        else
        {
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
      
        $obj_inpictures_photo = new inpictures_photos($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_inpictures_photo->$key = $value;
            }
        }
        
        $obj_theme = $this->db->get_where('inpictures_theme', array('is_active' => 1))->result();        
        $select_theme[NULL] = "Select";
        foreach ($obj_theme as $value)
        {
            $select_theme[$value->id] = $value->name;
        }        
        $data['theme_list'] = $select_theme;
        
        $obj_author = $this->db->get_where('inpictures_author', array('is_active' => 1))->result();        
        $select_author[NULL] = "Select";
        foreach ($obj_author as $value)
        {
            $select_author[$value->id] = $value->name;
        }        
        $data['author_list'] = $select_author;
        

        $data['model'] = $obj_inpictures_photo;
        
        if (!$obj_inpictures_photo->save()  || !$_POST )
        {
            $this->render('admin/inpictures_photo/insert', $data);
        }
        else
        {
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
        $obj_inpictures_photo = new  inpictures_photos($this->input->post('primary_id'));
        $obj_inpictures_photo->delete();
        echo 1;
    }
} 