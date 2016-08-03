<?php

class gallery_photos extends DataMapper {

    var $table = "gallery_image";
    
    var $validation = array(
        'gallery_id' => array(
            'label' => 'Gallery',
            'rules' => array('required', 'trim'),
        ),
        'image' => array(
            'label' => 'Select photo',
            'rules' => array('required', 'trim'),
        )
    );


   
}
