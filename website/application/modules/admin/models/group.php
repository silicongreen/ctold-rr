<?php

class Group extends DataMapper {

   
    var $has_many = array('user');

    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim', 'unique', 'alpha_dash', 'min_length' => 4, 'max_length' => 20),
        )
    );

   
}
