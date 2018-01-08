<?php

/**
 * Description of Assessment
 *
 * @author NIslam
 */
class sctopic extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "science_rocks_topics";
    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim'),
        ),
        'category_id' => array(
            'label' => 'Catgory',
            'rules' => array('required', 'trim', 'number')
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
