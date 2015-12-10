<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Country extends CI_Model {

    public $table_name = 'countries';
    
    public function __construct() {
        parent::__construct();
        $this->db->set_dbprefix('');
    }

    /**
     * @param int $id
     * @return assoc array data
     * */
    public function getById($id) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
    }

    /**
     * @param get Countries
     * @return assoc array data
     * */
    public function getAll() {
        $this->db->select('*');
        $this->db->from($this->table_name);
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data : FALSE;
    }

}
