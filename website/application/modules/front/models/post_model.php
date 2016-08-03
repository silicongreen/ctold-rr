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
        ),
        
// name of the join table that will lnk both Author and Book together
    );

    public function has_post($i_post_id, $b_md5 = FALSE)
    {
        if($b_md5 && strlen($i_post_id) != 32){
            return false;
        }
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
    public function get_leader_board($stDivision = "Dhaka", $iLimit = 30, $iCountryId = 14,$iYear = NULL)
    {
        if(  is_null( $iYear))
        {
            $iYear = date('Y');
        }

        
        $this->db->select('tds_free_users.id as user_id, tds_free_users.first_name, tds_free_users.middle_name, tds_free_users.last_name, tds_free_users.school_name, tds_spell_highscore.*')
                    ->from('tds_free_users')
                    ->join("tds_spell_highscore", "tds_free_users.id=tds_spell_highscore.userid", 'INNER')
                    ->where("tds_spell_highscore.is_cancel", 0)
                    ->where("tds_spell_highscore.spell_year", $iYear)
                    ->where('tds_spell_highscore.score >',0)
                    ->like('free_users.division', $stDivision, 'after') 
                    ->order_by("tds_spell_highscore.score", "desc")
                    ->order_by("tds_spell_highscore.test_time", "ASC")
                    ->limit(30);
        $query = $this->db->get();
        if ( $query->num_rows() > 0 )
        {
            return $rows = $query->result();
        }
        else
        {
            return 0;
        }
        
    }
    public function get_user_score($user_id)
    {
        if(  is_null( $user_id))
        {
            return -1;
        }
        $iYear = date('Y');
        
        $this->db->select('spell_highscore.*')
                    ->from('tds_spell_highscore')                    
                    ->where("tds_spell_highscore.userid", $user_id)
                    ->where("tds_spell_highscore.is_cancel", 0)
                    ->where("tds_spell_highscore.spell_year", $iYear)
                    ->limit(1);
        $query = $this->db->get();
        if ( $query->num_rows() > 0 )
        {            
            return $rows = $query->result();
        }
        else
        {
            return -1;
        }
        
    }
    public function get_user_rank($score_for_rank,$time_for_rank,$country,$division="")
    {
        if(  is_null( $division))
        {
            return -1;
        }
        if($score_for_rank==0)
        {
            return -1;
        } 
        $iYear = date('Y');
        $this->db->select('count(tds_spell_highscore.id)+1 AS rank')
                    ->from('tds_spell_highscore')    
                    ->where("tds_spell_highscore.is_cancel", 0)
                    ->where("tds_spell_highscore.spell_year", $iYear)
                    ->where("tds_spell_highscore.division", $division)                    
                    ->where('(tds_spell_highscore.score >', $score_for_rank)
                    ->or_where("(tds_spell_highscore.test_time <", $time_for_rank, FALSE)
                    ->where("tds_spell_highscore.score = ".$score_for_rank."))", NULL, FALSE)
                    ->limit(1);
        $query = $this->db->get();
        if ( $query->num_rows() > 0 )
        {
            return $rows = $query->result();
        }
        else
        {
            return -1;
        }
        
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
            $this->order_by("DATE(published_date)", 'desc');
            $s_order_by_new .= " ORDER BY DATE(published_date) desc, ";
            
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
            $this->order_by("DATE(published_date)", 'desc');
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
        if ( isset($_GET['date']) &&  strlen($_GET['date']) != "0"  )
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
    public function getIssueDate($myDate = "",$check_date=true)
    {
        //
        
        $CI = & get_instance();
        //$CI->load->config("tds");
        $b_issue_date = $this->config->config['issuedate_enable'];
        
//        if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  )
//        {
//            $arIssueDate['s_issue_date'] = date("Y-m-d", strtotime($_GET['archive']));
//            
//            $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
//            $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));
//
//            $arIssueDate['current_date'] = date("Y-m-d");
//
//            return $arIssueDate;
//        }
        if ( isset($_GET['date']) &&  strlen($_GET['date']) != "0" && $check_date  )
        {
            $arIssueDate['s_issue_date'] = date("Y-m-d", strtotime($_GET['date']));
            
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
    
    public function get_related_tags($i_post_id)
    {
       
        $CI = & get_instance();
        $sql_tags = "SELECT tags_name from tds_tags where tags_name!='' and id in (select tag_id from  tds_post_tags where post_id=".$i_post_id.")";
   
        $related_tags = $CI->db->query($sql_tags)->result();
        $related_tags_array = array();
        $related_tags_string = "";
        $extra_param = "";
        if (isset($_GET['archive']) &&  strlen($_GET['archive']) != "0")
        {
            $extra_param = "?archive=".$_GET['archive'];
        }
        if(count($related_tags) > 0)
        {
            foreach($related_tags as $value)
            {
                $related_tags_array[] = "<a style='padding:6px 6px;-webkit-border-top-left-radius:6px;
	-moz-border-radius-topleft:3px;
	border-top-left-radius:3px;
	-webkit-border-top-right-radius:3px;
	-moz-border-radius-topright:3px;
	border-top-right-radius:3px;
	-webkit-border-bottom-right-radius:3px;
	-moz-border-radius-bottomright:3px;
	border-bottom-right-radius:3px;
	-webkit-border-bottom-left-radius:3px;
	-moz-border-radius-bottomleft:3px;
	border-bottom-left-radius:3px;font-size:11px;font-style:normal;font-weight:normal;border:1px solid #dcdcdc; color:black; background: #dfdfdf;' href=\"".base_url()."tags/".$value->tags_name.$extra_param."\">".$value->tags_name."</a>";
                
            }
            $related_tags_string = implode(" ", $related_tags_array);
        }
        
        return $related_tags_string;
    }

    public function has_news($i_post_id, $ar_categories, $s_target = 'none', $date = "")
    {
        
        $this->db->select("*")
                  ->join("post_category", "post_category.post_id = post.id", "INNER")
                  ->join("post_type as postType", "post.id = postType.post_id", 'LEFT');
        if ( $s_target == "previous" )
        {
            $this->db->where("post.id < ",$i_post_id);
        }
        else if ( $s_target == "next" )
        {
            $this->db->where("post.id > ",$i_post_id);
        }
        else
        {
            $this->db->where("post.id != ",$i_post_id);
        }
        
        if ( strlen($date) > 0 )
        {
            $s_issue_date_from = date("Y-m-d 00:00:00", strtotime($date));
            $s_issue_date_to = date("Y-m-d 23:59:59", strtotime($date));
            $this->db->where('published_date BETWEEN "'. date('Y-m-d H:i:s', strtotime($s_issue_date_from)). '" and "'. date('Y-m-d H:i:s', strtotime($s_issue_date_to)).'"');
        }
        
        $this->db->where("post.status",5);
        $this->db->where("(referance_id IS NULL OR referance_id = '')");
        
        $user_post_type = 1; //Initially we assume we retrieve data for visitor
        if ( free_user_logged_in() )
        {
            $user_type = get_free_user_session("type");
            $user_post_type = (empty($user_type) || ($user_type == 0)) ? $user_post_type : $user_type;
        }
        
        $this->db->where("postType.type_id", $user_post_type);
        
        if( is_array($ar_categories) && (count($ar_categories) > 0) ) {
            $this->db->where("post_category.category_id IN (" . implode(",", $ar_categories) . ")" );
        }
        
        $this->db->group_by("post.id");
        
        $post = $this->db->get("post");
        
        return ($post->num_rows() > 0 ) ? TRUE : FALSE;
    }
    
    public function news_link($i_post_id, $ar_categories, $s_target = 'none')
    {
        
        $this->db->select("post.id, post.headline")
                  ->join("post_category", "post_category.post_id = post.id", "INNER")
                  ->join("post_type as postType", "post.id = postType.post_id", 'LEFT');
        if ( $s_target == "previous" )
        {
            $this->db->where("post.id < ",$i_post_id);
            $this->db->order_by("post.id desc");
        }
        else if ( $s_target == "next" )
        {
            $this->db->where("post.id > ",$i_post_id);
        }
        else
        {
            $this->db->where("post.id != ",$i_post_id);
        }
        
        $user_post_type = 1; //Initially we assume we retrieve data for visitor
        if ( free_user_logged_in() )
        {
            $user_type = get_free_user_session("type");
            $user_post_type = (empty($user_type) || ($user_type == 0)) ? $user_post_type : $user_type;
        }
        
        $this->db->where("postType.type_id", $user_post_type);
        $this->db->where("post.status",5);
        $this->db->where("(referance_id IS NULL OR referance_id = '')");
        
        if( is_array($ar_categories) && (count($ar_categories) > 0) ) {
            $this->db->where("post_category.category_id IN (" . implode(",", $ar_categories) . ")" );
        }
        $this->db->group_by("post.id");
        
        $this->db->limit("1");
        $post = $this->db->get("post")->row();
        
        $str_link = base_url() . sanitize($post->headline) . "-" . $post->id;
        
        return $str_link;
    }
    public function get_related_attach($id)
    {
        $this->db->select("file_name,show,caption");
        $this->db->where("post_id",$id);

        $attach = $this->db->get("post_attachment")->result();
        return $attach;
    }
    public function get_related_news($i_post_id)
    {
        $this->db->select("*");
        $this->db->where("post_id",$i_post_id);

        $related_news = $this->db->get("related_news")->result();
        return (count($related_news) > 0) ? $related_news : FALSE;
    }
    
    public function get_related_assessment($i_assessment_id)
    {
        $this->db->select("*");
        $this->db->where("id", $i_assessment_id);

        $related_assess = $this->db->get("assessment")->row();
        return (count($related_assess) > 0) ? $related_assess : FALSE;
    }
    
    public function get_assessment_levels($i_assessment_id)
    {
        $this->db->select('GROUP_CONCAT( DISTINCT `level`) AS `levels`');
        $this->db->where("assesment_id", $i_assessment_id);

        $assess_levels = $this->db->get("assessment_question")->row();
        return (count($assess_levels) > 0) ? $assess_levels->levels : FALSE;
    }
    
    public function get_user_assessment_marks($user_id, $i_assessment_id, $level = 0)
    {
        $this->db->select('mark, id, no_played, time_taken, avg_time_per_ques, level, created_date');
        $this->db->where("user_id", $user_id);
        $this->db->where("assessment_id", $i_assessment_id);
        if($level > 0){
            $this->db->where("level", $level);
        }
        
        $user_assess_mark = $this->db->get("assesment_mark")->result();
        
        return (count($user_assess_mark) > 0) ? $user_assess_mark : FALSE;
    }
    
    public function get_exclusive_news()
    {
        $arIssueDate = $this->getIssueDate();
        $this->db->select("*");
        $this->where("published_date <= '" . date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date'])) . "'", "", false);
        $this->db->where("priority_type",5);
        $this->db->where("is_exclusive",1);
        $this->db->where('status', 5); 
        $this->db->order_by("id", "desc");
        $this->db->limit(1);
        $related_news = $this->db->get("post")->result();
        return (count($related_news) > 0) ? $related_news : FALSE;
    }
    
    /*
     * Optimized: hehe
     */
    public function get_keywords( $i_post_id )
    {
        $this->db->select("keywords.value");
        $this->db->join("keywords","post_keyword.keyword_id = keywords.id","INNER");
        $this->db->where("post_keyword.post_id", $i_post_id);
        $keywords_data = $this->db->get("post_keyword")->result();
        $s_keywords = "";
        foreach ($keywords_data as $keywords)
        {
            $s_keywords .= $keywords->value . ",";
        }
        $s_keywords = substr($s_keywords, 0, -1);
        return $s_keywords;
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
        $this->where("gallery_tds_post_gallery.type", 1);
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
                    ->where("tds_ad.plan_id",$plan_id )
		    ->order_by("ad.priority", 'asc');
         
            
        $ads_query = $this->db->get();
//        if($plan_id == 35)
//        {
//            echo $this->db->last_query();
//        }
        
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
        $this->load->config("tds");
        $i_user_view_count_inc_no = 1;
        if ( isset($this->config->config['user_modified_view_count']) )
        {
            $i_user_view_count_inc_no = rand($this->config->config['user_modified_view_min'], $this->config->config['user_modified_view_max']);
        }
      
        $this->load->config("huffas");
        $normal_view_count_add = 1;
       
        if(isset($this->config->config['normal_view_count_add']))
        {
            $normal_view_count_add = $this->config->config['normal_view_count_add'];
            //$i_user_view_count_inc_no = $this->config->config['normal_view_count_add'];
        }    
        $this->db->set('view_count', 'view_count+'.$normal_view_count_add , false);
        $this->db->set('user_view_count', 'user_view_count+' .  $i_user_view_count_inc_no, false);
        $this->db->set('ip_address', $_SERVER['REMOTE_ADDR']);
        $this->db->where('id', $news_id);
        $this->db->update('post');
        
        update_cache_single($news_id, $i_user_view_count_inc_no, $normal_view_count_add);
        
        $insert_array['news_id']    = $news_id;
        $insert_array['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $insert_array['country'] = $this->visitor_country();
        $insert_array['city'] = $this->visitor_city();
        $insert_array['date'] = date("Y-m-d");

        $insert_array['home_or_abroad'] = ($insert_array['country']=="Bangladesh")?1:0;
        
        $this->db->insert("post_statistic",$insert_array);
        
        return true;
        
        
    }
    
    public function visitor_country() {
        if (function_exists("geoip_record_by_name") )
        {
            $ip = $_SERVER["REMOTE_ADDR"];
            if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];

            $info = geoip_record_by_name($ip);
            
            if($info)
            {
                $result = $info['country_name'];
                return $result <> NULL ? $result : "Bangladesh";
            }
            else
            {
             return "Bangladesh"; 
            }
        }
        else
        {
         return "Bangladesh"; 
        }
    }
    
    public function visitor_city() {
        if (function_exists("geoip_record_by_name") )
        {
            $ip = $_SERVER["REMOTE_ADDR"];
            if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            $info = geoip_record_by_name($ip);



            if($info)
            {
                $result = $info['city'];
                return $result <> NULL ? $result : "Dhaka";
            }
            else
            {
             return "Dhaka"; 
            }
        }
        else
        {
         return "Dhaka"; 
        }
    }
    
    public function get_post_by_tags($tag, $i_limit = 0, $i_start = 0){
        
        $arIssueDate = $this->getIssueDate();
        
        $sql = "SELECT
                	tds_post.*, tds_post_tags.tag_id,
                	tds_tags.tags_name,
                	tds_tags.hit_count
                FROM
                	tds_post_tags
                INNER JOIN tds_tags ON tds_post_tags.tag_id = tds_tags.id
                INNER JOIN tds_post ON tds_post_tags.post_id = tds_post.id
                WHERE
                	LOWER(tds_tags.tags_name) = \"".$tag."\"
                    AND DATE(tds_post.published_date) <= '".date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']))."'
                ORDER BY DATE(tds_post.published_date) DESC LIMIT $i_start, $i_limit";
        /* #AND DATE(tds_post.published_date) >= '".date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']))."' */
        $obj_rows = $this->query($sql);
        
        return ( ($obj_rows) && count($obj_rows->all) > 0 ) ? $obj_rows : FALSE;
        exit;
    }
    
    public function get_post_by_tags_count($tag){
        
        $sql = "SELECT COUNT(tds_tags.id) AS num_tags FROM tds_post_tags INNER JOIN tds_tags ON tds_post_tags.tag_id = tds_tags.id WHERE LOWER(tds_tags.tags_name) = \"".$tag."\"";
        
        $obj_rows = $this->db->query($sql)->row();
        
        return ( $obj_rows) ? $obj_rows->num_tags : FALSE;
    }
    
    public function getAllSectionAds($plan_id, $ci_key,$location="index")
    {
        $this->db->select('ad_section.*')
                    ->from('ad_section')                    
                    ->where("ad_section.ad_plan_id",$plan_id )
                    ->where("ad_section.menu_ci_key",$ci_key )
                    ->where("ad_section.location",$location );
        $ads_query = $this->db->get();
        
        return $ads_query;
    }
    
    public function get_available_language($i_post_id, $other_langs, $referance_id = 0, $s_lang = "")
    {
        $this->db->set_dbprefix('tds_');
        $s_country = visitor_country();
        $country_id = 14; //By Default Bangladesh
        
        if ($this->config->config['country_filter'])
        {
            $a_country = get_id_by_country($s_country);
            $country_id = $a_country->id;
            $this->db->set_dbprefix('tds_');
            
            $this->db->select('tds_post.language',false )
                ->from('post');
            $this->db->join("post_country as pc", "post.id = pc.post_id", 'LEFT');
            $this->db->where("( tds_post.all_country = 1 OR pc.country_id = " . $country_id . ")", '', FALSE);
            
            if($referance_id==0)
            {
                $this->db->where("tds_post.referance_id", $i_post_id, false);
            }
            else
            {
                $this->db->where("tds_post.id", $i_post_id, false);
            } 
            
            $this->db->where("tds_post.status",5);
            
            $query = $this->db->get(); 
            if ( $query->num_rows() > 0 )
            {
                $rows = $query->result();
                $s_languages = "";
   
                foreach ($rows as $lang)
                {

                    $s_languages .= $lang->language . "-1,";

                }
                $s_languages = substr($s_languages, 0, -1);
                
                
                return $s_languages;
            }
            else
            {
                return NULL;
            }
        }
        else
        {
            return $other_langs;
        }
        
    }
    
}

?>
