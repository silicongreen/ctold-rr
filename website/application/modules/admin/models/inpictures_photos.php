<?php

class inpictures_photos extends DataMapper {

    var $table = "inpictures_photos";
    
    var $validation = array(
        'theme_id' => array(
            'label' => 'Theme Title',
            'rules' => array('required', 'trim'),
        ),
        'author_id' => array(
            'label' => 'Author Name',
            'rules' => array('required', 'trim'),
        ),
        'date_taken' => array(
            'label' => 'Date Taken',
            'rules' => array('required', 'trim'),
        ),
        'image' => array(
            'label' => 'Select photo',
            'rules' => array('required', 'trim'),
        )
    );


   
}
