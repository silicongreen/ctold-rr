<?php

class schoolsactivities extends DataMapper {

    var $table = "school_activities";
    
    var $validation = array(
        'title' => array(
            'label' => 'Title',
            'rules' => array('required', 'trim'),
        ),
        'content' => array(
            'label' => 'Content',
            'rules' => array('required', 'trim'),
        ),
        'school_id' => array(
            'label' => 'School',
            'rules' => array('required', 'trim'),
        ),
        'date' => array(
            'label' => 'Activities Date',
            'rules' => array('required', 'trim'),
        ),
    );
    
    
    
    function getSchool()
    {
        return $this->db->get("school")->result();
    }
    function get_gallery($id)
    {
        $query_gallery = "select m.material_url from tds_school_activities_gallery as g left join  tds_materials as m on (g.material_id=m.id)
            where g.activities_id=".$id;
       
        $related_gallery =$this->db->query($query_gallery)->result();

        return $related_gallery;
    }


   
}
