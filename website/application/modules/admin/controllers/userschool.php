<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class userschool extends MX_Controller
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

        $this->table->set_heading('Name','Contact','Address', 'Zip Code','Logo','Picture','Action');
        $this->render('admin/userschool/index');
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
        $this->datatables->set_controller_name("userschool");
        $this->datatables->set_primary_key("id");

        $this->datatables->set_image_field_position(5);
        $this->datatables->set_image_field_position(6);
        $this->datatables->select('id,school_name,contact,address,zip_code,logo,picture')
                ->unset_column('id')
                ->from('user_created_school');

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

        
        $obj_school = new Userschools($id);

        $data['model'] = $obj_school;
       
        $this->render('admin/userschool/show', $data);
        
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
        $obj_school = new Userschools($this->input->post('primary_id'));
        $obj_school->delete();
        echo 1;
    }
    
    
   

    

}

?>
