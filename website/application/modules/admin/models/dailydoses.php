<?php

class dailydoses extends DataMapper {

    var $table = "dailydose";
    
    var $validation = array(
        'content' => array(
            'label' => 'Content',
            'rules' => array('required', 'trim'),
        ),
        'date' => array(
            'label' => 'Date',
            'rules' => array('required', 'trim', 'unique'),
        ),
    );

   
}
