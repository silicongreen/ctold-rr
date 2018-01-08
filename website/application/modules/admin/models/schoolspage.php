<?php

class schoolspage extends DataMapper {

    var $table = "school_page";
    
    var $validation = array(
        'title' => array(
            'label' => 'Title',
            'rules' => array('required', 'trim'),
        ),
//        'content' => array(
//            'label' => 'Content',
//            'rules' => array('required', 'trim'),
//        ),
        'menu_id' => array(
            'label' => 'Menu',
            'rules' => array('required', 'trim', 'unique_pair' => 'school_id'),
        ),
        'school_id' => array(
            'label' => 'School',
            'rules' => array('required', 'trim'),
        ),
    );
    
    function getSchoolMenu()
    {
        return $this->db->get("school_menu")->result();
    }
    
    function getSchool($condition=false)
    {
        return $this->db->get("school")->result();
    }
    function checkschoolhaspage($id)
    {
        $this->db->where("school_id",$id);
        $schoolspage = $this->db->get("school_page")->row();
        if(count($schoolspage)>0)
        {
            return false;
        }  
        return true;
    }
    function get_gallery($id)
    {
        $query_gallery = "select m.material_url from tds_school_page_gallery as g left join  tds_materials as m on (g.material_id=m.id)
            where g.page_id=".$id;
       
        $related_gallery =$this->db->query($query_gallery)->result();

        return $related_gallery;
    }


   
}
