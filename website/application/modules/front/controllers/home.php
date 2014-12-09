<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class home extends MX_Controller {

    public function __construct() 
    {
        parent::__construct();
        
        $ar_segmens = $this->uri->segment_array();
        
//        if (empty($ar_segmens) )
//        {
//            $cache_name = "home/CONTENT_CACHE_LAYOUT_" . str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
//            if ( $s_content = $this->cache->file->get($cache_name) )
//            {
//                echo $s_content;
//                exit;
//            }
//        }
        
         $this->load->database();
         $this->load->library('datamapper');
         $this->load->helper('form');

    }
    
    function join_to_school(){
        
        if($this->input->is_ajax_request()){
            
            $b_need_approval = TRUE;
            $additional_info = array();
            $response = array(
                'saved' => false,
            );
            
            $user_id = get_free_user_session('id');
            $user_type = get_free_user_session('type');
            
            if($user_type == 1){
                $user_type = $this->input->post('user_type');
            }
            
            $school_id = $this->input->post('school_id');
            $grade = $this->input->post('grade_ids');
            
            unset($_POST['school_id']);
            unset($_POST['grade_ids']);
            
            /* Additional Information */
            $additional_info = json_encode($this->input->post());
            /* Additional Information */
            
            $this->load->config('user_register');
            $b_need_approval = $this->config->config['join_user_approval'][$user_type];
            
            $user_school = new User_school();
            $user_school_data = $user_school->get_user_school($user_id, $school_id);
            
            if($user_school_data === FALSE){
                
                $User_school = new User_school;
                $User_school->user_id = $user_id;
                $User_school->school_id = $school_id;
                $User_school->grade = implode(',', $grade);
                $User_school->type = $user_type;
                $User_school->is_approved = ( $b_need_approval ) ? '0' : '1';
                $User_school->information = $additional_info;
                
                if($User_school->save()){
                   $response = array(
                        'saved' => true,
                        'is_approved' => $User_school->is_approved,
                    );
                   
                } else {
                    $response['errors'] = $User_school->error->all;
                }
                
            } else {
                $response['errors'][] = 'You are already a member of this school.';
            }
            
            echo( json_encode($response));
            exit;   
        }
    }
    
    function leave_school(){
        
        if($this->input->is_ajax_request()){
            
            $b_need_approval = TRUE;
            $additional_info = array();
            $response = array(
                'saved' => false,
                'left' => false
            );
            
            $user_id = get_free_user_session('id');
            $school_id = $this->input->post('school_id');
            
            unset($_POST['school_id']);
            
            $this->db->where('user_id', $user_id);
            $this->db->where('school_id', $school_id);
            $this->db->delete('user_school');
                
            if ( $this->db->affected_rows() > 0 ) {
               $response = array(
                    'saved' => true,
                    'left' => true
                );

            } else {
                $response['errors'] = $user_school->error->all;
            }


            echo( json_encode($response));
            exit;   
        }
    }
    
    function schools()
    {
        $ar_segmens = $this->uri->segment_array();
        if(count($ar_segmens) < 2)
        {            
            
            //$this->show_404_custom();
            $this->db->select('*');
            $this->db->from('tds_school');
            ($this->input->post('name') != "") ? $this->db->like('name', $this->input->post('name'), 'after') : '';
            ($this->input->post('district') != "") ? $this->db->or_like('division', $this->input->post('division'), 'after') : '';
            ($this->input->post('level') != "") ? $this->db->or_like('level', $this->input->post('level'), 'after') : '';
            ($this->input->get('str') != "") ? $this->db->like('name', $this->input->get('str'), 'after') : '';
            $query = $this->db->get();
            $data['schooldata'] = $query->result_array();


            $data['ci_key'] = 'schools';
            
            // User Data
            $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

            $data['model'] = $this->get_free_user($user_id);

            $data['free_user_types'] = $this->get_free_user_types();
            $data['join_user_types'] = $this->get_school_join_user_types();

            $data['country'] = $this->get_country();
            $data['country']['id'] = $data['model']->tds_country_id;

            $data['grades'] = $this->get_grades();

            $data['medium'] = $this->get_medium();

            $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
            
            $user_school = new User_school();
            $user_school_data = $user_school->get_user_school($user_id);

            if($user_school_data){
                foreach ($user_school_data as $row) {
                    $data['user_school_ids'][] = $row->school_id;
                }
                
                foreach ($user_school_data as $row) {
                    $data['user_school_status'][$row->school_id] = $row->is_approved;
                }
            }
            
            $obj_post = new Posts();
            $data['category_tree'] = $obj_post->user_preference_tree_for_pref();
            // User Data
            
            $s_content = $this->load->view('schools_all', $data, true);

            //has some work in right view
            $s_right_view = $this->load->view('right', $data, TRUE);
            //echo "<pre>";
            //print_r($data);

            $str_title = "Schools";
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';
            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;
            $ar_params = array(
                "javascripts" => $ar_js,
                "css" => $ar_css,
                "extra_head" => $extra_js,
                "title" => $str_title,
                "description" => $meta_description,
                "keywords" => $keywords,
                "side_bar" => $s_right_view,
                "target" => "schools",
                "fb_contents" => NULL,
                "content" => $s_content
            );

            $this->extra_params = $ar_params;
        } 
        else
        {
            
            $school_name = unsanitize($ar_segmens[2]);
            $school_obj = new schools();
            $school_menu_id = 0;
            if($school_details = $school_obj->find_school_by_name($school_name))
            {
                $userschool = get_user_school($school_details->id);
                if(!isset($ar_segmens[3]))
                {
                    
                    if($userschool)
                    {
                        if($userschool->is_approved==1)
                        {
                            redirect("schools/".$ar_segmens[2]."/feed");
                        }
                        else
                        {    
                            $school_menu_id = 1;
                            $menu_details = $school_obj->find_default_school_menu($school_details->id);
                        }
                    }
                    else
                    {    
                        $school_menu_id = 1;
                        $menu_details = $school_obj->find_default_school_menu($school_details->id);
                    }
                   
                } 
                else
                {
                    $menu_name = unsanitize($ar_segmens[3]);
                    
                    if($menu_details = $school_obj->find_menu_by_name($menu_name))
                    {
                        $school_menu_id = $menu_details->id;
                        
                    }
                    else if($ar_segmens[3] == "activities" && isset($ar_segmens[4]))
                    {
                        $activity_details = $school_obj->find_activity_details($ar_segmens[4]);
                    } 
                    else if($ar_segmens[3] == "activities" && !isset($ar_segmens[4]))
                    {
                        
                        $activities = $school_obj->find_all_ativity($school_details->id);
                    }
                    else if($ar_segmens[3] == "feed")
                    {
                        if(!$userschool || $userschool->is_approved==0)
                        {
                            
                            redirect("schools/".$ar_segmens[2]);
                                 
                        }
                        else
                        {    
                            $feed = true;
                        }
                    }    
                    else
                    {
                        $this->show_404_custom();
                    }    
                }
                
                if((isset($menu_details) && count($menu_details)>0) 
                      || (isset($activity_details) && count($activity_details)>0)
                      || (isset($activities) &&  count($activities)>0 || isset($feed) )
                  )
                {
                    $this->load->helper('cookie');
                    
                    $cookie_set = $this->input->cookie("school_views_".$school_details->id, false);
                   
                    if(!$cookie_set)
                    {
                        $cookie = array(
                            'name'   => "school_views_".$school_details->id,
                            'value'  => 'yes',
                            'expire' =>  886500,
                            'secure' => false
                        );
                       $school_obj->increament_views($school_details->id); 
                       $this->input->set_cookie($cookie);
                       
                    }        
                    $schools_pages = $school_obj->find_school_pages($school_details->id);
                    
                    if(count($schools_pages)==0)
                    {
                        $this->show_404_custom();
                    }    
                    else
                    {
                        if(isset($menu_details) && count($menu_details)>0)
                        {
                            $page_details = $school_obj->find_page_details($school_menu_id,$school_details->id);
                        }
                        else if(isset($activity_details) && count($activity_details)>0)
                        {
                            $page_details = $activity_details;
                        } 
                        else
                        {
                            $page_details = false;
                        }    


                        $data['school_details']      = $school_details;
                        $data['school_page_details'] = $page_details;
                        $data['schools_pages'] = $schools_pages;
                        if(count($menu_details)>0)
                        {

                            $data['menu_details'] = $menu_details;
                            $data['gallery'] = $school_obj->find_page_gallery($page_details->id); 
                        }
                        else if(isset($activity_details) && count($activity_details)>0)
                        {
                            $menu_details->title = "Activity";
                            $data['activity_link'] = true;
                            $data['menu_details'] = $menu_details;
                            $data['gallery'] = $school_obj->find_activity_gallery($activity_details->id); 
                        }
                        else if(isset($feed))
                        {
                           
                            $data['feeds'] = $feed;
                            $data['menu_details'] = $menu_details;
                        }
                        else
                        {
                            $menu_details->title = "Activity";
                            $data['menu_details'] = $menu_details;
                        }    




                        if(isset($school_menu_id) && $school_menu_id == 1)
                        {
                            $data['activities'] = $school_obj->getActivities($school_details->id);
                        }
                        else if(isset($activities) &&  count($activities)>0)
                        {
                            $data['activities'] = $activities;
                        }    

                        $data['ci_key'] = $school_details->name;
                        $data['ci_key_for_cover'] = $school_details->name;


                        $s_content = $this->load->view('school',$data, true);
                        
                        // User Data
                        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                        $data['model'] = $this->get_free_user($user_id);

                        $data['free_user_types'] = $this->get_free_user_types();

                        $data['country'] = $this->get_country();
                        $data['country']['id'] = $data2['model']->tds_country_id;

                        $data['grades'] = $this->get_grades();

                        $data['medium'] = $this->get_medium();

                        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                        // User Data
                        
                        $obj_post = new Posts();
                        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();

                        //has some work in right view
                        $s_right_view =  $this->load->view( 'right', $data, TRUE );

                        $str_title = $school_details->name." > ".$menu_details->title;
                        $ar_js = array();
                        $ar_css = array();
                        $extra_js = '';
                        $meta_description = $school_details->name." > ".$menu_details->title;
                        $keywords = $school_details->name." > ".$menu_details->title;
                        $fb_contents['description'] = $school_details->name." > ".$menu_details->title;

                        $logo_image = base_url()."images/backgrounds/bg_content.png";
                        if($school_details->logo)
                        {

                            $logo_image_url = base_url().$school_details->logo;
                            list($width, $height, $type, $attr) = @getimagesize($logo_image_url);
                            if(isset($width))
                            {
                                $logo_image = $logo_image_url;
                            }  

                        } 


                        $fb_contents['image'] = $logo_image;
                        $ar_params = array(
                            "javascripts"           => $ar_js,
                            "css"                   => $ar_css,
                            "extra_head"            => $extra_js,
                            "title"                 => $str_title,
                            "description"           => $meta_description,
                            "keywords"              => $keywords,
                            "side_bar"              => $s_right_view,
                            "target"                => "index",
                            "fb_contents"           => $fb_contents,
                            "content"               => $s_content
                        );

                        $this->extra_params = $ar_params;
                    }
                       
                       
                        
                }
                else
                {
                     $this->show_404_custom();
                }    
                
            }
            else
            {
                $this->show_404_custom();
            }    
            
        }    
        
    }
    function show_404_custom()
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $data = array();
        $data['ci_key'] = "error_page";
        $data['ci_key_for_cover'] = "error_page";
        $s_content = $this->load->view('error_page',$data, true);        
        
        // User Data
        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

        $data['model'] = $this->get_free_user($user_id);

        $data['free_user_types'] = $this->get_free_user_types();

        $data['country'] = $this->get_country();
        $data['country']['id'] = $data2['model']->tds_country_id;

        $data['grades'] = $this->get_grades();

        $data['medium'] = $this->get_medium();

        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
        // User Data
        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();
               
        $s_right_view =  $this->load->view( 'right', $data, TRUE );  
        
        $str_title = getCommonTitle();
        
      
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "index",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    
    function download()
    {
        $str_f_path = filter_var($_GET['f_path'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_SPECIAL_CHARS);
        
        $finfo = new finfo(FILEINFO_MIME);
        $type = $finfo->file($str_f_path);
        
        $ar_str_f_path = end(explode('/', $str_f_path));
        
        header("Content-Disposition: attachment; filename=" . sanitize($ar_str_f_path));
        header("Content-Type: {$type}");
        header("Content-Length: " . filesize($str_f_path));
        readfile($str_f_path);
    }
    
    function index()
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "index";
        $data['ci_key_for_cover'] = "index";
        $data['s_category_ids'] = "0";
        
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        
        $s_content = $this->load->view('home',$data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
           
            
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);
               
               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }

            
        
        $str_title = getCommonTitle();
        
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
				
		$fb_contents['title'] = META_DESCRIPTION;
		$fb_contents['site_name'] = "Champs21";
		$fb_contents['description'] = META_DESCRIPTION;
		$fb_contents['type'] = "website";
		$fb_contents['url'] = "http://www.champs21.com/";
		$fb_contents['image'] = base_url()."styles/layouts/tdsfront/images/c-21.jpg";
		
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "index",
            "fb_contents"           => $fb_contents,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
         
    }
    
    function good_read( $s_folder_name = "" )
    {
        if (free_user_logged_in() )
        {
            $ar_js = array();
            $ar_css = array();
            $extra_js = '';

            $data = array();
            $this->load->model("user_folder");
            $i_user_id = get_free_user_session("id");
            $s_folder_name = ($s_folder_name=="")?"Unread":$s_folder_name;
            $ar_folder_id_data = $this->user_folder->get_folder_id($i_user_id, $s_folder_name, FALSE);
            $i_folder_id = $ar_folder_id_data->id;
            $data['folder_visible'] = $ar_folder_id_data->visible;
            $ar_user_folder = $this->user_folder->get_user_good_read_folder($i_user_id, 0, 30);          
            
            $arFdata = array();
            if($ar_folder_id_data->visible==1)
            {
                $i = 1;
                foreach ($ar_user_folder['data'] as $folder)
                {
                    if($i_folder_id == $folder->id)
                    {
                        $selected_fdata = $folder;
                    }
                    else
                    {
                        $arFdata[$i] = $folder;
                    }

                    $i++;
                }

                array_unshift($arFdata, $selected_fdata);
            }
            else
            {
                $arFdata = $ar_user_folder['data'];
            }
            $data['i_user_folder_count'] = $ar_user_folder['total'];
            $data['ar_user_folder'] = $arFdata;
             //$obj_post_data->good_read_single = $this->load->view( 'good_read_single', $data, TRUE );

            
            if($i_folder_id!=0)
            {
                $folder_data = $this->user_folder->get_user_good_read_post_count($i_user_id, $i_folder_id,$data['folder_visible'],$s_folder_name);
                $data['totalpost'] = $folder_data['totalpost'];
                $data['selected_folder_id'] = $i_folder_id;
                $data['selected_folder_name'] = $s_folder_name;
            }
            else
            {
                $data['totalpost'] = 0;
                $data['selected_folder_id'] = 1;
                $data['selected_folder_name'] = $s_folder_name;
            }
            

            $data['ci_key']    = "index";
            $data['ci_key_for_cover'] = "index";
            $data['s_category_ids'] = "0";
            
            $this->load->config("user_register");
            $data['dfolders'] = $this->config->config['free_user_folders'];

            $s_content = $this->load->view('good_read',$data, true);

            $s_right_view = "";
            $cache_name = "common/right_view";
            if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
            {


                $this->db->where('is_enabled', 1);
                $query = $this->db->get('widget');

                $obj_widgets = $query->result();

                if ($obj_widgets )
                {
                    $data2['post_details'] = 0;
                    $data2['widgets'] = $obj_widgets;
                    $data2['cartoon'] = true;
                   
                    // User Data
                    $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                    $data2['model'] = $this->get_free_user($user_id);

                    $data2['free_user_types'] = $this->get_free_user_types();

                    $data2['country'] = $this->get_country();
                    $data2['country']['id'] = $data2['model']->tds_country_id;

                    $data2['grades'] = $this->get_grades();

                    $data2['medium'] = $this->get_medium();

                    $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                    // User Data
                    
                    $obj_post = new Posts();
                    $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();
               
                   $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
                   $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
                }
            }
            else
            {
                $s_right_view = $s_widgets;
            }



            $str_title = getCommonTitle();


            $meta_description = META_DESCRIPTION;
            $keywords = KEYWORDS;
            $ar_params = array(
                "javascripts"           => $ar_js,
                "css"                   => $ar_css,
                "extra_head"            => $extra_js,
                "title"                 => $str_title,
                "description"           => $meta_description,
                "keywords"              => $keywords,
                "side_bar"              => $s_right_view,
                "target"                => "index",
                "fb_contents"           => NULL,
                "content"               => $s_content
            );

            $this->extra_params = $ar_params;
        }
        else 
        {
            redirect("/");
        }
         
    }
    
    function __inner( $i_category_id, $s_category_name, $b_popular = FALSE )
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $data = array();
        
        
        /**
         * Get Category and Sub-category first for a single post
         */
        $obj_parent_category = get_parent_category($i_category_id);
        
        if ( $obj_parent_category->id != $i_category_id )
        {
            $data['a_category_ids'] =  array($i_category_id);
        }
        
        if ( ! $obj_parent_category )
        {
            $this->show_404_custom();
        }
        
        $data['name'] = $obj_parent_category->name;
        
        $data['display_name'] = $obj_parent_category->display_name;
        
        $data['has_categories'] = FALSE;
        
        $data['category_name'] = $s_category_name;
     
        
        $obj_category = new Category_model( $i_category_id );
        
        $this->load->config("huffas");
        if ( ($category_config = $this->config->config[sanitize($s_category_name)])  )
        {
            if ( isset($category_config['hide_category']) )
            {
                $obj_child_categories = $obj_category->where("parent_id", $obj_parent_category->id)->where("status",1)->where("id NOT IN (" . $category_config['hide_category'] . ")")->order_by("priority","asc")->get();
                $data['obj_child_categories'] = $obj_child_categories;
            }
            else
            {
                $obj_child_categories = $obj_category->where("parent_id", $obj_parent_category->id)->where("status",1)->order_by("priority","asc")->get();
                $data['obj_child_categories'] = $obj_child_categories;
            }
        }
        else
        {
            $obj_child_categories = $obj_category->where("parent_id", $obj_parent_category->id)->where("status",1)->order_by("priority","asc")->get();
            $data['obj_child_categories'] = $obj_child_categories;
        }
        if ( count($obj_child_categories->all) > 0 )
        {
            $data['has_categories'] = TRUE;
        }
        
        if ( $b_popular )
        {
            $data['ci_key']    = "inner-popular";
            $data['ci_key_for_cover'] = sanitize($s_category_name) . "-popular";
        }
        else 
        {
            $data['ci_key']    = "inner";
            $data['ci_key_for_cover'] = sanitize($s_category_name);
        }
        $data['popular'] = $b_popular;
        $data['s_category_ids'] = $i_category_id;
        
        
        if($obj_parent_category->game_type==1)
        {
            $s_content = $this->load->view('inner_page_game',$data, true); 
        }
        elseif($obj_parent_category->game_type==2)
        {
            $s_content = $this->load->view('inner_page_video',$data, true); 
        }
        else
        {    
            $s_content = $this->load->view('inner_page_new',$data, true);
        }
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
           
            
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);

               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }

            
        
        $str_title = getCommonTitle();
        
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "index",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
         
    }
    
    function get_external_scripts( $type = "js" )
    {
        $s_script_content = file_get_contents($_GET['script']);
        $headers['Content-Length'] = strlen($s_script_content);
        $headers['Expires'] =  gmdate("D, d M Y H:i:s", time() + 86400 * 30) . " GMT";
        $headers['Cache-control'] =  "max-age=2592000";
        if ( $type == "js" )
        {
            $headers['Content-Type'] = 'application/x-javascript; charset=utf-8';
        }
        if ( $type == "css" )
        {
            $headers['Content-Type'] = 'text/css; charset=utf-8';
        }
        //$headers['Content-Encoding'] = "gzip";
        $headers['Vary'] = 'Accept-Encoding';
        $headers['ETag'] = time() + 86400 * 30;
        foreach ($headers as $name => $val) {
            header($name . ': ' . $val);
        }
        echo $s_script_content;
    }


    function process_post_view( $i_category_id, $obj_post_data, $b_layout = true)
    {
        $this->load->config("huffas");
        //TAKE THE GOOD READ FOLDER IN CASE USER LOGIN
        $obj_post_data->good_read_single = "";
        $obj_post_data->attempt = FALSE;
        if (free_user_logged_in() )
        {            
            $this->load->model("user_folder");
            $i_user_id = get_free_user_session("id");
            $ar_user_folder = $this->user_folder->get_user_good_read_folder($i_user_id);
            $folder_name = "Unread";
            $folder_data = $this->user_folder->get_folder_id($i_user_id, $folder_name);            
            $folder_id = $folder_data->id;
            $this->user_folder->set_unread_post_to_read($i_user_id, $obj_post_data->post_id,$folder_id);
            $data['i_user_folder_count'] = $ar_user_folder['total'];
            $data['ar_user_folder'] = $ar_user_folder['data'];
            $obj_post_data->good_read_single = $this->load->view( 'good_read_single', $data, TRUE );
            
            
            //IN CASE POST_TYPE = 4
            if ( $obj_post_data->post_type == 4 )
            {
                $this->db->where('user_id', $i_user_id, FALSE);
                $this->db->where('post_id', $obj_post_data->post_id);

                $this->db->from("user_gk_answers");       
                $query = $this->db->get();
                
                if ( $query->num_rows() > 0 )
                {
                    $row = $query->row();
                    $obj_post_data->attempt = TRUE;
                    $obj_post_data->is_correct = $row->is_correct;
                    $obj_post_data->user_answer = $row->user_answer;
                }
                
            }
            
        }
        /**
         * Get Category and Sub-category first for a single post
         */
        $obj_parent_category = get_parent_category($i_category_id);
        
        if ( ! $obj_parent_category )
        {
            $this->show_404_custom();
        }
        
        $obj_post_data->name = $obj_parent_category->name;
        
        $obj_post_data->display_name = $obj_parent_category->display_name;
        
        $obj_post_data->has_categories = FALSE;
        $i_category_id = $obj_parent_category->id;
        
        
        $obj_category = new Category_model( $i_category_id );
        $obj_child_categories = $obj_category->where("parent_id", $obj_category->id)->where("status",1)->order_by("priority","asc")->get();
        $obj_post_data->obj_child_categories = $obj_child_categories;
        if ( count($obj_child_categories->all) > 0 )
        {
            $obj_post_data->has_categories = TRUE;
        }
        
        
        $this->load->model('post','model');
        $obj_post = new Post_model();    
        //NOW GET ALL CATEGORY FOR THE POST
        if ( isset($obj_post_data->post_id) )
        {
            $categories_for_the_post = $obj_post->get_category_by_post($obj_post_data->post_id);
            $a_category_ids = array();
            if ( count($categories_for_the_post) >0 ) foreach($categories_for_the_post as $cate)
            {
                array_push($a_category_ids, $cate->category_id);
            }
            $obj_post_data->a_category_ids = $a_category_ids;
        }
        if ( ! $b_layout )
        {
            $this->layout_front = false;
        }
        error_reporting(0);
            
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        
        $obj_post_data->ci_key = "post";
        $obj_post_data->ci_key_for_cover = "post";
        
        $extra_js = '';        

        //$ar_fb = NULL;
        $b_show_post = TRUE;
        $s_target = "inner";
        
        
        $obj_post_data->post_show_publish_date = $this->config->config['post_show_publish_date'];
        $obj_post_data->post_show_updated_date = $this->config->config['post_show_updated_date'];
        $obj_post_data->has_outbrain = $this->config->config['has_outbrain'];
        $obj_post_data->has_disqus = $this->config->config['has_disqus'];
        
        $ar_js = array("scripts/post/jquery.social.share.2.0.js","scripts/post/scripts.js","scripts/post/jquery.social.share.2.0.js");
        $ar_css = array(
                "styles/layouts/tdsfront/css/post/social.css" => "screen"
        );
        
        if ( strlen($obj_post_data->lead_material) == 0 )
        {
            $s_image = getImageForFacebook($obj_post_data);
        }
        else
        {
            $s_image = $obj_post_data->lead_material;
        }
        
        
        $strContent = preg_replace('/<div (.*?)>Source:(.*?)<\/div>/', '', $obj_post_data->content);
        $strContent = preg_replace('/<div class="img_caption" (.*?)>(.*?)<\/div>/', '', $strContent);
        $strContent = strip_tags($strContent);
        $s_content = ( strlen($strContent) > 200 ) ? substr($strContent, 0, 200) . "..." : $strContent;


        $i_pos = stripos($s_image, "gallery/");
        if ( $i_pos !== FALSE )
        {
            $s_img_first = substr($s_image, 0, $i_pos + strlen("gallery/"));
            $s_img_last = substr($s_image, $i_pos + strlen("gallery/"), strlen($s_image));
            $s_image = $s_img_first . "facebook/" . $s_img_last;
            $s_image = str_replace("http://bd.", "http://www.", $s_image);
        }

        $url_main = create_link_url(NULL, $obj_post_data->headline,$obj_post_data->post_id);
        
        $only_link = str_replace(base_url(), "", $url_main);
        
        $only_link_encoded = urlencode($only_link);
        
        $encoded_url = base_url().$only_link_encoded;
        $ar_fb = array(
            "type"          => "website",
            "site_name"     => WEBSITE_NAME,
            "title"         => $obj_post_data->headline,
            "image"         => $s_image,
            "url"           => $encoded_url,
            "description"   => trim($s_content)
        );

        $obj_post_data->fb_desc = trim($s_content);
        
        $obj_post_data->discus_short_name = $this->config->config['disqus_short_name'];
        
        $str_title = (!empty($obj_post_data)) ? getCustomTitle($obj_post_data): getCommonTitle();
        
        $obj_post->updateCount($obj_post_data->post_id);
        
        $data['related_tags'] = $obj_post->get_related_tags($obj_post_data->post_id);
        
        $related_news = $obj_post->get_related_news($obj_post_data->post_id);
        
        $data['all_attachment'] = $obj_post->get_related_attach($obj_post_data->post_id);
        
        $obj_post_data->has_related = FALSE;
        
        if (is_array($related_news))
        {
            $obj_post_data->has_related = TRUE;
        }
        foreach ($related_news as &$r_news)
        {
            if ( ! isset( $r_news->content ) )
            {
                $link = $r_news->new_link;
                $headline = $r_news->title;
                $related_news_id = str_replace(base_url() . sanitize($headline) . "-", "", $link);

                $obj_related_news = $obj_post->get_by_id($related_news_id);
                $ar_news_data = getFormatedContentAll($obj_related_news, 150);

                $r_news->lead_material = $ar_news_data['lead_material'];
                $r_news->content = $ar_news_data['content'];
                $r_news->image = $ar_news_data['image'];
            }
        }
        
        //Get Language for the post
        $i_lang_post_id  = ( $obj_post_data->referance_id > 0 ) ? $obj_post_data->referance_id : $obj_post_data->post_id;
        $s_lang = $obj_post->get_available_language( $i_lang_post_id, $obj_post_data->other_language, $obj_post_data->referance_id, $obj_post_data->language);
        $this->db->set_dbprefix('tds_');
        $data['s_lang'] = $s_lang;
        
        
        if ( $obj_post_data->referance_id > 0 )
        {
            $this->db->select("id, headline, referance_id");
            $this->db->from("post");
            $this->db->where("id", $obj_post_data->referance_id, FALSE);
            $post_data = $this->db->get()->row();
            $data['main_post_id'] = $post_data->id;
            $data['main_headline'] = $post_data->headline;
            $data['main_referance_id'] = $post_data->referance_id;
        }
        else
        {
            $data['main_post_id'] = $obj_post_data->post_id;
            $data['main_headline'] = $obj_post_data->headline;
            $data['main_referance_id'] = $obj_post_data->referance_id;
        }
        
        $data['related_news'] = $related_news;

        $data['post_images'] = $obj_post->get_related_gallery($obj_post_data->post_id, array(1,5));
        
        $data['post_videos'] = $obj_post->get_post_videos($obj_post_data->post_id);
        
        if ( !empty($obj_post_data->attach) && file_exists($obj_post_data->attach) ){
            $data['attachment'] = $obj_post_data->attach;
        }
        
        $meta_description = $obj_post_data->meta_description;
        
        $keywords = $obj_post->get_keywords($obj_post_data->post_id);
        
        $obj_post_data->b_layout = $b_layout;
            
        /* $data['related_doc'] = $obj_post->get_related_gallery(); */

        //$obj_post_data->related_news_content = $this->load->view( 'related_news', $data, TRUE );
        $obj_post_data->disqus_content = $this->load->view( 'disqus', $obj_post_data, TRUE );

        $data1['outbrain_url'] = $this->config->config['outbrain_url'];
        $obj_post_data->outbrain_content = $this->load->view( 'outbrain', $data1, TRUE );

        $obj_post_data->resource  = $obj_post->get_related_gallery($obj_post_data->post_id);

        if ( $obj_post_data->post_type == 4 )
        {
            $obj_post_data->has_previous = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'previous', $obj_post_data->published_date);
            $obj_post_data->has_next_news = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'next', $obj_post_data->published_date);
        }
        else
        {
            $obj_post_data->has_previous = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'previous');
            $obj_post_data->has_next_news = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'next');
        }

        if ( $obj_post_data->has_previous )
        {
            $obj_post_data->previous_news_link = $obj_post->news_link($obj_post_data->post_id, $a_category_ids, 'previous');
        }

        if ( $obj_post_data->has_next_news )
        {
            $obj_post_data->next_news_link = $obj_post->news_link($obj_post_data->post_id, $a_category_ids, 'next');
        }
        if ( $obj_post_data->post_type == 4 )
        {
            $obj_post_data->has_more = $obj_post->has_news($obj_post_data->post_id, $a_category_ids, 'none', $obj_post_data->published_date);
        }
        else
        {
            $obj_post_data->has_more = $obj_post->has_news($obj_post_data->post_id, $a_category_ids);
        }


        $dom = new DOMDocument;
        $dom->loadHTML($obj_post_data->content);
        $xp = new DOMXpath($dom);

        $b_found = false;
        foreach( $xp->query('//*[@style]') as $node) 
        {

            if ( $node->getAttribute('class') == "related_news_on_post" )
            {
                $related = (string) $dom->saveXML($node);
                $style = $node->getAttribute('style');
                $b_found = true;
                break;
            }
        }

        $s_related_news = "";
        $s_related_news_content = $this->load->view( 'related_news', $data, TRUE );
        if ( ! $b_found &&  is_array($data['related_news'])  )
        {
            $style = "width: 220px; height: 200px; float:right;";
        }
        if ( is_array($data['related_news']) )
        {
            $s_related_news = '<div class="related_news" style="' . $style . '">' . $s_related_news_content . '</div><p>';
        }

        if ( ! $b_found &&  is_array($data['related_news'])  )
        {
            $obj_post_data->related_news_append = '<div id="related_news_1" class="related_news_parent">' . $s_related_news_content . '</div>';
        }

        if(isset($obj_post_data->video_file) && $obj_post_data->video_file!="" && $obj_post_data->video_file!=null)
        {
            $video_file = trim($obj_post_data->video_file);
            if($video_file!="")
            {
                $s_content = $this->load->view( 'post_videos', $obj_post_data, TRUE );
            } 
            else
            {
                $s_content = $this->load->view( 'post', $obj_post_data, TRUE );
            }
        }  
        else
        {
           $s_content = $this->load->view( 'post', $obj_post_data, TRUE ); 
        }
        
        
        if ( $b_found )
        {
            $s_content = str_replace($related, $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace("/>", " />", $related), $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace(" />", "/>", $related), $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace("/>", "", $related), $s_related_news, $s_content);
            $s_content = str_replace(str_ireplace(" />", "", $related), $s_related_news, $s_content);
        }
        
        if ( $b_layout )
        {
            $s_right_view = "";
            $cache_name = "common/right_view";
            if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
            {


                $this->db->where('is_enabled', 1);
                $query = $this->db->get('widget');

                $obj_widgets = $query->result();

                if ($obj_widgets )
                {
                    $data2['post_details'] = 0;
                    $data2['widgets'] = $obj_widgets;
                    $data2['cartoon'] = true;
                   
                    // User Data
                    $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

                    $data2['model'] = $this->get_free_user($user_id);

                    $data2['free_user_types'] = $this->get_free_user_types();

                    $data2['country'] = $this->get_country();
                    $data2['country']['id'] = $data2['model']->tds_country_id;

                    $data2['grades'] = $this->get_grades();

                    $data2['medium'] = $this->get_medium();

                    $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
                    // User Data
                    
                    $obj_post = new Posts();
                    $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();
               
                   $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
                   $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
                }
            }
            else
            {
                $s_right_view = $s_widgets;
            }

            if(!isset($str_title) && $ci_key!="index" && $ci_key!="")
            {
                $str_title = ucfirst($ci_key)." News | ".WEBSITE_NAME;
            }
            else
            {    
                $str_title = (isset($str_title)) ? $str_title: getCommonTitle();
            }

            $ar_params = array(
                "javascripts"           => $ar_js,
                "css"                   => $ar_css,
                "extra_head"            => $extra_js,
                "description"           => $meta_description,
                "keywords"              => $keywords,
                "title"                 => $str_title,
                "side_bar"              => $s_right_view,
                "target"                => $s_target,
                "fb_contents"           => $ar_fb,
                "ci_key"                => sanitize($obj_post_data->name),
                "content"               => $s_content
            );
            $this->extra_params = $ar_params;
        }
        else
        {
            print $s_content;
        }
    }
    
    function delete_cache()
    {
        if ( isset($_GET['cache']) )
        {
            $cache_prefix = strtoupper(str_ireplace("*", "",$_GET['cache'] ));
            print "<h1>" . $cache_prefix . "</h1><br /><Br />";
            $ar_cache = $this->cache->cache_info();
            foreach(  $ar_cache['cache_list'] as $cache_list)
            {
                if (strpos($cache_list['info'], $cache_prefix) !== FALSE )
                {
                    print "Deleting cache ..." . $cache_list['info'] . "<Br />";
                    $this->cache->delete($cache_list['info']);
                }
            }
            
            $cache_prefix = strtolower(str_ireplace("*", "",$_GET['cache'] ));
            print "<h1>" . $cache_prefix . "</h1><br /><Br />";
            $ar_cache = $this->cache->cache_info();
            foreach(  $ar_cache['cache_list'] as $cache_list)
            {
                if (strpos($cache_list['info'], $cache_prefix) !== FALSE )
                {
                    print "Deleting cache ..." . $cache_list['info'] . "<Br />";
                    $this->cache->delete($cache_list['info']);
                }
            }
            
            $cache_prefix = str_ireplace("*", "",$_GET['cache'] );
            $cache_prefix = strtoupper(substr($cache_prefix, 0, 1)) . substr($cache_prefix, 1, strlen($cache_prefix));
            print "<h1>" . $cache_prefix . "</h1><br /><Br />";
            $ar_cache = $this->cache->cache_info();
            foreach(  $ar_cache['cache_list'] as $cache_list)
            {
                if (strpos($cache_list['info'], $cache_prefix) !== FALSE )
                {
                    print "Deleting cache ..." . $cache_list['info'] . "<Br />";
                    $this->cache->delete($cache_list['info']);
                }
            }
        }
        else 
        {
            return false;
        }
        $this->layout_front = false;
    }
            
    function socialPage()
    {
    
        
        $ar_segmens = $this->uri->segment_array();
        $image_url =  $ar_segmens[2];
   
        $this->layout_front = false;
        $this->db->where("material_url",str_replace("-d-","/",$image_url));
        $this->db->limit(1);
        $data=$this->db->get("materials")->row();
        
        $this->load->view("social_page_like",$data); 
        
    }
    
    function good_read_add_folder(  )
    {
       if (free_user_logged_in() )
       {
            $this->layout_front = FALSE;

            $data['user_id'] = get_free_user_session("id");

            $this->load->view("add_folder",$data); 
       }
       else
       {
           redirect("/");
       }
    }
    
    function print_post()
    {
        
        $b_news_link = false;
        $b_category_found = false;
        $ar_segmens = $this->uri->segment_array();
        $b_already_cached = false;
        $ar_ids = explode("-", $ar_segmens[2]);
        $ar_segmens[2] = $ar_ids[count($ar_ids) - 1];
        if ( isset($ar_segmens[2]) )  
        { 
            
            $i_post_id_md5 = $ar_segmens[2];

            $obj_post = new Post_model();
            $b_is_md5 = ( is_numeric($i_post_id_md5) ) ? FALSE : TRUE;
            if ( ($obj_post_data = $obj_post->has_post($i_post_id_md5, $b_is_md5)) )
            {
                $b_news_link = TRUE;
            }
        } 
        
        if ( $b_news_link )  
        {
            $this->process_post_view('', $obj_post_data, false);
        }
    }

    function _remap($method, $params=array())
    {
        $ar_segments_to_execute = array('good-read');
        $s_current_module = $this->load->get_current_module();
        
        $s_controller_name = $this->router->fetch_class();
        
        $ar_mod_controllers = array($s_current_module, $s_controller_name);
        
        $ar_mod = array('admin','ad');
        $funcs = get_class_methods($this);
        
        $ar_segmens = $this->uri->segment_array();
        
        $i_count_segments = count($ar_segmens);
        
        if ( $i_count_segments > 0 )
        {
            if ( $i_count_segments == 1 )
            {
                $s_controller = $this->uri->segment(1);
                if ( $s_controller == $s_current_module )
                {
                    redirect("/");
                }
                else if ( $s_controller == $s_controller_name )
                {
                    redirect("/");
                }
            }
            else
            {
                $s_controller = $this->uri->segment($i_count_segments);
            }
            
            if (in_array($s_controller, $ar_mod) )
            {
                $this->show_404_custom();
            }
            $method = $s_controller;
        }
        if(in_array($method, $funcs))
        { 
            // We are trying to go to a method in this class
            return call_user_func_array(array($this, $method), $params);
        }
        else if(in_array($this->uri->segment(1), $funcs))
        {
            return call_user_func_array(array($this, $this->uri->segment(1)), $params);
        }        
        else
        {
            $method = str_ireplace("-", "_", $method);
            if(in_array($method, $funcs))
            { 
                // We are trying to go to a method in this class
                return call_user_func_array(array($this, $method), $params);
            }
            
            if ( count($ar_segmens) > 1 )
            {
                $ar_segments_to_check = $ar_segmens[1];
                if (in_array($ar_segments_to_check, $ar_segments_to_execute) )
                {
                    $params = array("s_folder_name" => $ar_segmens[2]);
                    $method = str_ireplace("-", "_", $ar_segments_to_check);
                    if(in_array($method, $funcs))
                    { 
                        // We are trying to go to a method in this class
                        return call_user_func_array(array($this, $method), $params);
                    }
                }
            }
            
            $this->load->model('post');
            
            
            $obj_category = new Category_model();
            $i_parent_category_id = 0;
            if ( $i_count_segments > 1 )
            {
                //Now Come on to the multiple segments, let say we have only categories and news will be passed through the remap
                //Give me hell Yeah: Huffas
                $i_count = count($ar_segmens) - 1;
                $s_category = array_pop($ar_segmens);
                
                $i_parent_category_id = check_categories_recursive( $ar_segmens );
                if ( ! $i_parent_category_id )
                {
                    //TRY FOR NEWS POST
                    $s_news_category = array_pop($ar_segmens);
                    
                    $s_data = $s_category;
                    
                    
                    if ( ! empty($ar_segmens) )
                    {
                        
                        $i_parent_category_id = check_categories_recursive( $ar_segmens );
                        if ( ! $i_parent_category_id )
                        {
                            
                            $this->show_404_custom();
                        }
                    }
                    
                    $news_title = explode("-", $s_news_category);
                    $i_post_id = $news_title[count($news_title) - 1];
                    
                    $lang = "";
                    if (strlen($s_data) > 0 )
                    {
                        $lang = $s_data;
                    }
                    
                    $a_post_id_pop = array_pop($news_title);
                    $s_headline_sanitize = implode("-", $news_title);
                    $s_headline = ucwords(unsanitize($s_headline_sanitize));

                    
                    if ( $i_parent_category_id == 0 )
                    {
                      
                        $a_post_params = array("tds_post.referance_id" => $i_post_id, "tds_post.language" => $lang,"ignore_post_type"=>true );
                    }
                    else
                    {
                        $a_post_params = array(
                                            "tds_post.id"       => $i_post_id, 
                                            "category.id"       => $i_parent_category_id 
                        );
                    }

                    $a_post = $this->post->gePostNews($a_post_params);
                   
                    if ( !is_array($a_post) )
                    {
                        if($i_parent_category_id == 0 )
                        {
                            $a_post_params = array(
                                "tds_post.id"       => $i_post_id
                            );
                            $a_post = $this->post->gePostNews($a_post_params);
                        }
                        if(!is_array($a_post))
                        {
                            $this->show_404_custom();
                        }
                        else
                        {
                            $obj_post_data = $a_post['data'][0];
                            $this->process_post_view($obj_post_data->id, $obj_post_data);
                            return ;
                        }    
                        
                        
                    }
                    else
                    {
                        
                        $obj_post_data = $a_post['data'][0];
                        $this->process_post_view($obj_post_data->id, $obj_post_data);
                        return ;
                        
                    }
                }
            }
            else
            {
                $s_category = $ar_segmens[1];
            }
            $b_popular = FALSE;
            if ( $s_category == "popular" )
            {
                $b_popular = TRUE;
                $ar_segmens = $this->uri->segment_array();
                $s_category = $ar_segmens[count($ar_segmens) - 1];
                if ( ! $i_parent_category_id )
                {
                    if (strlen($s_category) < 4 )
                    {
                        $s_category .= ".";
                        $this->db->where('name', $s_category);
                        $this->db->where("status",1);
                        $query = $this->db->get('categories');
                        $obj_cate = $query->row();
                        $i_parent_category_id = $obj_cate->id;
                    }
                }
                else
                {
                    $this->db->where('id', $i_parent_category_id);
                    $this->db->where("status",1);
                    $query = $this->db->get('categories');
                    $obj_cate = $query->row();
                }
                $this->__inner($i_parent_category_id, $obj_cate->name, TRUE);
                return ;
            }
            $b_category_found = false;
            //First Let see if it is a Category
            
            $cate_name = ucwords(unsanitize($s_category));
            if (strlen(ucwords(unsanitize($s_category))) < 4 )
            {
                $cate_name .= ".";
            }
            $this->db->where('name', $cate_name);
            $this->db->where("status",1);
            if ( $i_parent_category_id == 0 )
            {
                $this->db->where('(parent_id = 0 OR parent_id IS NULL)');
            }
            else
            {
                $this->db->where("parent_id", $i_parent_category_id, FALSE);
            }
            $query = $this->db->get('categories');
            
            $s_category_name = unsanitize($s_category,'-');
            if( $query->num_rows() )
            {
                $obj_cate = $query->row();
                $b_category_found = true;
            }

            if ( ! $b_category_found )
            {
                
                //TRY FOR NEWS POST
                $news_title = explode("-", $s_category);
                $i_post_id = $news_title[count($news_title) - 1];
                
                $a_post_id_pop = array_pop($news_title);
                $s_headline_sanitize = implode("-", $news_title);
                $s_headline = ucwords(unsanitize($s_headline_sanitize));
                
                
                
                if ( $i_parent_category_id == 0 )
                {
                    $a_post_params = array("tds_post.id" => $i_post_id );
                }
                else
                {
                    $a_post_params = array(
                                        "tds_post.id"       => $i_post_id, 
                                        "category.id"       => $i_parent_category_id 
                    );
                }
                
                $a_post = $this->post->gePostNews($a_post_params);
                
                if ( !is_array($a_post) )
                {
                    //print urldecode($s_headline_sanitize) . "   " . sanitize($obj_post_data->headline);
                    $this->show_404_custom();
                }
                else
                {
                    $obj_post_data = $a_post['data'][0];
                    
//                    if ( urldecode($s_headline_sanitize) == sanitize($obj_post_data->headline) )
//                    {
                        $b_layout = TRUE;
                        $ar_segmens = $this->uri->segment_array();
                        foreach ($ar_segmens as $segment)
                        {
                            if ( $segment == "print_post" )
                            {
                                $b_layout = FALSE;
                                break;
                            }
                        }
                        $this->process_post_view($obj_post_data->id, $obj_post_data, $b_layout);
                    //}
//                    else
//                    {
//                        $this->show_404_custom();
//                    }
                }
            } 
            else
            {
                $this->__inner($obj_cate->id, $obj_cate->name);
            }
        }
    }
    
    function register_user(){
        
        $api_registration = FALSE;
        
        if( $this->input->is_ajax_request() && free_user_logged_in() ){
            $data['logged_in'] = free_user_logged_in();
            echo json_encode($data);
            exit;
        }
        
        $this->load->helper('form');
        
        $free_user = new Free_users();
        
        if($this->input->is_ajax_request()){
            
            $_POST['nick_name']          = filter_var($_POST['data']['nick_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $_POST['first_name']         = filter_var($_POST['data']['first_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $_POST['last_name']          = filter_var($_POST['data']['last_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            
            if( isset($_POST['data']) ) {
                $_POST['email']              = filter_var($_POST['data']['email'], FILTER_SANITIZE_EMAIL);
            }  else {
                $_POST['email']              = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            }
            
            if( isset($_POST['data']['location']) && !empty($_POST['data']['location']) ){
                $_POST['district']          = filter_var($_POST['data']['district'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['country']         = filter_var($_POST['data']['country'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['location']          = filter_var($_POST['data']['location'], FILTER_SANITIZE_SPECIAL_CHARS);
            }
            
            if( isset($_POST['data']['gender']) && !empty($_POST['data']['gender']) ){
                $_POST['gender']          = filter_var($_POST['data']['gender'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['gender'] = ($_POST['gender'] == 'female') ? '0' : '1';
            }
            
            if( isset($_POST['data']['dob']) && !empty($_POST['data']['dob']) ){
                $_POST['dob']          = filter_var($_POST['data']['dob'], FILTER_SANITIZE_SPECIAL_CHARS);
                $_POST['dob'] = date('Y-m-d', strtotime($_POST['dob']));
            }
            
            if( isset($_POST['data']['profile_image']) && !empty($_POST['data']['profile_image']) ){
                $_POST['profile_image']      = filter_var($_POST['data']['profile_image'], FILTER_VALIDATE_URL);
            }
            
            // Google Registration Starts
            if($_POST['data']['source'] == 'g'){
                $_POST['gl_profile_id']      = $_POST['data']['id'];
                $_POST['google_profile_url'] = filter_var($_POST['data']['profile_url'], FILTER_VALIDATE_URL);
            }
            // Google Registration Ends
            
            // Facebook Registration Starts
            if($_POST['data']['source'] == 'f'){
                $_POST['fb_profile_id']      = $_POST['data']['id'];
                $_POST['fb_profile_url'] = filter_var($_POST['data']['profile_url'], FILTER_VALIDATE_URL);
            }
            // Facebook Registration Ends
            
            if($_POST['data']['source'] == 'g' || $_POST['data']['source'] == 'f'){
                $api_registration = TRUE;
            }
            
            unset($_POST['data']);
        }
        
        if(isset($_POST) && !empty($_POST)){
            
            foreach ($_POST as $key => $value) {
                $free_user->$key = $value;
            }
            
            $free_user->grade_ids = implode(',', $free_user->grade_ids);
            
            if($api_registration) {
                
                $free_user->skip_validation();
                
                if( !$free_user->_email_unique() ) {
                    
                    $data['errors'][] = 'The Email Address you supplied is already taken.';
                    
                    $data['registered'] = FALSE;
                    $data['logged_in'] = FALSE;

                    echo json_encode($data);
                    exit;
                }
            }
            
            if($free_user->save()){
                
                ($api_registration) ? $free_user->api_login() : $free_user->login();
                
                $this->set_user_session($free_user);
                
                $this->create_free_user_folders();
                
                $data['registered'] = true;
                
            }else{
                
                $data['errors'] = $free_user->error->all;
                
                $this->session->sess_destroy();
                
                $data['registered'] = false;
                
            }
            
        }
        
        $data['logged_in'] = free_user_logged_in();
        
        echo json_encode($data);
        exit;
        
    }
    
    function login_user() {
        
        if( $this->input->is_ajax_request() && free_user_logged_in() ){
            $user_info['logged_in'] = free_user_logged_in();  
            echo json_encode($user_info);
            exit;
        }
        
        $this->load->helper('form');
        
        $api_login = FALSE;
        
        $free_user = new Free_users();
        
        $this->load->config("tds");
        
        if($this->input->is_ajax_request()){
            
            $source = '';
            if( isset($_POST['data']['source']) && !empty($_POST['data']['source']) ){
                
                $_POST['email'] = filter_var($_POST['data']['email'], FILTER_SANITIZE_EMAIL);
                
                if($_POST['data']['source'] == 'g'){
                    $source = 'g';
                    $_POST['gl_profile_id'] = filter_var($_POST['data']['id'], FILTER_SANITIZE_NUMBER_INT);
                }
                
                if($_POST['data']['source'] == 'f'){
                    $source = 'f';
                    $_POST['fb_profile_id'] = filter_var($_POST['data']['id'], FILTER_SANITIZE_NUMBER_INT);
                }
                
                $api_login = TRUE;
                unset($_POST['data']);
            }
            
        }
        
        if(isset($_POST) && !empty($_POST)){
            
            $free_user->email = $this->input->post('email');
            
            if($api_login){
                
                if( isset($_POST['fb_profile_id']) && !empty($_POST['fb_profile_id']) ){
                    $free_user->fb_profile_id = $this->input->post('fb_profile_id');
                }
                
                if( isset($_POST['gl_profile_id']) && !empty($_POST['gl_profile_id']) ){
                    $free_user->gl_profile_id = $this->input->post('gl_profile_id');
                }
                
                if ( $obj_free_user =  $free_user->api_login($source) ) {
                    
                    $this->set_user_session($obj_free_user);
                    
                }  else {
                    
                    $data['errors'] = $free_user->error->all;
                    
                    $this->session->unset_userdata($array_items);
                    $this->session->sess_destroy();
                }
                
            }  else {
                
                $free_user->password = $this->input->post('password');
            
                if ($free_user->login()) {
                    
                    $this->set_user_session($free_user);
                    
                }  else {
                    
                    $data['errors'] = $free_user->error->all;
                    
                    $this->session->unset_userdata($array_items);
                    $this->session->sess_destroy();
                    
                }
            }
        }
        
        $data['logged_in'] = free_user_logged_in();
        
        echo json_encode($data);
        exit;
    }
    
    function update_profile(){
        
        $user_id = '0';
        
        if( free_user_logged_in() ){
            $user_id = get_free_user_session('id');
        }else{
            $data['logged_in'] = FALSE;
            $data['registered'] = FALSE;
            echo json_encode($data);
            exit;
        }
        
        if($this->input->is_ajax_request()){
            
            $this->load->helper('form');

            $free_user = new Free_users($user_id);
            
            if (isset($_POST) && !empty($_POST)) {

                foreach ($_POST as $key => $value) {
                    if(!empty($value)){
                        $free_user->$key = $value;
                    }
                }
                
                $day = $free_user->dob_day;
                if ( strlen($day) < 2 && !empty($day) ) {
                    $day = '0'.$day;
                }
                
                $month = $free_user->dob_month;
                if ( strlen($month) < 2 && !empty($month) ) {
                    $month = '0'.$month;
                }
                
                $year = $free_user->dob_year;
                
                $dob = NULL;
                if ( !empty($free_user->dob_day) && !empty($free_user->dob_month) && !empty($free_user->dob_year) ) {
                    $dob = $year . '-' . $month . '-' . $day;
                }
                
                if ( !empty($dob) ) {
                    $free_user->dob = $dob;
                }
                
                unset($free_user->dob_day);
                unset($free_user->dob_month);
                unset($free_user->dob_year);
                
                if($_POST['gender'] == '0'){
                    $free_user->gender = '0';
                }
                
                if($_POST['gender'] == '1'){
                    $free_user->gender = '1';
                }
                
                $free_user->grade_ids = implode(',', $free_user->grade_ids);
                
                $free_user->skip_validation();
                
                if ( $free_user->save() ) {
                    $this->set_user_session($free_user);
                    $data['success'] = TRUE;
                } else {
                    
                    $errors = $free_user->error->all;
                
                    foreach ($errors as $error) {
                        $data['errors'][] = $error;
                    }
                }
            }
        }
        
        echo json_encode($data);
        exit;
    }
    
    function schoolsearch()
    {
        //echo $this->input->get('str');
        //echo $this->input->post('name');
        //echo $this->input->post('division');
        //echo $this->input->post('level');

        $this->db->select('*');
        $this->db->from('tds_school');
        ($this->input->post('name') != "") ? $this->db->like('name', $this->input->post('name'), 'after') : '';
        ($this->input->post('district') != "") ? $this->db->or_like('division', $this->input->post('division'), 'after') : '';
        ($this->input->post('level') != "") ? $this->db->or_like('level', $this->input->post('level'), 'after') : '';
        ($this->input->get('str') != "") ? $this->db->like('name', $this->input->get('str'), 'after') : '';
        $query = $this->db->get();
        $data['schooldata'] = $query->result_array();


        $data['ci_key'] = 'schoolsearch';
        $s_content = $this->load->view('schoolsearch', $data, true);

        // User Data
        $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;

        $data['model'] = $this->get_free_user($user_id);

        $data['free_user_types'] = $this->get_free_user_types();

        $data['country'] = $this->get_country();
        $data['country']['id'] = $data2['model']->tds_country_id;

        $data['grades'] = $this->get_grades();

        $data['medium'] = $this->get_medium();

        $data['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
        // User Data

        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->user_preference_tree_for_pref();

        //has some work in right view
        $s_right_view = $this->load->view('right', $data, TRUE);
        //echo "<pre>";
        //print_r($data);

        $str_title = "School Search";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schoolsearch",
            "fb_contents" => NULL,
            "content" => $s_content
        );

        $this->extra_params = $ar_params;
    }
    
    function search()
    {
        
        $q = '';
        
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $q = $this->input->get('s');
        }
        
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "search";
        $data['ci_key_for_cover'] = "search";
        $data['s_category_ids'] = "0";
        $data['q'] = $q;
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        
        $s_content = $this->load->view('newssearch', $data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);
               
               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree_for_pref();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }
        
        $str_title = getCommonTitle();
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "search",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
         
    }

    function logout_user()
    {
        $array_items = array('free_user' => array());
        $this->session->unset_userdata($array_items);
        $this->session->sess_destroy();
        redirect(base_url());
    }
    
    private function set_user_session($obj_user){
        
        set_user_sessions($obj_user);
    }
    
    function upload_profile_image() {
        
        $this->load->library('upload');
        
        if (!empty($_FILES['profile_image']['name'])) {

            $user_data = get_free_user_session();

            $free_user = new Free_users($user_data['id']);

            $profile_image_ext = end(explode('.', $_FILES['profile_image']['name']));

            $config_profile['upload_path'] = 'upload/free_user_profile_images/';
            $config_profile['allowed_types'] = 'jpg|jpeg|JPG|JPEG|png|PNG';
            $config_profile['max_size'] = '512';
            $config_profile['max_width'] = '2000';
            $config_profile['max_height'] = '1600';
            $config_profile['is_image'] = TRUE;
            $config_profile['file_name'] = time().'_'.$free_user->id;
            $config_profile['overwrite'] = TRUE;

            $this->upload->initialize($config_profile);

            if ($this->upload->do_upload('profile_image')) {

                $file_path = base_url($config_profile['upload_path'] . $config_profile['file_name'] . '.' . $profile_image_ext);
                $free_user->profile_image = $file_path;

                $free_user->skip_validation();
                $free_user->save();

                unset($_FILES['profile_image']['name']);
                
                $this->set_user_session($free_user);
                
                echo $free_user->profile_image;
                exit;
            } else {
                echo 0;
                exit;
            }
        } else {
            echo 0;
            exit;
        }
        
    }
    
    function set_preference(){
        
        $user_id = '0';
        
        if(free_user_logged_in()){
            $user_id = get_free_user_session('id');
        }else{
            echo -1;
            exit;
        }
        
        if($this->input->is_ajax_request()){
            
            if( isset($_POST) && !empty($_POST['category']) && count($_POST['category'])>0 ){
                $obj_category = new Category();
                $array = array('status' => 1,'show'=>1);
                $obj_category->where($array)->order_by('name', 'asc')->get();
                $all_category_selected = true;
                if (count($obj_category) > 0)
                {
                    foreach($obj_category as $value)
                    {
                        if(!in_array($value->id, $_POST['category']))
                        {
                            $all_category_selected = false;
                            break;
                        }
                        
                    }    
                }
                
                if($all_category_selected === false)
                {    
                    $str_categories = implode(',', $this->input->post('category'));

                    $user_pref_mod = new Free_user_preference;
                    $user_pref = $user_pref_mod->get_by_user_id($user_id);

                    if( !$user_pref ){
                        $user_pref_mod->free_user_id = $user_id;
                        $user_pref_mod->category_ids = $str_categories;

                        if($user_pref_mod->save()){
                            echo 1;
                        }else{
                            echo 0;
                        }

                    }else{
                        $user_pref->category_ids = $str_categories;

                        if($user_pref->save()){
                            echo 1;
                        }else{
                            echo 0;
                        }
                    }
                }
                else
                {
                    $user_pref_mod = new Free_user_preference;
                    $user_pref = $user_pref_mod->get_by_user_id($user_id);
                    if($user_pref)
                    {
                        $user_pref_delete =  new Free_user_preference($user_pref->id);
                        $user_pref_delete->delete();
                    }
                    echo 1;
                }    
            }
        }
        exit;
    }
    
    function testbar()
	{
		
		$s_content = $this->load->view('sidebar');
		$data['category_tree'] = array();

        //has some work in right view
        $s_right_view = "";
		$s_content = $this->load->view('sidebar',$data, TRUE);
        //echo "<pre>";
        //print_r($data);

        $str_title = "School Search";
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts" => $ar_js,
            "css" => $ar_css,
            "extra_head" => $extra_js,
            "title" => $str_title,
            "description" => $meta_description,
            "keywords" => $keywords,
            "side_bar" => $s_right_view,
            "target" => "schoolsearch",
            "fb_contents" => NULL,
            "content" => $s_content
        );
		$this->extra_params = $ar_params;
    }
    
    private function get_country() {
        
        $country = new Country();
        $country = $country->formatCounrtyForDropdown($country->get());
        
        return $country;
    }
    
    private function get_grades() {
        
        $grades = new Grades();
        return $grades->getActiveGrades();
    }
    
    private function get_free_user($id = null) {
        return (!empty($id)) ? new Free_users($id) : new Free_users();
    }
    
    private function get_medium() {
        $this->load->config("user_register");
        return $this->config->config['medium'];
    }
    
    private function get_free_user_types() {
        $this->load->config("user_register");
        return $this->config->config['free_user_types'];
    }
    
    private function get_school_join_user_types() {
        $this->load->config("user_register");
        return $this->config->config['join_user_types'];
    }
    
    private function create_free_user_folders() {
        
        $this->load->config("user_register");
        
        $ar_data['folders'] = $this->config->config['free_user_folders'];
        $ar_data['user_id'] = get_free_user_session('id');
        
        $this->load->model("user_folder", 'ur_mod');
        
        return $this->ur_mod->created_good_read_folders($ar_data);
    }
    
    public function translate_tts()
    {
        $this->layout_front = false;
        $s_music_file = $this->input->get("q");
        $s_music_file = base64_decode($s_music_file);
        
        $str_music_dir = FCPATH . 'games-old/var/upload/spellingbee/' . $s_music_file . '.mp3';
        
        $b_file_exist = false;
        if ( file_exists($str_music_dir) && is_file($str_music_dir) && is_readable($str_music_dir) )
        {
            $b_file_exist = true;
        }
        else
        {
            $str_music_dir = FCPATH . 'upload/spellingbee/' . $s_music_file . '.mp3';
            if (file_exists($str_music_dir) && is_file($str_music_dir) && is_readable($str_music_dir)  )
            {
                $b_file_exist = true;
            }
        }
        if ( $b_file_exist )
        {
            header("Content-Length: " . filesize($str_music_dir));
            ob_clean();
            flush();
            @readfile($str_music_dir);
        }
    }
    public function about_us()
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "aboutus";
        $data['ci_key_for_cover'] = "aboutus";
        $data['s_category_ids'] = "0";
        
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        
        $s_content = $this->load->view('aboutus',$data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
           
            
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);
               
               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }

        $str_title = WEBSITE_NAME . " | About Us";
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "aboutus",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    
    public function terms()
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "terms";
        $data['ci_key_for_cover'] = "terms";
        $data['s_category_ids'] = "0";
        
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        
        $s_content = $this->load->view('terms',$data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);
               
               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }
        
        $str_title = WEBSITE_NAME . " | Terms";
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "terms",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    
    public function privacy_policy()
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "privacypolicy";
        $data['ci_key_for_cover'] = "privacypolicy";
        $data['s_category_ids'] = "0";
        
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        
        $s_content = $this->load->view('privacypolicy',$data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
           
            
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);
               
               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }

            
        
        $str_title = WEBSITE_NAME . " | Privacy Policy";
        
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "privacypolicy",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    public function copyright()
    {
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "copyright";
        $data['ci_key_for_cover'] = "copyright";
        $data['s_category_ids'] = "0";
        
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        
        $s_content = $this->load->view('copyright',$data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
           
            
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['post_details'] = 0;
               $data2['widgets'] = $obj_widgets;
               $data2['cartoon'] = true;
               
               // User Data
               $user_id = (free_user_logged_in()) ? get_free_user_session('id') : NULL;
               
               $data2['model'] = $this->get_free_user($user_id);
               
               $data2['free_user_types'] = $this->get_free_user_types();
               
               $data2['country'] = $this->get_country();
               $data2['country']['id'] = $data2['model']->tds_country_id;
               
               $data2['grades'] = $this->get_grades();
               
               $data2['medium'] = $this->get_medium();
               
               $data2['edit'] = (free_user_logged_in()) ? TRUE : FALSE;
               // User Data
               
               $obj_post = new Posts();
               $data2['category_tree'] = $obj_post->user_preference_tree();
               
               $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
               $this->cache->file->save($cache_name, $s_right_view, 86400 * 30 * 12);
            }
        }
        else
        {
            $s_right_view = $s_widgets;
        }

            
        
        $str_title = WEBSITE_NAME . " | Copyright";
        
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "copyright",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    
    public function contact_us()
    {
        
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "contact_us";
        $data['ci_key_for_cover'] = "contact_us";
        $data['s_category_ids'] = "0";
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        if(isset($_POST) && !empty($_POST)){
            
            $this->load->config('champs21');
            
            $email_config = $this->config->config['contact_email_addr'];
            
            $contact_model = new Contact_us();
            
            $contact_model->full_name = $this->input->post('full_name');
            $contact_model->email = $this->input->post('email');
            $contact_model->contact_type = $this->input->post('contact_type');
            $contact_model->description = $this->input->post('ques_description');
            $contact_model->created_date = date('Y-m-d H:i:s', time());
            
            $ar_email['sender_full_name'] = $contact_model->full_name;
            $ar_email['sender_email'] = $contact_model->email;
            $ar_email['to_name'] = $email_config[$contact_model->contact_type]['to']['full_name'];
            $ar_email['to_email'] = $email_config[$contact_model->contact_type]['to']['email'];
            $ar_email['cc_name'] = $email_config[$contact_model->contact_type]['cc']['full_name'];
            $ar_email['cc_email'] = $email_config[$contact_model->contact_type]['cc']['email'];
            $ar_email['bcc_name'] = $email_config[$contact_model->contact_type]['bcc']['full_name'];
            $ar_email['bcc_email'] = $email_config[$contact_model->contact_type]['bcc']['email'];
            $ar_email['html'] = true;
            
            $ar_email['subject'] = $email_config[$contact_model->contact_type]['subject'];
//            $ar_email['message'] = $contact_model->description;
            $ar_email['message'] = $this->get_welcome_message();
            
            if($contact_model->validate()){
                
                if(send_mail($ar_email)){
                    
                    if($contact_model->save()){
                        $data['saved'] = TRUE;
                        $data['errors'][] = 'We appreciate that you have taken the time to write us. We’ll get back to you very soon. Please come back and see us often.';
                    }else{
                        $data['saved'] = FALSE;
                        $data['errors'] = $contact_model->error->all;
                    }
                }
                else{
                    $data['saved'] = FALSE;
                    $data['errors'][] = 'Something bad happend. Your message could not be sent at the moment. Please try again after sometime.';
                }
            }
            
            echo json_encode($data);
            exit;
        }
        
        $s_content = $this->load->view('contact_us', $data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['free_user_types'] = $this->get_free_user_types();
            }
        }
        
        $str_title = WEBSITE_NAME . " | Contact Us";
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "contact-us",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    
    public function createpage()
    {
        
        $ar_js = array();
        $ar_css = array();
        $extra_js = '';
        
        $data = array();
        
        $data['ci_key']    = "createpage";
        $data['ci_key_for_cover'] = "createpage";
        $data['s_category_ids'] = "0";
        
        $this->db->where('key', 'layout');
        $query = $this->db->get('settings');
        $layout_settings = $query->row();
        
        $data['layout'] = $layout_settings->value;
        
        if(isset($_POST) && !empty($_POST)){
            
            $this->load->config('champs21');
            
            $email_config = $this->config->config['contact_email_addr'];
            
            $contact_model = new Contact_us();
            
            $contact_model->full_name = $this->input->post('full_name');
            $contact_model->email = $this->input->post('email');
            $contact_model->contact_type = $this->input->post('contact_type');
            $contact_model->description = $this->input->post('ques_description');
            $contact_model->created_date = date('Y-m-d H:i:s', time());
            
            $ar_email['sender_full_name'] = $contact_model->full_name;
            $ar_email['sender_email'] = $contact_model->email;
            $ar_email['to_name'] = $email_config[$contact_model->contact_type]['to']['full_name'];
            $ar_email['to_email'] = $email_config[$contact_model->contact_type]['to']['email'];
            $ar_email['cc_name'] = $email_config[$contact_model->contact_type]['cc']['full_name'];
            $ar_email['cc_email'] = $email_config[$contact_model->contact_type]['cc']['email'];
            $ar_email['bcc_name'] = $email_config[$contact_model->contact_type]['bcc']['full_name'];
            $ar_email['bcc_email'] = $email_config[$contact_model->contact_type]['bcc']['email'];
            
            $ar_email['subject'] = $email_config[$contact_model->contact_type]['subject'];
            $ar_email['message'] = $contact_model->description;
            
            if($contact_model->validate()){
                
                if(send_mail($ar_email)){
                    
                    if($contact_model->save()){
                        $data['saved'] = TRUE;
                        $data['errors'][] = 'Seccessfully Saved.';
                    }else{
                        $data['saved'] = FALSE;
                        $data['errors'] = $contact_model->error->all;
                    }
                }
            }
            
            echo json_encode($data);
            exit;
        }
        
        $s_content = $this->load->view('createpage', $data, true);
        
        $s_right_view = "";
        $cache_name = "common/right_view";
        if ( ! $s_widgets = $this->cache->file->get($cache_name)  )
        {
            $this->db->where('is_enabled', 1);
            $query = $this->db->get('widget');
            
            $obj_widgets = $query->result();
            
            if ($obj_widgets )
            {
               $data2['free_user_types'] = $this->get_free_user_types();
            }
        }
        
        $str_title = WEBSITE_NAME . " | Create Page";
        
        $meta_description = META_DESCRIPTION;
        $keywords = KEYWORDS;
        $ar_params = array(
            "javascripts"           => $ar_js,
            "css"                   => $ar_css,
            "extra_head"            => $extra_js,
            "title"                 => $str_title,
            "description"           => $meta_description,
            "keywords"              => $keywords,
            "side_bar"              => $s_right_view,
            "target"                => "contact-us",
            "fb_contents"           => NULL,
            "content"               => $s_content
        );
        
        $this->extra_params = $ar_params;
    }
    
    private function get_welcome_message(){
        
        $message = '<!DOCTYPE HTML>';
        
        $message .= '<head>';
            $message .= '<meta http-equiv="content-type" content="text/html">';
            $message .= '<title>Welcome to Champs21.com</title>';
        $message .= '<body>';
        
            $message .= '<div id="header" style="width: 50%; height: 60px; margin: 0 auto; padding: 10px; color: #fff; text-align: center; background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">';
                $message .= '<img height="50" width="220" style="border-width:0" src="'.  base_url('styles/layouts/tdsfront/images/logo-new.png').'" alt="Champs21.com" title="Champs21.com">';
            $message .= '</div>';
            
            $message .= '<p>Thank you for joining Champs21.com and welcome to country&#39;s largest portal for Students | Teachers | Parents. I&#39;m writing this mail to Thank You and giving you a little brief on our services and features.</p>';
            $message .= '<p>
                Champs21.com, the pioneer eLearning program of Bangladesh, has been dedicatedly and very
                humbly working with the objectives to better prepare our students as the Champions of 21st Century. 
                The portal offers various educational and non-educational contents on daily basis for every family 
                that has a school going student.</p>';
            
            $message .= '<p>
                <a href="'.base_url('resource-centre').'" style="color:#000000; text-decoration: underline; font-weight: bold; ">Resource Centre</a> is the most important section where you will find education content not for students 
                but also teaching and learning resources for teachers and parents on various subjects. All the 
                education contents are developed by professional pool of teachers from Champs21.com. Please feel 
                free and <a href="'.base_url().'" style="color:#000000; text-decoration: underline; ">apply</a>, if you want to join us as a teacher. Education resources uploaded by others are 
                carefully checked and modified before it is uploaded for our respected users. Please <a href="'.base_url().'" style="color:#000000; text-decoration: underline; font-weight: bold; ">Candle</a> now if 
                you want to share any resources with our education community.</p>';
            
            $message .= '<p>
                Our non-education contents i.e. Tech News, Sports News, Entertainment, Health & Nutrition, 
                Literature, Travel, Games and Videos are also very popular among our family members. Our 
                continued efforts are always there to research and develop contents in order to make them truly 
                useful for you.</p>';
            
            $message .= '<p>
                <a href="'.base_url('schools').'" style="color:#000000; text-decoration: underline; font-weight: bold; ">Schools</a> section offers and extensive database of schools in the country. This makes your life simpler 
                to collect information about any particular school. If you are a teacher, create your <a href="'.base_url('schools').'" style="color:#000000; text-decoration: underline; ">School</a> if it is not 
                already there.</p>';
            
            $message .= '<p>
                <strong>Good Read</strong> allows you to save the articles and create your own library of resources. You can save 
                your favourite articles and read them again and again at later dates at your convenience.</p>';
            
            $message .= '<p>
                Do you think you can contribute to our Students | Teachers | Parents community? <a href="'.base_url().'" style="color:#000000; text-decoration: underline; font-weight: bold; ">Candle</a> us your 
                article now and spread light. Other than only education, you can write and Candle on any available 
                sections of Champs21.com.</p>';
            
            $message .= '<p>
                As a registered user, you can now make <strong>preference settings</strong> and get only favourite content feeding 
                on your home page.</p>';
            
            $message .= '<p>
                You are very important to us. So is our every other student, teacher and parent of our beloved 
                country. If you like our resources, please do <span style="text-decoration: underline; ">spread</span> this message among your near and dear ones.</p>';
            
            $message .= '<p>Thank you once again for your time and patience.</p>';
            $message .= '<p>Best Regards,</p>';
            $message .= '<p>&nbsp;</p>';
            $message .= '<p>&nbsp;</p>';
            $message .= '<p>Russell T. Ahmed</p>';
            $message .= '<p>Founder &amp; CEO</p>';
            
        $message .= '</body>';
        $message .= '</head>';
        
        return $message;
    }
    
    public function plus_api($param) {
        
        $this->load->library('plus_api');
        
        /**
         * ONLY set username and password where needed otherwise don't event think to set the username
         * and password keys
         * 
         * USE AT YOUR OWN RISK { BEST OF LUCK :D :D :P }
         * 
         * Regards,
         * NIslam :D :D :P
         */
        
        $ar_params = array(
            'school_code' => 'ais',
            'username' => 'ST0001',
            'password' => '123456'
        );
        
        $int_response = $this->plus_api->init($ar_params, false);
        
        if($int_response != FALSE){
            
//            $res = $this->plus_api->call__('get', 'student_attendance', 'get_data_student_attendance');
            $res = $this->plus_api->call__('get', 'reminders', 'get_data_reminder');
//            $res = $this->plus_api->call__('get', 'batches');
            
            var_dump($res);
            
        }
        exit;
        
    }
}
