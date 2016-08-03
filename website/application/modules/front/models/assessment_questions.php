<?php

/**
 * Description of Assessment Question
 *
 * @author NIslam
 */
class Assessment_questions extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "assessment_question";
    var $validation = array(
        'assesment_id' => array(
            'label' => 'Assesment',
            'rules' => array('required', 'trim'),
        ),
        'question' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim'),
        ),
        'mark' => array(
            'label' => 'Mark',
            'rules' => array('required', 'trim', 'number'),
        ),
    );
    
    private $ar_fields = array(
        'id',
        'assesment_id',
        'question',
        'mark',
        'time',
        'style',
        'created_date'
    );

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    function get_assessment_question_by_id($id) {
        
        $this->db->select('*');
        $this->db->from("assessment_question");
        $this->db->where("assessment_question.id", $id);
        $obj_assessment_q = $this->db->get()->row();
        
        $data['data'] = $obj_assessment_q;

        return $data;
    }

    public function get_attributes() {

        return array(
            'assesment_id' => 'Assesment',
            'question' => 'Question',
            'mark' => 'Mark',
            'time' => 'Time',
            'style' => 'style'
        );
    }

}
