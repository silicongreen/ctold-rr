<?php

class schools extends DataMapper {

    var $table = "school";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'unique'),
        )
    );


   
}
