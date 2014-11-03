<?php

class schools extends DataMapper {

    var $table = "school";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'unique'),
        )
    );
    
    function find_school_by_name($name)
    {
        $this->db->select("id,name,district,cover,logo,views");
        $this->db->where("name",trim($name));
        $this->db->limit(1);
        return $this->db->get("school")->row();
    }
    function find_menu_by_name($title)
    {
        $this->db->select("id,title");
        $this->db->where("title",trim($title));
        $this->db->limit(1);
        return $this->db->get("school_menu")->row();
    } 
    function find_default_school_menu($school_id)
    {
        $this->db->select("school_menu.title,school_menu.id");
        $this->db->join('school_menu', 'school_menu.id = school_page.menu_id',"INNER");
        $this->db->where("school_id",$school_id);
        $this->db->order_by("school_menu.id","ASC");
        $this->db->limit(1);
        return $this->db->get("school_page")->row();
        
    }
    function find_menu_by_id($id)
    {
        $this->db->select("id,title");
        $this->db->where("id",$id);
        $this->db->limit(1);
        return $this->db->get("school_menu")->row();
    } 
    function find_page_details($menu_id,$school_id)
    {
        $this->db->select("id,title,content");
        $this->db->where("school_id",$school_id);
        $this->db->where("menu_id",$menu_id);
        $this->db->limit(1);
        $pages = $this->db->get("school_page")->row();
        if(count($pages)>0)
        {
            return $pages;
        }
        else
        {
            $this->db->select("id,title,content");
            $this->db->where("school_id",$school_id);
            $this->db->limit(1);
            return $this->db->get("school_page")->row();
        }    
        
        
        
        
    } 
    function find_activity_details($activity_id)
    {
        $this->db->select("id,title,content");
        $this->db->where("id",$activity_id);
        $this->db->limit(1);
        return $this->db->get("school_activities")->row();
        
        
    } 
    function find_school_pages($school_id)
    {
        $this->db->select("tds_school_page.id,school_menu.title");
        $this->db->join('school_menu', 'school_menu.id = school_page.menu_id',"INNER");
        $this->db->where("school_id",$school_id);
        $this->db->order_by("school_menu.id","ASC");
        return $this->db->get("school_page")->result();
        
    }
    function find_activity_gallery($activity_id)
    {
        $query_gallery = "select m.material_url from tds_school_activities_gallery as g left join  tds_materials as m on (g.material_id=m.id)
            where g.activities_id=".$activity_id;
       
        $related_gallery =$this->db->query($query_gallery)->result();

        return $related_gallery;
    }
    function find_page_gallery($page_id)
    {
        $query_gallery = "select m.material_url from tds_school_page_gallery as g left join  tds_materials as m on (g.material_id=m.id)
            where g.page_id=".$page_id;
       
        $related_gallery =$this->db->query($query_gallery)->result();

        return $related_gallery;
    }
    function increament_views($school_id)
    {
        $sql = "update tds_school set views=views+1 where id=".$school_id;
        $this->db->query($sql);
    }
    function find_all_ativity($school_id)
    {
         $this->db->select("id,content,title");
         $this->db->where("school_id",$school_id);
         $this->db->order_by("Date","DESC");
         
         $activities = $this->db->get("school_activities")->result();
         
         $activities_array = array();
         $i = 0;
         if($activities)
         foreach($activities as $value)
         {
             $activities_array[$i]['title'] = $value->title;
             $activities_array[$i]['id'] = $value->id;
             
             $activities_array[$i]['image'] = "";
             
             $images = $this->content_images($value->content);
             
             if($images)
             {
                $activities_array[$i]['image'] = $images[0]; 
                $activities_array[$i]['content'] = $this->substr_with_unicode($value->content,false,200);
             }
             else
             {
                 $activities_array[$i]['content'] = $this->substr_with_unicode($value->content,false,400);
             }    
             
             
             
             $i++;
         }   
         return $activities_array;
    }
    
    function getActivities($school_id)
    {
         $this->db->select("id,content,title");
         $this->db->where("school_id",$school_id);
         $this->db->order_by("Date","DESC");
         $this->db->limit(4);
         
         $activities = $this->db->get("school_activities")->result();
         
         $activities_array = array();
         $i = 0;
         if($activities)
         foreach($activities as $value)
         {
             $activities_array[$i]['title'] = $value->title;
             $activities_array[$i]['id'] = $value->id;
             
             $activities_array[$i]['image'] = "";
             
             $images = $this->content_images($value->content);
             
             if($images)
             {
                $activities_array[$i]['image'] = $images[0]; 
                $activities_array[$i]['content'] = $this->substr_with_unicode($value->content,false,200);
             }
             else
             {
                 $activities_array[$i]['content'] = $this->substr_with_unicode($value->content,false,400);
             }    
             
             
             
             $i++;
         }   
         return $activities_array;
    }
    
    public static function substr_with_unicode($string,$full_length=false,$length=80) 
    {
        $string = preg_replace('/<div (.*?)>Source:(.*?)<\/div>/', '', $string);
        $string = preg_replace('/<div class="img_caption" (.*?)>(.*?)<\/div>/', '', $string);

        if($full_length===false)
        {
            return  mb_substr(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')),0,$length, 'UTF-8');
        }
        else
        {
            $main_string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
            return  mb_substr($main_string,0,  mb_strlen($main_string, 'UTF-8'), 'UTF-8');
        }    
    }
    
    public static function get_activity_image($url,$replace_url = "gallery/facebook/")
    {
       $image = str_replace("gallery/", $replace_url, $url);
       list($width, $height, $type, $attr) = @getimagesize($image);
       if(!isset($width))
       {
           return $url;
       }
       return $image;
    }    
    
    public static function content_images($content,$first_image=true)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $images = $doc->getElementsByTagName('img');
        $all_image = array();
        $i = 1;
        foreach ($images as $image) 
        {
           if(strpos($image->getAttribute('src'),"relatednews.jpg") !== FALSE)
           {
               continue;
           }
           else if($i==1 && $first_image===false)
           {
              continue; 
           }    
           else
           {
               $all_image[] = self::get_activity_image($image->getAttribute('src'));
           } 
           $i++;

        }
        return $all_image;
    }  
    


   
}
