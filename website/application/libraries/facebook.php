<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once( APPPATH . 'libraries/facebook/Facebook/GraphObject.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/GraphSessionInfo.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookSession.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/HttpClients/FacebookCurl.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/HttpClients/FacebookHttpable.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookResponse.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookSDKException.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookRequestException.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookAuthorizationException.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookRequest.php' );
require_once( APPPATH . 'libraries/facebook/Facebook/FacebookRedirectLoginHelper.php' );

use Facebook\GraphSessionInfo;
use Facebook\FacebookSession;
use Facebook\FacebookCurl;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookResponse;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\FacebookSDKException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphObject;

class Facebook {

    var $ci;
    var $helper;
    var $session;
    
    public function __construct($redirect_url = NULL) {
        $this->ci = & get_instance();
        
        $this->ci->load->config("tds");
        
        $app_id = $this->ci->load->config->config['facebook']['app_id'];
        $app_secret = $this->ci->load->config->config['facebook']['app_secret'];
        
        $redirect_url = (!empty($redirect_url)) ? $redirect_url : $this->ci->load->config->config['facebook']['redirect_url'];
        
        FacebookSession::setDefaultApplication($app_id, $app_secret);
        $this->helper = new FacebookRedirectLoginHelper($redirect_url);

        if ($this->ci->session->userdata('fb_token')) {
            $this->session = new FacebookSession($this->ci->session->userdata('fb_token'));

            // Validate the access_token to make sure it's still valid
            try {
                if (!$this->session->validate()) {
                    $this->session = false;
                }
            } catch (Exception $e) {
                // Catch any exceptions
                $this->session = false;
            }
        } else {
            try {
                $this->session = $this->helper->getSessionFromRedirect();
            } catch (FacebookRequestException $ex) {
                // When Facebook returns an error
            } catch (\Exception $ex) {
                // When validation fails or other local issues
            }
        }

        if ($this->session) {
            $this->ci->session->set_userdata('fb_token', $this->session->getToken());

            $this->session = new FacebookSession($this->session->getToken());
        }
    }
    
    public function get_login_url() {
        return $this->helper->getLoginUrl($this->ci->load->config->config['facebook']['permissions']);
    }

    public function get_logout_url() {
        if ($this->session) {
            return $this->helper->getLogoutUrl($this->session, site_url('logout'));
        }
        return false;
    }

    public function get_user() {
        if ($this->session) {
            try {
                $request = (new FacebookRequest($this->session, 'GET', '/me'))->execute();
                $user = $request->getGraphObject()->asArray();

                return $user;
            } catch (FacebookRequestException $e) {
                return false;

                /* echo "Exception occured, code: " . $e->getCode();
                  echo " with message: " . $e->getMessage(); */
            }
        }
    }

}

/* End of file Facebook.php */