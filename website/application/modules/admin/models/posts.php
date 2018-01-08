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
        'short_title' => array(
            'label' => 'Short Title',
            'rules' => array('trim','max_length' => 255),
        ),
//        'type_post[]' => array(
//            'label' => 'Type',
//            'rules' => array('required')
//        ),
//        'content' => array(
//            'label' => 'Content',
//            'rules' => array('required','trim')
//        ),
        'type' => array(
            'label' => 'Type',
            'rules' => array('required','trim'),
        ),
        'published_date' => array(
            'label' => 'Published Date',
            'rules' => array('required','trim'),
        ),
        'editor_picks' => array(
            'label' => 'Editor Picks',
            'rules' => array('required', 'integer'),
        ),
        'top_rated' => array(
            'label' => 'Top Rated',
            'rules' => array('required', 'integer'),
        ),
        'most_popular' => array(
            'label' => 'Most Popular',
            'rules' => array('required', 'integer'),
        ),
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
    function get_country_string($id)
    {
        $sql_countries = "select GROUP_CONCAT(DISTINCT countries.name) as countries from "
                . " tds_post LEFT JOIN tds_post_country on tds_post.id=tds_post_country.post_id"
                . " LEFT JOIN countries on countries.id = tds_post_country.country_id"
                . " where tds_post.id=$id";
        $obj_tags = $this->db->query($sql_countries)->row();
        
        $country_array = explode(",",$obj_tags->countries);
        $c_nam = array();
        foreach($country_array as $value)
        {
            $name_array = explode("(", trim($value));
            $c_nam[] = trim($name_array[0]);
        }    
        
        return implode(",",$c_nam);
        
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
    
    function get_gallery($id,$type=1)
    {
        $query_gallery = "select g.caption,g.source,m.material_url from tds_post_gallery as g left join  tds_materials as m on (g.material_id=m.id)
            where g.post_id=".$id." and g.type=".$type;
       
        $related_gallery =$this->db->query($query_gallery)->result();

        return $related_gallery;
    }
    
    function get_related_attach($id)
    {
        $this->db->select("file_name,show,caption");
        $this->db->where("post_id",$id);

        $attach = $this->db->get("post_attachment")->result();
        return $attach;
    }
    
   
    
    function get_caption_source($material_url)
    {
        $query_gallery = "select caption,source from tds_materials
            where material_url='".str_replace(base_url(),"",$material_url)."'";
       
        $related_caption =$this->db->query($query_gallery)->row();

        return $related_caption;
    }
    
    function get_count_news_in_priority($post_id,$publish_date)
    {
        $this->db->select("priority_type,count(id) as count_news");
        $this->db->where("id!=",$post_id,false);
        $this->db->where("status!=",6,false);
        $this->db->where("published_date",$publish_date);
        $this->db->group_by("priority_type");

        $news = $this->db->get("post")->result();
        $count_array = array();
        foreach($news as $value)
        {
           $count_array[$value->priority_type] =  $value->count_news;
        }    
        
        
        return $count_array;
    }
    
    function get_related_news($id)
    {
        $this->db->select("new_link,title,published_date");
        $this->db->where("post_id",$id);

        $related_news = $this->db->get("related_news")->result();
        return $related_news;
    }
    function category_tree($id=0)
    {
        $arTemp = array(); // array('title' => 'Choose Links'); 

        $obj_category = new Category();
        $array = array('parent_id' => NULL, 'status' => 1, 'show' => 1);

        $obj_category->where($array)->order_by('name', 'asc')->get();

        if (count($obj_category) > 0)
        {
            foreach ($obj_category as $value)
            {
                $has_category = $this->db->get_where('post_category', array('category_id' => $value->id, 'post_id' => $id))->row();

                $arTemps['title'] = $value->name;
                $arTemps['game_type'] = $value->game_type;
                $arTemps['id'] = $value->id;
                $arTemps['checked'] = (count($has_category) > 0) ? true : false;
                $arTemps['children'] = array();

                $arChildrens = $this->db->get_where('categories', array('parent_id' => $value->id, 'status' => 1))->result();

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

    
    function get_posts_published_date($s_issue_date_from, $s_issue_date_to, $ar_priority_type = array(1,2,3))
    {
        #GET NEWS FROM POST TABLE
        $this->select("DISTINCT tds_post.id,post.*", false);
        $this->include_join_fields(FALSE);
        
        $this->where_between("published_date", $s_issue_date_from, $s_issue_date_to);
        $this->where("priority_type IN (" .  implode(",", $ar_priority_type) . ")");
        $this->order_by("post.priority_type", "asc");
        $this->order_by("post.priority", "asc");


        $obj_rows = $this->get();

        return $obj_rows;
    }
    
    function update_priority($i_priority_id, $i_priority_type, $id)
    {
        $ar_data = array(
            'priority_type' => $i_priority_type,
            'priority'      => $i_priority_id
        );
        $this->db->where('id', $id);
        $this->db->update("post", $ar_data);
    }
    
    
    function type_tree_homepage($id)
    {
        $this->load->config("champs21");
        $type_array = $this->config->config['user_type'];
        $html = "";
        foreach($type_array as $i=>$value)
        {
            
            $has_category = $this->db->get_where('post_type', array('type_id' => $i, 'post_id' => $id))->row();
            
          
            if(count($has_category)>0) 
            {
                $html.= "<ul>";   

                $html.= '<li><input type="checkbox" value="'.$i.'" checked="checked" name="type_post[]"><span>'.$value.'</span></li>'; 


                $html.= "</ul>"; 
            }


        }
        
        
        return $html;
    }
    
    function type_tree_news($id=0,$parent_id=NULL)
    {
        $this->load->config("champs21");
        $type_array = $this->config->config['user_type'];
       
        foreach($type_array as $i=>$value)
        {
            if($id)
            {
                $has_category = $this->db->get_where('post_type', array('type_id' => $i, 'post_id' => $id))->row();
                $checked = (count($has_category) > 0) ? "checked='checked'" : "";
            }
            else
            {
                $checked =  "checked='checked'";
            }    
             
            $html.= "<ul>";   

            $html.= '<li><input type="checkbox" value="'.$i.'" '.$checked.' name="type_post[]"><span>'.$value.'</span></li>'; 

           
            $html.= "</ul>"; 


        }
        
        
        return $html;
    }
    
    function class_tree_news($id=0,$parent_id=NULL)
    {
       
        for ($i=1;$i<=10; $i++)
        {
            if($id)
            {
                $has_category = $this->db->get_where('post_class', array('class_id' => $i, 'post_id' => $id))->row();
                $checked = (count($has_category) > 0) ? "checked='checked'" : "";
            }
            else
            {
                $checked =  "checked='checked'";
            }    
             
            $html.= "<ul>";   

            $html.= '<li><input type="checkbox" value="'.$i.'" '.$checked.' name="class[]"><span>Class '.$i.'</span></li>'; 

           
            $html.= "</ul>"; 


        }
        
        
        return $html;
    }
    function category_array($parent_id=null)
    {
       $obj_category = new Category();
       if($parent_id>1)
       {
          $array = array('parent_id' => $parent_id, 'status' => 1,'show'=>1);  
       }   
       else
       {
          $array = array('parent_id' => null, 'status' => 1,'show'=>1);  
       }    
       
       $obj_category->where($array)->order_by('name', 'asc')->get(); 
       $select_category[0] = "Select";
       if (count($obj_category) > 0)
       {
           foreach ($obj_category as $value)
           {
               $select_category[$value->id] = $value->name;
           }
       }
       return $select_category;
    }
    
    function category_tree_news($id=0,$parent_id=NULL)
    {
        $obj_category = new Category();
        $array = array('parent_id' => $parent_id, 'status' => 1,'show'=>1);

        $obj_category->where($array)->order_by('name', 'asc')->get();
       
        $html = "";
        if (count($obj_category) > 0)
        {
            foreach ($obj_category as $value)
            {
                $has_category = $this->db->get_where('post_category', array('category_id' => $value->id, 'post_id' => $id))->row();
                $checked = (count($has_category) > 0) ? "checked='checked'" : "";
                
                $category_id = ($value->game_type)?"id='game_type'":"";
                if($parent_id!=NULL)
                $html.= "<ul>";   
                
                $html.= '<li><input  type="checkbox" value="'.$value->id.'" '.$category_id.' '.$checked.' name="category[]"><span>'.$value->name.'</span>'; 

                $html.= $this->category_tree_news($id,$value->id);
                $html.= "</li>";   
                if($parent_id!=NULL)
                $html.= "</ul>"; 

               
            }
        }
        return $html;
    }
    
   
}
