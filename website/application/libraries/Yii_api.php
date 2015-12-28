<?php

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;

class Yii_api {

    private $_api_endpoint;
    private $_client;
    private $_headers;
    private $_CI;
    public $_error_code = null;
    public $_error_message = null;

    public function init() {
        require_once 'vendor/autoload.php';

        $this->_CI = & get_instance();

        $this->_api_endpoint = 'http://api.champs21.dev/api/';
        $this->_client = new Client();

        $this->_headers = array(
            "Cache-Control" => "no-cache",
            "User-Agent" => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
            'Content-type' => 'application/x-www-form-urlencoded',
        );
    }

    public function call__($verb = 'post', $userEndpoint = 'create_school', $ar_data = array()) {
        
        try {
            
            $response = $this->_client->$verb($this->_api_endpoint . $userEndpoint, array(
                'body' => $ar_data
            ));
            
            $res_code = $response->getStatusCode();
            
            if ($res_code == 200 || $res_code == 201) {
                
                $response_data = json_decode($response->getBody(), TRUE);
                
                if ($response_data['status']['code'] !== 200) {
                    $this->_error_code = $response_data['status']['code'];
                    $this->_error_message = $response_data['status']['msg'];
                }
                
                return json_decode($response->getBody(), TRUE);
            }
        } catch (Exception $e) {
            
            $this->setMessages($e->getCode());
            return FALSE;
        }

        return false;
    }
    
    private function setMessages($code = NULL) {
        $errors = array(
            500 => 'Somthing bad happened, please try again later',
            403 => 'Token invalid or expired',
            400 => 'Bad Request',
            404 => 'Invalid username or email',
        );
        $this->_error_code = $code;
        $this->_error_message = $errors[$code];
    }

}

?>
