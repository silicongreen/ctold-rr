<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Country
 *
 * @author ahuffas
 */
class Country extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "countries";
    private $ar_fields = array('id', 'name', 'created_at', 'updated_at');

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    public function get_country($id = NULL) {

        $sql = "SELECT c.id, c.name FROM `countries` AS c";

        $where = (!empty($id)) ? " WHERE c.id = '" . $id . "'" : "";

        $sql .= $where;

        $obj_country = $this->query($sql);

        return (sizeof($obj_country->all) > 0) ? $obj_country : false;
    }

    public function formatCounrtyForDropdown($obj_country) {

        $array[0] = 'Country';
        foreach ($obj_country as $row) {
            $array[$row->id] = mb_convert_encoding($row->name, 'UTF-8');
        }
        return $array;
    }

}
