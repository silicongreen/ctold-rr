<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class gallery_photo extends MX_Controller
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

        $this->table->set_heading('Image','Gallery Name','Caption','Source', 'Action');
        
        $obj_gallery = $this->db->get('gallery_name')->result();        
        $select_gallery[NULL] = "Select";
        foreach ($obj_gallery as $value)
        {
            $select_gallery[$value->name] = $value->name;
        }        
        $data['gallery_list'] = $select_gallery;
        $this->render('admin/gallery_photo/index',$data);
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
        $this->datatables->set_controller_name("gallery_photo");        
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_image_field_position(1);

        $this->datatables->select('gallery_image.id as primary_id,gallery_image.image,pre_gallery.name as gallery,caption,source')
                ->unset_column('primary_id')
                ->from('gallery_image')
                ->join("gallery_name as pre_gallery", "pre_gallery.id=gallery_image.gallery_id", 'LEFT');

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
        $obj_gallery_photo = new gallery_photos();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gallery_photo->$key = $value;
            }
        }
        
        $obj_gallery = $this->db->get('gallery_name')->result();         
        $select_gallery[NULL] = "Select";
        foreach ($obj_gallery as $value)
        {
            $select_gallery[$value->id] = $value->name;
        }        
        $data['gallery_list'] = $select_gallery;
        
        

        $data['model'] = $obj_gallery_photo;
        
        if (!$obj_gallery_photo->save())
        {
            $this->render('admin/gallery_photo/insert', $data);
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
      
        $obj_gallery_photo = new gallery_photos($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gallery_photo->$key = $value;
            }
        }
        
        $obj_gallery = $this->db->get('gallery_name')->result();         
        $select_gallery[NULL] = "Select";
        foreach ($obj_gallery as $value)
        {
            $select_gallery[$value->id] = $value->name;
        }        
        $data['gallery_list'] = $select_gallery;
        
        

        $data['model'] = $obj_gallery_photo;
        
        if (!$obj_gallery_photo->save()  || !$_POST )
        {
            $this->render('admin/gallery_photo/insert', $data);
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
        $obj_gallery_photo = new  gallery_photos($this->input->post('primary_id'));
        $obj_gallery_photo->delete();
        echo 1;
    }
} 