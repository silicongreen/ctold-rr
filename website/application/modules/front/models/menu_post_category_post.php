<?php
class Menu_post_category_post extends DataMapper{
    var $table = "menu";
    var $has_many = array(
        'menu_post_category' => array(			// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_post_category',
            'other_field' => 'category',
            'class' => "Post_category"
        ),
        'post_category_post' => array(			// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_post',
            'other_field' => 'id',
            'class' => "Post_model"
        ),
    );
    
    public function get_news($category_id, $issue_date, $limit = null){
        $this->select("post_category_post_tds_post.id,post_category_post_tds_post.headline,post_category_post_tds_post.content,post_category_post_tds_post.lead_material,post_category_post_tds_post.published_date");
        $this->include_join_fields(FALSE);
        $this->include_related("menu_post_category","id",FALSE, FALSE, TRUE, "INNER","tds_menu","category_id");
        $this->include_related("post_category_post","id",FALSE, FALSE, TRUE, "INNER", "menu_post_category_tds_post_category", "post_id");
        $this->where_in('menu_post_category_tds_post_category.category_id', $category_id);
        $this->where('DATE(post_category_post_tds_post.published_date) <= ', $issue_date);
        $this->group_by('post_category_post_tds_post.id');
        $this->order_by('post_category_post_tds_post.id','DESC');
        $this->order_by('post_category_post_tds_post.published_date','DESC');
        if(!empty($limit)){
            $this->limit($limit);
        }
        $obj_news = $this->get();
        return $obj_news;
    }
}
?>