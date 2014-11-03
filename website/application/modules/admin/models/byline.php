<?php

class Byline extends DataMapper {

    var $table = "bylines";
    
    var $validation = array(
        'title' => array(
            'label' => 'Title',
            'rules' => array('required', 'trim', 'unique'),
        )
    );


   
}
