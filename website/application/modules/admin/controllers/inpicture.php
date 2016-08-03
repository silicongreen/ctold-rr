<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class inpicture extends MX_Controller
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

        $this->table->set_heading('ID','Name','Publish Date', 'Is Current', 'Is Active', 'Action');
        $this->render('admin/inpicture/index');
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
        $this->datatables->set_controller_name("inpicture");
        $this->datatables->set_custom_string(3, array(1 => 'Yes', 0 => 'No'));
         $this->datatables->set_custom_string(4, array(1 => 'Yes', 0 => 'No'));
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,name,publish_date,is_current,is_active')
               // ->unset_column('id')
                ->from('inpictures_theme');

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
        $obj_inpicture = new Inpictures(); // model
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_inpicture->$key = $value;
            }
        }

        $data['model'] = $obj_inpicture;
        
        if (!$obj_inpicture->save())
        {
            $this->render('admin/inpicture/insert', $data);
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

        
        $obj_inpicture = new Inpictures($id);
       
        
        if ($_POST)
        {
            $obj_inpicture->image = null;
            foreach ($this->input->post() as $key => $value)
            {
                $obj_inpicture->$key = $value;
            }
          
        }
        

        $data['model'] = $obj_inpicture;
        if (!$obj_inpicture->save() || !$_POST)
        {
            $this->render('admin/inpicture/insert', $data);
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
        $obj_inpicture = new Inpictures($this->input->post('primary_id'));
        $obj_inpicture->delete();
        echo 1;
    }
} 