<?php

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;


class Api_fedena  
{
        private $_request;
        private $_api_endpoint;
        private $_client;
        private $_token;
    
	public function __construct($config = array())
	{
            $ci = &get_instance();
	}
        
        public function init($endPoint, $token = "")
	{
            $this->_token = $token;
            $this->_api_endpoint = "http://".$endPoint."/api/";
            $this->_client = new Client($this->_api_endpoint);
	}
        
        public function generate_access_token($client_id, $client_secret, $endPoint)
	{
            $post_data = array(
                'username'      => "nsint-E0001",
                'password'      => "123456",
                'grant_type'    => 'password',
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => "http://www.champs21.dev"
            );
            
            $api_endpoint = "http://".$endPoint."/";
            $httpClient = new GuzzleClient("http://nbs.school.champs21.dev/oauth/token");
//            $httpClient->setSslVerification(FALSE);
//
//            $cookieJar = new ArrayCookieJar();
//            // Create a new cookie plugin
//            $cookiePlugin = new CookiePlugin($cookieJar);
//            // Add the cookie plugin to the client
//            $httpClient->addSubscriber($cookiePlugin);
            
            $response = $httpClient->post('', array(), $post_data)->send();
            print '<pre>';
            print_r($response->getBody(true));
            
	}
        
        public function preparePostData($client_id, $client_secret)
        {
            $ar_post_data = array("client_id" => $client_id, "client_secret" => $client_secret, "redirect_uri" => "www.champs21.dev");
            
            return $ar_post_data;
        }
}
?>