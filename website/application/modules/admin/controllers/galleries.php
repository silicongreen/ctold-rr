<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class galleries extends MX_Controller
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

        $this->table->set_heading('Title','Gallery For', 'Action');
        $this->render('admin/galleries/index');
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
        $this->datatables->set_controller_name("galleries");
        $this->datatables->set_custom_string(2, array(1 => 'Spelling Bee', 2 => 'Other'));
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,name,gallery_for')
                ->unset_column('id')
                ->from('gallery_name');

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
        $obj_gallery_name = new Gallery_names();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gallery_name->$key = $value;
            }
        }

        $data['model'] = $obj_gallery_name;
        if (!$obj_gallery_name->save())
        {
            $this->render('admin/galleries/insert', $data);
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

        
        $obj_gallery_name = new Gallery_names($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gallery_name->$key = $value;
            }
        }

        $data['model'] = $obj_gallery_name;
        if (!$obj_gallery_name->save() || !$_POST)
        {
            $this->render('admin/galleries/insert', $data);
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
        $obj_gallery_name = new Gallery_names($this->input->post('primary_id'));
        
        $this->db->where("gallery_id",$this->input->post('primary_id'));
        $this->db->delete("gallery_image");
        $obj_gallery_name->delete();
        echo 1;
    }
    
    
    public function sort_galleries()
    {
        $obj_gallery_name = new Gallery_names();
        $obj_gallery_name->order_by('priority');
        $obj_gallery_name->where("gallery_for", "1");
        $data['gallery_name'] = $obj_gallery_name->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/galleries/sort', $data);
    }
    
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $ar_priority_sets = array();
       
        
        $s_gallery_data = $this->input->post("category_ids");
        
        $obj_gallery_name = new Gallery_names();
        $ar_gallery_name = explode(",", $s_gallery_data);
        $i = 1;
        foreach( $ar_gallery_name as $gallery_name )
        {
            if ( stripos($gallery_name, "_") === FALSE )
            {
                $obj_gallery_name->where('id', $gallery_name);
                $obj_gallery_name->update("priority", $i);
                $i++;
            }
            
        }
    }

    

}

?>
