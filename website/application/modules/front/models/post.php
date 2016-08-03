<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class post extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function newstricker()
    {
        $this->db->where('key', 'issue_date');
        $issuequery = $this->db->get('settings');
        $this->db->select('DISTINCT tds_post.id,post.headline,post.is_breaking,post.exclusive_expired',false )
                ->from('post')
                ->join("bylines as bl", "post.byline_id=bl.id", 'INNER')
                ->join("post_category as pcat", "post.id=pcat.post_id", 'INNER')
                ->join("categories as cat", "pcat.category_id=cat.id", 'INNER')
                ->where("tds_post.priority!=''",'',false)
                ->where("cat.category_type_id",1,false)
                ->where("tds_post.status",5,false)
                ->like("post.published_date",$issuequery->row()->value, 'after')
                ->order_by("published_date", "DESC")
                ->limit(25);
        $query = $this->db->get(); 
      
        
        return $query->result();
    }  
    
    public function get_post_gallery( $i_post_id )
    {
        $this->db->where('tds_post.id', $i_post_id);
        $this->db->select('DISTINCT m.material_url,pg.source,pg.caption',false )
                ->from('post')
                ->join("post_gallery as pg", "tds_post.id=pg.post_id", 'INNER')
                ->join("materials as m", "pg.material_id=m.id", 'INNER')
                ->limit(2);
        $query = $this->db->get(); 
      
        
        return $query->result();
    }  
    
    /**
     * 
     * @param type $a_post_params, arrays to filter from post 
     * @param type $target, i use same function for inner page and home page and also good read and search page
     *                      for Homepage = $target is index, and homepage_data will be join
     *                      for Inner    = $target is inner, no home page data will join and category ID will pass 
     * @param type $s_issue_date_condition
     * @param type $s_order_by
     * @param type $s_category_ids
     * @param type $i_limit
     * @param type $i_page
     * @param type $b_featured
     * @param type $i_featured_pos
     * @param type $b_check_issue_date
     * @param type $s_issue_date
     * @return type
     */
    public function gePostNews( $a_post_params, $target = "inner", $s_issue_date_condition = 'smaller', 
                                $s_order_by = 'post.priority,asc', $s_category_ids = 0, 
                                $i_limit = 9, $i_page = 0, $b_featured = FALSE, $i_featured_pos = 1, 
                                $b_check_issue_date = TRUE, $s_issue_date =  "" )
    {
        $b_from_api = $this->config->config['from_api'];
        $a_api_index = $this->config->config['api_index'];
        
        if( (isset($a_post_params['q']) && !empty($a_post_params['q'])) && ($target == 'search') ) {
            $b_from_api = FALSE;
        }
        
        if( (isset($a_post_params['lang']) && !empty($a_post_params['lang']))) {
            $lang = $a_post_params['lang'];
        }
        
        if( (isset($a_post_params['tds_post.referance_id']) && !empty($a_post_params['tds_post.referance_id']))) {
            $b_from_api = FALSE;
        }
        
        if( $target == 'Single' ) {
            $b_from_api = FALSE;
        }
        
        if (($b_from_api && in_array($target, $a_api_index)) || $target=="school" || $target=="teacher"  )
        {
            $a_exclude_id = array();
            if( isset($a_post_params['NOT_IN'][1]) )
            {
                $a_exclude_id = $a_post_params['NOT_IN'][1];
                
            }  
            $page_number = $i_page+1;
           
            $category_id = 0;
            $link = "";
            $popular = false;
            $game_type = false;
            if($target == "inner" || $target == "inner-popular" )
            {
                $link = "getcategorypost";
                $category_id = $s_category_ids;
                if($s_order_by == "tds_post.user_view_count,desc")
                {
                    $popular = 1;
                }    
                
            }
            $stbid=0;
            if($target=="school" || $target=="teacher")
            {
                $link = "getschoolteacherbylinepost";
                $stbid = $a_post_params['stbid'];
            
            }    
            $paze_size = 9;
            $fetaured = 0;
            if(isset($a_post_params['CUSTOM']))
            {
                if(strpos($a_post_params['CUSTOM'], "tds_post.game_type")!==false)
                {
                    $a_game_types = explode("=", $a_post_params['CUSTOM']);
                    if(isset($a_game_types[1]))
                    {
                        $game_type = trim(str_replace(")", "", trim($a_game_types[1])));
                        $paze_size = 6;
                        $fetaured = 2;
                    }    
                }        
            }
            
            if(isset($a_post_params['force_limit']) && $a_post_params['force_limit'])
            {
                $paze_size = 1;
            }
            
            if(isset($a_post_params['featured']) && $a_post_params['featured']===1)
            {
                $fetaured = 1;
            }    
            
            $b_get_related = false;
            if(isset($a_post_params['b_get_related']) && $a_post_params['post_id'] > 0)
            {
                $link = "relatednews";
                $b_get_related = true;
                $stbid = $a_post_params['post_id'];
            }
            return get_api_data_from_yii($a_exclude_id, $page_number, $link, $category_id,
                    $popular, $paze_size, $game_type, $fetaured, $stbid, $target,
                    $b_get_related, $i_post_id, $lang);
            
        }
        else
        {
            $s_country = visitor_country();
            $country_id = 14; //By Default Bangladesh
            if ($this->config->config['country_filter'])
            {
                $a_country = get_id_by_country($s_country);
                $country_id = $a_country->id;
                $this->db->set_dbprefix('tds_');
            }

            if ( $b_check_issue_date )
            {   
                $obj_post = new Post_model();
                $arIssueDate = $obj_post->getIssueDate();
                $s_issue_date = $arIssueDate['s_issue_date'];
                $s_issue_date_from = $arIssueDate['issue_date_from'];
                $s_issue_date_to = $arIssueDate['issue_date_to'];
            }
            else
            {
                $s_issue_date_from = date("Y-m-d 00:00:00", strtotime($s_issue_date));
                $s_issue_date_to = date("Y-m-d 23:59:59", strtotime($s_issue_date));
            }
            $extra_select = "";
            if($target == "index")
            {
                //$extra_select = " ,MAX(t.date) as maxorder,MIN(t.priority) as minorder";
            }
            $this->db->select('SQL_CALC_FOUND_ROWS DISTINCT tds_post.id as post_id, tds_post.headline, tds_post.assessment_id, tds_post.content, tds_post.is_featured, tds_post.show_byline_image,
                               tds_post.headline_color, tds_post.summary, tds_post.short_title, tds_post.shoulder, tds_post.other_language, tds_post.post_type,
                               tds_post.sub_head,tds_post.pdf_top, tds_post.lead_material, tds_post.lead_caption, tds_post.is_breaking, tds_post.breaking_expire, tds_post.language, tds_post.lead_link,
                               tds_post.published_date, tds_post.view_count, postCategories.id, tds_post.user_view_count, tds_post.embedded, tds_post.layout_color, tds_post.is_exclusive, tds_post.exclusive_expired,
                               category.id, category.menu_icon, category.icon, category.name, byline.title, byline.image as author_image, tds_post.referance_id, tds_post.attach, tds_post.layout,
                               tds_post.sort_title_type,tds_post.mobile_view_type, tds_post.inside_image, tds_post.post_layout, tds_post.video_file, tds_post.user_type,tds_post.school_id,tds_post.teacher_id,tds_post.wow_count, tds_post.can_comment,tds_post.show_comment_to_all,tds_post.user_id'.$extra_select,false )
                    ->from('post');

            if ( $target == "index" )
            {
                $this->db->join("homepage_data as t", "post.id = t.post_id", 'INNER');
            }
            else if ( $target == "good_read" )
            {
                $this->db->join("user_good_read as ugr", "post.id = ugr.post_id", 'INNER');
                $this->db->join("post_type as postType", "post.id = postType.post_id", 'LEFT');
            }
            else if ( $target == "good_read_unread" )
            {
                $this->db->join("user_good_read as ugr", "post.id = ugr.post_id", 'INNER');
                $this->db->join("post_type as postType", "post.id = postType.post_id", 'LEFT');
            }
            else if($target == "inner" || $target == "inner-popular" )
            {
                $this->db->join("post_type as postType", "post.id = postType.post_id", 'LEFT');
            }
            else if ($target == 'search') {
                $this->db->join("post_type as postType", "post.id = postType.post_id", 'LEFT');
            }

            if ($this->config->config['country_filter'] && $i_post_id > 0)
            {
                $this->db->join("post_country as pc", "post.id = pc.post_id", 'LEFT');
                $this->db->where("( tds_post.all_country = 1 OR pc.country_id = " . $country_id . ")", '', FALSE);
            }

            $this->db
                 ->join("bylines as byline", "post.byline_id = byline.id", 'LEFT')
                 ->join("post_category as postCategories", "post.id = postCategories.post_id", 'LEFT')
                 ->join("categories as category", "category.id = postCategories.category_id", 'LEFT');
            if ($this->session->userdata("admin"))
            {
                
            }
            else
            {
                $this->db->where("tds_post.status",5,false);
            }    


            if ( $b_featured == 1  )
            {
                $this->db->where("tds_post.is_featured", 1, false);
                $this->db->where("tds_post.feature_position IN (" . $i_featured_pos . ")", '',false);
            }
            else if ( $b_featured == 2  )
            {
                $this->db->where("tds_post.feature_position IN (" . $i_featured_pos . ")", '',false);
            }
            else
            {
                if ( empty($a_post_params) )
                {
                    $this->db->where("( tds_post.is_featured = 0 OR tds_post.is_featured IS NULL)", '', FALSE);
                }
            }

            $index_news['show_old_news'] = TRUE;
            if ( $target == "index")
            {
                $CI = & get_instance();

                $CI->load->config("huffas");

                $index_news = $CI->config->config['news_in_index'];


                if ( ! $index_news['show_old_news'] )
                {
                    $this->db
                         ->where("t.status",1,false)
                        ->where("t.date",date("Y-m-d", strtotime($s_issue_date)));  
                }
                else
                {
                    if ( $index_news['days_to_retrieve_news'] === "0" )
                    {
                        $this->db
                             ->where("t.status",1,false)
                             ->where("t.date <= '" . date("Y-m-d", strtotime($s_issue_date)) . "'");  
                    }
                    else
                    {
                        $target_date = strtotime($s_issue_date);
                        $s_issue_date_from = date("Y-m-d", strtotime($index_news['days_to_retrieve_news'], $target_date));
                        $s_issue_date_to = date('Y-m-d', strtotime($s_issue_date));
                        $this->db
                             ->where("t.status",1,false)
                             ->where("t.date BETWEEN '" . $s_issue_date_from . "' AND '" . $s_issue_date_to . "'");  

                    }
                }
            }

            if ( $s_issue_date_condition == "between" )
            {
                $this->db->where('post.published_date BETWEEN "'. date('Y-m-d H:i:s', strtotime($s_issue_date_from)). '" and "'. date('Y-m-d H:i:s', strtotime($s_issue_date_to)).'"');
            }
            else if ( $s_issue_date_condition == "smaller" )
            {
                if ($this->session->userdata("admin"))
                {

                }
                else
                {
                    $this->db->where('post.published_date <= "'. date('Y-m-d H:i:s', strtotime($s_issue_date_to)). '"');
                }
            }
            else if ( $s_issue_date_condition == "greater" )
            {
                $this->db->where('post.published_date >= "'. date('Y-m-d H:i:s', strtotime($s_issue_date_to)). '"');
            }
            else if ( $s_issue_date_condition == "equal" )
            {
                $this->db->where('post.published_date = "'. date('Y-m-d H:i:s', strtotime($s_issue_date_to)). '"');
            }

            if ( $s_category_ids != 0 && $target != "good_read" && $target != "good_read_unread" )
            {
                $this->db->where("( category.id IN ( ". $s_category_ids . " ) OR ( category.parent_id IN (". $s_category_ids . ")  AND category.id NOT IN (". $s_category_ids . ")))");
            }

            if ( $s_category_ids == 0 && empty($a_post_params) )
            {
                $this->db->where("(category.parent_id IS NULL OR category.parent_id = '')",'',false);
            }
            
            $ignore_post_type = false;
            if ( !empty($a_post_params) )
            {
                foreach( $a_post_params as $key => $value )
                {
                    if ( $key == "CUSTOM" )
                    {
                        $this->db->where($value, '', FALSE);
                    }
                    else if($key == "NOT_IN")
                    {
                        $this->db->where_not_in($value[0], $value[1]);
                    }
                    else if($key == "ignore_post_type")
                    {
                        $ignore_post_type = true;
                    }    
                    else if($key == "q")
                    {
                        $this->db->like('tds_post.headline', $value); 
                    }
                    else if($key == "b_get_related")
                    {
                       //do nothing 
                    }
                    else if($key == "post_id")
                    {
                        $this->db->where("tds_post.id",$value);
                       //do nothing 
                    }
                    else if($key == "force_limit")
                    {
                        $i_limit = 1;
                    }
                    else
                    {
                        $this->db->where($key, $value);
                    }
                }
            }

            $user_post_type = 1; //Initially we assume we retrieve data for visitor
            if ( free_user_logged_in() )
            {
                $user_type = get_free_user_session("type");
                $user_post_type = (empty($user_type) || ($user_type == 0)) ? $user_post_type : $user_type;
            }
            
            if ( $target == "index")
            {
                $this->db->where("t.post_type", $user_post_type);
            }
            elseif (($target == "inner" || $target == "inner-popular" || $target == "search" ) && !$ignore_post_type )
            {
                $this->db->where("postType.type_id", $user_post_type);
            }

            $a_order_by = explode(",", $s_order_by);
            
            $this->db->group_by("tds_post.id");

            if (stripos($s_order_by, "+") !== FALSE )
            {
                $a_order_by = explode("+", $s_order_by);
                foreach( $a_order_by as $order_by )
                {
                    $ar_order_by = explode(",", $order_by);
                    $this->db->order_by($ar_order_by[0], $ar_order_by[1]);
                }
            }
            else
            {
                $a_order_by = explode(",", $s_order_by);
                $this->db->order_by($a_order_by[0], $a_order_by[1]);
            }
            //var_dump($a_post_params['q']);exit;
            if($target == "index")
            {
                $i_page = 0;
            }    
            
            $this->db->offset($i_limit * $i_page);
            $this->db->limit($i_limit);

            $query = $this->db->get(); 

            $query_num_rows = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
            $totaldata = $query_num_rows->row()->Count;

            //In Case of Index and No News, Lets make some fun
            if ( ($target == "index" || $target == "search") && $totaldata == 0 && $b_featured != 1 && ! $index_news['show_old_news'] )
            {
                $target_date = strtotime($s_issue_date);
                $s_issue_date = date("Y-m-d", strtotime("-1 day", $target_date));

                $ar_post_news = $this->gePostNews( $a_post_params, $target, $s_issue_date_condition, $s_order_by, 
                                                   $s_category_ids, $i_limit, $i_page, $b_featured, $i_featured_pos, 
                                                   FALSE, $s_issue_date );
                return $ar_post_news;
            }
            else
            {
                return ( $query->num_rows() > 0 ) ? array("total" => $totaldata, "data" => $query->result()) : FALSE;
            }
        }
    }        
    
    
    public function getHomeNews()
    {
        
        $str = "SELECT `t`.`id` AS `t0_c0`, `post`.`id` AS `tds_post_id`, `post`.`headline`,"
                . " `post`.`content`, `post`.`headline_color`,"
                . " `post`.`summary`, `post`.`short_title`,"
                . " `post`.`lead_material`,"
                . " `post`.`is_breaking`, `post`.`breaking_expire`,"
                . " `post`.`is_exclusive`, `post`.`exclusive_expired`,"
                . " `post`.`published_date`, `postCategories`.`id`,"
                . " `category`.`id`, `category`.`menu_icon`, `category`.`icon`,"
                . " `category`.`name` FROM `tds_homepage_data` `t` "
                . "LEFT OUTER JOIN `tds_post` `post` ON (`t`.`post_id`=`post`.`id`) "
                . "LEFT OUTER JOIN `tds_post_category` `postCategories` ON (`postCategories`.`post_id`=`post`.`id`) "
                . "LEFT OUTER JOIN `tds_categories` `category` ON (`postCategories`.`category_id`=`category`.`id`) "
                . "WHERE (((((post.status=5) AND (t.status=1)) "
                . "AND (t.date='".date("Y-m-d")."')) "
                . "AND (post.published_date <= '".date("Y-m-d H:i:s")."')) "
                . "AND (category.parent_id IS NULL OR category.parent_id = '')) "
                . "ORDER BY t.priority ASC";
        
        print $str;
        $query = $this->db->query($str);
        return $query->result();
    }        
    
      
}
?>
