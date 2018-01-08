<?php

class Posts extends DataMapper {

    var $table = "post";
    public $single_page_controller = "single";
    
    var $validation = array(
        'shoulder' => array(
            'label' => 'Shoulder',
            'rules' => array('trim','max_length' => 60),
        ),
        'headline' => array(
            'label' => 'Headline',
            'rules' => array('required', 'trim','max_length' => 100),
        ),
        'sub_head' => array(
            'label' => 'Sub Head',
            'rules' => array('trim','max_length' => 255),
        ),
        'category[]' => array(
            'label' => 'Category',
            'rules' => array('required'),
        ),
        'short_title' => array(
            'label' => 'Short Title',
            'rules' => array('trim','max_length' => 60),
        ),
        'content' => array(
            'label' => 'Content',
            'rules' => array('required','trim')
        ),
        'type' => array(
            'label' => 'Type',
            'rules' => array('required','trim'),
        ),
        'published_date' => array(
            'label' => 'Published Date',
            'rules' => array('required','trim'),
        )
    );
    
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
        )
    );
    
    function get_tag_string($id)
    {
        $this->db->select("GROUP_CONCAT(DISTINCT pre_tag.tags_name) as tags");
        $this->db->from("post")->join("post_tags as pre_post_tags", "post.id=pre_post_tags.post_id", 'LEFT')
        ->join("tags as pre_tag", "pre_post_tags.tag_id=pre_tag.id", 'LEFT');
        $this->db->where("tds_post.id",$id,false);
        $obj_tags = $this->db->get()->row();
        return $obj_tags->tags;
        
    }
    
    function get_keyword_string($id)
    {
        $this->db->select("GROUP_CONCAT(DISTINCT pre_keyword.value) as keywords");
        $this->db->from("post")->join("post_keyword as pre_post_keyword", "post.id=pre_post_keyword.post_id", 'LEFT')
        ->join("keywords as pre_keyword", "pre_post_keyword.keyword_id=pre_keyword.id", 'LEFT');
        $this->db->where("tds_post.id",$id,false);
        $obj_keyword = $this->db->get()->row();
        return $obj_keyword->keywords;
        
    }
    function get_byline_by_id($id)
    {
        if($id!=0)
        {
            $this->db->select("title");
            $this->db->where("id",$id);
            $this->db->limit(1);
            $byline = $this->db->get("bylines")->row();
            return $byline->title;
        }
        else
        {
            return "";
        }    
    }
    
    function get_gallery($id)
    {
        $query_gallery = "select g.caption,g.source,m.material_url from tds_post_gallery as g left join  tds_materials as m on (g.material_id=m.id)
            where g.post_id=".$id;
       
        $related_gallery =$this->db->query($query_gallery)->result();

        return $related_gallery;
    }
    
    
    function get_related_news($id)
    {
        $this->db->select("new_link,title");
        $this->db->where("post_id",$id);

        $related_news = $this->db->get("related_news")->result();
        return $related_news;
    }
    function category_tree($id=0)
    {
        $arTemp = array(); // array('title' => 'Choose Links'); 

        $obj_category = new Category();
        $array = array('parent_id'=> NULL);

        $obj_category->where($array)->get();

        if (count($obj_category) > 0)
        {
            foreach ($obj_category as $value)
            {
                $has_category = $this->db->get_where('post_category', array('category_id' => $value->id, 'post_id' => $id))->row();

                $arTemps['title'] = $value->name;
                $arTemps['id'] = $value->id;
                $arTemps['checked'] = (count($has_category) > 0) ? true : false;
                $arTemps['children'] = array();

                $arChildrens = $this->db->get_where('categories', array('parent_id' => $value->id))->result();


                $arChildrenTemp = array();
                if (count($arChildrens > 0))
                {
                    foreach ($arChildrens as $objChildren)
                    {
                        $has_category = $this->db->get_where('post_category', array('category_id' => $objChildren->id, 'post_id' => $id))->row();
                        $arChildrenTemp['title'] = $objChildren->name;
                        $arChildrenTemp['checked'] = (count($has_category) > 0) ? true : false;
                        $arChildrenTemp['id'] = $objChildren->id;
                        $arTemps['children'][] = $arChildrenTemp;
                    }
                }
                $arTemp[] = $arTemps;
            }
        }

        return $arTemp;
    }

    function get_posts_by_category($i_category_id, $s_issue_date_from, $s_issue_date_to)
    {
        #GET NEWS FROM POST TABLE
        $this->select("DISTINCT tds_post.id,post.*", false);
        $this->include_join_fields(FALSE);
        $this->include_related("post_category", "id", FALSE, FALSE, TRUE, "INNER");
        $this->include_related("category", "id", FALSE, FALSE, TRUE, "INNER", "post_category_tds_post_category", "category_id");
        
        $this->where("category_tds_categories.id", $i_category_id);
        $this->where_between("published_date", $s_issue_date_from, $s_issue_date_to);
        $this->order_by("post.priority", "asc");


        $obj_rows = $this->get();

        return $obj_rows;
    }

    
   
}
