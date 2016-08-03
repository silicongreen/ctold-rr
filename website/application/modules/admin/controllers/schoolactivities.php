<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class schoolactivities extends MX_Controller
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

        $this->table->set_heading('School', 'Title','Date Time','Action');
        $this->render('admin/schoolactivities/index');
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
        $this->datatables->set_controller_name("schoolactivities");
        $this->datatables->set_primary_key("primary_id");

        $this->datatables->select('school_activities.id as primary_id,pre_school.name,school_activities.title as s_title,school_activities.date as s_date')
                ->unset_column('primary_id')
                ->join("school as pre_school", "pre_school.id=school_activities.school_id", 'INNER')
                ->from('school_activities');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    
    private function insert_gallery($post, $id)
    {
        
        $reletad_gallery = array();
        $this->db->where("activities_id", $id);
        $this->db->delete("school_activities_gallery");
       
        if(isset($_POST['related_img']) && $_POST['related_img'])
        {
            $i_loop = 0;
            foreach($_POST['related_img'] as $value)
            {
               $this->db->where("material_url", str_replace(base_url(), "", $value));
               $meterials = $this->db->get("materials")->row();
               if(count($meterials)>0)
               {
                    $reletad_gallery[$i_loop]['material_id'] = $meterials->id;
                    $reletad_gallery[$i_loop]['activities_id'] = $id;
                    $i_loop++;
               }
            }    
        }
        if($reletad_gallery)
        {
            $this->db->insert_batch('school_activities_gallery', $reletad_gallery);
        }
       
    }
    function add()
    {
        $obj_school_activities = new schoolsactivities();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_school_activities->$key = $value;
            }
        }
       
        $schools = $obj_school_activities->getSchool();
        
        $schools_dropDown = array();
        foreach($schools as $value)
        {
            $schools_dropDown[$value->id] = $value->name;
        } 
        
        

        $data['model'] = $obj_school_activities;
        $data['schools_dropDown'] = $schools_dropDown;
        if (!$obj_school_activities->save())
        {
            $this->render('admin/schoolactivities/insert', $data);
        }
        else
        {
            $this->insert_gallery($_POST, $obj_school_activities->id);
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

        
        $obj_school_activities = new schoolsactivities($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_school_activities->$key = $value;
            }
        }

         
        
        $schools = $obj_school_activities->getSchool();
        
        $schools_dropDown = array();
        foreach($schools as $value)
        {
            $schools_dropDown[$value->id] = $value->name;
        } 
        
        $data['related_gallery'] = $obj_school_activities->get_gallery($id);

        $data['model'] = $obj_school_activities;
        $data['schools_dropDown'] = $schools_dropDown;
        if (!$obj_school_activities->save() || !$_POST)
        {
            $this->render('admin/schoolactivities/insert', $data);
        }
        else
        {
            $this->insert_gallery($_POST, $obj_school_activities->id);
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
        $obj_school_activities = new schoolsactivities($this->input->post('primary_id'));
        $obj_school_activities->delete();
        echo 1;
    }
    
    

    

}

?>
