<?php

class Category_cover extends DataMapper {

    var $table = "category_cover";
    
    var $validation = array(
        'category_id' => array(
            'label' => 'category',
            'rules' => array('required', 'trim')
        ),
        'image' => array(
            'label' => 'Image',
            'rules' => array('required', 'trim')
        ),
        'issue_date' => array(
            'label' => 'Issue Date',
            'rules' => array('required', 'trim', 'unique_pair'=>'category_id')
        )
    );

    
}
