<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class asktheanchor extends MX_Controller
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

        $this->table->set_heading('Name','Question','Answer','Status','Date','Action');
        $this->render('admin/asktheanchor/index');
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
        $this->datatables->set_buttons("change_status","ajax");
        $this->datatables->set_buttons("answer");
        $this->datatables->set_controller_name("asktheanchor");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_custom_string(4, array(1 => 'Active', 0 => 'Inactive'));

        $this->datatables->select('ask_the_anchor.id as primary_id,ask_the_anchor.name,ask_the_anchor.question,ask_the_anchor.answer,ask_the_anchor.status,ask_the_anchor.date')
                ->unset_column('primary_id')
                ->from('ask_the_anchor');

        echo $this->datatables->generate();
    }

   
    function add()
    {
        $obj_asktheanchor = new asktheanchors();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_asktheanchor->$key = $value;
            }
        }
       
        

        $data['model'] = $obj_asktheanchor;
        if (!$obj_asktheanchor->save())
        {
            $this->render('admin/asktheanchor/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    function change_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_asktheanchor = new asktheanchors($this->input->post('primary_id'));
       
        if($obj_asktheanchor->status)
        {
            $status = 0;
        }    
        else
        {
            $status = 1;
        }    
        
        $data  = array('status' =>$status);
        $where = "id = ".$this->input->post('primary_id');
        $str   = $this->db->update_string('tds_ask_the_anchor', $data, $where);
        $this->db->query($str);
        echo 1;
    }
    
    function answer($id)
    {
        $obj_asktheanchor = new asktheanchors($id);
        if ($_POST)
        {
            $obj_asktheanchor->answer = $this->input->post('answer');
            
        }
        $data['model'] = $obj_asktheanchor;
        if (!$obj_asktheanchor->save()  || !$_POST)
        {
            $this->render('admin/asktheanchor/answer', $data);
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
        $obj_asktheanchor = new asktheanchors($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_asktheanchor->$key = $value;
            }
        }
       
        

        $data['model'] = $obj_asktheanchor;
        if (!$obj_asktheanchor->save()  || !$_POST)
        {
            $this->render('admin/asktheanchor/insert', $data);
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
        $obj_asktheanchor = new asktheanchors($this->input->post('primary_id'));
        $obj_asktheanchor->delete();
        echo 1;
    }
    
    

    

}

?>
