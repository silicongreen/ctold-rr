<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Post_model extends DataMapper
{

    var $table = "post";
    var $has_many = array(
        'post_category' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_post_category',
            'other_field' => 'post',
            'class' => "Post_category"
        ),
        'category' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_categories',
            'other_field' => 'id',
            'class' => "Category_model"
        ),
        'keyword' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_keywords',
            'other_field' => 'id',
            'class' => "Keyword_model"
        ),
        'byline' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_bylines',
            'other_field' => 'id',
            'class' => "Byline"
        ),
        'related' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_related_news',
            'other_field' => 'post',
            'class' => "Related_news"
        ),
        'post_keywords' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_post_keyword',
            'other_field' => 'post',
            'class' => "Post_keyword"
        ),
        'gallery' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_post_gallery',
            'other_field' => 'post',
            'class' => "Related_gallery"
        ),
        'material' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_materials',
            'other_field' => 'id',
            'class' => "Related_materials"
        ),
        'maingallery' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_gallery',
            'other_field' => 'id',
            'class' => "gallery"
        ),
        'video' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_materials_video',
            'other_field' => 'id',
            'class' => "Material_videos"
        ),
        'category_cover' => array(// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_category_cover',
            'other_field' => 'category',
            'class' => "category_cover"
        )
// name of the join table that will lnk both Author and Book together
    );

    public function has_post($i_post_id, $b_md5 = FALSE)
    {
        $this->select("tds_post.id as tds_post_id, tds_post.*, post_category_tds_post_category.*, category_tds_categories.*, byline_tds_bylines.id,byline_tds_bylines.title,byline_tds_bylines.is_columnist");
        $this->include_join_fields(FALSE);
        $this->include_related("post_category", "id", FALSE, FALSE, TRUE, "INNER");
        $this->include_related("category", "id", FALSE, FALSE, TRUE, "INNER", "post_category_tds_post_category", "category_id");
        $this->include_related("byline", "id", FALSE, FALSE, TRUE, "LEFT", "tds_post", "byline_id");
        if ($b_md5)
        {
            $this->where("MD5(tds_post.id)", $i_post_id);
        }
        else
        {
            $this->where("tds_post.id", $i_post_id);
        }

        $obj_rows = $this->get();

        return (count($obj_rows->all) > 0) ? $obj_rows : FALSE;
    }

    public function get_posts($ar_from_menu,$ar_issue_date, $ar_priority_type = array(1, 2, 3, 4), $i_category_type_id = 1, $i_category_id = 0, 
                              $s_has_issue_date = 'between', $s_order_by = 'post.priority,asc', $i_limit = 0, $b_limit_check = true,
                              $s_group_by = "", $s_check_images = '', $i_pareant_id = 0, $i_having_category_type_id = 1, $b_limit_execute = TRUE
                             ,$i_byline_id=0,$s_news_type = "")
    {
        $b_check_published_date = TRUE;
        list( $s_method, $s_controller ) = $ar_from_menu;
        
        #Get the Last Published Date for Other Category Type Rather than Daily
        if ($s_method!="RSS-CATEGORY-WOMEN" && $s_method!="newsArchive" && is_numeric($i_category_id) && $i_having_category_type_id != 1 && $i_category_id > 0 && is_null($ar_issue_date) )
        {
            $arIssueDate = $this->checkForLastPublishNewsdate($i_category_id);
            $b_check_published_date = FALSE;
        }
        
        
        #GET NEWS ISSUE DATE FROM SETTINF TABLE
        $check_date_paramiter = true;
        if($s_controller == "home")
        {
            $check_date_paramiter = false;
        }    
       
        if($b_check_published_date && isset($_GET['date']) &&  strlen($_GET['date']) != "0" )
        {
            $arIssueDate = $this->getIssueDate("",$check_date_paramiter);
        }    
        else
        {
            if ( $b_check_published_date && $s_has_issue_date != "no" && is_null($ar_issue_date)  )
            {
                $CI = & get_instance();
                //$arIssueDate = $CI->session->userdata("issue_date");
                //if ( ! $arIssueDate )
                //{

                    $arIssueDate = $this->getIssueDate("",$check_date_paramiter);

                //    $CI->session->set_userdata("issue_date", $arIssueDate);
                //}
            }
        }
        
        /* $s_sql = "SELECT * from post_model_hit WHERE hit_date = '" . date("Y-m-d", strtotime($arIssueDate['s_issue_date'])) . "' AND from_data = '" . $s_method . "' AND name = '" . $s_controller . "'";
        $o_row = $this->db->query($s_sql);
        
        if ($o_row->num_rows() == 0 )
        {
            $s_sql = "INSERT INTO post_model_hit SET hit = 1, hit_date = '" . date("Y-m-d", strtotime($arIssueDate['s_issue_date'])) . "', from_data = '" . $s_method . "', name = '" . $s_controller . "'";
            //$this->db->query($s_sql);
        }
        else
        {
            $res = $o_row->row();
            $hit = $res->hit + 1;
            $s_sql = "UPDATE post_model_hit SET hit = " . $hit . " WHERE hit_date = '" . date("Y-m-d", strtotime($arIssueDate['s_issue_date'])) . "' AND from_data = '" . $s_method . "' AND name = '" . $s_controller . "'";
            $this->db->query($s_sql);
        } */
        
        $b_has_published_date_order_by = false;
        #GET NEWS FROM POST TABLE
        if (strrpos($s_check_images, "cover") !== FALSE  )
        {
            $this->select("post.*, DATE(tds_post.published_date) AS published_date_only, byline_tds_bylines.title as byline, post_category_tds_post_category.inner_priority AS inner_priority, GROUP_CONCAT(DISTINCT tds_category_cover.issue_date) as cover_issue_date, 
                           category_tds_categories.id as category_id, GROUP_CONCAT(DISTINCT tds_category_cover.image) as cover_image, 
                           GROUP_CONCAT(DISTINCT category_tds_categories.name) as name, tds_category_cover.image, 
                           GROUP_CONCAT(DISTINCT category_tds_categories.category_type_id) as category_type", false);
        }
        else if($s_method=="newsArchive")
        {
            $this->select("DISTINCT tds_post.*, DATE(tds_post.published_date) AS published_date_only, byline_tds_bylines.title as byline, post_category_tds_post_category.inner_priority AS inner_priority, GROUP_CONCAT(DISTINCT category_tds_categories.id) as category_id_string, GROUP_CONCAT(DISTINCT category_tds_categories.category_type_id) as category_type", false);
        } 
        else
        {
            $this->select("DISTINCT tds_post.*, DATE(tds_post.published_date) AS published_date_only, byline_tds_bylines.title as byline, post_category_tds_post_category.inner_priority AS inner_priority, GROUP_CONCAT(DISTINCT category_tds_categories.id) as category_id_string, GROUP_CONCAT(DISTINCT category_tds_categories.category_type_id) as category_type", false);
        }
        $this->include_join_fields(FALSE);
        $this->include_related("post_category", "id", FALSE, FALSE, TRUE, "INNER");
        $this->include_related("category", "id", FALSE, FALSE, TRUE, "INNER", "post_category_tds_post_category", "category_id");
        $this->include_related("byline", "id", FALSE, FALSE, TRUE, "LEFT", "tds_post", "byline_id");

        $this->where("tds_post.priority!=''", '', false);
        if ( $i_byline_id > 0 )
        {
            $this->where("tds_post.byline_id", $i_byline_id, false);
        }
//        if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  )
//        {
//            $this->where("tds_post.type", "'Print'", false);
//        }
//        else
//        {
            if ( $s_news_type != "" )
            {
                $this->where("tds_post.type", '"'.$s_news_type.'"', false);
            }
//        }
        
        
        if ( $i_category_type_id > 0 )
        {
            $this->where("category_tds_categories.category_type_id", $i_category_type_id, false);
        }
        
        if ( is_numeric($i_category_id) && $i_category_id > 0 && $i_having_category_type_id!=10)
        {
            $this->where("category_tds_categories.id", $i_category_id, false);
        }
        else if (is_string($i_category_id) )
        {
            $this->where("category_tds_categories.id IN (" . $i_category_id . ")");
        }
        
      
        
        if (strlen($s_check_images) > 0 )
        {
            $s_where = "";
            $s_post_where = "";
            $s_cover_where = "";
            if (strrpos($s_check_images, "cover") !== FALSE  )
            {
                $this->include_related("category_cover", "id", FALSE, FALSE, TRUE, "LEFT", "category_tds_categories", "id");
                $s_where .= "tds_category_cover.image IS NOT NULL OR";
            }
            if (strrpos($s_check_images, "lead_materials") !== FALSE  )
            {
                $s_where .= " tds_post.lead_material != '' OR";
            }
            if (strrpos($s_check_images, "post") !== FALSE  )
            {
                $s_where .= " tds_post.content LIKE '%<img%' OR";
            }
            $s_where = substr($s_where, 0, -3);
            $this->where("(" . $s_where . ")", '', false);
        }
        
        $this->where("tds_post.status", 5, false); //Status Published
        
        $s_order_by_new = ""; 
        
        if ( !is_null($ar_priority_type) )
        {
            $this->where_in('tds_post.priority_type', $ar_priority_type);
        }
        
        if ( !empty($arIssueDate) && $s_has_issue_date == "between" )
        {
            $this->where_between("published_date", $arIssueDate['issue_date_from'], $arIssueDate['issue_date_to']);
        }
        else if ( !empty($arIssueDate) && $s_has_issue_date == "smaller" )
        {
            
            //$this->where("DATE(published_date) <= '" . $arIssueDate['issue_date_from'] . "'", "", false);
            $this->where("published_date <= '" . date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date'])) . "'");
            $this->order_by("published_date_only", 'desc');
            $s_order_by_new .= " ORDER BY published_date desc, ";
            
            if( is_numeric($i_category_id) && ($i_category_id > 0) ){
                $s_order_by_new .= "inner_priority ASC, ";
            }
            
            $b_has_published_date_order_by = true;
        }
        
        if ( $i_pareant_id > 0 )
        {
            $this->where("category_tds_categories.parent_id", $i_pareant_id, false);
        }
        
        if ( is_numeric($i_category_id) && $i_category_id > 0)
        {
            $this->order_by('inner_priority','asc');
        }
        
        list( $s_order_by_field, $s_order_by_type ) = explode(",", $s_order_by);
        $this->order_by($s_order_by_field, $s_order_by_type);
        
        $s_order_by_field_new = str_ireplace("post.", "", $s_order_by_field);
        $s_order_by_new .= $s_order_by_field_new . " " . $s_order_by_type;
        $s_new_order = $s_order_by_field_new . " " . $s_order_by_type;
        
        if (strlen($s_group_by) > 0 )
        {
            $this->group_by("tds_post.id");
            
            $s_sql = $this->get_sql();
            
            $s_modified_sql = "SELECT * FROM (" . $s_sql . ") as Post_table GROUP BY " . $s_group_by . " " . $s_order_by_new;
            
            if ( $i_limit > 0 )
            {
                $s_modified_sql .= " LIMIT " . $i_limit;
            }
            //echo $s_modified_sql;
//            exit;            
            $obj_rows = $this->query($s_modified_sql);
        }
        else
        {
            $this->group_by("tds_post.id");
            
            if ( $i_category_type_id == 0 )
            {
                if ( $i_having_category_type_id > 0 && ($i_having_category_type_id!=10 || is_string($i_category_id)) )
                {
                    $this->having("LENGTH(category_type)", strlen($i_having_category_type_id));
                    $this->having("category_type = " . $i_having_category_type_id , "");
                }
                else if($i_having_category_type_id==10 && !is_string($i_category_id) && is_numeric($i_category_id) && $i_category_id > 0  )
                {
                    $this->having("category_id_string like '%" . $i_category_id."%'" , "");
                }
            }
            
            if ( $i_limit > 0 && $b_limit_execute )
            {
                $this->limit($i_limit);
            }
            
           /* if ( $b_has_published_date_order_by )
            {
                $s_sql = $this->get_sql();
                
                
                if( is_numeric($i_category_id) && ($i_category_id > 0) ){
                    $s_modified_sql = "SELECT * FROM (" . $s_sql . ") as Post_table ORDER BY inner_priority ASC, " . $s_new_order;
                }else{
                    $s_modified_sql = "SELECT * FROM (" . $s_sql . ") as Post_table ORDER BY " . $s_new_order;
                }
                $s_modified_sql = $s_sql;
                
                $obj_rows = $this->query($s_modified_sql);
                //$obj_rows = $this->get();
            }
            else
            {*/
            
                $obj_rows = $this->get();
//                var_dump($this->db->last_query());
//                exit;
            //}
            
            if ( $i_limit > 0 && $b_limit_check )
            {
                if (count($obj_rows->all) < $i_limit )
                {
                    $i_current_count = count($obj_rows->all);
                    $i_require_count = $i_limit - $i_current_count;
                    
                    if ( ($i_require_count > 0 && $i_having_category_type_id < 2) )
                    {
                        $obj_rows = $this->get_posts($ar_from_menu, $ar_issue_date, $ar_priority_type, $i_category_type_id, $i_category_id, "smaller", $s_order_by, $i_limit, false, $s_group_by, $s_check_images, $i_pareant_id, $i_having_category_type_id,TRUE,$i_byline_id);
                        
                        /*if ( empty($obj_rows) )
                        {
                            return $query = $obj_rows;
                        }
                        if ( count($obj_rows->all) == 0 )
                        {
                            return $query = $obj_rows;
                        }

                        $obj_tmp_object = array();
                        $i=0; foreach($obj_rows as $dt)
                        {
                            if ( date("Y-m-d", strtotime($dt->published_date)) == date("Y-m-d", strtotime($arIssueDate['s_issue_date'])) )
                            {
                                array_push($obj_tmp_object, $dt);
                            }
                            $i++;
                            //print $dt->id . "  " . $dt->priority . ",";
                        }
                                              
                        if ( count($obj_rows->all) > 0 ) 
                        {
                            foreach($obj_rows as $dt)
                            {
                                $i_post_id = $dt->id;
                                $b_found = false;
                                
                                if(count($obj_tmp_object) > 0){
                                }
                                foreach( $obj_tmp_object as $tmp )
                                {
                                    if ( $tmp->id == $i_post_id )
                                    {
                                        $b_found = true;
                                        break;
                                    }
                                }
                                if ( ! $b_found )
                                {
                                     array_push($obj_tmp_object, $dt);
                                }
                            }
                            $obj_rows = $obj_tmp_object;
                        }
                        unset ($obj_tmp_object);*/   
                    }
                }
            }
        }
        return $query = $obj_rows;
    }
    
    public function checkForLastPublishNewsdate($i_category_id = 10)
    {
        if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  )
        {
            $arIssueDate = $this->getIssueDate();
        }
        else
        {
            $CI = & get_instance();
            $arIssueDate = $CI->session->userdata("issue_date");
            if ( ! $arIssueDate )
            {

                $arIssueDate = $this->getIssueDate();

                $CI->session->set_userdata("issue_date", $arIssueDate);
            }
        }
        
        
        $this->db->select('post.published_date')
                    ->from('post')
                    ->join("post_category as pc", "post.id=pc.post_id", 'INNER')
                    ->where("tds_post.status", 5)
                    ->where("pc.category_id", $i_category_id)
                    ->where("published_date <= '" . date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date'])) . "'")
                    ->order_by("published_date", "desc")
                    ->limit(1);
        $news_query = $this->db->get();
            
        if ($news_query->num_rows() > 0)
        {
            $rows = $news_query->_fetch_object();
            $ar_issue_date['s_issue_date'] = $rows->published_date;
            
            $ar_issue_date['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($ar_issue_date['s_issue_date']));
            $ar_issue_date['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($ar_issue_date['s_issue_date']));

            $ar_issue_date['current_date'] = date("Y-m-d");
            
            return $ar_issue_date;
        }
        else 
        {
            return FALSE;
        }
    }
    public function getIssueDate($myDate = "")
    {
        //
        
        $CI = & get_instance();
        $CI->load->config("tds");
        $b_issue_date = $this->config->config['issuedate_enable'];
        
        if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  )
        {
            $arIssueDate['s_issue_date'] = date("Y-m-d", strtotime($_GET['archive']));
            
            $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
            $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));

            $arIssueDate['current_date'] = date("Y-m-d");

            return $arIssueDate;
        }
        else if ( ! $b_issue_date )
        {
            $arIssueDate['s_issue_date'] = date("Y-m-d");
            $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
            $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));

            $arIssueDate['current_date'] = date("Y-m-d");

            return $arIssueDate;
        }
        #GET NEWS ISSUE DATE FROM SETTINF TABLE
       
        $arIssueDate = array();
        if($myDate == "")
        {
            $this->db->where('key', 'issue_date');
            $issuequery = $this->db->get('settings');
            $arIssueDate['s_issue_date'] = $issuequery->row()->value;
        }        
        else
        {             
            $arIssueDate['s_issue_date'] = $myDate;           
        }
        
        $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
        $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));
        
        $arIssueDate['current_date'] = date("Y-m-d");
        
        return $arIssueDate;
    }

    public function get_related_news($i_post_id)
    {
        $this->db->select("*");
        $this->db->where("post_id",$i_post_id);

        $related_news = $this->db->get("related_news")->result();
        return (count($related_news) > 0) ? $related_news : FALSE;
    }
    public function get_exclusive_news()
    {
        $CI = & get_instance();
        $arIssueDate = $CI->session->userdata("issue_date");
        $this->db->select("*");
        $this->where("published_date <= '" . $arIssueDate['issue_date_from'] . "'", "", false);
        $this->db->where("priority_type",5);
        $this->db->where("is_exclusive",1);
        $this->db->where('status', 5); 
        $this->db->order_by("id", "desc");
        $this->db->limit(1);
        $related_news = $this->db->get("post")->result();
        return (count($related_news) > 0) ? $related_news : FALSE;
    }
    public function get_keywords( $i_post_id )
    {
        $this->select("GROUP_CONCAT( keyword_tds_keywords.value ) as keywords_data");
        $this->include_join_fields(FALSE);
        $this->include_related("post_keywords", "id", FALSE, FALSE, TRUE, "INNER");
        $this->include_related("keyword", "id", FALSE, FALSE, TRUE, "INNER", "post_keywords_tds_post_keyword", "keyword_id");
        $this->where("tds_post.id =", $i_post_id);
        $this->group_by("tds_post.id");
        $obj_rows = $this->get();
        return (count($obj_rows)) ? $obj_rows : FALSE;
    }
    
    public function get_post_videos($i_post_id)
    {
        $this->select("video_tds_materials_video.url,video_tds_materials_video.video_type,video_tds_materials_video.video_id");
        $this->include_join_fields(FALSE);
        
        $this->include_related("gallery", "id", FALSE, FALSE, TRUE, "INNER");
        $this->include_related("material", "id", FALSE, FALSE, TRUE, "INNER", "gallery_tds_post_gallery", "material_id");
        $this->include_related("video", "id", FALSE, FALSE, TRUE, "INNER", "material_tds_materials", "video_id");
        $this->include_related("maingallery", "id", FALSE, FALSE, TRUE, "INNER", "material_tds_materials", "gallery_id");

        $this->where("tds_post.id", $i_post_id);
        $this->where("maingallery_tds_gallery.gallery_type", "2");
        $obj_rows = $this->get();
        return (count($obj_rows)) ? $obj_rows : FALSE;
    }

    public function get_related_gallery($i_post_id, $ar_type = array(3, 4))
    {
        $this->select("gallery_tds_post_gallery.caption,material_tds_materials.material_url");
        $this->include_join_fields(FALSE);
        $this->include_related("gallery", "id", FALSE, FALSE, TRUE, "INNER");
        
        $this->include_related("material", "id", FALSE, FALSE, TRUE, "INNER", "gallery_tds_post_gallery", "material_id");
        
        $this->include_related("maingallery", "id", FALSE, FALSE, TRUE, "INNER", "material_tds_materials", "gallery_id");

        $this->where("tds_post.id", $i_post_id);
        $this->where("maingallery_tds_gallery.gallery_type in (" . implode(",", $ar_type) . ")");
        $obj_rows = $this->get();
        return (count($obj_rows)) ? $obj_rows : FALSE;
    }
    
    public function get_post_by_category($category_id = null){
        $this->select("tds_post.id as tds_post_id, tds_post.headline, tds_post.lead_material");
        $this->include_join_fields(FALSE);
        $this->include_related("post_category", "id", FALSE, FALSE, TRUE, "INNER");
        $this->include_related("category", "id", FALSE, FALSE, TRUE, "INNER", "post_category_tds_post_category", "category_id");
        if(!empty($category_id)){
            $this->where('post_category_tds_post_category.category_id', $category_id);
        }
        $this->where('tds_post.lead_material IS NOT NULL');
        $this->order_by('tds_post.published_date','DESC');
        $obj_rows = $this->get();
        
        return (count($obj_rows)) ? $obj_rows : FALSE;
    }
    public function getAllAds($plan_id, $ci_key)
    {
         $this->db->select('ad.*,ap.d_width,ap.d_height,ap.block,ap.qty')
                    ->from('ad')
                    ->join("ad_plan as ap", "ad.plan_id=ap.id", 'INNER')
                    ->where("tds_ad.is_active", 1)
                    ->where("(tds_ad.menu_ci_key = 'index' OR tds_ad.menu_ci_key like '%" . $ci_key . "%')")
                    ->where("tds_ad.plan_id",$plan_id );
        $ads_query = $this->db->get();
        
        return $ads_query;
    }
    public function getMenuHasAds($menu_id)
    {
         $this->db->select('ad_menu.*')
                    ->from('ad_menu')
                    ->where("ad_menu.menu_id",$menu_id );
        $ads_query = $this->db->get()->row();
        
        return $ads_query;
    }
    public function get_category_by_post($post_id = null){
        $categories_query = "SELECT category_id FROM tds_post_category WHERE post_id = " . $post_id;
        
        $categories = $this->db->query($categories_query)->result();
        
        return $categories;
    }
    
    public function updateCount($news_id)
    {
        $this->db->set('view_count', 'view_count+1' , false);
        $this->db->set('ip_address', $_SERVER['REMOTE_ADDR']);
        $this->db->where('id', $news_id);
        return ($this->db->update('post')) ? true : false;
    }
    
}

?>
