<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FreeUsers
 *
 * @author ahuffas
 */
class Free_users extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "free_users";
    private $ar_fields = array('id', 'username', 'email', 'password', 'fb_profile_id', 'gl_profile_id', 'first_name', 'middle_name', 'last_name', 'tds_country_id', 'district', 'mobile_no', 'dob', 'medium', 'gender', 'grade_ids', 'user_type', 'user_type_text', 'salt', 'cover_image', 'profile_image', 'status');
    var $validation = array(
        'email' => array(
            'label' => 'Email Address',
            'rules' => array('required', 'trim', 'unique', 'min_length' => 10, 'max_length' => 60, 'valid_email'),
        ),
        'cnf_email' => array(
            'label' => 'Re-enter Email Address',
            'rules' => array('required', 'trim', 'min_length' => 10, 'max_length' => 60, 'valid_email', 'matches' => 'email'),
        ),
        'password' => array(
            'label' => 'Enter Password',
            'rules' => array('required', 'min_length' => 4, 'encrypt'),
        ),
        'cnf_password' => array(// accessed via $this->cnf_password
            'label' => 'Re-enter Password',
            'rules' => array('required', 'encrypt', 'matches' => 'password')
        ),
        'dob' => array(
            'label' => 'Date of Birth',
            'rules' => array('trim', 'valid_date')
        ),
        'first_name' => array(
            'label' => 'First Name',
            'rules' => array('trim')
        ),
    );

    function login() {
        // Call to api user/auth to fix mis-matched data
        $this->authBeforelogin($this->email, $this->password);
        
        // Create a temporary user object
        $u = new Free_users();

        // Get this users stored record via their username
        $u->where('email', $this->email)->get();

        // Give this user their stored salt
        $this->salt = $u->salt;
        // Validate and get this user by their property values,
        // this will see the 'encrypt' validation run, encrypting the password with the salt
        $this->validate()->get();
        // If the username and encrypted password matched a record in the database,
        // this user object would be fully populated, complete with their ID.
        // If there was no matching record, this user would be completely cleared so their id would be empty.
        if (empty($this->id)) {
            // Login failed, so set a custom error message
            $this->error_message('login', 'Username or password invalid');
        } else {
            //$b_valid_captcha = ( $b_has_captcha ) ? $this->captcha_check($captcha) : TRUE;
            $b_valid_captcha = TRUE;
            if ($b_valid_captcha) {
                return TRUE;
            }
            // Login succeeded
        }
    }

    function api_login($source = '') {
        $u = new Free_users();

        // Get this users stored record via their username
//        if(!empty($this->gl_profile_id)){
//            $source = 'Google';
//            $u->where('gl_profile_id', $this->gl_profile_id);
//        }
//        
//        if(!empty($this->fb_profile_id)){
//            $source = 'Facebook';
//            $u->where('fb_profile_id', $this->fb_profile_id);
//        }

        $u->where('email', $this->email);

        $u->get();

        if (empty($u->id) || ($u->email != $this->email)) {
            // Login failed, so set a custom error message
            $this->error_message('login', "unregistered");
        } else {

            if (!empty($source) && ( empty($u->gl_profile_id) || empty($u->fb_profile_id) )) {

                if ($source == 'g' && empty($u->gl_profile_id)) {
                    $u->gl_profile_id = $this->gl_profile_id;
                }

                if ($source == 'f' && empty($u->fb_profile_id)) {
                    $u->fb_profile_id = $this->fb_profile_id;
                }

                $u->save();
            }

            //$b_valid_captcha = ( $b_has_captcha ) ? $this->captcha_check($captcha) : TRUE;
            $b_valid_captcha = TRUE;
            if ($b_valid_captcha) {
                return $u;
            }
            // Login succeeded
        }
    }

    private function captcha_check($str) {
        // First, delete old captchas
        $expiration = time() - 60; // Two hour limit
        $this->db->query("DELETE FROM tds_captcha WHERE captcha_time < " . $expiration);

        $CI = & get_instance();

        // Then see if a captcha exists:
        $sql = "SELECT COUNT(*) AS count FROM tds_captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
        $binds = array($str, $CI->input->ip_address(), $expiration);
        $query = $CI->db->query($sql, $binds);
        $row = $query->row();

        if ($row->count == 0) {
            return FALSE;
        } else {
            return true;
        }
    }

    // Validation prepping function to encrypt passwords
    // If you look at the $validation array, you will see the password field will use this function
    function _encrypt($field) {
        // Don't encrypt an empty string
        if (!empty($this->{$field})) {
            // Generate a random salt if empty
            if (empty($this->salt)) {
                $this->salt = md5(uniqid(rand(), true));
            }

            $this->{$field} = hash('sha512', $this->salt . $this->{$field});
        }
    }

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    public function _email_unique() {
        $u = new Free_users();

        // Get this users stored record via their username
        $u->where('email', $this->email)->get();

        return ( empty($u->id) ) ? TRUE : FALSE;
        exit;
    }

    public function authBeforelogin($username, $password) {

        $this->load->config("huffas");
        $url = $this->config->config['paid_auth_api_url'];
        
        $fields_string .= 'username=' . $username . '&password=' . $password;

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
                )
        );
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        $a_data = json_decode($result);

        return $a_data;
    }
    
    public function cookie_login() {

        $u = new Free_users();
        $u->where('cookie_token', $this->cookie_token)->get();
        
        if($this->cookie_validate($this->cookie_token, $u)) {
            return $u;
        } else {
            return false;
        }
    }

    public function cookie_validate($token_to_validate, $obj_to_match) {

        if( !time() < strtotime($obj_to_match->cookie_expire) ) {
            $token = get_session_cookie_token($obj_to_match, $obj_to_match->cookie_key);
            return ($token === $token_to_validate);
        } else {
            return false;
        }
    }

}
