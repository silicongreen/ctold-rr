<?php

class Widget_model extends DataMapper {

    var $table = "widget";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'unique'),
        )
    );


   
}
