<?php

class Srwinners extends DataMapper {

    var $table = "science_rocks_winner";
    
    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'date' => array(
            'label' => 'date',
            'rules' => array('required', 'trim'),
        ),
        'winner1' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner1_district' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner2' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner2_district' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner3' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner3_district' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner1_occupation' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner2_occupation' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'winner3_occupation' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'question1' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'ans1' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'question2' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'ans2' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
    );

    
}
