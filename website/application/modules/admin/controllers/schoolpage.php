<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class schoolpage extends MX_Controller
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

        $this->table->set_heading('School','Menu', 'Title','Action');
        $this->render('admin/schoolpage/index');
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
        $this->datatables->set_controller_name("schoolpage");
        $this->datatables->set_primary_key("primary_id");

        $this->datatables->select('school_page.id as primary_id,pre_school.name,pre_menu.title,school_page.title as s_title')
                ->unset_column('primary_id')
                ->join("school as pre_school", "pre_school.id=school_page.school_id", 'INNER')
                ->join("school_menu as pre_menu", "pre_menu.id=school_page.menu_id", 'INNER')
                ->from('school_page');

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
        $this->db->where("page_id", $id);
        $this->db->delete("school_page_gallery");
       
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
                    $reletad_gallery[$i_loop]['page_id'] = $id;
                    $i_loop++;
               }
            }    
        }
        if($reletad_gallery)
        {
            $this->db->insert_batch('school_page_gallery', $reletad_gallery);
        }
       
    }
    function add_all()
    {
        $obj_school_page = new schoolspage();
        $school_menu = $obj_school_page->getSchoolMenu();
        
        $school_menu_dropDown = array();
        foreach($school_menu as $value)
        {
            $school_menu_dropDown[$value->id] = $value->title;
        }  
        
        $schools = $obj_school_page->getSchool();
        
        $schools_dropDown = array();
        foreach($schools as $value)
        {
            if($obj_school_page->checkschoolhaspage($value->id))
            $schools_dropDown[$value->id] = $value->name;
        } 
        
        

        $data['model'] = $obj_school_page;
        $data['school_menu_dropDown'] = $school_menu_dropDown;
        $data['schools_dropDown'] = $schools_dropDown;
        $save_some = false;
        if ($_POST)
        {
            foreach($school_menu_dropDown as $key=>$value)
            {
                if(isset($_POST['title_'.$key]) && isset($_POST['content_'.$key]) 
                        && $_POST['title_'.$key]!="" && $_POST['content_'.$key]!="")
                {
                    $obj_school_page_all = new schoolspage();
                    $obj_school_page_all->school_id = $this->input->post("school_id");
                    $obj_school_page_all->menu_id = $key;
                    $obj_school_page_all->title   = $this->input->post('title_'.$key);
                    $obj_school_page_all->content = $this->input->post('content_'.$key);
                    $obj_school_page_all->mobile_content = $this->input->post('mobile_content_'.$key);
                 
                    if ($obj_school_page_all->save())
                    {
                        $save_some = true;
                    }
                    
                }        
                
                
            }    
            
           
        }
        
        if (!$save_some)
        {
            $this->render('admin/schoolpage/insert_all', $data);
        }
        else
        {
            redirect("admin/schoolpage");
        }
    }
    function add()
    {
        $obj_school_page = new schoolspage();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_school_page->$key = $value;
            }
        }
        $school_menu = $obj_school_page->getSchoolMenu();
        
        $school_menu_dropDown = array();
        foreach($school_menu as $value)
        {
            $school_menu_dropDown[$value->id] = $value->title;
        }  
        
        $schools = $obj_school_page->getSchool();
        
        $schools_dropDown = array();
        foreach($schools as $value)
        {
            $schools_dropDown[$value->id] = $value->name;
        } 
        
        

        $data['model'] = $obj_school_page;
        $data['school_menu_dropDown'] = $school_menu_dropDown;
        $data['schools_dropDown'] = $schools_dropDown;
        if (!$obj_school_page->save())
        {
            $this->render('admin/schoolpage/insert', $data);
        }
        else
        {
            $this->insert_gallery($_POST, $obj_school_page->id);
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

        
        $obj_school_page = new schoolspage($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_school_page->$key = $value;
            }
        }

        $school_menu = $obj_school_page->getSchoolMenu();
        
        $school_menu_dropDown = array();
        foreach($school_menu as $value)
        {
            $school_menu_dropDown[$value->id] = $value->title;
        }  
        
        $schools = $obj_school_page->getSchool();
        
        $schools_dropDown = array();
        foreach($schools as $value)
        {
            $schools_dropDown[$value->id] = $value->name;
        } 
        
        $data['related_gallery'] = $obj_school_page->get_gallery($id);

        $data['model'] = $obj_school_page;
        $data['school_menu_dropDown'] = $school_menu_dropDown;
        $data['schools_dropDown'] = $schools_dropDown;
        if (!$obj_school_page->save() || !$_POST)
        {
            $this->render('admin/schoolpage/insert', $data);
        }
        else
        {
            $this->insert_gallery($_POST, $obj_school_page->id);
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
        $obj_school_page = new schoolspage($this->input->post('primary_id'));
        $obj_school_page->delete();
        echo 1;
    }
    
    

    

}

?>
