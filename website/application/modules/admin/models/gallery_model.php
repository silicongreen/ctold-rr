<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Gallery
 *
 * @author ahuffas
 */
class Gallery_model extends DataMapper  {
    //put your code here
    
    var $table = "gallery";
    
    var $has_many = array(
        'material' => array(			// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_materials',
            'other_field' => 'gallery',
            'class' => "material"
        ),
        'post_gallery' => array(			// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'tds_post_gallery',
            'other_field' => 'material'
        )// name of the join table that will lnk both Author and Book together
    );
    
    var $validation = array(
        'gallery_name' => array(
            'label' => 'Gallery Name',
            'rules' => array('required', 'trim', 'unique'),
        )
    );
    
    public function has_post_image()
    {
        $obj_materials = new Material();
        $obj_post_gallery = new Post_gallery();
        $i_gallery_id = $this->id;
        
        $this->include_join_fields(FALSE);
        $this->include_related($obj_materials,"id",FALSE, FALSE, TRUE, "INNER");
        $this->include_related($obj_post_gallery,"id",FALSE, FALSE, TRUE, "INNER", $obj_materials->table);
        $this->where("id", $i_gallery_id);
        
        $query = $this->query($this->get_sql());
        return ( $query === FALSE ) ? FALSE : TRUE;
    }
}

?>
