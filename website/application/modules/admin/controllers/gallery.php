<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gallery
 *
 * @author ahuffas
 */


class Gallery extends MX_Controller {
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI = & $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }
    
    public function index()
    {
        $this->load->library("kcfinder");
        $data['lib'] = $this->kcfinder;
        $data['page'] = 'Gallery';
        $this->render('admin/gallery/index', $data);
    }
    
    public function gallery_list()
    {
        $data['page'] = 'Gallery';
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable_gallery">');
        $this->table->set_template($tmpl);

        $this->load->config("tds");
        $data['type'] = $this->config->config['gallery_type'];
        
        $this->table->set_heading('Title','Type', 'Action');
        $this->render('admin/gallery/gallery', $data);
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
        
        $this->load->config("tds");
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("gallery");
        $this->datatables->set_custom_string(2, $this->config->config['gallery_type']);
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,gallery_name,gallery_type')
                ->unset_column('id')
                ->from('gallery')
                ->where('gallery_type >', "0");

        echo $this->datatables->generate();
    }
    
    /**
     * add function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    function add()
    {
        $obj_gallery = new Gallery_model();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_gallery->$key = $value;
            }
        }

        $data['model'] = $obj_gallery;
        
        $this->load->config("tds");
        if (!$obj_gallery->save())
        {
            $data['type'] = $this->config->config['gallery_type'];
            $this->render('admin/gallery/insert', $data);
        }
        else
        {
            //Make dir for the Gallery
            //Create Path
            $s_path = FCPATH . 'upload/gallery/' . strtolower($this->config->config['gallery_type'][$obj_gallery->gallery_type]) . '/' . strtolower($obj_gallery->gallery_name);
            @mkdir($s_path, 0777);
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    /**
     * add Caption And Source function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    function add_cpation_source()
    {
        $data['page'] = 'Gallery';
        $obj_material = new Material();
        
        $ar_images = $this->uri->segment_array();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        
        $ar_dt = array();
        $j = 0;
        for( $i=4; $i<count($ar_images); $i++ )
        {
            
            $ar_materials_data = $obj_material->like("material_url", $ar_images[$i])->limit(1)->get();
            $ar_dt[$j]['material_url'] = $ar_materials_data->material_url;
            $ar_dt[$j]['id'] = $ar_materials_data->id;
            $j++;
        }
        $data['images'] = $ar_dt;
        
        $data['pos'] = $ar_images[count($ar_images)];
        
        $this->load->view('admin/gallery/insert_caption_source', $data);
        
    }
    
    /**
     * Assign Images to menu
     * @param none
     * @defination use for assign
     * @author Fahim
     */
    function assign_to_menu()
    {
        $data['page'] = 'Gallery';
        
        $obj_menus = new Menu_model();
        $obj_menus_data = $obj_menus->where('is_active', '1')->get();
        $data['menus'] = $obj_menus_data;
        
        $obj_material = new Material();
        
        $ar_images = $this->uri->segment_array();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        
        $ar_dt = array();
        $j = 0;
        for( $i=4; $i<count($ar_images) - 1; $i++ )
        {
            $ar_materials_data = $obj_material->like("material_url", $ar_images[$i],'before')->limit(1)->get();
            if(!empty($ar_materials_data->material_url)){
                $ar_dt[$j]['material_url']  = $ar_materials_data->material_url;
                $ar_dt[$j]['id']            = $ar_materials_data->id;
                $ar_dt[$j]['filename']      = $ar_images[$i];
                $ar_dt[$j]['dir']           = str_ireplace("/" . $ar_images[$i], "", str_ireplace("upload/", "", $ar_materials_data->material_url));
                $j++;
            }
        }
        $data['images'] = $ar_dt;
        if ( $ar_images[count($ar_images) - 1] != 0 )
        {
            $data['type'] = $ar_images[count($ar_images) - 2];
            $data['name'] = $ar_images[count($ar_images)];
            $data['type_id'] = $ar_images[count($ar_images) - 1];
        }
        else
        {
            $data['type'] = $ar_images[count($ar_images) - 1];
            $data['type_id'] = $ar_images[count($ar_images)];
            $data['name'] = "";
        }
        
        
        $this->load->view('admin/gallery/assign_to_menu', $data);
        
    }
    
    function assign_to_menu_single()
    {
        $data['page'] = 'Gallery';
        
        $obj_menus = new Menu_model();
        $obj_menus_data = $obj_menus->where('is_active', '1')->get();
        $data['menus'] = $obj_menus_data;
        
        $obj_material = new Material();
        
        $ar_images = $this->uri->segment_array();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        
        $ar_dt = array();
        
        if($ar_images[4] != '')
        {
            $ar_materials_data = $obj_material->like("material_url", $ar_images[4])->limit(1)->get();
            
            $ar_dt[0]['material_url']  = $ar_materials_data->material_url;
            $ar_dt[0]['id']            = $ar_materials_data->id;
            $ar_dt[0]['filename']      = $ar_images[4];
            $ar_dt[0]['dir']           = str_ireplace("/" . $ar_images[4], "", str_ireplace("upload/", "", $ar_materials_data->material_url));
        }
        
        $data['images'] = $ar_dt;
        
        if ( $ar_images[count($ar_images) - 1] != 0 )
        {
            $data['type'] = $ar_images[count($ar_images) - 2];
            $data['name'] = $ar_images[count($ar_images)];
            $data['type_id'] = $ar_images[count($ar_images) - 1];
        }
        else
        {
            $data['type'] = $ar_images[count($ar_images) - 1];
            $data['type_id'] = $ar_images[count($ar_images)];
            $data['name'] = "";
        }
                
        $this->load->view('admin/gallery/assign_to_menu_single', $data);
    }

    function get_menu_gallery()
    {
        $s_type = $this->input->post("material_type");
        $i_menu_id = $this->input->post("menu");
        $s_date = $this->input->post("issue_data");
        
        $s_name = $this->input->post("name");
        
        $obj_material_menu = new Material_menu();
        
        if($s_type==5 && $s_name!="0")
        {
             $obj_material_menu_data = $obj_material_menu->where("menu_id", $i_menu_id)->where("sname", $s_name)->where("issue_date", $s_date)->where("type", $s_type)->get();
        }
        else
        {
             $obj_material_menu_data = $obj_material_menu->where("menu_id", $i_menu_id)->where("issue_date", $s_date)->where("type", $s_type)->get();
        }
        
        $ar_dt = array();
        $obj_material = new Material();
        $j = 0;
        
        $s_materials = $this->input->post("images");
        
        $ar_materials = explode(",", $s_materials);
        foreach( $obj_material_menu_data as $material_menu )
        {
            $i_material_id = $material_menu->material_id;
            if ( !in_array($i_material_id, $ar_materials) )
            {
                $ar_materials_data = $obj_material->where("id", $i_material_id)->get();

                $s_dir                      = str_ireplace("upload/", "", $ar_materials_data->material_url);
                $i_pos = strrpos($s_dir, "/");
                if(!empty($ar_materials_data->material_url)){
                    $ar_dt[$j]['material_url']  = $ar_materials_data->material_url;
                    $ar_dt[$j]['id']            = $ar_materials_data->id;
                    $ar_dt[$j]['filename']      = substr($s_dir, $i_pos + 1, strlen($s_dir));
    
    
    
                    $ar_dt[$j]['dir']           = substr($s_dir, 0, $i_pos + 1);
    
                    $j++;
                }
            }
        }
        
        $data['images'] = $ar_dt;
        $this->load->view('admin/gallery/menu_gallery', $data);
    }


    function assign_menu()
    {
        $s_date = $this->input->post("issue_data");
        
        $s_menu_name = $this->input->post("name");
        
        if ( $s_menu_name == "0" ||  $this->input->post("type_val")!="Cartoon")
        {
            $s_type_name = $this->input->post("type_val");
        }
        else
        {
            $s_type_name = $s_menu_name;
        }
        
     
        $s_type = $this->input->post("material_type");
      
        $i_menu_id = $this->input->post("menu");
        $obj_menu = new Menu_model($i_menu_id);
        
        $s_menu_name = strtolower($obj_menu->title);
        
        $s_materials = $this->input->post("images");
        $ar_materials = explode(",", $s_materials);
        
        $obj_material_menu = new Material_menu();
        
        if ( $s_menu_name == "0" ||  $this->input->post("type_val")!="Cartoon")
        {
            $obj_material_menu_data = $obj_material_menu->where("menu_id", $i_menu_id)->where("issue_date", $s_date)->where("type", $s_type)->get();
        }
        else
        {
            $obj_material_menu_data = $obj_material_menu->where("menu_id", $i_menu_id)->where("sname", $this->input->post("name"))->where("issue_date", $s_date)->where("type", $s_type)->get();
        }
        
        
        foreach($obj_material_menu_data as $menu)
        {
            $menu->delete();
        }
        
        foreach ($ar_materials as $material_id) 
        {
            $obj_material_menu = new Material_menu();
            $obj_material_menu->menu_id = $i_menu_id;
            $obj_material_menu->material_id = $material_id;
            $obj_material_menu->type = $s_type;
            $obj_material_menu->issue_date = $s_date;
            $obj_material_menu->sname = $this->input->post("name");
            $obj_material_menu->save();
        }
        
        //Create the Gallery XML
        $s_XML_name = FCPATH . 'gallery/xml/' . $obj_menu->ci_key . '-' . strtolower($s_type_name) . '-' . date("Ymd", strtotime($s_date)) . '.xml';            
        
        if (file_exists($s_XML_name))
        {
            @unlink($s_XML_name);
        }
        //Create NODE for XML
        $xmlElement = new SimpleXMLElement( '<playlist />' );
        foreach ($ar_materials as $material_id) 
        {
            $obj_material = new Material( $material_id );
            if ( $s_type == 2 && $obj_material->video_id > 0 )
            {
                $obj_material_video = new Materials_video( $obj_material->video_id );
                $s_material_url = $obj_material_video->url;
                $s_material_url_thumbnail = $obj_material->material_url;
            }
            else
            {
                $s_material_url = $obj_material->material_url;
                $s_material_url_thumbnail = $obj_material->material_url;
            }
            $objMatrialXML = $xmlElement->addChild( 'slide' );
            $objMatrialXML->addChild( 'file', $s_material_url );
            
            $objMatrialXML->addChild( 'thumbnail', $s_material_url_thumbnail );
            $objMatrialXML->addChild( 'title', $obj_material->caption );
            $objMatrialXML->addChild( 'link', "" );
            $objMatrialXML->addChild( 'linktarget', "" );
            
        }
        
        $objDOC = new DOMDocument( '1.0' );
        $objDOC->formatOutput = true;
        $nodeDom = dom_import_simplexml( $xmlElement );
        $nodeDom = $objDOC->importNode( $nodeDom, true );
        $nodeDom = $objDOC->appendChild( $nodeDom );
        
        $objDOC->save( $s_XML_name );
        garbage_collector_gallery(true);
        echo "save";
    }
    
    function add_video()
    {
        $data['page'] = 'Gallery';
        $s_gallery_name = $this->uri->segment(4);
        $obj_gallery = new Gallery_model();
        $obj_gallery_data = $obj_gallery->where("gallery_name", $s_gallery_name)->get();
        
        if ( $obj_gallery_data )
        {
            $data['gallery_id'] = $obj_gallery_data->id;
        }
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        
        $this->render('admin/gallery/insert_video', $data);
    }
    
    function check_video()
    {
        $s_video_url = $this->input->post('video_url');
        
        $this->load->helper("video");
        
        list( $b_video_exists, $b_unsuppoted_video ) = check_video($s_video_url);
        if ( $b_unsuppoted_video )
        {
            echo "not_supported";
        }
        else if ( $b_video_exists )
        {
            echo "exists";
        }
        else 
        {
            echo "not_exists";
        }
    }
            
    function add_video_data()
    {
        //Let work with some video now
        $this->load->helper("video");
        
        $s_video_url = $this->input->post('video_url');
        $i_gallery_id = $this->input->post('gallery_id');
        
        $obj_gallery = new Gallery_model($i_gallery_id);
        
        $obj_materials = new Material();
        $obj_materials->material_url = $s_video_url;
        $obj_materials->gallery_id = $i_gallery_id;
        $obj_materials->imagedate = date("Y-m-d");
        $obj_materials->caption = "";
        $obj_materials->source = "";
        $obj_materials->save();
        $i_material_id = $obj_materials->id;
        
        $this->load->helper("video");
        
        list( $b_video_exists, $b_unsuppoted_video ) = check_video($s_video_url);
        if ( $b_unsuppoted_video )
        {
            echo "not_supported";
        }
        else if ( $b_video_exists )
        {
            $ar_video_data = video_info($s_video_url);
            
            if ( $ar_video_data['is_exists'] )
            {
                $ar_video_object = array();
                if ( $ar_video_data['video_type'] == "youtube" )
                {
                    $ar_video_object['video_type'] = $ar_video_data['video_type'];
                    $ar_video_object['video_id'] = $ar_video_data['video_id'];
                    $s_thumb = "";
                    foreach( $ar_video_data['thumb_1'] as $key => $value )
                    {
                        $s_thumb .= $key . ":" . $value . ",";
                    }
                    $s_thumb = substr($s_thumb, 0, -1);
                    $ar_video_object['thumb_small'] = $s_thumb;

                    $s_thumb = "";
                    foreach( $ar_video_data['thumb_2'] as $key => $value )
                    {
                        $s_thumb .= $key . ":" . $value . ",";
                    }
                    $s_thumb = substr($s_thumb, 0, -1);
                    $ar_video_object['thumb_medium'] = $s_thumb;

                    $s_thumb = "";
                    foreach( $ar_video_data['thumb_large'] as $key => $value )
                    {
                        $s_thumb .= $key . ":" . $value . ",";
                    }
                    $s_thumb = substr($s_thumb, 0, -1);
                    $ar_video_object['thumb_large'] = $s_thumb;

                    $s_thumb = "";
                    foreach( $ar_video_data['thumb_3'] as $key => $value )
                    {
                        $s_thumb .= $key . ":" . $value . ",";
                    }
                    $s_thumb = substr($s_thumb, 0, -1);
                    $ar_video_object['additional_thumb'] = $s_thumb;
                    
                    $s_filename = save_video_image($ar_video_data['thumb_3']['url'], $obj_gallery->gallery_name);
                    $obj_materials->update("material_url", $s_filename, TRUE, "id", $obj_materials->id);
                    
                    $ar_video_object['video_title'] = $ar_video_data['title'];
                    $ar_video_object['url'] = $ar_video_data['url'];
                    $ar_video_object['likes'] = "0";
                    $ar_video_object['comments'] = "0";
                    $ar_video_object['views'] = $ar_video_data['views'];
                    $ar_video_object['height'] = "0";
                    $ar_video_object['width'] = "0";
                    $ar_video_object['video_cat'] = $ar_video_data['cat'];
                    $ar_video_object['tags'] = $ar_video_data['tags'];
                }
                else
                {
                    $ar_video_object['video_type'] = $ar_video_data['video_type'];
                    $ar_video_object['video_id'] = $ar_video_data['video_id'];
                    $ar_video_object['thumb_small'] = $ar_video_data['thumb_small'];
                    $ar_video_object['thumb_medium'] = $ar_video_data['thumb_medium'];
                    $ar_video_object['thumb_large'] = $ar_video_data['thumb_large'];
                    $s_filename = save_video_image($ar_video_data['thumb_large'], $obj_gallery->gallery_name);
                    $obj_materials->update("material_url", $s_filename, TRUE, "id", $obj_materials->id);
                    $ar_video_object['additional_thumb'] = "";

                    $ar_video_object['video_title'] = $ar_video_data['title'];
                    $ar_video_object['url'] = "//player.vimeo.com/video/" . $ar_video_data['video_id'];//$ar_video_data['url'];
                    $ar_video_object['likes'] = (int) $ar_video_data['likes'];
                    $ar_video_object['views'] = (int) $ar_video_data['views'];
                    $ar_video_object['comments'] = (int) $ar_video_data['comments'];
                    $ar_video_object['height'] = $ar_video_data['height'];
                    $ar_video_object['width'] = $ar_video_data['width'];
                    $ar_video_object['video_cat'] = "";
                    $ar_video_object['tags'] = $ar_video_data['tags'];
                }
                $ar_video_object['material_id'] = $i_material_id;
                
                $obj_material_video = new Materials_video();
                foreach ($ar_video_object as $key => $value) 
                {
                    $obj_material_video->$key = $value;
                }
                
                $obj_material_video->save();
                
                $i_video_id = $obj_material_video->id;
                
                $ar_field = array(
                    "video_id"  => $i_video_id
                );
                
                $obj_materials->update("video_id", $i_video_id, TRUE, "id", $obj_materials->id);
                echo "saved";
            }
        }
        else 
        {
            echo "not_exists";
        }
        
    }
    
    function save_caption_data()
    {
        $obj_material = new Material(  );
        $ar_image_captions = $this->input->post("images");
        foreach( $ar_image_captions as $captions )
        {
            $ar_data['caption'] = $captions['caption'];
            $ar_data['source'] = $captions['source'];
            $obj_material->update($ar_data, NULL, TRUE, 'id', $captions['material_id']);
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
        $obj_gallery = new Gallery_model($id);
        
        $this->load->config("tds");
        $old_folder_name = FCPATH . 'upload/gallery/' . strtolower($this->config->config['gallery_type'][$obj_gallery->gallery_type]) . '/' . strtolower($obj_gallery->gallery_name);
        //@rmdir($s_path);
        
        $ar_new_gallery = array();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $ar_new_gallery[$key] = $value;
            }
        }
        
        
        $data['model'] = $obj_gallery;
        if (!$obj_gallery || !$_POST)
        {
            $this->load->config("tds");
            $data['type'] = $this->config->config['gallery_type'];
            $this->render('admin/gallery/insert', $data);
        }
        else
        {
            $new_folder_name = FCPATH . 'upload/gallery/' . strtolower($this->config->config['gallery_type'][$ar_new_gallery['gallery_type']]) . '/' . strtolower($ar_new_gallery['gallery_name']);
            $obj_gallery->update($ar_new_gallery);
            @rename($old_folder_name, $new_folder_name);
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
        
        $obj_gallery = new Gallery_model($this->input->post('primary_id'));
        if ( !$obj_gallery->has_post_image() )
        {
            $this->load->config("tds");
            $s_path = FCPATH . 'upload/gallery/' . strtolower($this->config->config['gallery_type'][$obj_gallery->gallery_type]) . '/' . strtolower($obj_gallery->gallery_name);
            @rmdir($s_path);
            $obj_gallery->delete();
            echo 1;
        }
        else
        {
            echo "image_exists_on_gallery";
        }
    }
    
    /**
     * delete function
     * @param none
     * @defination use for delete a byline
     * @author Fahim
     */
    function deleteall()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_gallery = new Gallery_model($this->input->post('primary_id'));
        
        ########################################################################
        ##Delete all data from post gallery and materials
        ########################################################################
        $obj_materials = new Material( );
        $obj_post_gallery = new Post_gallery();
        $obj_materials_data = $obj_materials->get_where(array( "gallery_id" => $obj_gallery->id ));
        foreach ($obj_materials_data as $material)
        {
            $obj_post_gallery_data = $obj_post_gallery->get_where(array( "material_id" => $material->id ));
            foreach( $obj_post_gallery_data as $post_gallery )
            {
                if ( $post_gallery->id == 1 )
                {
                    $post_gallery->delete();
                }
            }
            $material->delete();
        }
        ########################################################################
        ##Delete all data from post gallery and materials
        ########################################################################
        
        ########################################################################
        ##Delete all files recursively
        ########################################################################
        $this->load->config("tds");
        $s_path = FCPATH . 'upload/gallery/' . strtolower($this->config->config['gallery_type'][$obj_gallery->gallery_type]) . '/' . strtolower($obj_gallery->gallery_name);
        $this->_delete_files_from_dir($s_path);
        ########################################################################
        ##Delete all files recursively
        ########################################################################
        
        $obj_gallery->delete();
        echo 1;
    }
    
    function _delete_files_from_dir($s_path)
    {
        $files = glob($s_path . '/{,.}*', GLOB_BRACE);
        foreach ($files as $file)
        {
            if ($file != "." && $file != "..") 
            {
                if ( filetype($file) == "dir") 
                {
                    @rmdir($file);
                }
                else
                {
                    @unlink($file);
                }
            }
        }
        @rmdir($s_path);
    }
}
?>
