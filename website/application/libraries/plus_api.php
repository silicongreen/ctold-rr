<?php

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

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
    private $_school_code;
   
   public function init($ar_params, $b_use_session = true) {

        $this->_CI = & get_instance();

        $this->_CI->load->config('plus');
        $endPoint = $this->_CI->config->config['plus_api_endpoint'];

        $school_code = ( isset($ar_params['school_code']) && !empty($ar_params['school_code'])) ? $ar_params['school_code'] : '';
        $username = ( isset($ar_params['username']) && !empty($ar_params['username'])) ? $ar_params['username'] : $school_code . '-champs21';
        $password = ( isset($ar_params['password']) && !empty($ar_params['password'])) ? $ar_params['password'] : $school_code . $this->_CI->config->config['champs_password'];
        
        $school_code = strtolower($school_code);
        
        $this->_username = $username;
        $this->_password = $password;

        $this->_client_id = sha1($school_code . $this->_CI->config->config['client_id']);
        $this->_client_secret = sha1($school_code . $this->_CI->config->config['client_secret']);
        $this->_redirect_url = 'http://' . $school_code . '.' . $endPoint . '/authenticate';

        $this->_api_endpoint = "http://" . $school_code . '.' . $endPoint . "/api/";

        $this->_oauth_endpoint = "http://" . $school_code . '.' . $endPoint . "/";
        
        $this->_school_code = $school_code;

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
    public function getAccessToken() {

        $accessTokenEndpoint = "oauth/token";

        $postData = array(
            'client_id' => $this->_client_id,
            'client_secret' => $this->_client_secret,
            'grant_type' => 'password',
            'username' => $this->_username,
            'password' => $this->_password,
            'redirect_uri' => $this->_redirect_url,
        );

        try {
            $request = $this->_client_oauth->post($accessTokenEndpoint, array('Content-type' => 'application/x-www-form-urlencoded'), $postData);
            $response = $request->send();

            if ($response->getStatusCode() == 200) {

                $obj_response = json_decode($response->getBody());

                $this->_token = $obj_response->access_token;
                $this->_CI->session->set_userdata('plus_access_token', $this->_token);

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    public function login($ar_params,$userEndpoint)
    {
        
        $userEndpoint .= '?';
        foreach ($ar_params as $k => $v) {
           $userEndpoint .= $k . '=' . $v . '&';
        }
        $ar_params = NULL;
        $userEndpoint = substr($userEndpoint, 0, -1);
       
        $headers = array(
                "Cache-Control"=>"no-cache",
                "Cookie"=>"_champs21_session_=4b7cd4cc882a145b578f279dc44c1650",
                "User-Agent"=>"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
                "Host"=>"nbs.plus.champs21.com",
                'Content-type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Token token="' . $this->_token . '"',
                'cookies' => true
            );
        
        $request = $this->_client->get($userEndpoint, $headers, $ar_params);
        
        $response = $request->send();  
       
        
        
    }        
    
    public function call__($verb = 'post', $userEndpoint = 'reminders', $function_name = '', $b_first_call = true) {

        
        
        $key = $userEndpoint;
        $ar_params = NULL;
        $b_found = false;

        $str_fnc = end(explode('/', $userEndpoint));

        if (method_exists($this, $str_fnc)) {
            $b_found = true;
            $ar_params = $this->$str_fnc();
        } else if (!empty($function_name)) {
            $ar_params = $this->$function_name();
        }
        if(isset($ar_params['username']))
        {
           $cookie = md5($ar_params['username']);
           $headers = array(
                "Cache-Control"=>"no-cache",
                "Cookie"=>"_champs21_session_=".$cookie,
                "User-Agent"=>"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
                "Host"=>$this->_school_code.".champs21.com",
                'Content-type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Token token="' . $this->_token . '"',
                'cookies' => true
            ); 
        }   
        else
        {    
            $headers = array(
                "Cache-Control"=>"no-cache",
                "User-Agent"=>"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
                'Content-type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Token token="' . $this->_token . '"',
                'cookies' => true
            );
        }

        if ($verb == 'get' && !is_null($ar_params)) {
            $userEndpoint .= '?';
            foreach ($ar_params as $k => $v) {
                if ($k == 'search') {
                    if (is_array($ar_params['search'])) {
                        foreach ($ar_params['search'] as $k1 => $v1) {
                            $userEndpoint .= 'search[' . $k1 . ']=' . $v1 . '&';
                        }
                    } else {
                        $userEndpoint .= 'search[' . $v . ']=' . $v . '&';
                    }
                } else {
                    $userEndpoint .= $k . '=' . $v . '&';
                }
            }

            $ar_params = NULL;
            $userEndpoint = substr($userEndpoint, 0, -1);
        } else if ($verb == 'get' && is_null($ar_params)) {

            $userEndpoint .= '?';
            foreach ($this->_CI->config->config[$key]['mandatory_params'] as $k => $v) {
                $userEndpoint .= $k . $v . '=&';
            }
            $userEndpoint = substr($userEndpoint, 0, -1);
        }
        try {
            
         
            
           
            $request = $this->_client->$verb($userEndpoint, $headers, $ar_params);

            $response = $request->send();

            
            
            

            // Save in session or cache of your app.
            // In example laravel:
           
            if ($response->getStatusCode() == 200) {

                $ret = $this->_CI->config->config[$key]['return'];
                $api_resp = $this->_CI->config->config[$key]['api_response'];

                if (!isset($ret) && isset($api_resp)) {

                    if ($api_resp == 'xml') {
                        return $response->xml();
                    } else if ($api_resp == 'json') {

                        if (strpos($response->getContentType(), 'application/xml') !== FALSE) {

                            $json = json_encode($response->xml());
                            $response_jobj = json_decode($json);
                            return $response_jobj;
                        } else {
                            return json_decode($response->getBody());
                        }
                    }
                } else if (isset($ret) && !isset($api_resp)) {

                    if ($ret == 'xml') {
                        return $response->xml();
                    } else if ($ret == 'json') {

                        if (strpos($response->getContentType(), 'application/xml') !== FALSE) {

                            $json = json_encode($response->xml());
                            $response_jobj = json_decode($json);
                            return $response_jobj;
                        } else {
                            return json_decode($response->getBody());
                        }
                    }
                } else if (!isset($ret) && !isset($api_resp)) {

                    if (strpos($response->getContentType(), 'application/xml') !== FALSE) {

                        $json = json_encode($response->xml());
                        $response_jobj = json_decode($json);
                        return $response_jobj;
                    } else {
                        return json_decode($response->getBody());
                    }
                }

                if ($api_resp == $ret) {

                    if ($api_resp == 'xml') {
                        return $response->xml();
                    } else if ($api_resp == 'json') {

                        if (strpos($response->getContentType(), 'application/xml') !== FALSE) {

                            $json = json_encode($response->xml());
                            $response_jobj = json_decode($json);
                            return $response_jobj;
                        } else {
                            return json_decode($response->getBody());
                        }
                    }
                } else {

                    if ($ret == 'xml') {

                        if ($api_resp == 'xml') {
                            return $response->xml();
                        } else if ($api_resp == 'json') {
                            
                            if (strpos($response->getContentType(), 'application/xml') !== FALSE) {

                                $json = json_encode($response->xml());
                                $response_jobj = json_decode($json);
                                return $response_jobj;
                            } else {
                                return json_decode($response->getBody());
                            }
                        }
                    } else if ($ret == 'json') {

                        if ($api_resp == 'xml') {

                            if (strpos($response->getContentType(), 'application/xml') !== FALSE) {

                                $json = json_encode($response->xml());
                                $response_jobj = json_decode($json);
                                return $response_jobj;
                            } else {
                                return json_decode($response->getBody());
                            }
                        } else if ($api_resp == 'json') {
                            return json_decode($response->getBody());
                        }
                    }
                }
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Unauthorized') !== FALSE && $b_first_call) {
                $this->getAccessToken();

                $this->call__($verb, $userEndpoint, $function_name, false);
            }
            print $e->getMessage();
            return false;
        }

        

        return false;
    }
    
    public function get_data_login() {

        $login_ar = array(
            'username' => get_free_user_session('paid_username'),
            'password' => get_free_user_session('paid_password'),
            'school_code' => get_free_user_session('paid_school_code')
        );

//        $ar_ex_param = array('created_at' => '2013-03-04');
        //return 'search:' . json_encode($search_ar);
        return $login_ar;
    }

    public function get_data_reminder() {

        $search_ar = array(
            'search' => array(
                'to_user_username_equals' => $this->_username
            ),
            'created_at' => date('Y-m-d')
        );

//        $ar_ex_param = array('created_at' => '2013-03-04');
        //return 'search:' . json_encode($search_ar);
        return $search_ar;
    }

    public function get_data_student_attendance() {

        return array(
            'student_admisson_no_equals' => 'JKL124',
            'batch_name_equals' => 'BATCH2001',
            'month_date_gt' => '2013-07-06',
            'month_date_lt' => '2013-07-06',
            'month_date_equals' => '2013-07-06'
        );
    }

    public function get_data_batch() {

        $search_ar = array(
            'search' => array()
        );
        return $search_ar;
    }
    public function get_data_timetables() {

        $search_ar = array(
            'search' => array(
                'start_date_equals' => date('Y-m-d'),
                'end_date_equals' => date('Y-m-d')
            ),
            'username' => $this->_username
        );

//        $ar_ex_param = array('created_at' => '2013-03-04');
        //return 'search:' . json_encode($search_ar);
        return $search_ar;
    }

}

?>
