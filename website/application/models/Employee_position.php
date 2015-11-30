<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Employee_position extends CI_Model {

    public $table_name = 'employee_positions';
    private $_now;
    private $_school_id;

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
        $this->db->where('name <>', 'System Admin');
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

    public function getCategories() {
        $this->db->select('*');
        $this->db->where('name <>', 'System Admin');
        $this->db->where('school_id', $this->_school_id);
        $this->db->where('status', 1);
        $this->db->from('employee_categories');
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data : FALSE;
    }

    public function formatForDropdown($param) {

        $response = array();
        foreach ($param as $row) {
            $response[$row['id']] = $row['name'] . ' (' . $row['prefix'] . ')';
        }
        return $response;
    }

    private function preprocess($data, $mode = 'create') {

        $i = 0;
        $response = array();
        foreach ($data as $key => $value) {

            $ar_name_cat_id = explode('==', $value);
            $name = $ar_name_cat_id[0];
            $emp_category_id = $ar_name_cat_id[1];

            $response[$i]['name'] = $name;
            $response[$i]['employee_category_id'] = $emp_category_id;
            $response[$i]['school_id'] = $this->_school_id;

            $response[$i] = array_merge($response[$i], $this->before_save());

            $i++;
        }

        return $response;
    }

    private function before_save() {
        $data = array(
            'created_at' => $this->_now,
            'updated_at' => $this->_now,
            'status' => 1
        );

        return $data;
    }

}
