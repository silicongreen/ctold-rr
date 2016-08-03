<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class srwinner extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->load->library('image_lib');
        $this->form_validation->CI = & $this;
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id and filtering for admin category
     * @author Fahim
     */
    public function index()
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1"  class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Name','Date', 'description', 'Action');
       
        
        $data['datatableSortBy'] = 2;
        $data['datatableSortDirection'] = 'asc';
        
        $this->render('admin/srwinner/index', $data);
    }

    /**
     * datatable function
     * @param none
     * @defination use for showing datatable of category with child tree callback function
     * @author Fahim
     */
    public function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("srwinner");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_custom_string(2, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('science_rocks_winner.id as primary_id,science_rocks_winner.name,science_rocks_winner.date,science_rocks_winner.description')
                ->unset_column('primary_id')
                ->from('science_rocks_winner');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert Category and child category as tree
     * @author Fahim
     */
    public function add()
    {
        $obj_srwinner = new Srwinners();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_srwinner->$key = $value;
            }
            
        }

        $data['model'] = $obj_srwinner;
        
        
        if (!$obj_srwinner->save())
        {
            $this->render('admin/srwinner/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for edit category
     * @author Fahim
     */
    public function edit($id)
    {
        $obj_srwinner = new Srwinners($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_srwinner->$key = $value;
            }
            
        }

        $data['model'] = $obj_srwinner;
        
        
        if (!$obj_srwinner->save()  || !$_POST)
        {
            $this->render('admin/srwinner/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
        
        

        
    }
    function delete()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        $obj_srwinner = new Srwinners($this->input->post('primary_id') );
        $obj_srwinner->delete(); 
        echo 1;
    }


}

?>
