<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Course extends CI_Model {

    public $table_name = 'courses';
    private $_now;
    private $_school_id;
    private $_num_zeros_for_course_code = array(
        '0' => '0000',
        '1' => '000',
        '2' => '00',
        '3' => '0',
    );

    public function __construct() {
        parent::__construct();
        $this->db->set_dbprefix('');
        $this->_now = date('Y-m-d H:i:s');
    }

    public function init($school_id = 0) {
        $this->_school_id = $school_id;
    }

    /**
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return int last inserted id or bool false
     * */
    public function create($param) {
        $data = $this->preprocess($param);
        $this->clear();

        if (isset($data['error']) && !empty($data['error'])) {
            return $data;
        }

        return ($this->db->insert_batch($this->table_name, $data)) ? (int) $this->db->insert_id() : FALSE;
    }

    /**
     * @param int $id
     * @return assoc array data
     * */
    public function getDataById($id) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
    }

    /**
     * @param int $id
     * @return bool true or false
     * */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_name);
    }

    /**
     * @param delete all records from table except System Admin
     * @return nothing
     * */
    public function clear() {
        $this->db->where('school_id', $this->_school_id);
        return $this->db->delete($this->table_name);
    }

    /**
     * @param int $id id of the record to be updated
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return bool true or false
     * */
    public function update($id, $param) {
        $this->db->where('id', $id);
        return $this->db->update($this->table_name, $param);
    }

    private function preprocess($data, $mode = 'create') {

        $i = 0;
        $grading_type = 1;

        $response = array();
        foreach ($data as $key => $value) {

            if (preg_match('#\((.*?)\)#', $value, $str_section_names)) {
                $str_class_name = trim(preg_replace('#\((.*?)\)#', '', $value));
                $ar_section_names = explode(',', trim($str_section_names[1]));
            } else {
                $str_class_name = $value;
                $ar_section_names = array('a');
            }

            if (!empty($ar_section_names)) {

                $ar_class_names = explode(' ', $str_class_name);
                $class_name_suffix = end($ar_class_names);

                if (strlen($class_name_suffix) > 2) {
                    $class_name_suffix = substr($class_name_suffix, -2);
                }

                $class_code = $this->_num_zeros_for_course_code[strlen($class_name_suffix)] . $class_name_suffix;

                foreach ($ar_section_names as $section_name) {
                    $response[$i]['course_name'] = $str_class_name;
                    $response[$i]['code'] = strtoupper($str_class_name[0] . trim($section_name)) . $class_code;
                    $response[$i]['section_name'] = strtoupper(trim($section_name));
                    $response[$i]['grading_type'] = $grading_type;
                    $response[$i]['school_id'] = $this->_school_id;

                    $response[$i] = array_merge($response[$i], $this->before_save());
                    $i++;
                }
            }
        }

        return $response;
    }

    private function before_save() {
        $data = array(
            'created_at' => $this->_now,
            'updated_at' => $this->_now
        );

        return $data;
    }

}
