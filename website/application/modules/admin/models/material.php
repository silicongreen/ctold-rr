<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Material
 *
 * @author ahuffas
 */
class Material extends DataMapper {
    //put your code here
    var $table = "materials";
    
    var $has_many = array(
        'post_gallery' => array(			// in the code, we will refer to this relation by using the object name 'author'
            'join_table' => 'post_gallery',
            'other_field' => 'material'
        )// name of the join table that will link both Author and Book together
    );
}

?>
