<?php

/**
 * Description of Assessment Question
 *
 * @author NIslam
 */
class Assessment_questions_science extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "science_rocks_question";
    var $validation = array(
        'topic_id' => array(
            'label' => 'Topic',
            'rules' => array('required', 'trim'),
        ),
        'question' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim'),
        ),
        'mark' => array(
            'label' => 'Mark',
            'rules' => array('required', 'trim', 'number'),
        ),
        
        'time' => array(
            'label' => 'Time',
            'rules' => array('required', 'trim', 'number'),
        ),
    );
    
    

}
