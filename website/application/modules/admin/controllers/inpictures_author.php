<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class inpictures_author extends MX_Controller
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

        $this->table->set_heading('Name','E-mail','Phone','Created Date','Profession','Address', 'Action');
        $this->render('admin/inpictures_author/index');
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
        $this->datatables->set_controller_name("inpictures_author");
        //$this->datatables->set_custom_string(2, array(1 => 'Yes', 0 => 'No'));
        $this->datatables->set_primary_key("id");
        $this->datatables->select('id,name,email,phone,created_date,profession,address')
                ->unset_column('id')
                ->from('inpictures_author');
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
        $obj_inpictures_author = new Inpictures_authors();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_inpictures_author->$key = $value;
            }
        }

        $data['model'] = $obj_inpictures_author;
        
        if (!$obj_inpictures_author->save())
        {
            $this->render('admin/inpictures_author/insert', $data);
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

        
        $obj_inpictures_author = new Inpictures_authors($id);
       
        
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_inpictures_author->$key = $value;
            }
          
        }
        

        $data['model'] = $obj_inpictures_author;
        if (!$obj_inpictures_author->save() || !$_POST)
        {
            $this->render('admin/inpictures_author/insert', $data);
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
        $obj_inpictures_author = new Inpictures_authors($this->input->post('primary_id'));
        $obj_inpictures_author->delete();
        echo 1;
    }
}
?>