<?php

class Controller extends DataMapper {

    var $has_many = array('functions');
    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim', 'unique',),
        ),
        'controller' => array(
            'label' => 'controller',
            'rules' => array('required', 'trim', 'unique',),
        )
    );

   
}
