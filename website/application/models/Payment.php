<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Payment extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return int last inserted id or bool false
     * * */
    public function create($table, $param) {
        $this->db->set_dbprefix('');
        return ($this->db->insert($table, $param)) ? (int) $this->db->insert_id() : FALSE;
    }


}
