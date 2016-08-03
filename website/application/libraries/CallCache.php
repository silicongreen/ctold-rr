<?php

class CallCache  {

	public function __construct($config = array())
	{
            $ci = &get_instance();
            $ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file')); 
	}
}
?>