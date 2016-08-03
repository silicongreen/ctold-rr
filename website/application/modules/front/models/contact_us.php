<?php
/**
 * Description of contact_us
 *
 * @author NIslam
 */
class Contact_us extends DataMapper {

    var $table = "contact_us";
    
    private $ar_fields = array('id', 'full_name', 'email', 'contact_type', 'description', 'created_date', 'modified_date', 'answered_by_user_id', 'answered_date', 'status');
    var $validation = array(
        'full_name' => array(
            'label' => 'Full Name',
            'rules' => array('required', 'trim', 'min_length' => 5, 'max_length' => 150),
        ),
        'email' => array(
            'label' => 'Email Address',
            'rules' => array('required', 'trim', 'min_length' => 10, 'max_length' => 150, 'valid_email'),
        ),
        'contact_type' => array(
            'label' => 'Contact Reason',
            'rules' => array('required', 'trim', 'min_length' => 5, 'max_length' => 50),
        ),
        'description' => array(
            'label' => 'Description',
            'rules' => array('required', 'trim', 'min_length' => 20),
        ),
        
        
    );
    
}
