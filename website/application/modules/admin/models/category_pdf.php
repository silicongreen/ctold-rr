<?php

class Category_pdf extends DataMapper {

    var $table = "category_pdf";
    
    var $validation = array(
        'category_id' => array(
            'label' => 'category',
            'rules' => array('required', 'trim')
        ),
        'pdf' => array(
            'label' => 'pdf',
            'rules' => array('required', 'trim')
        ),
        'issue_date' => array(
            'label' => 'Issue Date',
            'rules' => array('required', 'trim','unique_pair'=>'category_id')
        )
    );

    
}
