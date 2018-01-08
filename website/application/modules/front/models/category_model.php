<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Category_model
 *
 * @author ahuffas
 */
class Category_model extends DataMapper {
    //put your code here
    var $table = "categories";
    
    public function get_categories_by_category_type($category_type_id = null){
        $categories_query = "SELECT tds_categories.id, tds_categories.`name`, tds_post_category.post_id, tds_post.lead_material, tds_post.published_date
                        FROM
                        	tds_category_type
                        INNER JOIN tds_categories ON tds_categories.category_type_id = tds_category_type.id
                        INNER JOIN tds_post_category ON tds_post_category.category_id = tds_categories.id
                        INNER JOIN tds_post ON tds_post_category.post_id = tds_post.id
                        WHERE
                        	tds_category_type.id = '".$category_type_id."' AND tds_categories.parent_id IS NOT NULL
                        GROUP BY
                        	tds_post_category.category_id
                        ORDER BY
                        	tds_post.published_date DESC
                        LIMIT 15";
        
        $categories = $this->db->query($categories_query)->result();
        
        return $categories;
    }
    
    public function get_category_id_by_name($s_name = ''){
        $this->select('id');
        $this->where('name', $s_name);
        $obj_category = $this->get();
        return (!empty($obj_category)) ? $obj_category->id : 0;
    }
    
    public function getCategoryInfoByName($s_name = ''){
        $this->select('id AS category_id, category_type_id');
        $this->where('name', $s_name);
        $this->where('status', 1);
        $this->order_by('category_id', 'desc');
        $this->limit(1);
        $obj_category = $this->get();
        return (count($obj_category->all) > 0 ) ? $obj_category : FALSE;
    }
    
    public function getCategoryPdfByName($s_name){
        $this->db->select('categories.id AS category_id, categories.name AS category_name, categories.category_type_id');
        $this->db->from("categories");
        $this->db->where("name", $s_name);
        $this->db->where("status", 1);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
    }
    
    public function get_categories($category_id = null){
        $categories_query = "SELECT tds_categories.id, tds_categories.`name`, tds_categories.`background_color`, tds_categories.category_type_id FROM tds_categories 
                        WHERE tds_categories.id IN (" . $category_id .")";
        
        $categories = $this->db->query($categories_query)->result();
        
        return $categories;
    }
    
    public function get_cover_by_category_id($category_id = null){
        $covers_query = "SELECT tds_category_cover.image, tds_category_cover.issue_date
                        FROM
                        	tds_category_cover
                        WHERE tds_category_cover.category_id = '".$category_id."'
                        ORDER BY tds_category_cover.issue_date DESC LIMIT 1";
                            
        $covers = $this->db->query($covers_query)->result();
        
        return $covers;
    }
    
    public function get_category_cover_by_type($category_type_id = null){
        $obj_post = new Post_model();

        $arIssueDate = $obj_post->getIssueDate();
        $covers_query = "SELECT * FROM ( SELECT tds_categories.id AS category_id, tds_categories.`name` AS category_name, tds_category_cover.image, tds_category_cover.issue_date
		                              FROM
			                             tds_category_type
                                		INNER JOIN tds_categories ON tds_categories.category_type_id = tds_category_type.id
                                		INNER JOIN tds_category_cover ON tds_category_cover.category_id = tds_categories.id
                                		WHERE
                                			tds_category_type.id = '".$category_type_id."' AND 
                                                        tds_category_cover.issue_date <= '" . $arIssueDate['s_issue_date'] . "'    
                                		ORDER BY
                                			tds_category_cover.issue_date DESC,
                                			tds_categories.id ASC
                                	) AS tmp_cover
                                    GROUP BY category_id
                                    ORDER BY issue_date DESC, category_id ASC";
        $covers = $this->db->query($covers_query)->result();
        
        return $covers;
    }
    
    public function get_weekly_categories($limit = 0){
        $this->order_by('weekly_priority');
        $this->where(array("category_type_id" => 2, "status" => 1));
        $this->where("parent_id IS NULL");
        if($limit > 0){
            $this->limit($limit);
        }
        $obj_cat = $this->get();
        return (count($obj_cat->all) > 0) ? $obj_cat : FALSE;
    }
    
    public function getCategoryByPostId($i_post_id){
        $this->db->select('category_id, categories.name AS category_name');
        $this->db->from('post_category');
        $this->db->join('categories','categories.id = post_category.category_id','inner');
        $this->db->where('post_id', $i_post_id);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
    }
    
    public function get_magazine_menu($id = 0, $parent_id = null){
        
        $html = '';
        
        $cate_id = ($id > 0) ? $id : $parent_id;
        
        $this->where(array('parent_id' => $cate_id, 'status' => 1, 'inner_page_menu' => 1));
        $obj_sub_categories = $this->get();
        
        if($id > 0){
            $html = '<div class="ym-grid drop">';
            
                $html .= '<div class="ym-g20 menu-list ym-gl">
                             <img src="images/magazin/menu-list.png" alt="Menu List" />
                          </div>';
            
                $html .= '<div class="ym-g80 menu-content ym-gl">';
        }
        
                    if(count($obj_sub_categories->all) > 0){
                        
                            $html .= ($id > 0) ? '<ul class="drop_menu">' : '<ul>';
                        
                            foreach($obj_sub_categories as $obj_sub_category){
                                $html .= '<li><a href="'. sanitize($obj_sub_category->name) .'">'. $obj_sub_category->name .'</a>'.  $this->get_magazine_menu(0, $obj_sub_category->id) .'</li>';
                            }
                            
                        $html .= '  </ul>';
                        
                    }
                    
        if($id > 0){
                $html .= '</div>';
            $html .= '</div>';
        }
        
        return $html;
        
    }

}

?>
