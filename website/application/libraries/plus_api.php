<?php

use Guzzle\Http\Client;

class Plus_api {

    private $_api_endpoint;
    private $_client;
    private $_client_oauth;
    private $_token;
    private $_CI;
    private $_username;
    private $_password;
    private $_client_id;
    private $_client_secret;
    private $_redirect_url;
    private $_oauth_endpoint;

   
   public function init($ar_params, $b_use_session = true) {

        $this->_CI = & get_instance();

        $this->_CI->load->config('plus');
        $endPoint = $this->_CI->config->config['plus_api_endpoint'];

        $school_code = ( isset($ar_params['school_code']) && !empty($ar_params['school_code'])) ? $ar_params['school_code'] : '';
        $username = ( isset($ar_params['username']) && !empty($ar_params['username'])) ? $school_code . '-' . $ar_params['username'] : $school_code . '-champs21';
        $password = ( isset($ar_params['password']) && !empty($ar_params['password'])) ? $ar_params['password'] : $school_code . $this->_CI->config->config['champs_password'];

        $this->_username = $username;
        $this->_password = $password;

        $this->_client_id = sha1($school_code . $this->_CI->config->config['client_id']);
        $this->_client_secret = sha1($school_code . $this->_CI->config->config['client_secret']);
        $this->_redirect_url = 'http://' . $school_code . '.' . $endPoint . '/authenticate';

        $this->_api_endpoint = "http://" . $school_code . '.' . $endPoint . "/api/";

        $this->_oauth_endpoint = "http://" . $school_code . '.' . $endPoint . "/";

        $this->_client = new Client($this->_api_endpoint);
        $this->_client_oauth = new Client($this->_oauth_endpoint);


        if (isset($_SESSION['plus_access_token']) && $b_use_session) {

            if (isset($this->_token) && ($this->_token != $_SESSION['plus_access_token'])) {

                $this->_token = $_SESSION['plus_access_token'];
            } else if (!isset($this->_token)) {

                $this->_token = $_SESSION['plus_access_token'];
            }

            return $_SESSION['plus_access_token'];
        }

        return $this->getAccessToken();
    }

}

?>
