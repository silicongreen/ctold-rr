<?php

class Spelling_bee extends DataMapper {

    var $table = "spellingbee";
    
    var $validation = array(
        'word' => array(
            'label' => 'Word',
            'rules' => array('required', 'trim', 'unique'),
        ),
        'wtype' => array(
            'label' => 'Type',
            'rules' => array('required', 'trim'),
        ),
        'sentence' => array(
            'label' => 'Sentence',
            'rules' => array('required', 'trim'),
        ),
        'definition' => array(
            'label' => 'Definition',
            'rules' => array('required', 'trim'),
        ),
        'bangla_meaning' => array(
            'label' => 'Bangla Meaning',
            'rules' => array('required', 'trim'),
        ),
        'year' => array(
            'label' => 'Year',
            'rules' => array('required', 'trim'),
        ),
        'level' => array(
            'label' => 'Word Strength',
            'rules' => array('required', 'trim'),
        ),
        'source' => array(
            'label' => 'Word Source',
            'rules' => array('required', 'trim'),
        ),
    );
   
}
