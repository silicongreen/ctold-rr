<?php

class Gallery_names extends DataMapper {

    var $table = "gallery_name";
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'unique'),
        ),
        'image' => array(
            'label' => 'Gallery Image',
            'rules' => array('required', 'trim'),
        )
    );


   
}
