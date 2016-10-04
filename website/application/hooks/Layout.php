<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Layout extends CI_Hooks
{
    /**
     * 
     * @global type $OUT
     * This function will let us create Layout for our front-end
     */
    function show_layout()
    {
        global  $OUT;
        
        $ar_registry = Modules::$registry;
        $this->CI =& get_instance();
        
        $output = $this->CI->output->get_output();
        
        $s_class = $this->CI->router->class;
        $s_current_module = $ar_registry[$s_class]->load->get_current_module();
        $s_front_layout = $ar_registry[$s_class]->layout_front;
        
        $ar_params = $ar_registry[$s_class]->extra_params;
         
        ini_set("error_reporting", "E_ALL");
        ini_set("display_errors", "on");
        if ( strcasecmp($s_front_layout, "yes") === 0 && $s_current_module != "admin" )
        {
            $view_data = $ar_params;
            
            //echo "<pre>";
            //print_r($view_data);exit;
            $tds_config = $this->CI->load->config("tds");
            
            $view_data['js_version'] = $this->CI->config->config['JS_CSS_V'];
            
            $view_data['zero_comment_show'] = $this->CI->config->config['zero_comment_show'];
            $view_data['show_more'] = $this->CI->config->config['show_more'];
            $view_data['show_overlay'] = $this->CI->config->config['show_overlay'];
            $view_data['discus_short_name'] = $this->CI->config->config['disqus_short_name'];
            
            $title = $this->CI->cache->get('title-index');
            $title = false;
            if ( $title )
            {
                $view_data['title'] = $title;
            }
            else
            {
                $view_data['title'] = (isset($view_data['title'])) ? $view_data['title'] : WEBSITE_TITLE;
                //$this->CI->cache->write($view_data['title'], 'title-index');
            }
            
             $view_data['header'] = $this->CI->load->view('layout/tdsfront/include/header', NULL, TRUE);
             
            $headerinclude = $this->CI->cache->get('headinclude');
            $headerinclude = false;
           
            if ( $headerinclude )
            {
                $view_data['headerinclude'] = $headerinclude;
            }
            else
            {
                $view_data['headerinclude'] = $this->CI->load->view('layout/tdsfront/include/headerinclude', $view_data, TRUE);
                //$this->CI->cache->write($view_data['headerinclude'], 'headinclude');
            }
            
            $view_data['content'] = ( strlen($output) > 0 ) ? $output : ( isset($ar_params['content']) ) ? $ar_params['content'] : "";
            
            $view_data['exclusive'] = ( strlen($output) > 0 ) ? $output : ( isset($ar_params['exclusive']) ) ? $ar_params['exclusive'] : "";
            
            $footer = $this->CI->cache->get('footer-index');
            $footer = false;
            if ( $footer )
            {
                $view_data['footer'] = $footer;
            }
            else
            {
                $view_data['footer'] = $this->CI->load->view('layout/tdsfront/include/footer', NULL, TRUE);
                //$this->CI->cache->write($view_data['footer'], 'footer-index');
            }
            
            $footerinclude = $this->CI->cache->get('footerinclude');
            $footerinclude = false;
            if ( $footerinclude )
            {
                $view_data['footerinclude'] = $footerinclude;
            }
            else
            {
                $view_data['footerinclude'] = $this->CI->load->view('layout/tdsfront/include/footerinclude', NULL, TRUE);
                //$this->CI->cache->write($view_data['footerinclude'], 'footerinclude');
            }
            
            $view_data['target'] = isset($ar_params['target']) ? $ar_params['target'] : "index";
            $view_data['full_template'] = ($ar_params['full_template'] === FALSE) ? FALSE: TRUE;
		 
            $layout_file = $this->CI->config->config['front_layout'];
            $s_default_layout = BASEPATH . '../application/' . $layout_file;
            $layout = $this->CI->load->view($s_default_layout, $view_data, true, false);
        }
        else
        {
            $layout = $output;
        }
	$search = array(
		'/\n/',			// replace end of line by a space
		'/\>[^\S ]+/s',		// strip whitespaces after tags, except space
		'/[^\S ]+\</s',		// strip whitespaces before tags, except space
	 	'/(\s)+/s'		// shorten multiple whitespace sequences
	);
 
	$replace = array(
		' ',
		'>',
	 	'<',
	 	'\\1'
	);
	$buffer = $layout;
	if ( stripos($_SERVER["REQUEST_URI"],"admin") === FALSE && stripos($_SERVER["REQUEST_URI"],"/m") === FALSE)
	{
		//$buffer = preg_replace($search, $replace, $layout);
        //$buffer = preg_replace("/<!--.*?-->/ms","",$buffer);
	}
	$options = array(
	'clean' => true,
	'hide-comments' => true,
	'indent' => true
	);
        
        
        $ar_segmens = $this->CI->uri->segment_array();
        
        if ( empty($ar_segmens) )
        {
            $cache_name = "home/CONTENT_CACHE_LAYOUT_" . str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
            $this->CI->cache->file->save($cache_name, $buffer, 86400);
        }
        
        //$buffer = tidy_parse_string($layout, $options, 'utf8');
	//tidy_clean_repair($buffer);
      
        $OUT->_display($buffer);
        
    }
}  

?>
