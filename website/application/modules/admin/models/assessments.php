<?php

/**
 * Description of Assessment
 *
 * @author NIslam
 */
class Assessments extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "assessment";
    var $validation = array(
        'title' => array(
            'label' => 'Title',
            'rules' => array('required', 'trim'),
        ),
        'time' => array(
            'label' => 'time',
            'rules' => array('required', 'trim', 'number'),
        ),
    );
    
    private $ar_fields = array(
        'id',
        'title',
        'type',
        'use_time',
        'time',
        'created_date'
    );

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    function get_assessment_by_id($id) {
        
        $this->db->select('*');
        $this->db->from("assessment");
        $this->db->where("assessment.id", $id);
        $obj_assessment = $this->db->get()->row();
        
        $data['data'] = $obj_assessment;

        return $data;
    }

    public function get_attributes() {

        return array(
            'title' => 'Title',
            'type' => 'Type',
            'use_time' => 'Use Time',
            'time' => 'Time'
        );
    }

}
