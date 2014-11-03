<?php

class gks extends DataMapper {

    var $table = "general_knowladge";
    
    var $validation = array(
        'question' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim', 'unique'),
        ),
        'ans1' => array(
            'label' => 'Answer 1',
            'rules' => array('required', 'trim'),
        ),
        'ans2' => array(
            'label' => 'Answer 2',
            'rules' => array('required', 'trim'),
        ),
        'ans3' => array(
            'label' => 'Answer 3',
            'rules' => array('required', 'trim'),
        ),
        'ans4' => array(
            'label' => 'Answer 4',
            'rules' => array('required', 'trim'),
        ),
        'correct' => array(
            'label' => 'Correct Answer',
            'rules' => array('required', 'trim'),
        ),
        'post_date' => array(
            'label' => 'Post Date',
            'rules' => array('required', 'trim'),
        )
    );


   
}
