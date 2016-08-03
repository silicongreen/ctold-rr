<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class innermiddlenews extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_category_with_news($con_value,$issue_end_time)
    {
        $category_with_news_query = "SELECT tds_bylines.title,tds_post_category.category_id,tds_categories.name,tds_post.headline,tds_post.id,tds_post.content,tds_post.lead_material FROM 
                    tds_post_category left join tds_post on (tds_post_category.post_id =tds_post.id) 
                    left join tds_categories on (tds_post_category.category_id =tds_categories.id) 
                    left join tds_bylines on (tds_post.byline_id =tds_bylines.id) 
                    where 
                    category_id = " . $con_value['category_id'] . "
                    and tds_post.status = 5
                    and tds_post.published_date<='" . $issue_end_time . "'";

        if ($con_value['show_image'] && !$con_value['show_content'])
            $category_with_news_query.=" and tds_post.lead_material!='' ";

        $category_with_news_query.=" order by tds_post.published_date desc limit " . $con_value['news_count'];

        $data = $this->db->query($category_with_news_query)->result();
      
        
        return $data;
    } 
    public function get_quotes($con_value)
    {
         $CI = & get_instance();
         
         $CI->load->model("post_model");
         $arIssueDate = $CI->post_model->getIssueDate();
         
         $issue_end_time = $arIssueDate['issue_date_to'];
    
         $quotes_query = "SELECT tds_personality.name,tds_quotes.quote FROM 
                    tds_quotes left join tds_personality on (tds_quotes.personality_id =tds_personality.id) 
                    where tds_quotes.is_active = 1
                    and tds_quotes.published_date<'" . $issue_end_time . "' 
                    order by tds_quotes.published_date desc limit " . $con_value['news_count'];
       
        $data = $this->db->query($quotes_query)->result();
        
        return $data;
    } 
}
?>
