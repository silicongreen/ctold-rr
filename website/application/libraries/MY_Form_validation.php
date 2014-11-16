<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MY_Form_validation extends CI_Form_validation 
{
    public $CI;
    
    /**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function is_unique($str, $field)
	{
		list($table, $field) = explode('.', $field);
        
        if(isset($_POST['id'])){
            $id = $_POST['id'];
        }elseif(isset($_POST[key($_POST)]['id'])){
            $id = $_POST[key($_POST)]['id'];
        }else{
            $id = 0;
        }
        
        $this->CI->db->select('*');
        $this->CI->db->from($table);
        
        $this->CI->db->where($field, $str);
        $this->CI->db->where('id !=', $id);
        
        $query = $this->CI->db->get();
        
		return $query->num_rows() === 0;
    }
}
?>
