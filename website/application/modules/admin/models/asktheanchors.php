<?php

class asktheanchors extends DataMapper {

    var $table = "ask_the_anchor";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim'),
        ),
        'question' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim'),
        ),
        'date' => array(
            'label' => 'Date',
            'rules' => array('required', 'trim'),
        ),
    );

   
}
