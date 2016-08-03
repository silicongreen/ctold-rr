<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class bylines extends MX_Controller
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

        $this->table->set_heading('Title','Columnist', 'Action');
        $this->render('admin/byline/index');
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
        $this->datatables->set_controller_name("bylines");
        $this->datatables->set_custom_string(2, array(1 => 'Yes', 0 => 'No'));
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,title,is_columnist')
                ->unset_column('id')
                ->from('bylines');

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
        $obj_byline = new Byline();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_byline->$key = $value;
            }
        }

        $data['model'] = $obj_byline;
        if (!$obj_byline->save())
        {
            $this->render('admin/byline/insert', $data);
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

        
        $obj_byline = new Byline($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_byline->$key = $value;
            }
        }

        $data['model'] = $obj_byline;
        if (!$obj_byline->save() || !$_POST)
        {
            $this->render('admin/byline/insert', $data);
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
        $obj_byline = new Byline($this->input->post('primary_id'));
        $obj_byline->delete();
        echo 1;
    }
    
    
    public function sort_bylines()
    {
        $obj_byline = new Byline();
        $obj_byline->order_by('priority');
        $obj_byline->where("is_columnist", "1");
        $data['bylines'] = $obj_byline->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/byline/sort', $data);
    }
    
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $ar_priority_sets = array();
       
        
        $s_bylines_data = $this->input->post("category_ids");
        
        $obj_byline = new Byline( );
        $ar_bylines = explode(",", $s_bylines_data);
        $i = 1;
        foreach( $ar_bylines as $byline_id )
        {
            if ( stripos($byline_id, "_") === FALSE )
            {
                $obj_byline->where('id', $byline_id);
                $obj_byline->update("priority", $i);
                $i++;
            }
            
        }
    }

    

}

?>
