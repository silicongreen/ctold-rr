<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class userschoolinfo extends MX_Controller
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

        $this->table->set_heading('Name','Phone','Email','School Name','School Address','Action');
        $this->render('admin/userschoolinfo/index');
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
        $this->datatables->set_buttons("show","new_window");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("userschoolinfo");
        $this->datatables->set_primary_key("id");
        
        $this->datatables->select('id,name,email,phone,school_name,school_address')
                ->unset_column('id')
                ->where("status",1)
                ->from('user_school_information');

        echo $this->datatables->generate();
    }

    

    /**
     * edit function
     * @param none
     * @defination use for Update Byline
     * @author Fahim
     */
    function show($id)
    {

        
        $obj_school = new Userschoolinformations($id);

        $data['model'] = $obj_school;
        
        $this->db->select("file_location,file_type");
        $this->db->where("school_id",$id);
        $data['school_file'] = $this->db->get("user_school_file")->result();      
       
        $this->render('admin/userschoolinfo/show', $data);
        
    }
    
    public function download_all_file($id)
    {
        $obj_school = new Userschoolinformations($id);
        
        $school_name = sanitize($obj_school->school_name);
        $zipname = $school_name.'.zip';
        
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        $this->db->select("file_location,file_type");
        $this->db->where("school_id",$id);
        $school_file = $this->db->get("user_school_file")->result(); 
        
        foreach($school_file as $value)
        {
           $zip->addFile($value->file_location); 
        }

        $zip->close();

        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename='".$zipname."'");
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
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
        $obj_school = new Userschoolinformations($this->input->post('primary_id'));
        $obj_school->status=0;
        $obj_school->save();
        echo 1;
    }
    
    
   

    

}

?>
