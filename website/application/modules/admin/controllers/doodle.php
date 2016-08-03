<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class doodle extends MX_Controller
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
		$data = array('msg' => "");
		if($this->input->post('image') == "")
        {
            if (empty($_FILES['image']['name']))
            {
                $this->form_validation->set_rules('image', 'Image', 'required');
            }
        }
		
		if(!empty($_FILES['image']['name']))
		{	
			$image = $this->doUpload();
			if (array_key_exists("error",$image))
			{
				$data['msg'] = $image['error'];
			}
		}
		
		
		
        
        $this->table->set_heading('Title','Doodle', 'Action');
        $this->render('admin/doodle/index', $data);
    }


    public function doUpload()
    {
        $config['upload_path'] = 'styles/layouts/tdsfront/images/';

        $config['allowed_types'] = 'png|jpg|jpeg';

        $config['file_name'] = 'doodle-f';

        $config['max_size'] = '2000';

        $config['max_width'] = '1900';

        $config['max_height'] = '160';        


        $this->load->library('upload', $config);

        $field_name = 'image';

		if (@file_exists(FCPATH."styles/layouts/tdsfront/images/doodle-f.png"))
		{
			unlink(FCPATH."styles/layouts/tdsfront/images/doodle-f.png");
		}
		if(!$this->upload->do_upload($field_name))
        {
            return $error = array('error' => $this->upload->display_errors());
			//echo "<pre>";
			//print_r($error);
			//exit;
        }
        else
        {	
			$fInfo = $this->upload->data();
            $data['uploadInfo'] = $fInfo;
            return $fInfo['file_name'];
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
        if (@file_exists(FCPATH."styles/layouts/tdsfront/images/doodle-f.png"))
		{
			unlink(FCPATH."styles/layouts/tdsfront/images/doodle-f.png");
		}
		redirect('admin/doodle/index');
    }
    
    

    
    

    

}

?>
