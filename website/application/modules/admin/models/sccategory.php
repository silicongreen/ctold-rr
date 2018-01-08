<?php

class Sccategory extends DataMapper {

    var $table = "science_rocks_category";
    
    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim', 'unique_pair' => 'parent_id'),
        )
    );

    
}
