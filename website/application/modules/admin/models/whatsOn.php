<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Gallery
 *
 * @author ahuffas
 */
class WhatsOn extends DataMapper  {
    public $my_errors = array();
    
    //put your code here
    var $table = "whats_on";
    private $ar_fields = array('id', 'channel_id', 'program_type', 'program_details', 'category_id', 'show_date', 'is_active');
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function myValidation($ar_model){
        if(!isset($ar_model['channel_id'])){
            $this->my_errors['channel_id'][] = 'Channel Name is required.';
        }
        
        $int_ch_id = (isset($ar_model['channel_id'])) ? (int)$ar_model['channel_id'] : null;
        if($int_ch_id < 1){
            $this->my_errors['channel_id'][] = 'Invalid Channel Name format.';
        }
        
        if(!isset($ar_model['category_id'])){
            $this->my_errors['category_id'][] = 'Category is required.';
        }
        
        $int_cat_id =  (isset($ar_model['category_id'])) ? (int)$ar_model['category_id'] : null;
        if($int_cat_id < 1){
            $this->my_errors['category_id'][] = 'Invalid Category format.';
        }
        
        $s_date = strtotime($ar_model['show_date']);
        if(!is_integer($s_date) || strlen($s_date) < 4){
            $this->my_errors['show_date'][] = 'Invalid Show Date format.';
        }
        
        return (!empty($this->my_errors)) ? false : true;
    }
    
}

?>
