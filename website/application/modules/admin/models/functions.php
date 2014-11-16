<?php

class Functions extends DataMapper {

    var $table = "functions";
    var $has_one = array('controller');
    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim', 'unique_pair' => 'controller_id'),
        ),
        'function' => array(
            'label' => 'function',
            'rules' => array('required', 'trim', 'unique_pair' => 'controller_id'),
        ),
        'controller_id' => array(
            'label' => 'Controller',
            'rules' => array('required')
        )
    );
    
    
    
   
}
