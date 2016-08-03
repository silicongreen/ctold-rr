<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ( ! function_exists('base_url'))
{
	function base_url($uri = '')
	{
		$CI =& get_instance();
                if (strpos($uri, $CI->config->config['base_url']) !== FALSE )
                {
                    $uri = str_ireplace($CI->config->config['base_url'], "", $uri);
                }
                return $CI->config->base_url($uri);
	}
}