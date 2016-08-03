<?php

class Poll extends DataMapper {

    var $table = "questions";
    public $maxNumber = 3;
    var $validation = array(
        'ques' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim'),
        ),
        'value_1' => array(
            'label' => 'Option 1',
            'rules' => array('required', 'trim'),
        ),
        'value_2' => array(
            'label' => 'Option 2',
            'rules' => array('required', 'trim'),
        ),
        'sub_head' => array(
            'label' => 'Sub Head',
            'rules' => array('trim','max_length' => 255),
        )
    );
    
    function create_option($ques_id=null)
    {
        error_reporting(0);
        if(!$ques_id)
        {
            $options[0]->value = "Yes";
            $options[0]->id = "";

            $options[1]->value = "No";
            $options[1]->id = "";
        }
        else
        {
            $this->db->select("id,value");
            $this->db->where("ques_id",$ques_id);

            $options = $this->db->get("options")->result();
            
        }  
        return $options;
        
    }
    


   
}
