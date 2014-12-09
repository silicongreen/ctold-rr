<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class school extends MX_Controller
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

        $this->table->set_heading('Name','Location', 'District','Medium','Action');
        $this->render('admin/school/index');
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
        $this->datatables->set_buttons("feeds", false,true);
        $this->datatables->set_buttons("add_feed", false,true);
        $this->datatables->set_controller_name("school");
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,name,location,district,medium')
                ->unset_column('id')
                ->from('school');

        echo $this->datatables->generate();
    }
    function feeds($id)
    {
        redirect("admin/news/index/".$id);
    }
    
    function add_feed($id)
    {
        redirect("admin/news/add/".$id);
    }

    /**
     * add function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    function add()
    {
        $obj_school = new Schools();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_school->$key = $value;
            }
        }

        $data['model'] = $obj_school;
        if (!$obj_school->save())
        {
            $this->render('admin/school/insert', $data);
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

        
        $obj_school = new Schools($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_school->$key = $value;
            }
        }

        $data['model'] = $obj_school;
        if (!$obj_school->save() || !$_POST)
        {
            $this->render('admin/school/insert', $data);
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
        $obj_school = new Schools($this->input->post('primary_id'));
        $obj_school->delete();
        echo 1;
    }
    
    
    public function sort_schools()
    {
        $obj_school = new Schools();
        $obj_school->order_by('priority');
        $obj_school->where("is_columnist", "1");
        $data['schools'] = $obj_school->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/school/sort', $data);
    }
    
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $ar_priority_sets = array();
       
        
        $s_school_data = $this->input->post("category_ids");
        
        $obj_school = new Schools();
        $ar_schools = explode(",", $s_school_data);
        $i = 1;
        foreach( $ar_schools as $school_id )
        {
            if ( stripos($school_id, "_") === FALSE )
            {
                $obj_school->where('id', $school_id);
                $obj_school->update("priority", $i);
                $i++;
            }
            
        }
    }

    

}

?>
