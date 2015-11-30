<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Tmp extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return int last inserted id or bool false
     * * */
    public function create($param) {
        $this->db->set_dbprefix('');
        return ($this->db->insert('tmp_data', $param)) ? (int) $this->db->insert_id() : FALSE;
    }

    /**
     * @param int $id
     * @return assoc array data
     * * */
    public function getData($id) {
        $data = $this->getRawData($id);
        return ($data !== FALSE) ? json_decode($data['value'], true) : FALSE;
    }

    /**
     * @param int $id
     * @return assoc array but the value as json string
     * * */
    public function getRawData($id) {
        $this->db->set_dbprefix('');
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from('tmp_data', $id);
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
    }

    /**
     * @param int $id
     * @return bool true or false
     * * */
    public function delete($id) {
        $this->db->set_dbprefix('');
        $this->db->where('id', $id);
        return $this->db->delete('tmp_data');
    }

    /**
     * @param int $id id of the record to be updated
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return bool true or false
     * * */
    public function update($id, $param) {
        $this->db->set_dbprefix('');
        $this->db->where('id', $id);
        return $this->db->update('tmp_data', $param);
    }

}
