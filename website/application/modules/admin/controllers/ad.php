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
        //01913382916
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('ID','Name','Status', 'Location', 'Plan','For All', 'Priority','Action');
        
        $this->db->select('name');
        $group = $this->db->get('tds_ad')->result();
        $obj_ad_plans = $this->get_plans(2);
	$data['ad_plans'] = $obj_ad_plans;  
        
        $data['datatableSortBy'] = 5;
        $data['datatableSortDirection'] = 'asc';
        
        
        $this->render('admin/ad/ad',$data);        
    }
/* 
 * ADD NEW ADS
 * 
 */
    function add()
    {
        $obj_ad_plans = $this->get_plans();
	$data['ad_plans'] = $obj_ad_plans;  
        $this->load->helper('date');
        
        $rss_category_key = get_menu_category(); 
        
        foreach ($rss_category_key['ci_key'] as $key => $value)
        {
            $data['menus'][$value] = $value;
        }

        //echo "<pre>";
        //print_r($data['menus']);exit;
        
        
        
        
        
        
        
        //$data['ad_link_location'] = array('Home','Section','Details');
        
        $this->form_validation->set_rules('type', 'Banner Type', 'required');
        
        $this->form_validation->set_rules('name', 'Banner Name', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('priority', 'Priority Value', 'trim|required|integer|min_length[1]');
        
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
            $this->render('admin/ad/ad_add',$data);
        }
        else
        {  
            $image = $this->doUpload();
            if($this->input->post('start_date') == "")
            {
                $_POST['start_date'] = "0000-00-00 00:00:00";
            }
            if($this->input->post('end_option') == 0)
            {
                $_POST['end_date'] = "0000-00-00 00:00:00";
            }
            $_POST['plan_id'] = ($this->input->post('plan_id_home') != 0 ?$this->input->post('plan_id_home'):($this->input->post('plan_id_section') != 0 ?$this->input->post('plan_id_section'):($this->input->post('plan_id_details') != 0 ?$this->input->post('plan_id_details'):0)));
            //$_POST['menu_ci_key'] = ($this->input->post('menu_ci_key')=="section" && $this->input->post('menu_option') == 1)?$this->input->post('ci_key'):$this->input->post('menu_ci_key');

            unset($_POST['image']);
            unset($_POST['end_option']);
            unset($_POST['view_unlimited']);
            unset($_POST['click_unlimited']);
            unset($_POST['plan_id_home']);
            unset($_POST['plan_id_section']);
            unset($_POST['plan_id_details']);
            unset($_POST['menu_option']);
            unset($_POST['ci_key']);           
           
            
            $_POST['image_path'] = date('Y').'/'.date('m').'/'.date('d').'/'. $image;
            $_POST['imagethumb_path'] = date('Y').'/'.date('m').'/'.date('d').'/thumbs/'. $image;            
            //$_POST['start_date'] = convert_to_unix_timestamp($this->input->post('start_date'));
            //$_POST['end_date'] = convert_to_unix_timestamp($this->input->post('end_date'),true);
            $_POST['user_id'] = 1;
            
            $this->db->insert('tds_ad',$this->input->post()); 
            
            $this->cache->clean();
            $cache_name = "common/right_view";
            if($this->cache->file->get($cache_name))
            {
               $this->cache->file->delete($cache_name); 
            }
            
            garbage_collector();
            
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
        $obj_ad_plans = $this->get_plans();
	$data['ad_plans'] = $obj_ad_plans;  
        
        $this->load->helper('date');
        
        $this->form_validation->set_rules('type', 'Banner Type', 'required');
        
        $this->form_validation->set_rules('name', 'Banner Name', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('priority', 'Priority Value', 'trim|required|integer|min_length[1]');
        if($this->input->post('type') == 1)
        {
            if (empty($_FILES['image']['name']))
            {
                //$this->form_validation->set_rules('image', 'Image', 'required');
            }
            $this->form_validation->set_rules('url_link', 'HTML', 'trim|required|min_length[6]');
        }
        if($this->input->post('type') == 2)
        {
            $this->form_validation->set_rules('html_code', 'HTML', 'trim|required|min_length[6]');            
        }  
        
        $data['ad_data'] = $this->db->get_where('tds_ad', array('id' => $id),1)->row();
        //echo "<pre>";
        //print_r($rss_category_key);exit;
        if ($this->form_validation->run() == FALSE)
        {
            $this->render('ad/ad_edit',$data);
        }
        else
        {            
            if(isset($_FILES['image']['error']) && $_FILES['image']['error']== 0)
            {
                $image = $this->doUpload();
                
                $path = 'upload/ads/'.$data['ad_data']->image_path;
                $pathThumb = 'upload/ads/'.$data['ad_data']->imagethumb_path;

                @unlink($path);
                @unlink($pathThumb);
                $_POST['image_path'] = date('Y').'/'.date('m').'/'.date('d').'/'. $image;
                $_POST['imagethumb_path'] = date('Y').'/'.date('m').'/'.date('d').'/thumbs/'. $image; 
                
            }
            
            if($this->input->post('start_date') == "")
            {
                $_POST['start_date'] = "0000-00-00 00:00:00";
            }
            if($this->input->post('end_option') == 0)
            {
                $_POST['end_date'] = "0000-00-00 00:00:00";
            }
            
            $_POST['plan_id'] = ($this->input->post('plan_id_home') != 0 ?$this->input->post('plan_id_home'):($this->input->post('plan_id_section') != 0 ?$this->input->post('plan_id_section'):($this->input->post('plan_id_details') != 0 ?$this->input->post('plan_id_details'):0)));
            
            unset($_POST['image']);
            unset($_POST['end_option']);
            unset($_POST['view_unlimited']);
            unset($_POST['click_unlimited']);
            unset($_POST['plan_id_home']);
            unset($_POST['plan_id_section']);
            unset($_POST['plan_id_details']);
           
            
                       
            //$_POST['start_date'] = convert_to_unix_timestamp($this->input->post('start_date'));
            //$_POST['end_date'] = convert_to_unix_timestamp($this->input->post('end_date'),true);
            $_POST['user_id'] = 1;
            
            $this->db->where('id', $id);
            $this->db->update('tds_ad',$this->input->post() ); 
            $this->cache->clean();
            $cache_name = "common/right_view";
            if($this->cache->file->get($cache_name))
            {
               $this->cache->file->delete($cache_name); 
            }
            garbage_collector();
            
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
       
    }
    private function get_plans($default = 1)
    {
        $this->db->select("*");
        $this->db->from(" tds_ad_plan");
        $this->db->where('is_active', 1);
        $this->db->order_by('priority');
        $menus = $this->db->get()->result();
        if($default != 1)
        {
            $ad_plans[Null] ="Select";
        }
        
        $ad_plans['home'] = ($default==1)?array( 0 => "Select"):array();
        $ad_plans['section'] = ($default==1)?array( 0 => "Select"):array();
        $ad_plans['details'] = ($default==1)?array( 0 => "Select"):array();

        foreach ($menus as $value)
        {
            if($value->link_location=="index"){if($default != 1){$ad_plans['home'][$value->title] = $value->title;}else{$ad_plans['home'][$value->id] = $value->title;}}
            if($value->link_location=="section"){if($default != 1){$ad_plans['section'][$value->title] = $value->title;}else{$ad_plans['section'][$value->id] = $value->title;}}
            if($value->link_location=="details"){if($default != 1){$ad_plans['details'][$value->title] = $value->title;}else{$ad_plans['details'][$value->id] = $value->title;}}
        }

        return $ad_plans;        
    }
    public function get_plans_by()
    {
        if($this->input->post('plan_id') != "")
        {
            $plan_id = $this->input->post('plan_id');
        }else
        {
            $plan_id=0;
        }
        $this->db->select("*");
        $this->db->from(" tds_ad");
        $this->db->where('is_active', 1);
        $this->db->where('plan_id', $plan_id);
        $this->db->order_by('priority');
        $results = $this->db->get()->result();
        //echo "<pre>";
        //print_r($results);
        //$html = "<option>Select Ad</option>";
        $html = "";
        foreach ($results as $row)
        {
            $html .= "<option value='". $row->id ."'>". $row->name ."</option>"; 
        }
        echo $html;
    }
    public function adassignmenu()
    {        
        if( $this->input->post('menu_ads') != "" && $this->input->post('ci_key') != "" )
        {
            $this->adassignmenu_update($this->input->post('menu_ads'),$this->input->post('ci_key') );            
        }
        $rss_category_key = get_menu_category();
        
        $data['menus'] = $rss_category_key['menu'];   
        
        $data['menu_s'] = $rss_category_key['menu_s'];
        $data['menu_s'][NULL] = "Select"; 
        
        
        
        $obj_post = new Post_model();
        $arAds = $obj_post->getAllAds( 9, 'index' );
        
        if($arAds->num_rows)
        {
            foreach ( $arAds->result() as $row )
            {
                $data['menu_ads'][$row->id] = $row->name;
            }
        }
        else
        {
            $data['menu_ads'][0] = 'Select';
        }
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="menuad_table">' );
        $this->table->set_template($tmpl); 
        
        
        
        $this->table->set_heading('ID','Name', 'Menu','Status','Action');
        
        $this->render('ad/ad_assign_menu', $data);
    }
    
    public function adassignmenu_update( $ad_id, $ci_key_id )
    {
        $this->db->select("*");
        $this->db->from("tds_ad_menu");
        $this->db->where('menu_id', $ci_key_id);
        $this->db->where('ad_id', $ad_id);
        $count1 = $this->db->count_all_results();
        if($count1>0)
        {
            return false;
        }
        else
        {
            $query = $this->db->query("SELECT * FROM tds_ad_menu WHERE menu_id = ".$ci_key_id);
            
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row){
                    $data = $row;
                }                   
                $obj_post_update = array('ad_id' => $ad_id);               
                
                //UPDATE
                $this->db->where('id', $data->id);
                $this->db->update('tds_ad_menu',$obj_post_update );
                
                
            }
            else
            {
                //INSERT
                $obj_post_insert = array('menu_id' => $ci_key_id,'ad_id' => $ad_id);     
                $this->db->insert('tds_ad_menu',$obj_post_insert); 
            } 
            garbage_collector();
            $this->cache->clean();
        }
        
//        $menus = $this->db->get()->result();
    }
    public function adassignsection()
    {        
        if( $this->input->post('menu_ci_key') != "" && $this->input->post('ci_key') != "" && $this->input->post('menu_ads') != "" && ($this->input->post('plan_id_details') != 0 || $this->input->post('plan_id_section') != 0 ))
        {
            if($this->input->post('plan_id_details')!= 0)
            {
                
                $plan_id = $this->input->post('plan_id_details');
            }
            else
            {
                $plan_id = $this->input->post('plan_id_section');
            }            
            
            $this->adassignsection_update($this->input->post('menu_ci_key'), $plan_id,$this->input->post('menu_ads'),$this->input->post('ci_key') );            
        }
        $rss_category_key = get_menu_category();        
        $data['menus'] = $rss_category_key['menu'];
        $data['menu_s'] = $rss_category_key['menu_s'];
        $data['menu_s'][NULL] = "Select"; 
        
        $obj_post = new Post_model();
        $arAds = $obj_post->getAllAds( 9, 'index' );
        foreach ( $arAds->result() as $row )
        {
            $data['menu_ads'][$row->id] = $row->name;
        }
        $obj_ad_plans = $this->get_plans();
	$data['ad_plans'] = $obj_ad_plans;  
        
        
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="sectionad_table">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('ID','Name', 'Menu', 'Status', 'Plan','Action');
        
        $this->render('ad/ad_assign_section', $data);
    }
    public function adassignsection_update( $location,$plan_id,$ad_id,$ci_key_id )
    {
        
        
        
        $this->db->select("*");
        $this->db->from("tds_ad_section");
        $this->db->where('section_menu_id', $ci_key_id);
        $this->db->where('ad_id', $ad_id);
        $count1 = $this->db->count_all_results();
        if($count1>0)
        {
            echo "Same ad";
                
            //return false;
        }
        else
        {
            $query_ci = $this->db->query("SELECT * FROM tds_menu WHERE id = ".$ci_key_id);
            if ($query_ci->num_rows() > 0)
            {
               $row_ci = $query_ci->row_array(); 
               $ci_key = $row_ci['ci_key'];
            }
            
            //INSERT
            $obj_post_insert = array('section_menu_id' => $ci_key_id,'ad_id' => $ad_id, 'ad_plan_id' => $plan_id, 'menu_ci_key' => $ci_key,'location' => $location);     
            $this->db->insert('tds_ad_section',$obj_post_insert); 
            
            $this->cache->clean();
            garbage_collector();
                        
        }
        
//        $menus = $this->db->get()->result();
    }

    public function ajaxupload()
    {
        //var_dump($_FILES);
        //exit;
        
        if ($_FILES["filename"]["error"] == 0) {
                $name = $_FILES["filename"]["name"];
                $fileType =  $_FILES['filename']['type'];
                $upload_path = 'upload/ads/'.date('Y').'/'.date('m').'/'.date('d').'/' ;
                if(!is_dir($upload_path)) //create the folder if it's not already exists
                {
                  mkdir($upload_path,0755,TRUE);
                } 
                if(move_uploaded_file( $_FILES["filename"]["tmp_name"], $upload_path. $_FILES['filename']['name']))
                {
                    $html = "";
                    if($fileType == 'image/jpeg' || $fileType == 'image/gif' || $fileType == 'image/png') 
                    {                                     
                        $html .= "<img src='". base_url().$upload_path. $_FILES['filename']['name'] ."' />";
                        $html .= '<div>'.base_url().$upload_path. $_FILES['filename']['name'].'</div>' ;
                    }
                    else if($fileType == 'application/x-shockwave-flash')
                    {
                        $html .= '<object data="'.base_url().$upload_path. $_FILES['filename']['name'].'" height="78" width="700" type="application/x-shockwave-flash" >
                        <param name="movie" value="'.base_url().$upload_path. $_FILES['filename']['name'].'" >
                        <param name="quality" value="High">
                        <param name="wmode" value="opaque">
                        <param name="menu" value="false">
                        </object>';
                        $html .= '<div>'.base_url().$upload_path. $_FILES['filename']['name'].'</div>' ;
                    }
                    else
                    {
                        $html .= '<div>'.base_url().$upload_path. $_FILES['filename']['name'].'</div>' ;
                    }
                    echo $html; 
                }
                else
                {
                    echo "error";                    
                }
            
        }
        
    }
    public function ajaxuploadaasdasdasdasd()
    {
//        /var_dump($_FILES);
        //exit;
        
        foreach ($_FILES["filename"]["error"] as $key => $error)
        {
            if ($error == UPLOAD_ERR_OK) {
                $name = $_FILES["filename"]["name"][$key];
                $fileType =  $_FILES['filename']['type'][$key];
                $upload_path = 'upload/ads/'.date('Y').'/'.date('m').'/'.date('d').'/' ;
                if(!is_dir($upload_path)) //create the folder if it's not already exists
                {
                  mkdir($upload_path,0755,TRUE);
                } 
                if(move_uploaded_file( $_FILES["filename"]["tmp_name"][$key], $upload_path. $_FILES['filename']['name'][$key]))
                {
                    if($fileType == 'image/jpeg' || $imgType == 'image/gif' || $imgType == 'image/png') 
                    {                                     
                        "<ul id='image-list'>
                            <li><img src='". base_url().$upload_path. $_FILES['filename']['name'][$key] ."' /><li>
                        </ul>";
                    }
                    // echo ;
                }
                else
                {
                    echo "Upload error.";                    
                }
            }
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
        $data['ad_data'] = $this->db->get_where('tds_ad', array('id' => $this->input->post('primary_id')),1)->row(); 
        
        $path = 'upload/ads/'.$data['ad_data']->image_path;
        $pathThumb = 'upload/ads/'.$data['ad_data']->imagethumb_path;
        
        $this->db->select("plan_id");
        $this->db->where('id', $this->input->post('primary_id') );
        $plan_id_obj = $this->db->get('tds_ad')->row();
        
        $this->db->where('id', $this->input->post('primary_id') );
        $this->db->delete('tds_ad');
        
        if($this->db->affected_rows() >= 1){
            $this->cache->clean();
            garbage_collector();
            if(unlink($path)&&unlink($pathThumb))
            echo 1;
        }        
    }
    function del()
    {
        $this->db->where('id', $this->input->post('primary_id') );
        $this->db->delete('tds_ad_menu');  
        if($this->db->affected_rows() >= 1){
            $this->cache->clean();
            garbage_collector();
            echo 1;
        }       
    }
    function remove()
    {
        $this->db->select("ad_plan_id");
        $this->db->where('id', $this->input->post('primary_id') );
        $plan_id_obj = $this->db->get('tds_ad_section')->row();
        
        $this->db->where('id', $this->input->post('primary_id') );
        $this->db->delete('tds_ad_section');  
        if($this->db->affected_rows() >= 1){
            $this->cache->clean();
            garbage_collector();
            echo 1;
        }        
    }
    
    
    public function doUpload()
    {
        $config['upload_path'] = 'upload/ads/'.date('Y').'/'.date('m').'/'.date('d').'/';

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

        $config['source_image'] = 'upload/ads/'.date('Y').'/'.date('m').'/'.date('d').'/'. $fileName;

        $config['new_image'] = 'upload/ads/'.date('Y').'/'.date('m').'/'.date('d').'/thumbs/' . $fileName;

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
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("ad");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(2, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->set_custom_string(5, array(1 => 'Yes', 0 => 'No'));
        $this->datatables->select('tds_ad.id as primary_id,tds_ad.name,tds_ad.is_active,tds_ad.menu_ci_key , tds_ad_plan.title,tds_ad.for_all, tds_ad.priority')        
        ->from('tds_ad')
        ->join("tds_ad_plan as tds_ad_plan", "tds_ad_plan.id=tds_ad.plan_id", 'LEFT');
        
        echo $this->datatables->generate();
    }
    function datatable_menuad()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        $this->datatables->set_buttons("del",'ajax');
        $this->datatables->set_controller_name("ad");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(3, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('tds_ad_menu.id as primary_id,tds_ad.name,tds_menu.title as menu, tds_ad.is_active')        
        ->from('tds_ad_menu')
        ->join("tds_ad as tds_ad", "tds_ad_menu.ad_id=tds_ad.id", 'LEFT')
        ->join("tds_menu as tds_menu", "tds_menu.id=tds_ad_menu.menu_id", 'LEFT')
        ->where("tds_ad.plan_id", "9", false);
        echo $this->datatables->generate();
    }
    function datatable_sectionad()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        $this->datatables->set_buttons("remove");
        $this->datatables->set_controller_name("ad");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(3, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('tds_ad_section.id as primary_id,tds_ad.name,tds_menu.title as menu, tds_ad.is_active, tds_ad_plan.title as plan')        
        ->from('tds_ad_section')
        ->join("tds_ad as tds_ad", "tds_ad_section.ad_id=tds_ad.id", 'LEFT')
        ->join("tds_menu as tds_menu", "tds_menu.id=tds_ad_section.section_menu_id", 'LEFT')
        ->join("tds_ad_plan as tds_ad_plan", "tds_ad_section.ad_plan_id=tds_ad_plan.id", 'LEFT')
        ->where("tds_ad.menu_ci_key", 'section', true);
        echo $this->datatables->generate();
    }
    function delete_cache_header()
    {
        $html_header_cache = "common/HEADER_MENU";
        if($this->cache->file->get($html_header_cache))
        {
            $this->cache->file->delete($html_header_cache);
        }
        garbage_collector();
       $this->layout_front = false;
        
    }
}
