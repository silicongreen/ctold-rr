<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Min(ify) controller
 *
 * This controller will minify on the fly and cache (if set) results in order to quicker serve your static content
 * This code use Minify libs : http://code.google.com/p/minify/
 *
 * @author              Spir
 * @license             MIT
 * @version             0.2
 */

class minify extends CI_Controller {

	private $_request;
	private $_offset; // header cache delay
	private $_type; // type of requested file : js/css

	function __construct()
	{
		parent::__construct();
                
                $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'apc'));
		// loading config
                $this->load->config('minify');
		
		// get type of request
		$segments = $this->uri->segment(1);

		if (count($segments))
		{
			switch($segments)
			{
				case $this->config->item('js_route_segment'):
					$this->_type = 'js';
					break;
					
                                case $this->config->item('css_route_segment'):
					$this->_type = 'css';
					break;
					
				// should not append if route propely done, this switch could be extended for some other static items
				default:
					$this->_type = 'unknow';
					break;
			}
		}
	}
    

	function _remap($request)
	{
		if ($this->_type=='unknow')
			$this->_error();
		
		# check if requested files is actually a group of js files
		$groups = $this->config->item($this->_type.'_groups'); // loading group of files
		if(array_key_exists($request, $groups))
		{
			$files = Array();
	    		$this->_request = $request;
	    		foreach($groups[$request] as $file)
	    		{
	    			$files[] = $this->config->item($this->_type.'_local_path').$file;
	    		}
	    		$this->_display($files);
	    	} 
	    	else # should be a file
	    	{    		
	    		# check if we are requesting a file within some folders
	    		if (is_dir($this->config->item($this->_type.'_local_path').$request))
	    		{
	    			$args = array_slice($this->uri->rsegments, 2);
	    			$path = implode("/", $args);
	    			$this->_request = $request.'/'.$path;
	    			$requested_file = $this->config->item($this->_type.'_local_path').$request.'/'.$path;
	    		} 
	    		else
	    		{
	    			$this->_request = $request;
	    			$requested_file = $this->config->item($this->_type.'_local_path').$request;
	    		}
	    		
	    		# check if requested files is actually a file
	    		if (file_exists($requested_file))
	    		{
	    			$this->_display(Array($requested_file));
	    		}
	    		else 
	    		{
	    			# error
	    			$this->_error();
	    		}
	    	}
	}


	private function _display($files)
	{
		$cache_name = str_replace('/','_', $this->_request); // this is why we needed that request var
                
                $cache_name = "Minify-CI-" . strtoupper(str_replace("." . $this->_type, "", $cache_name)) . "-" . strtoupper($this->_type);
                if (!$cache = $this->read_from_cache($cache_name))
		{
                    $cache = $this->_minify($files, $cache_name);
                    $this->write_to_cache($cache_name, $cache);
                }
                if (isset($cache['headers'])) foreach ($cache['headers'] as $header => $value)
                {
                    // we don't want to display ETag
                    if ($header!='ETag')
                            Header($header . ': ' . $value);
                }
		echo ( isset($cache['content'])) ? $cache['content'] : $cache ;
		exit;
	}    

	
	private function _minify($files=Array(), $cache_name)
	{
		
	
                $cache = Array();
                $cache['headers'] = Array();
                $cache['headers']['Expires'] = gmdate("D, d M Y H:i:s", time() + $this->_offset) . " GMT";
                $cache['headers']['Cache-Control'] = "public";
                $cache['headers']['Cache-Control'] = "max-age=2592000";
                switch($this->_type)
                {
                        case 'js':
                                $cache['headers']['Content-type'] = 'application/x-javascript';
                                break;

                        case 'css':
                                $cache['headers']['Content-type'] = 'text/css';
                                break;
                }

                # loop on each file to grab it content
                $cache['content'] = '';
                if($this->config->item("use_css_compress"))
                {
                    $compressor = new CSSmin();
                }
                
                foreach($files as $file){
                    
                    if($this->_type == 'css'){
                        if($this->config->item("use_css_compress"))
                        {
                            $cache['content'] .= $compressor->run(file_get_contents($file));
                        }
                        else
                        {
                            $cache['content'] .= file_get_contents($file);
                        }    
                    }  else {
                        if($this->config->item("use_js_compress"))
                        {
                            $cache['content'] .=  \JShrink\Minifier::minify(file_get_contents($file));
                        }
                        else
                        {
                            $cache['content'] .=  file_get_contents($file);
                        }    
                    }
                    
                }
                
		return $cache;
		
	}
              
        private function compress( $minify )
        {
            $minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );

            /* remove tabs, spaces, newlines, etc. */ 
            $minify = str_replace( array("\r\n", "\r", "\n", "\t"), '', $minify );

            return $minify;
        }
    
    
	private function _error()
	{
		# 404
		show_404();
		exit;
	}
	

	function read_from_cache($name)
	{
           
            return $this->cache->file->get($name);
          
	}


	function write_to_cache($name, $value)
	{

            $this->cache->file->save($name, $value, $this->config->item($this->_type.'_cache_max_age'));
           
	}

}
