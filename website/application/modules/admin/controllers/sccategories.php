<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class sccategories extends MX_Controller
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

        $this->table->set_heading('ID','Name', 'Status', 'Action');
       
        
        $data['datatableSortBy'] = 2;
        $data['datatableSortDirection'] = 'asc';
        
        $this->render('admin/sccategory/index', $data);
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
        
        $this->datatables->set_buttons("change_status","ajax");
        $this->datatables->set_controller_name("sccategories");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_custom_string(2, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('science_rocks_category.id as primary_id,science_rocks_category.name,science_rocks_category.status')
                ->from('science_rocks_category');

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
        $obj_sccategory = new Sccategory();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_sccategory->$key = $value;
            }
            
        }

        $data['model'] = $obj_sccategory;
        
        
        if (!$obj_sccategory->save())
        {
            $this->render('admin/sccategory/insert', $data);
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
        $obj_sccategory = new Sccategory($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_sccategory->$key = $value;
            }
            
        }

        $data['model'] = $obj_sccategory;
        
        
        if (!$obj_sccategory->save()  || !$_POST)
        {
            $this->render('admin/sccategory/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }

        
    }

   

    /**
     * delete function
     * @param None
     * @defination use for delete category Ajax
     * @author Fahim
     */
    function change_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_cccategory = new Sccategory($this->input->post('primary_id'));
       
        if($obj_cccategory->status)
        {
            $status = 0;
        }    
        else
        {
            $status = 1;
        }    
        
        $data  = array('status' =>$status);
        $where = "id = ".$this->input->post('primary_id');
        $str   = $this->db->update_string('tds_science_rocks_category', $data, $where);
        $this->db->query($str);
        echo 1;
    }
    
   
    public function sort_categories()
    {
        $obj_cccategory = new Sccategory();
        $obj_cccategory->order_by('priority');
        $obj_cccategory->where("science_rocks_category.status", "1");
        $data['categories'] = $obj_cccategory->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/sccategory/sort', $data);
    } 
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $ar_priority_sets = array();
        
        $s_category_data = $this->input->post("category_ids");
        
        $obj_cccategory = new Sccategory( );
        $ar_categories = explode(",", $s_category_data);
        $i = 1;
        foreach( $ar_categories as $category_id )
        {
            if ( stripos($category_id, "_") === FALSE )
            {
                $obj_cccategory->where('id', $category_id);
                $obj_cccategory->update("priority", $i);
                $i++;
            }
            else
            {
                $ar_cat_ids = explode("_", $category_id);
                $i_category_id = $ar_cat_ids[0];
                $i_parent_id = $ar_cat_ids[1];
                if ( !isset ($ar_priority_sets[$i_parent_id]) )
                {
                    $j = 1;
                    $ar_priority_sets[$i_parent_id] = $j;
                }
                else
                {
                    $j = $ar_priority_sets[$i_parent_id] + 1;
                    $ar_priority_sets[$i_parent_id] = $j;
                }
                $obj_cccategory->where('id', $i_category_id);
                $obj_cccategory->update("priority", $j);
            }
        }
       
    }

}

?>
