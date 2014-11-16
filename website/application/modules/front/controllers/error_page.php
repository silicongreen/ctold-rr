<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class error_page extends MX_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table'); 

       
        $this->form_validation->CI =& $this;
        
    }
    
    

    function index()
    {
  
        
        $ar_js = array();
        $ar_css = array(
            "styles/themes/layout_fixed.css"
        );
        $extra_js = '';
        $data = array();
        $data['ci_key'] = "index";
        $data2['ci_key'] = "index";
        $s_content = $this->load->view('error_page',$data, true);        
        
      
        $cache_name = "WIDGET_OBJECT";
        if ( ! $ar_widgets = $this->cache->file->get($cache_name)  )
        {
            $obj_widget = new Widget_model();
            $ar_widgets = $obj_widget->get_where(array("is_enabled" => 1));
            $this->cache->file->save($cache_name, $ar_widgets, 86400 * 30 * 12);
        }
        $s_right_view = "";

        if (count($ar_widgets->all) > 0 )
        {
           $data2['post_details'] = 0;
		   $data2['widgets'] = $ar_widgets;
           $s_right_view =  $this->load->view( 'right', $data2, TRUE );  
        }
            
        
        $str_title = getCommonTitle();
        
      
        
        $s_exclusive_view =  false;//$this->load->view( 'exclusive', $data2, TRUE);
        
        
        
        
        /* $this->output->cache(10000); */
        $ar_params = array(
            "javascripts"   => $ar_js,
            "css"           => $ar_css,
            "extra_head"    => $extra_js,
            "title"         => $str_title,
            "exclusive"     => $s_exclusive_view,
            "side_bar"      => $s_right_view,
            "target"        => "index",
            "fb_contents"   => NULL,
            "content"       => $s_content
        );
        
        $this->extra_params = $ar_params;
         
    }
    
   
    
    
}
