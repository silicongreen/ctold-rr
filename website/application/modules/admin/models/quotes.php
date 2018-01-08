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
class Quotes extends DataMapper  {
    public $my_errors = array();
    
    //put your code here
    var $table = "quotes";
    private $ar_fields = array('id', 'quote', 'personality_id', 'published_date', 'is_active', 'create_date', 'update_date');
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function myValidation($ar_model){
        if(!isset($ar_model['personality_id']) || empty($ar_model['personality_id'])){
            $this->my_errors['personality_id'][] = 'Personality Name is required.';
        }
        
        $int_per_id = (isset($ar_model['personality_id'])) ? (int)$ar_model['personality_id'] : null;
        if($int_per_id < 1){
            $this->my_errors['personality_id'][] = 'Invalid Personality Name format.';
        }
        
        if(!isset($ar_model['quote']) || empty($ar_model['quote'])){
            $this->my_errors['quote'][] = 'Quote is required.';
        }
        
        if(!isset($ar_model['published_date']) || empty($ar_model['published_date'])){
            $this->my_errors['published_date'][] = 'Publish Date Required.';
        }
        
        $p_date = strtotime($ar_model['published_date']);
        if(!is_integer($p_date) || strlen($p_date) < 4){
            $this->my_errors['published_date'][] = 'Invalid Publish Date format.';
        }
        
        return (!empty($this->my_errors)) ? false : true;
    }
    
}

?>
