<?php

class User extends DataMapper
{

    var $has_one = array('group');
    var $validation = array(
        'username' => array(
            'label' => 'Username',
            'rules' => array('required', 'trim', 'unique', 'alpha_dash', 'min_length' => 4, 'max_length' => 20),
        ),
        'password' => array(
            'label' => 'Password',
            'rules' => array('required', 'min_length' => 6, 'encrypt'),
        ),
        
        'email' => array(
            'label' => 'Email Address',
            'rules' => array('required', 'trim', 'valid_email')
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim')
        ),
        'group' => array(
            'label' => 'Group',
            'rules' => array('required')
        )
    );

    function login($captcha, $b_has_captcha)
    {
        // Create a temporary user object
        $u = new User();

        // Get this users stored record via their username
        $u->where('username', $this->username)->get();

        // Give this user their stored salt
        $this->salt = $u->salt;

        // Validate and get this user by their property values,
        // this will see the 'encrypt' validation run, encrypting the password with the salt
        $this->validate()->get();

        // If the username and encrypted password matched a record in the database,
        // this user object would be fully populated, complete with their ID.
        // If there was no matching record, this user would be completely cleared so their id would be empty.
        if (empty($this->id))
        {
            // Login failed, so set a custom error message
            $this->error_message('login', 'Username or password invalid');

            
        }
        else
        {
            $b_valid_captcha = ( $b_has_captcha ) ? $this->captcha_check($captcha) : TRUE;
            if( $b_valid_captcha )
            {
                return TRUE;
            }   
            else
            {
                $this->error_message('captcha', 'Invalid Captcha');
                return FALSE;
            }    
            // Login succeeded
            
        }
    }
    
    private function captcha_check($str)
    {
        // First, delete old captchas
        $expiration = time() - 60; // Two hour limit
        $this->db->query("DELETE FROM tds_captcha WHERE captcha_time < " . $expiration);
        
        $CI = & get_instance();

        // Then see if a captcha exists:
        $sql = "SELECT COUNT(*) AS count FROM tds_captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
        $binds = array($str, $CI->input->ip_address(), $expiration);
        $query = $CI->db->query($sql, $binds);
        $row = $query->row();

        if ($row->count == 0)
        {
            return FALSE;
        }
        else
        {
            return true;
        }
    }

    // Validation prepping function to encrypt passwords
    // If you look at the $validation array, you will see the password field will use this function
    function _encrypt($field)
    {
        // Don't encrypt an empty string
        if (!empty($this->{$field}))
        {
            // Generate a random salt if empty
            if (empty($this->salt))
            {
                $this->salt = md5(uniqid(rand(), true));
            }

            $this->{$field} = hash('sha512', $this->salt . $this->{$field});
        }
    }

}
