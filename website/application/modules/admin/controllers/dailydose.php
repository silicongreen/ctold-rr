<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class dailydose extends MX_Controller
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

        $this->table->set_heading('Description','Date','Action');
        $this->render('admin/dailydose/index');
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
        $this->datatables->set_controller_name("dailydose");
        $this->datatables->set_primary_key("primary_id");

        $this->datatables->select('dailydose.id as primary_id,dailydose.content,dailydose.date')
                ->unset_column('primary_id')
                ->from('dailydose');

        echo $this->datatables->generate();
    }

   
    function add()
    {
        $obj_dailydose = new dailydoses();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_dailydose->$key = $value;
            }
        }
       
        

        $data['model'] = $obj_dailydose;
        if (!$obj_dailydose->save())
        {
            $this->render('admin/dailydose/insert', $data);
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

        $obj_dailydose = new dailydoses($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_dailydose->$key = $value;
            }
        }
       
        

        $data['model'] = $obj_dailydose;
        if (!$obj_dailydose->save()  || !$_POST)
        {
            $this->render('admin/dailydose/insert', $data);
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
        $obj_dailydose = new dailydoses($this->input->post('primary_id'));
        $obj_dailydose->delete();
        echo 1;
    }
    
    

    

}

?>
