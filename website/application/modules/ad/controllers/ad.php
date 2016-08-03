<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ad extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');        
        $this->form_validation->CI =& $this;
    
    }
    function index()
    {   
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('ID','Name','Status','Views','Clicks','Action');
        
        $this->db->select('name');
        $group = $this->db->get('tds_ad')->result();
        
        $this->render('ad/ad');        
    }
/* 
 * ADD NEW ADS
 * 
 */
    function add()
    {
        $this->load->helper('date');
        
        $this->form_validation->set_rules('type_id', 'Banner Type', 'required');
        
        $this->form_validation->set_rules('name', 'Banner Name', 'trim|required|min_length[4]');
        
        if($this->input->post('type_id') == 1)
        {
            if (empty($_FILES['image']['name']))
            {
                $this->form_validation->set_rules('image', 'Image', 'required');
            }
            $this->form_validation->set_rules('url_link', 'HTML', 'trim|required|min_length[6]');
        }
        if($this->input->post('type_id') == 2)
        {
            $this->form_validation->set_rules('html_code', 'HTML', 'trim|required|min_length[6]');            
        }        
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->render('ad/ad_add',$data);
        }
        else
        {             
            
            $image = $this->doUpload();
            
            unset($_POST['image']);
            unset($_POST['end_option']);
            unset($_POST['view_unlimited']);
            unset($_POST['click_unlimited']);
            
            $_POST['image_path'] = date('Y').'/'.date('m').'/'.date('d').'/'. $image;
            $_POST['imagethumb_path'] = date('Y').'/'.date('m').'/'.date('d').'/thumbs/'. $image;            
            $_POST['start_date'] = convert_to_unix_timestamp($this->input->post('start_date'));
            $_POST['end_date'] = convert_to_unix_timestamp($this->input->post('end_date'),true);
            
            $this->db->insert('tds_ad',$this->input->post()); 
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
       
    }
    /**
     * edit function
     * @param Ad id
     * @defination use for Update ad
     * @author Likhon
     */
    function edit($id)
    {      
        $this->load->helper('date');
        
        $this->form_validation->set_rules('type_id', 'Banner Type', 'required');
        
        $this->form_validation->set_rules('name', 'Banner Name', 'trim|required|min_length[4]');
        
        if($this->input->post('type_id') == 1)
        {
            if (empty($_FILES['image']['name']))
            {
                //$this->form_validation->set_rules('image', 'Image', 'required');
            }
            $this->form_validation->set_rules('url_link', 'HTML', 'trim|required|min_length[6]');
        }
        if($this->input->post('type_id') == 2)
        {
            $this->form_validation->set_rules('html_code', 'HTML', 'trim|required|min_length[6]');            
        }         

        
        
        $data['ad_data'] = $this->db->get_where('tds_ad', array('ad_id' => $id),1)->row();
        //echo "<pre>";
        //print_r($data['ad_data']);exit;
        if ($this->form_validation->run() == FALSE)
        {
            $this->render('ad/ad_edit',$data);
        }
        else
        {            
            if($_FILES['image']['error'] == 0)
            {
                $image = $this->doUpload();
                
                $path = 'upload/ad/'.$data['ad_data']->image_path;
                $pathThumb = 'upload/ad/'.$data['ad_data']->imagethumb_path;

                @unlink($path);
                @unlink($pathThumb);
            }
            
            unset($_POST['image']);
            unset($_POST['end_option']);
            unset($_POST['view_unlimited']);
            unset($_POST['click_unlimited']);
            
            $_POST['image_path'] = date('Y').'/'.date('m').'/'.date('d').'/'. $image;
            $_POST['imagethumb_path'] = date('Y').'/'.date('m').'/'.date('d').'/thumbs/'. $image;            
            $_POST['start_date'] = convert_to_unix_timestamp($this->input->post('start_date'));
            $_POST['end_date'] = convert_to_unix_timestamp($this->input->post('end_date'),true);
            
            $this->db->where('admin_id', $id);
            $this->db->update('admin',$this->input->post() ); 
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
       
    }
    /**
     * delete function
     * @param none
     * @defination use for delete an ad
     * @author Likhon
     */
    function delete()
    {
        $data['ad_data'] = $this->db->get_where('tds_ad', array('ad_id' => $this->input->post('primary_id')),1)->row(); 
        
        $path = 'upload/ad/'.$data['ad_data']->image_path;
        $pathThumb = 'upload/ad/'.$data['ad_data']->imagethumb_path;
        
        $this->db->where('ad_id', $this->input->post('primary_id') );
        $this->db->delete('tds_ad');
        
        if($this->db->affected_rows() >= 1){
            if(unlink($path)&&unlink($pathThumb))
            echo 1;
        }
        
    }
    
    
    public function doUpload()
    {
        $config['upload_path'] = 'upload/ad/'.date('Y').'/'.date('m').'/'.date('d').'/';

        $config['allowed_types'] = 'gif|jpg|jpeg|png';

        $config['file_name'] = 'ad_'.time();

        $config['max_size'] = '5000';

        $config['max_width'] = '3920';

        $config['max_height'] = '4280';

        if(!is_dir($config['upload_path'])) //create the folder if it's not already exists
        {
          mkdir($config['upload_path'],0755,TRUE);
        } 


        $this->load->library('upload', $config);



       $field_name = 'image';

        if(!$this->upload->do_upload($field_name))
        {
            $error = array('error' => $this->upload->display_errors());
        }
        else
        {			
            $fInfo = $this->upload->data();

            $this->_createThumbnail($fInfo['file_name']);

            $data['uploadInfo'] = $fInfo;

            $data['thumbnail_name'] = $fInfo['file_name'];

            return $fInfo['file_name'];

        }

    }

    function _createThumbnail($fileName) {

        $config['image_library'] = 'gd2';

        $config['source_image'] = 'upload/ad/'.date('Y').'/'.date('m').'/'.date('d').'/'. $fileName;

        $config['new_image'] = 'upload/ad/'.date('Y').'/'.date('m').'/'.date('d').'/thumbs/' . $fileName;

        $config['create_thumb'] = TRUE;

        $config['thumb_marker'] = '';

        $config['maintain_ratio'] = FALSE;

        $config['width'] = 120;

        $config['height'] = 100;
        
        if(!is_dir($config['source_image'])) //create the folder if it's not already exists
        {
          mkdir($config['source_image'],0755,TRUE);
        } 

        $this->load->library('image_lib', $config);

        if(!$this->image_lib->resize()) echo $this->image_lib->display_errors();

    }
    
/* To handle Ajax CallBank for data Table from view file
 * 
 * 
 */
    function datatable()
    {
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("ad");
        $this->datatables->set_primary_key("ad_id");
        
        $this->datatables->set_custom_string(2, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('ad_id,name,is_active,count_view,count_click')        
        ->from('tds_ad');
        
        echo $this->datatables->generate();
    }
}
