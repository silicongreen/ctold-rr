<?php

class Inpictures_authors extends DataMapper {

    var $table = "inpictures_author";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim'),
        )
    );   
} 