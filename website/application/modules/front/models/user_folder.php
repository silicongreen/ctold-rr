<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class User_folder extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_user_good_read_folder( $i_user_id, $offset = 0, $limit = 100 )
    {
        $this->db->select('SQL_CALC_FOUND_ROWS id, title',false )
                ->from('user_folder')
                ->where('user_id', $i_user_id, FALSE)
                ->where('status', 1, FALSE)
                ->where('visible', 1, FALSE)
                ->offset( $offset * $limit )
                ->limit( $limit );
        $query = $this->db->get(); 
      
        $query_num_rows = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $totaldata = $query_num_rows->row()->Count;
        return ( $query->num_rows() > 0 ) ? array("total" => $totaldata, "data" => $query->result()) : FALSE;
    }  
    public function get_user_good_read_post_count($i_user_id, $i_folder_id, $folder_visible,$s_folder_name="")
    {
        $is_read = ($folder_visible == 1 )?1:0;
        
        $this->db->select('SQL_CALC_FOUND_ROWS id',false )
                ->from('user_good_read')
                ->where('user_id', $i_user_id, FALSE);
        
        //if($s_folder_name!="Unread" && $s_folder_name!="unread" )
        //{
            $this->db->where('folder_id', $i_folder_id, FALSE);
        //}
              
              
        //$this->db->where('is_read', $is_read, FALSE);
                
        $query = $this->db->get(); 
      
        $query_num_rows = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $totaldata = $query_num_rows->row()->Count;
        return ( $query->num_rows() > 0 ) ? array("totalpost" => $totaldata) : FALSE;
        
    }

    public function save_post_to_user_good_read_folder( $ar_user_good_read  )
    {
        $this->db->select('*',false )
                ->from('user_good_read')
                ->where('user_id', $ar_user_good_read['user_id'], FALSE)
                ->where('folder_id', $ar_user_good_read['folder_id'], FALSE)
                ->where('post_id', $ar_user_good_read['post_id'], FALSE);
        $query = $this->db->get(); 
        
        if ( $query->num_rows() > 0 )
        {
            return "The Post already is in your selected folder" ;
        }
        else
        {
            $this->db->insert("user_good_read", $ar_user_good_read); 
        }
      
        return TRUE;
    }
    public function delete_post_from_user_good_read_folder( $ar_user_good_read  )
    {
        $this->db->select('*',false )
                ->from('user_good_read')
                ->where('user_id', $ar_user_good_read['user_id'], FALSE)
                ->where('folder_id', $ar_user_good_read['folder_id'], FALSE)
                ->where('post_id', $ar_user_good_read['post_id'], FALSE);
        $query = $this->db->get(); 
        
        if ( $query->num_rows() > 0 )
        {
            $this->db->where('post_id', $ar_user_good_read['post_id'], FALSE);
            $this->db->where('user_id', $ar_user_good_read['user_id'], FALSE);
            $this->db->where('folder_id', $ar_user_good_read['folder_id'], FALSE);
            $this->db->delete("user_good_read", $data); 
            
            echo "Done";
        }
        else
        {
            
            return "The Post is already deleted" ;
            //$this->db->insert("user_good_read", $ar_user_good_read); 
        }
      
        return TRUE;
    }
    public function save_user_good_read_folder( $ar_user_good_read  )
    {
        $this->db->select('*',false )
                ->from('user_folder')
                ->where('user_id', $ar_user_good_read['user_id'], FALSE)
                ->where('title', $ar_user_good_read['title']);
        $query = $this->db->get(); 
        
        if ( $query->num_rows() > 0 )
        {
            return "The Folder you try to create is already exists" ;
        }
        else
        {
            $this->db->insert("user_folder", $ar_user_good_read); 
            return $this->db->insert_id();
        }
      
        return TRUE;
    }
    
    public function remove_user_good_read_folder( $i_user_id, $i_folder_id  )
    {
        $data = array("status" => 0);
        
        $this->db->where('id', $i_folder_id, FALSE);
        $this->db->where('user_id', $i_user_id, FALSE);
        
        $this->db->update("user_folder", $data); 
      
        return TRUE;
    }
    public function set_unread_post_to_read($i_user_id, $i_post_id,$folder_id)
    {
        $data = array("is_read" => 0);
        
        $this->db->where('post_id', $i_post_id, FALSE);
        $this->db->where('user_id', $i_user_id, FALSE);
        $this->db->where('folder_id', $folder_id, FALSE);
        
        $this->db->delete("user_good_read", $data); 
      
        return TRUE;
    }
    public function get_folder_id( $i_user_id, $s_folder_name, $b_saved = TRUE  )
    {  
        $this->db->where('title', $s_folder_name);
        $this->db->where('user_id', $i_user_id, FALSE);
        
        $this->db->from("user_folder");       
        $query = $this->db->get();        
        if ( $query->num_rows() > 0 )
        {
            $row = $query->row();            
            return $row;
        }
        else if ( $b_saved )
        {     
            $data['user_id'] = $i_user_id;
            $data['title'] = $s_folder_name;
            $data['status'] = 1;
            $data['visible'] = 0;
            $this->db->insert("user_folder", $data); 
            return $this->db->insert_id();
        }
        
        return FALSE;
    }
    public function get_folder_data( $i_user_id, $i_folder_id  )
    { 
        $this->db->where('id', $i_folder_id);
        $this->db->where('user_id', $i_user_id, FALSE);
        
        $this->db->from("user_folder");       
        $query = $this->db->get();
        
        if ( $query->num_rows() > 0 )
        {
            $row = $query->row();            
            return $row;
        }
        
        return FALSE;
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
        
        $this->db->select('SQL_CALC_FOUND_ROWS DISTINCT tds_post.id as post_id, tds_post.headline, tds_post.content,
                           tds_post.headline_color, tds_post.summary, tds_post.short_title, tds_post.shoulder, tds_post.other_language,
                           tds_post.sub_head, tds_post.lead_material, tds_post.is_breaking, tds_post.breaking_expire, tds_post.language,
                           tds_post.published_date, tds_post.view_count, postCategories.id, tds_post.user_view_count, 
                           category.id, category.menu_icon, category.icon, category.name, byline.title, tds_post.referance_id ',false )
                ->from('post');
        
        if ( $target == "index" )
        {
            $this->db->join("homepage_data as t", "post.id = t.post_id", 'INNER');
        }
        
        if ($this->config->config['country_filter'] && $i_post_id > 0)
        {
            $this->db->join("post_country as pc", "post.id = pc.post_id", 'LEFT');
            $this->db->where("( tds_post.all_country = 1 OR pc.country_id = " . $country_id . ")", '', FALSE);
        }
        
        $this->db
             ->join("bylines as byline", "post.byline_id = byline.id", 'LEFT')
             ->join("post_category as postCategories", "post.id = postCategories.post_id", 'INNER')
             ->join("categories as category", "category.id = postCategories.category_id", 'INNER')
             ->where("tds_post.status",5,false);
        
        if ( $b_featured  )
        {
            $this->db->where("tds_post.is_featured", 1, false);
            $this->db->where("tds_post.feature_position", $i_featured_pos,false);
        }
        else
        {
            if ( empty($a_post_params) )
            {
                $this->db->where("( tds_post.is_featured = 0 OR tds_post.is_featured IS NULL)", '', FALSE);
            }
        }
        
        if ( $target == "index" )
        {
            $this->db
                 ->where("t.status",1,false)
                 ->where("t.date",date("Y-m-d", strtotime($arIssueDate['s_issue_date'])));  
        }
        
        if ( $s_issue_date_condition == "between" )
        {
            $this->db->where('post.published_date BETWEEN "'. date('Y-m-d H:i:s', strtotime($s_issue_date_from)). '" and "'. date('Y-m-d H:i:s', strtotime($s_issue_date_to)).'"');
        }
        else if ( $s_issue_date_condition == "smaller" )
        {
            $this->db->where('post.published_date <= "'. date('Y-m-d H:i:s', strtotime($s_issue_date_from)). '"');
        }
        else if ( $s_issue_date_condition == "greater" )
        {
            $this->db->where('post.published_date >= "'. date('Y-m-d H:i:s', strtotime($s_issue_date_from)). '"');
        }
        else if ( $s_issue_date_condition == "equal" )
        {
            $this->db->where('post.published_date = "'. date('Y-m-d H:i:s', strtotime($s_issue_date_from)). '"');
        }
        
        if ( $s_category_ids != 0 )
        {
            $this->db->where("category.id IN (". $s_category_ids . ")");
        }
        
        
        if ( $s_category_ids == 0 && empty($a_post_params) )
        {
            $this->db->where("(category.parent_id IS NULL OR category.parent_id = '')",'',false);
        }
        
        if ( !empty($a_post_params) )
        {
            foreach( $a_post_params as $key => $value )
            {
                $this->db->where($key, $value);
            }
        }
        
        $type = 1; //Initially we assume we retrieve data for visitor
        if ( free_user_logged_in() )
        {
            $type = get_free_user_session("type");
        }
        if ( $target == "index" )
        {
            $this->db->where("t.post_type", $type, false);
        }
        
        $a_order_by = explode(",", $s_order_by);
        
        $this->db->offset($i_limit * $i_page);
        $this->db->limit($i_limit);
        $this->db->order_by($a_order_by[0], $a_order_by[1]);
        $query = $this->db->get(); 
        
        $query_num_rows = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $totaldata = $query_num_rows->row()->Count;
        return ( $query->num_rows() > 0 ) ? array("total" => $totaldata, "data" => $query->result()) : FALSE;
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
    
    public function created_good_read_folders($ar_param) {
        
        $str_folder = "";
        foreach($ar_param['folders'] as $folder){
            $str_folder .= "'" . $folder . "', ";
        }
        
        $this->db->where('user_id', $ar_param['user_id']);
        $this->db->where("title IN (". rtrim($str_folder, ', ') . ")");
        
        $this->db->from('user_folder');
        
        $rows = $this->db->get()->result(); 
        
        if(!empty($rows)){
            
            foreach($rows as $row){
                
                foreach ($ar_param['folders'] as $folder) {
                    
                    if( trim($row->title) != $folder ){
                        $data['user_id'] = $ar_param['user_id'];
                        $data['title'] = $folder;

                        $this->db->insert("user_folder", $data);
                    }
                }
            }
        }  else {
            
            foreach ($ar_param['folders'] as $folder) {

                $data['user_id'] = $ar_param['user_id'];
                $data['title'] = $folder;
                $data['visible'] = 1;
                if($folder == "Unread")
                {
                    $data['visible'] = 0;
                }

                $this->db->insert("user_folder", $data);
            }
        }
        
        return TRUE;
    }
    
      
}
?>
