<?php

/**
 * Description of Assessment Option
 *
 * @author NIslam
 */
class Assessment_options extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "assessment_option";
    var $validation = array(
        'question_id' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim'),
        ),
        'answer' => array(
            'label' => 'Answer',
            'rules' => array('required', 'trim'),
        ),
        'correct' => array(
            'label' => 'Correct',
            'rules' => array('number'),
        ),
    );
    private $ar_fields = array(
        'id',
        'question_id',
        'answer',
        'answer_image',
        'correct',
    );

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    public function get_assessment_option_by_id($id) {

        $this->db->select('*');
        $this->db->from("assessment_option");
        $this->db->where("assessment_option.id", $id);
        return $this->db->get()->row();
    }

    public function get_assessment_option_by_q_id($q_id) {

        $this->db->select('*');
        $this->db->from("assessment_option");
        $this->db->where("assessment_option.question_id", $q_id);
        return $this->db->get()->result();
    }

    public function del_assessment_option_by_q_id($q_id) {

        $this->db->delete('assessment_option', array('question_id' => $q_id));
        if ($this->db->_error_message()) {
            return false;
        } else if (!$this->db->affected_rows()) {
            return false;
        } else {
            return true;
        }
    }

    public function get_attributes() {

        return array(
            'question_id' => 'Question',
            'answer' => 'Answer',
            'answer_image' => 'Answer Image',
            'correct' => 'correct'
        );
    }

}
