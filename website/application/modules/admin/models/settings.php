<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author ahuffas
 */
class Settings extends DataMapper 
{
    private $ar_fields = array(
                        'id',
                        'key',
                        'value',
                        'is_active',
                        'description',
                    );
    
    //put your code here
    function get_value( $s_settings_key = '', $s_settings_key_value = "" )
    {
        if ( $s_settings_key_value == "issue_date" )
        {
            $CI = & get_instance();
            $CI->load->config("tds");
            $b_issue_date = $this->config->config['issuedate_enable'];

            if ( ! $b_issue_date )
            {
                $obj_issue_date = new stdClass();
                $obj_issue_date->value = date("Y-m-d");
                
                return $obj_issue_date;
            }
        }
        if ( strlen($s_settings_key) > 0 )
        {
            $this->select("id","value");
            $this->where($s_settings_key, $s_settings_key_value);
        }
        return $this->get();
    }
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function rules(){
        $rules = array(
              array(
                 'field'   => 'Settings[key]',
                 'label'   => 'Key',
                 'rules'   => 'required|is_unique[settings.key]',
              ),
              array(
                 'field'   => 'Settings[value]',
                 'label'   => 'Value',
                 'rules'   => 'required',
              ),
              array(
                 'field'   => 'Settings[is_active]',
                 'label'   => 'Status',
                 'rules'   => 'required',
              ),
              array(
                 'field'   => 'Settings[description]',
                 'label'   => 'description',
                 'rules'   => '',
              ),
        );
        
        return $rules;
    }
    
        
}

?>
