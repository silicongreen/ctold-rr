<?php

class Inpictures extends DataMapper {

    var $table = "inpictures_theme";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim')
        ),
        'publish_date' => array(
            'label' => 'Publish Date',
            'rules' => array('required','trim')
        )
    );


   
}
