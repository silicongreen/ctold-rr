<?php

/**
 * Description of Assessment
 *
 * @author NIslam
 */
class Assesment_mark extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "assesment_mark";
    
    private $ar_fields = array(
        'id',
        'user_id',
        'assessment_id',
        'mark',
        'level',
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
        $this->db->from("assesment_mark");
        $this->db->where("assesment_mark.id", $id);
        $obj_assessment = $this->db->get()->row();
        
        $data['data'] = $obj_assessment;

        return $data;
    }

    public function get_attributes() {

        return array(
            'user_id' => 'User',
            'assessment_id' => 'Assessment',
            'mark' => 'Mark'
        );
    }

}
