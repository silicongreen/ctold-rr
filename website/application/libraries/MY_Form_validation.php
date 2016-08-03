<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MY_Form_validation extends CI_Form_validation {

    public $CI;
 

    /**
     * Match one field to another
     *
     * @access	public
     * @param	string
     * @param	field
     * @return	bool
     */
    public function is_unique($str, $field) {
        list($table, $field) = explode('.', $field);

        if (isset($_POST['id'])) {
            $id = $_POST['id'];
        } elseif (isset($_POST[key($_POST)]['id'])) {
            $id = $_POST[key($_POST)]['id'];
        } else {
            $id = 0;
        }

        $this->CI->db->select('*');
        $this->CI->db->from($table);

        $this->CI->db->where($field, $str);
        $this->CI->db->where('id !=', $id);

        $query = $this->CI->db->get();

        return $query->num_rows() === 0;
    }
    public function ci_school_code_check($str) {
        $paid_school_id = $this->CI->input->post('paid_school_id');

        if (!check_school_code_paid($paid_school_id, $str)) {
            $this->CI->form_validation->set_message('ci_school_code_check', 'Invalid School Code');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    public function ci_check_admission_no_parent($str)
    { 
        
     
        $this->CI->form_validation->set_message('ci_check_admission_no_parent','Admission No is Not Valid. Use the admission no That your children use to create the student account');
        if (free_user_logged_in()) 
        {
            $user_data = get_user_data();
            if (!$user_data->applied_paid && !$user_data->paid_id && $user_data->paid_school_id) 
            {
                $std = get_parent_children($str,$user_data);

                if($std)
                {
                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
            }
        }
        return FALSE;
    } 
    public function ci_check_admission_no($str)
    { 
        
     
        $this->CI->form_validation->set_message('ci_check_admission_no','Admission No is already used');
        if (free_user_logged_in()) 
        {
            $user_data = get_user_data();
            if (!$user_data->applied_paid && !$user_data->paid_id && $user_data->paid_school_id) 
            {
                $user_name = make_paid_username($user_data, trim($str));
                $this->CI->db->dbprefix = '';
                $this->CI->db->select('id');
                $this->CI->db->from('users');
                $this->CI->db->where('username', trim($user_name));
                $this->CI->db->where('school_id',$user_data->paid_school_id);                 
                $std = $this->CI->db->get()->row();
                $this->CI->db->dbprefix = 'tds_';

                if($std)
                {
                    return FALSE;
                }
                else
                {
                    return TRUE;
                }
            }
        }
        return FALSE;
    } 
    public function ci_check_password($str)
    { 
       
        $this->CI->form_validation->set_message('ci_check_password','Wrong Password. Please use your current password');
        if (free_user_logged_in()) 
        {
            $user_data = get_user_data();
            if (!$user_data->applied_paid && !$user_data->paid_id && $user_data->paid_school_id) 
            {
                if(hash('sha512', $user_data->salt . $str)==$user_data->password)
                {
                    return TRUE;
                } 
            }
        }
        return FALSE;
    }
    public function ci_validate_date($str)
    { 
       
        $this->CI->form_validation->set_message('ci_validate_date','Wrong Date Format for Birth date Use YYYY-mm-dd');
        if (free_user_logged_in()) 
        {
            $user_data = get_user_data();
            if (!$user_data->applied_paid && !$user_data->paid_id && $user_data->paid_school_id) 
            {
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$str))
                {
                    return true;
                }else{
                    return false;
                }
            }
        }
        return FALSE;
    }

}

?>
