<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
 if(!function_exists('getIssueDate')){
    function getIssueDate($myDate = "",$check_date=true)
    {
        //
        
        $CI = & get_instance();
        //$CI->load->config("tds");
        $b_issue_date = $CI->config->config['issuedate_enable'];
        
//        if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  )
//        {
//            $arIssueDate['s_issue_date'] = date("Y-m-d", strtotime($_GET['archive']));
//            
//            $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
//            $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));
//
//            $arIssueDate['current_date'] = date("Y-m-d");
//
//            return $arIssueDate;
//        }
        if ( isset($_GET['date']) &&  strlen($_GET['date']) != "0" && $check_date  )
        {
            $arIssueDate['s_issue_date'] = date("Y-m-d", strtotime($_GET['date']));
            
            $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
            $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));

            $arIssueDate['current_date'] = date("Y-m-d");

            return $arIssueDate;
        }
        else if ( ! $b_issue_date )
        {
            $arIssueDate['s_issue_date'] = date("Y-m-d");
            $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
            $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));

            $arIssueDate['current_date'] = date("Y-m-d");

            return $arIssueDate;
        }
        #GET NEWS ISSUE DATE FROM SETTINF TABLE
       
        $arIssueDate = array();
        if($myDate == "")
        {
            $CI->load->database();
            $CI->db->where('key', 'issue_date');
            $issuequery = $CI->db->get('settings');
            $arIssueDate['s_issue_date'] = $issuequery->row()->value;
        }        
        else
        {             
            $arIssueDate['s_issue_date'] = $myDate;           
        }
        
        $arIssueDate['issue_date_from'] = date("Y-m-d 00:00:00", strtotime($arIssueDate['s_issue_date']));
        $arIssueDate['issue_date_to'] = date("Y-m-d 23:59:59", strtotime($arIssueDate['s_issue_date']));
        
        $arIssueDate['current_date'] = date("Y-m-d");
        
        return $arIssueDate;
    }
 }
 
 if ( ! function_exists('sanitize')){
    function sanitize($str, $char = '-'){
        // Lower case the string and remove whitespace from the beginning or end
         $str = trim(strtolower($str));
         
         // Remove single quotes from the string
         $str = str_replace("'", '', $str);
         
         $str = str_replace("?", '', $str);
         
         $str = str_replace("|", '', $str);
		 
         $str = str_replace("&", 'and', $str);
         
         $str = str_replace("=", 'equal', $str);

         $str = str_replace("!", '', $str);
         $str = str_replace(",", '', $str);

         
         // Every character other than a-z, 0-9 will be replaced with a single dash (-)
         //$str = mb_ereg_replace("/[^a-z0-9]+/", $char, $str);
         $str = str_replace(" ","-", $str);
         
         // Remove any beginning or trailing dashes
         $str = trim($str, $char);
         
         return $str;
      } 
  }
  
  if ( ! function_exists('unsanitize')){
      function unsanitize($str, $char = '-')
      {
         // Lower case the string and remove whitespace from the beginning or end
         $str = trim(strtolower($str));
         
         // Remove single quotes from the string
         $str = str_replace("-"," ", $str);
         
         
         // Remove any beginning or trailing dashes
         $str = trim($str, $char);
         
         return $str;
      }
  }
  if(!function_exists('limit_words')){
        function limit_words($string, $word_limit)
        {
          $words = explode(" ",$string);
          return implode(" ", array_splice($words, 0, $word_limit));
        }
    }
    
    if(!function_exists('limit_string')){
        function limit_string($string,$limit=50)
        {
            if(count($string)>$limit)
                $string = substr ($string, 0,$limit)."...";
            return $string;
        }
    }      
  if ( ! function_exists('link_tag')){
    function link_tag($href = '', $rel = 'stylesheet', $type = '', $title = '', $media = '', $index_page = FALSE){
    	$CI =& get_instance();
    
    	$link = '<link ';
    
    	if( is_array( $href ) )
    	{
    		foreach( $href as $k => $v )
    		{
    			if( $k == 'href' AND strpos( $v, '://' ) === FALSE )
    			{
    				if( $index_page === TRUE )
    				{
    					$site_url = $CI->config->site_url( $href );
    
    					if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
    					{
    						if( parse_url( $site_url, PHP_URL_SCHEME ) == 'http' )
    						{
    							$site_url = substr( $site_url, 0, 4 ) . 's' . substr( $site_url, 4 );
    						}
    					}
    
    					$link .= 'href="' . $site_url . '" ';
    				}
    				else
    				{
    					$base_url = $CI->config->slash_item('base_url');
    
    					if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
    					{
    						if( parse_url( $base_url, PHP_URL_SCHEME ) == 'http' )
    						{
    							$base_url = substr( $base_url, 0, 4 ) . 's' . substr( $base_url, 4 );
    						}
    					}
    
    					$link .= 'href="' . $base_url . $v .'" ';
    				}
    			}
    			else
    			{
    				$link .= "$k=\"$v\" ";
    			}
    		}
    
    		$link .= "/>";
    	}
    	else
    	{
    		if( strpos( $href, '://' ) !== FALSE )
    		{
    			$link .= 'href="'.$href.'" ';
    		}
    		else if( $index_page === TRUE )
    		{
    			$site_url = $CI->config->site_url( $href );
    
    			if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
    			{
    				if( parse_url( $site_url, PHP_URL_SCHEME ) == 'http' )
    				{
    					$site_url = substr( $site_url, 0, 4 ) . 's' . substr( $site_url, 4 );
    				}
    			}
    
    			$link .= 'href="' . $site_url . '" ';
    		}
    		else
    		{
    			$base_url = $CI->config->slash_item('base_url');
    
    			if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
    			{
    				if( parse_url( $base_url, PHP_URL_SCHEME ) == 'http' )
    				{
    					$base_url = substr( $base_url, 0, 4 ) . 's' . substr( $base_url, 4 );
    				}
    			}
    
    			$link .= 'href="' . $base_url . $href . '" ';
    		}
    
    		$link .= 'rel="'.$rel.'" ';
    
    		if ($type	!= '')
    		{
    			$link .= 'type="'.$type.'" ';
    		}
    		if ($media	!= '')
    		{
    			$link .= 'media="'.$media.'" ';
    		}
    
    		if ($title	!= '')
    		{
    			$link .= 'title="'.$title.'" ';
    		}
    
    		$link .= '/>';
    	}
    
    	return $link;
    }
 }
// --------------------------------------------------------------

/**
 * Script
 *
 * Generates a script tage to load javascript
 *
 * @access	public
 * @param	string	javascript location
 * @return	string
 */
if ( ! function_exists('script_tag'))
{
	function script_tag( $src = '' )
	{
		$CI =& get_instance();

		$script = '<script type="text/javascript" ';

		if( strpos( $src, '://' ) !== FALSE )
		{
			$script .= 'src="'.$src.'"></script>';
		}
		else
		{
			$base_url = $CI->config->slash_item('base_url');

			if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
			{
				if( parse_url( $base_url, PHP_URL_SCHEME ) == 'http' )
				{
					$base_url = substr( $base_url, 0, 4 ) . 's' . substr( $base_url, 4 );
				}
			}

			$script .= 'src="' . $base_url . $src . '"></script>';
		}

		return $script;
	}
}

// ------------------------------------------------------------------------

/**
 * Image
 *
 * Generates an <img /> element, and allows for HTTPS
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
 if(!function_exists('img')){
    function img($src = '', $index_page = FALSE, $base64_encoded = FALSE){
    	$CI =& get_instance();
    
    	if ( ! is_array($src) )
    	{
    		$src = array('src' => $src);
    	}
    
    	// If there is no alt attribute defined, set it to an empty string
    	if ( ! isset($src['alt']))
    	{
    		$src['alt'] = '';
    	}
    
    	$img = '<img';
    
    	foreach ($src as $k=>$v)
    	{
    
    		if ($k == 'src' AND strpos($v, '://') === FALSE)
    		{
    			if( $base64_encoded !== FALSE )
    			{
    				$img .= ' src="data:image/jpg;base64,'. $v .'"';
    			}
    			else if ($index_page === TRUE)
    			{
    				$site_url = $CI->config->site_url( $v );
    
    				if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
    				{
    					if( parse_url( $site_url, PHP_URL_SCHEME ) == 'http' )
    					{
    						$site_url = substr( $site_url, 0, 4 ) . 's' . substr( $site_url, 4 );
    					}
    				}
    
    				$img .= ' src="'. $site_url .'" alt="'.$v.'"';
    			}
    			else
    			{
    				$base_url = $CI->config->slash_item('base_url');
    
    				if( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
    				{
    					if( parse_url( $base_url, PHP_URL_SCHEME ) == 'http' )
    					{
    						$base_url = substr( $base_url, 0, 4 ) . 's' . substr( $base_url, 4 );
    					}
    				}
    
    				$img .= ' src="'. $base_url . $v .'"alt="'.$v.'"';
    			}
    		}
    		else
    		{
    			$img .= " $k=\"$v\"";
    		}
    	}
    
    	$img .= '/>';
    
    	return $img;
    }
 }
 
if (! function_exists("create_link_url") )
{
    function create_link_url($s_ci_key, $str, $i_post_id = 0,$b_md5 = FALSE, $use_dash=TRUE,$use_archive = true )
    {
        
        if($s_ci_key=="index")
            $s_ci_key = null;
        
        $s_link_text = ( is_null($s_ci_key) ) ? sanitize($str) : sanitize($s_ci_key) . '/' . sanitize($str);
        
        //$s_link_text = urldecode($s_link_text);
        
        if ( $i_post_id > 0 && $b_md5 )
        {
            if($use_dash)
            {
                $s_href_link = base_url() . $s_link_text . "-" . md5($i_post_id);
            }
            else
            {
                $s_href_link = base_url() . $s_link_text . "/" . md5($i_post_id);
            }    
        }
        else if ( $i_post_id > 0 && ! $b_md5 )
        {
            if($use_dash)
            {
                $s_href_link = base_url() . $s_link_text . "-" . $i_post_id;
            }
            else
            {
                $s_href_link = base_url() . $s_link_text . "/" . $i_post_id;
            } 
           
        }
        else 
        {
            $s_href_link = base_url() . $s_link_text;
        }
        
        return $s_href_link;
    }    
}



if (! function_exists("create_link") )
{
    function create_link($s_ci_key, $obj_post, $s_class = "", $b_md5 = FALSE, $style ="color: #111;", $href_text = "", $use_dash=TRUE, $show_published_date = TRUE, $width = '58%' )
    {
        $str = $obj_post->headline;
        $i_post_id  = $obj_post->id;
        if($s_ci_key=="index")
            $s_ci_key = null;
            
        $s_link_text = ( is_null($s_ci_key) ) ? sanitize($str) : sanitize($s_ci_key) . '/' . sanitize($str);
        
        if ( $i_post_id > 0 && $b_md5 )
        {
            if($use_dash)
            {
                $s_href_link = base_url() . $s_link_text . "-" . md5($i_post_id);
            }
            else
            {
                $s_href_link = base_url() . $s_link_text . "/" . md5($i_post_id);
            }    
        }
        else if ( $i_post_id > 0 && ! $b_md5 )
        {
            if($use_dash)
            {
                $s_href_link = base_url() . $s_link_text . "-" . $i_post_id;
            }
            else
            {
                $s_href_link = base_url() . $s_link_text . "/" . $i_post_id;
            } 
           
        }
        else 
        {
            $s_href_link = base_url() . $s_link_text;
        }
        if (strlen($href_text) == 0 )
        {
            $href_text = $str;
        }
        if ( isset($obj_post->headline_color) && trim($obj_post->headline_color) != "0" && strlen(trim($obj_post->headline_color)) > 0  )
        {
            $style = "color: #" . $obj_post->headline_color;
        }
        
        //Check If Published Date is smaller than Current Data
        $obj_post_data = new Post_model();

        $arIssueDate = $obj_post_data->getIssueDate();
        
        $s_alink = "";
        if ( $show_published_date && ( date("Y-m-d", strtotime($obj_post->published_date)) < date("Y-m-d", strtotime($arIssueDate['s_issue_date'])) ) )
        {
            if ( ! is_null($s_ci_key) )
            {
                $s_alink .= "<div style='font-size: 10px; padding: 5px 5px 5px 21px; position: absolute;  right: -1px; top:0; background: #f5f5f5; width: 175px;font-weight:normal; border-radius: 0 0 0 35px;'>Published Date: " . date("F j, Y", strtotime($obj_post->published_date)) . "</div>";
                $style .= "width: ".$width."; display:block;";
            }
        }
        
        $s_alink .= '<a title="' . $str . '" href="' . $s_href_link . '" class="' . $s_class . ' headline_link" style="'.$style.'">' . $href_text . "</a>";
		$v_alink = "";
//        if ( isset($obj_post->has_video) && $obj_post->has_video )
//        {
//           $s_alink .= '&nbsp;&nbsp;<img class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/play.png" />';
//           $v_alink .= '&nbsp;&nbsp;<img title="Video" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/video.png" />';
//        }
//        if ( isset($obj_post->has_image) && $obj_post->has_image )
//        {
//           $v_alink .= '&nbsp;&nbsp;<img title="Images" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/photo.png" />';
//        }
//		if ( isset($obj_post->has_pdf) && $obj_post->has_pdf )
//        {
//           $v_alink .= '&nbsp;&nbsp;<img title="PDF" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/PDF.png" />';
//        }
        if ( isset($obj_post->is_exclusive) && $obj_post->is_exclusive && (!isset($obj_post->exclusive_expired) || ($obj_post->exclusive_expired==null) || ($obj_post->exclusive_expired > date("Y-m-d H:i:s"))))
        {
           $s_alink .= '<sup style="color: #f00; font-size: 10px; padding-left:5px;">Exclusive</sup>';
        }
        else if ( isset($obj_post->is_breaking) && $obj_post->is_breaking && (!isset($obj_post->breaking_expire) || ($obj_post->breaking_expire==null) || ($obj_post->breaking_expire > date("Y-m-d H:i:s"))) )
        {
            $s_alink .= '<sup style="color: #f00; font-size: 10px; padding-left:5px;">Breaking</sup>';
        }
        else if (  isset($obj_post->is_featured) && $obj_post->is_featured )
        {
            $s_alink .= '<sup style="color: #f00; font-size: 10px; padding-left:5px;">featured</sup>';
        }
        else if (  isset($obj_post->is_developing) && $obj_post->is_developing )
        {
            $s_alink .= '<sup style="color: #f00; font-size: 10px; padding-left:5px;">Developing</sup>';
        }
		
//        if ( isset( $obj_post->type ) && $s_class == "" )
//        {
//            if(isset( $obj_post->type ) && $obj_post->type=="Online")
//            {
//                    $type_icon = '<img title="Online" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/online.png" />';
//            }
//            if(isset( $obj_post->type ) && $obj_post->type=="Print")
//            {
//                    $type_icon = '<img title="Print" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/print.png" />';
//            }		
//			
//            $s_alink .= '<div class="print_online" style="display:none;">
//                <div style="font-size: 11px; margin-left: 10px; color: grey; font: 10px/12px \'Giovanni Book\',Arial,Helvetica,sans-serif; float: left;">' . $obj_post->type . '</div>
//                <div style="float:right; margin-right: 5px;"><a class="comment_count" href="' . $href_text . '#disqus_thread" style="display: none; color: grey; font: 10px/12px \'Giovanni Book\',Arial,Helvetica,sans-serif;">Comments</a></div>
//                                            '.$v_alink.'
//            </div>';
//        }
//        if ( isset( $obj_post->type ) && $s_class == "" )
//        {
//            $s_alink .= '<div class="print_online" style="display:none;">
//                            <div style="font-size: 11px; margin-left: 14px;margin-top:3px; color: red; font: 10px/12px \'Giovanni Book\',Arial,Helvetica,sans-serif; float: left;">' . $obj_post->type . '</div>
//                            <div style="float:right; margin-right: 5px; display: none;"><a class="comment_count" href="' . $href_text . '#disqus_thread" data-disqus-identifier="' . $i_post_id . '" style="color: grey; font: 10px/12px \'Giovanni Book\',Arial,Helvetica,sans-serif;">Comments</a></div> <!-- #disqus_thread -->
//                            '.$v_alink.'
//                        </div>';
//            
//        }
        return $s_alink;
    }   
}

if (! function_exists("check_lead_image") )
{
    function check_lead_image( $s_content, $s_lead_material )
    {
        error_reporting(0);
        if(strlen($s_lead_material) == 0)
        {
            $b_has_image = true;
        }
        else
        {        
            $dom = new DOMDocument();
            $dom->loadHtml($s_content);
            $b_has_image = false;
            foreach ($dom->getElementsByTagName('img') as $img) 
            {
                $img_href = $img->getAttribute("src");
                if ( str_ireplace(base_url(), "", $img_href) == $s_lead_material )
                {
                    $b_has_image = true;
                    break;
                }
            }
        }
        return $b_has_image;
    }
}

if (! function_exists("rearrange_image_content") )
{
    function rearrange_image_content( $s_content, $s_alt = "" )
    {
        error_reporting(0);
        $dom = new DOMDocument();
        $dom->loadHtml($s_content);
        foreach ($dom->getElementsByTagName('img') as $img) 
        {
            //Create a Image Container
            $div = $dom->createElement("DIV");
            $div->setAttribute("class", "image_container");
            $div->setAttribute("id", "img_cont");
            
            //Create the toolbar
            $ul = $dom->createElement("UL");
            $ul->setAttribute("class", "toolbar transparent");
            $ul->setAttribute("id", "tlbar");
            $ul->setAttribute("style", "display:none");
            
            $ar_li_classes = array("zoomin","zoomout","shop","fav","edit","label","info");
            //Create LI And Link Element
            for( $i=0; $i<$ar_li_classes; $i++ )
            {
                $li[$i] = $dom->createElement("LI");
                $a[$i] = $dom->createElement("A");
                $a[$i]->setAttribute("class", $ar_li_classes[$i]);
                
                $li[$i]->appendChild($a[$i]);
                $ul->appendChild($li[$i]);
            }
            $div->appendChild($ul);
            if ( $img->parentNode->nodeName == "a" )
            {
                $img->parentNode->setAttribute("href", "javascript:;");
                $img->parentNode->setAttribute("rel", "");
            }
            $img->setAttribute("class", "toolbar");
            $img->setAttribute("alt", $s_alt);
            $img->setAttribute("style", "cursor: pointer");
            $div->appendChild($img);
        }
        
        $s_content = $dom->saveHTML();
        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $s_content));
    }
}

if (! function_exists("getImageForFacebook") )
{
    function getImageForFacebook($objNews, $length=800,$s_ci_key=-1)
    {
            $arData = array();
            error_reporting(0);
            $doc = new DOMDocument();
            $doc->loadHTML($objNews->content);
            
            $xpath = new DOMXPath($doc);
            $s_caption = $xpath->evaluate("string(//div[contains(@class, 'img_caption')])");
            
            $img_src = "";
            $images = $doc->getElementsByTagName('img');
            foreach ($images as $image) 
            {
               if($image->getAttribute('src') =="/images/relatednews.jpg" || $image->getAttribute('src') ==base_url()."images/relatednews.jpg" || str_replace(base_url(),"",$image->getAttribute('src')) == "images/relatednews.jpg")
               {
                   continue;
               }
               else if($i==0)
               {
                  
                   $i++;
                   $img_src = $image->getAttribute('src');
                   break;
                   
               } 
               else
               {
                   //$arData['all_image'][] = $image->getAttribute('src');
               }    

            }
            
            return $img_src;
    }
}


if ( !function_exists("strip_selected_tags") )
{
    function strip_selected_tags($text, $tags = array())
    {
        $args = func_get_args();
        $text = array_shift($args);
        
        $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
        foreach ($tags as $tag){
            if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)){
                $text = str_replace($found[0],$found[1],$text);
          }
        }

        return $text;
    }
}

if ( !function_exists("gallery_exists") )
{
    function gallery_exists($ci_key)
    {
        $ci = & get_instance();
        $ci->load->model("menus");

        $obj_menus = $ci->menus->get_menu_by_ci_key($ci_key);
        return (isset($obj_menus->has_gallery)) ? array($obj_menus->gallery_name, $obj_menus->ad_plan_id_for_gallery) : false;
    }
}

if ( !function_exists("get_category_type") )
{
    function get_category_type($ci_key)
    {
        $ci = & get_instance();
        $ci->load->model("menus");

        $obj_menus = $ci->menus->get_menu_by_ci_key($ci_key);
        
        if ( $obj_menus ) 
        {
            $i_category_id = $obj_menus->category_id;
            if ( $i_category_id != 0 )
            {
                $obj_category = new Category_model( $i_category_id );
                return array($obj_category->category_type_id, $i_category_id, $obj_category->name) ;
            }
        }
        else
        {
            $s_category_name = unsanitize($ci_key);
            $obj_category = new Category_model();
            $obj_category_data = $obj_category->where("name",$s_category_name)->limit(1)->get();
            return array($obj_category_data->category_type_id, $obj_category_data->id, $obj_category_data->name) ;
        }
        return false;
    }
}

if ( !function_exists("show_download") )
{
    function show_download( $i_category_id, $s_published_date, $s_category_name, $ci_key = '', $i_category_type_id = 1 )
    {
        $cache_name = 'OBJ_INNER_PDF_'.strtoupper($ci_key).'_'.$s_published_date;
        
        $CI = & get_instance();
        
        if( !$obj_category_pdf_data = $CI->cache->get($cache_name)){
            
            $obj_category_pdf = new Category_pdf();
            
            if( $i_category_type_id > 1 ){
                $obj_category_pdf_data = $obj_category_pdf->where("category_id", $i_category_id)->where("issue_date <= '" . date("Y-m-d", strtotime($s_published_date)) . "'")->order_by("issue_date", "desc")->limit(1)->get();
            }else{
                $obj_category_pdf_data = $obj_category_pdf->where("category_id", $i_category_id)->where("issue_date", date("Y-m-d", strtotime($s_published_date)))->limit(1)->get();
            }
            
            $time = ($i_category_type_id == 1) ? 86400 : 604800;
            $CI->cache->save($cache_name, $obj_category_pdf_data, $time);
        }
        
        if (count($obj_category_pdf_data->pdf) == 0 )
        {
            return "";
        }
        else
        {
            return create_download_link($obj_category_pdf_data->id, $s_category_name, "100%", "center", $ci_key, $s_published_date, $i_category_type_id);
        }
    }
}

if ( !function_exists("create_download_link") )
{
    function create_download_link( $i_pdf_id, $s_category_name, $s_div_width = "100%", $s_text_align = "center", $ci_key = '', $s_published_date = '', $i_category_type_id = 1 )
    {
        $cache_name = 'HTML_INNER_PDF_'.strtoupper($ci_key).'_'.$s_published_date;
        
        $ci = & get_instance();
        
        if( !$str_download_link = $ci->cache->get($cache_name)){
            
            $ci->load->config("tds");
            $s_download_type = isset($ci->config->config['download_magazine_option']) ? $ci->config->config['download_magazine_option'] : "download";
            $s_target = ($s_download_type == "download") ? "_self" : "_blank";
            
            $str_download_link = '<div id="download_link" style="width:' . $s_div_width . '; text-align: ' . $s_text_align . '; padding-top: 20px; margin-top:10px; text-shadow: 2px 2px #FFFF00;"><a target="' . $s_target . '" href="' . base_url() . $s_download_type . '/' . $i_pdf_id . '/' . $s_category_name . '">Download PDF Version of ' . ucwords($s_category_name) . ' <br />
                                    <img alt="' . $s_category_name . '" src="' . base_url() . 'ckeditor/kcfinder/themes/oxygen/img/files/big/download.png" style="margin-top:10px;"/></a></div>';
            
            $time = ($i_category_type_id == 1) ? 86400 : 604800;
            $ci->cache->save($cache_name, $str_download_link, $time);
        }
        
        return $str_download_link;
    }
}

if ( !function_exists("show_cover") )
{
    function show_cover( $i_category_id, $s_published_date, $s_category_name )
    {
        $obj_category_cover = new Category_cover();
        
        $obj_post = new Post_model();

        $arIssueDate = $obj_post->getIssueDate();
        
        $obj_category_cover_data = $obj_category_cover->where("category_id", $i_category_id)->where("issue_date <=", date("Y-m-d", strtotime($arIssueDate['s_issue_date'])))->order_by("issue_date DESC")->limit(1)->get();
        
        
        if (count($obj_category_cover_data->all) == 0 )
        {
            //Collect the Latest Cover to Show on Site, It can be Disabled By Config
            $CI = & get_instance();
            
            $CI->load->config("tds");
            if ( $CI->config->config['show_old_magazine_cover'] )
            {
                $obj_category_cover_data = $obj_category_cover->where("category_id", $i_category_id)->where("issue_date <= ", date("Y-m-d", strtotime($arIssueDate['s_issue_date'])))->order_by("issue_date DESC")->limit(1)->get();
                if (count($obj_category_cover_data->all) == 0 )
                {
                    return "";
                }
                else
                {
                    $s_download_link = show_download($i_category_id, $s_published_date, $s_category_name);
                    return create_image($s_download_link, $obj_category_cover_data->image, $s_category_name, "toolbar", "90%", "43%");
                }
            }
            else
            {
                return "";
            }
        }
        else
        {
            $s_download_link = show_download($i_category_id, $s_published_date, $s_category_name);
            return create_image($s_download_link, $obj_category_cover_data->image, $s_category_name, "toolbar", "90%", "43%");
        }
    }
}

if ( !function_exists("create_image") )
{
    function create_image( $s_download_link, $s_image_name, $alt = "",$s_class = "", $s_width = "100%", $s_div_width = "100%", $s_text_align = "center", $s_float = "right" )
    {
        return '<div id="cover_image" style="padding:10px 2px; width:' . $s_div_width . '; text-align: ' . $s_text_align . '; float: ' . $s_float . ';"><img alt="' . $alt . '" class="' . $s_class .  '" src="' . base_url() . $s_image_name . '" width="' . $s_width . '" />' . $s_download_link . '</div>';
    }
}

if ( !function_exists("get_issue_date") )
{
    function get_issue_date()
    {
        $CI = & get_instance();
        $arIssueDate = $CI->session->userdata("issue_date");
        return $arIssueDate['s_issue_date'];
    }
}

if ( !function_exists("get_twitter_info") )
{
    function get_twitter_info( $s_ci_key )
    {
        $CI = & get_instance();
        $CI->load->database();
        
        $CI->db->select('twitter_name, widget_id');
        $query = $CI->db->get_where("menu",array('ci_key' => $s_ci_key));
        if ( $query->num_rows() > 0 )
        {
            $obj_res = $query->_fetch_object();
            return array($obj_res->twitter_name, $obj_res->widget_id);
        }
        else
        {
            $CI->load->config("tds");
            return isset($ci->config->config['twitter_default_account']) ? $ci->config->config['twitter_default_account'] : array(0,0);
        }
    }
}

if ( !function_exists("show_more") )
{
    function show_more(  )
    {
        $CI = & get_instance();
        $CI->load->config("tds");
        return isset($CI->config->config['show_more']) ? $CI->config->config['show_more'] : FALSE;
    }
}

if ( !function_exists("print_news") )
{
    function print_news( $s_ci_key, $s_content, $news, $style = "", $b_return = FALSE, $b_check_summary = TRUE, $b_add_bottom_bar=TRUE, $ar_images_sizes = array() )
    {
        $b_show_more = true;
        if ( empty($ar_images_sizes) )
        {
            $s_print_data = '<div class="contents-news overflow" style="' . $style . '; height:70px;"><p>' . $s_content . '</p>';
        }
        else
        {
            if ( isset( $ar_images_sizes['line'] ) )
            {
                $i_line = $ar_images_sizes['line'];
                $i_word_per_line  = $ar_images_sizes['word_per_line'];
                $i_total_word = $i_line * $i_word_per_line;
                
                if (strlen($style) > 0 )
                {
                    $style .= ";";
                }
                //$style .= "font-size: 14px; word-wrap: break-word;";
                
                $ar_words = explode(' ',$s_content);
            
                if ( count($ar_words) > $i_total_word )
                {
                    $s_word_prefix = "...";
                    $s_news_content = "<p style='line-height: 1.2em;'>";
                    $i=0; foreach( $ar_words as $words )
                    {
                        $s_news_content .= $words . " ";
                        $i++;
                        if ( $i > $i_total_word )
                        {
                            break;
                        }
                    }
                    $s_news_content = substr($s_news_content, 0, -1) . $s_word_prefix . "</p>";
                    $s_print_data = '<div class="contents-news" style="' . $style . '">' . $s_news_content . '';
                }                
                else
                {
                    $s_print_data = '<div class="contents-news" style="' . $style . '"><p>' . $s_content . '</p>';
                }
            }
            else
            {
                $i_text_height = 20;
                $i_word_per_line  = 15;
                $i_per_word_width = 30; 
                list( $i_image_height, $i_image_width, $b_float, $i_box_height, $i_box_width, $s_styles ) = $ar_images_sizes;

                $i_total_word = 0;

                $i_box_height_req = 0;

                if ( $b_float )
                {
                    $i_diff_width = $i_box_width - $i_image_width;
                    $i_total_word_per_line = floor( $i_diff_width / $i_per_word_width );
                    $i_total_line = floor($i_image_height / $i_text_height) ;
                    $i_total_word = $i_total_word_per_line * $i_total_line;

                    $i_box_height_req += $i_image_height;
                }
                
                $i_height = $i_box_height - $i_image_height;

                $i_box_height_req += $i_height;

                
                
                $i_line = floor( $i_height / $i_text_height );
                
                $i_word_per_line = floor( $i_box_width / $i_per_word_width );
                
                
                $i_word = $i_line * $i_word_per_line;

                $i_total_word += $i_word;
                
                if (strlen($style) > 0 )
                {
                    $style .= ";";
                }
                $style .= "height:" . $i_box_height_req . "px; ";
                
                $ar_words = explode(' ',$s_content);
            
                if ( count($ar_words) > $i_total_word )
                {
                    $s_word_prefix = "...";
                    $s_extra_style = ( strlen($s_styles) > 0 ) ? 'style="' . $s_styles . ';"' : "";
                    $s_news_content = "<p " . $s_extra_style . ">";
                    $i=0; foreach( $ar_words as $words )
                    {
                        $s_news_content .= $words . " ";
                        $i++;
                        if ( $i > $i_total_word )
                        {
                            break;
                        }
                    }
                    $s_news_content = substr($s_news_content, 0, -1) . $s_word_prefix  . "</p>";
                    $s_print_data = '<div class="contents-news" style="' . $style . '">' . $s_news_content . '';
                }
                else
                {
                    $s_print_data = '<div class="contents-news" style="' . $style . '"><p>' . $s_content . '</p>';
                }
            }
        }
        if ( $b_check_summary && strlen( trim($news->summary) ) > 0 )
        {
            $s_print_data = '<div class="news" style="' . $style . '"><p>' . $news->summary . '</p>';
            $b_show_more =  false;
        }
        if ( show_more() && $b_show_more )
        {
            $s_print_data .= create_link($s_ci_key, $news, "more", FALSE, "color: #f00; display: none;", "More");
        }
        $s_print_data .= '</div>';
        
        if($b_add_bottom_bar)
        {
            $s_print_data .="<div class='news-bottom-bar'>";
            
            if ( isset($news->has_video) && $news->has_video )
            {
               $s_print_data .= '&nbsp;&nbsp;<img title="Video" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/video.png" />';
            }
            
            if ( isset($news->has_image) && $news->has_image )
            {
               $s_print_data .= '&nbsp;&nbsp;<img title="Images" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/photo.png" />';
            }
            
            if ( isset($news->has_pdf) && $news->has_pdf )
            {
               $s_print_data .= '&nbsp;&nbsp;<img title="PDF" class="play_icon" src="' . base_url() . 'styles/layouts/tdsfront/images/icons/PDF.png" />';
            }
            
            
            if ( isset( $news->type ) && $s_class == "" )
            {
                $i_post_id = $news->id;
                $s_class = ( $news->can_comment == 0 ) ? "display: none;" : "";
                $s_print_data .= '<div class="print_online" style="display:none;">
                                <div style="font-size: 11px; margin-left: 14px;margin-top:3px; color: red; font: 10px/12px \'Giovanni Book\',Arial,Helvetica,sans-serif; float: left;">' . $news->type . '</div>
                                <div style="float:right; margin-right: 5px;' . $s_class . '"><a class="comment_count" href="' . $href_text . '#disqus_thread" data-disqus-identifier="' . $i_post_id . '" style="color: grey; font: 10px/12px \'Giovanni Book\',Arial,Helvetica,sans-serif;">Comments</a></div> <!-- #disqus_thread -->
                                '.$v_alink.'
                            </div>';

            }
            
            if ( isset($obj_post->caption_inside) && strlen($obj_post->caption_inside) > 0 )
            {
                $s_print_data .= '<div class="cap" style="color: #f00; font-size: 10px; display:none;">' . $obj_post->caption_inside . '</div>';
            }
            else
            {
                $s_print_data .= '<div class="cap" style="color: #f00; font-size: 10px; display:none;">' . $obj_post->headline . '</div>';
            }
            
            $s_print_data .="</div>";
            
        }
        
        
        if ( $b_return )
        {
            return $s_print_data;
        }
        else
        {
            echo $s_print_data;
        }
    }
}


function get_gallery_data_video($ci_key, $s_date_issue)
{
    $CI = & get_instance();
    $CI->load->database();
    $sql_materials ="select material_id from tds_material_menu 
       where issue_date='".date("Y-m-d", strtotime($s_date_issue))."'
         AND menu_id = (select id from tds_menu where ci_key = '".$ci_key."' limit 1) order by id ASC";
    $results_materials = $CI->db->query($sql_materials)->result();
    $mat_array = array();
    foreach($results_materials as $value)
    {
        $mat_array[] = $value->material_id;
    }
    $mat_string = implode(",", $mat_array);
    
    
    $sql_images = "select * from  tds_materials_video where material_id in
       (".$mat_string.")
            ORDER BY FIELD(material_id, $mat_string)";
    
    $results = $CI->db->query($sql_images)->result();
    
    return $results;
    
}


function get_gallery_data($ci_key, $s_date_issue)
{
    $CI = & get_instance();
    $CI->load->database();
    $sql_materials ="select material_id from tds_material_menu 
       where issue_date='".date("Y-m-d", strtotime($s_date_issue))."'
        AND type = 1 AND menu_id = (select id from tds_menu where ci_key = '".$ci_key."' limit 1) order by id ASC";
    $results_materials = $CI->db->query($sql_materials)->result();
    $mat_array = array();
    foreach($results_materials as $value)
    {
        $mat_array[] = $value->material_id;
    }
    $mat_string = implode(",", $mat_array);
    
    
    $sql_images = "select * from  tds_materials where id in
       (".$mat_string.")
            ORDER BY FIELD(id, $mat_string)";
    
    $results = $CI->db->query($sql_images)->result();
    
    return $results;
    
}

if ( !function_exists("check_gallery_active_ci_key") )
{
    function check_gallery_active_ci_key($ci_key)
    {
         $CI = & get_instance();
         $CI->load->database();
         $sql_check_gallery = "SELECT show_gallery from tds_menu where ci_key='".$ci_key."' limit 1"; 
         $menu_data = $CI->db->query($sql_check_gallery)->row();

         if($menu_data->show_gallery)
         {
             return true;
         }    
         else
         {
             return false;
         }  
    }
}

function has_separate_gallery_data($ci_key, $s_date , $type = "image" )
{
    $url = 'gallery/xml/' . $ci_key . '-'.$type.'-' . date("Ymd", strtotime($s_date)) . '.xml';
    $b_found_date = false;
    
    if ( file_exists($url) )
    {
        $xml = simplexml_load_file($url);
        foreach ($xml->slide as $entry)
        {
            if(isset($entry->file) && trim($entry->file)!="")
            {
               
               $b_found_date = true; 
               break;
            }
        } 
    }
    return $b_found_date;
}        

function has_gallery_data( $ci_key, $s_date, $b_issue_date = true, $i_count = 0,$current_date_only = false ) 
{
    $url_image      = 'gallery/xml/' . $ci_key . '-image-' . date("Ymd", strtotime($s_date)) . '.xml';
    $url_video      = 'gallery/xml/' . $ci_key . '-video-' . date("Ymd", strtotime($s_date)) . '.xml';
    $url_prodcast   = 'gallery/xml/' . $ci_key . '-podcast-' . date("Ymd", strtotime($s_date)) . '.xml';
    $b_found_date = false;
    $s_date_data  =  "";
    if ( file_exists($url_image) || file_exists($url_video) || file_exists($url_prodcast) )
    {
        if(file_exists($url_image))
        {
            $xml = simplexml_load_file($url_image);
            foreach ($xml->slide as $entry)
            {
                if(isset($entry->file) && trim($entry->file)!="")
                {
                   $s_date_data = $s_date;   
                   $b_found_date = true; 
                   break;
                }
            }
        }
        
        if($b_found_date===false && file_exists($url_video))
        {
            $xml = simplexml_load_file($url_video);
            foreach ($xml->slide as $entry)
            {
                if(isset($entry->file) && trim($entry->file)!="")
                {
                   $s_date_data = $s_date;   
                   $b_found_date = true; 
                   break;
                }
            }
        }  
        if($b_found_date===false && file_exists($url_prodcast))
        {
            $xml = simplexml_load_file($url_prodcast);
            foreach ($xml->slide as $entry)
            {
                if(isset($entry->file) && trim($entry->file)!="")
                {
                   $s_date_data = $s_date;   
                   $b_found_date = true;
                   break;
                }
            }
        } 
        
    }    
    else if($b_found_date===false && $current_date_only === false)
    {
        
        
        for ( $i = ( $b_issue_date ) ? 0 : 1; $i < 7; $i++  )
        {
            $dt = ( $i == 0 ) ? "now" : "-" . $i . " day";
            
            $url_image      = 'gallery/xml/' . $ci_key . '-image-' . date("Ymd", strtotime($dt)) . '.xml';
            $url_video      = 'gallery/xml/' . $ci_key . '-video-' . date("Ymd", strtotime($dt)) . '.xml';
            $url_prodcast   = 'gallery/xml/' . $ci_key . '-podcast-' . date("Ymd", strtotime($dt)) . '.xml';
            
            if ( file_exists($url_image) || file_exists($url_video) || file_exists($url_prodcast) )
            {
                if(file_exists($url_image))
                {
                
                    $xml = simplexml_load_file($url_image);
                    foreach ($xml->slide as $entry)
                    {
                        if(isset($entry->file) && trim($entry->file)!="")
                        {
                           $s_date_data = date("Y-m-d", strtotime($dt));
                           $b_found_date = true;
                           break;
                        }
                    }
                }
                if($b_found_date===false && file_exists($url_video))
                {
                    $xml = simplexml_load_file($url_video);
                    foreach ($xml->slide as $entry)
                    {
                        if(isset($entry->file) && trim($entry->file)!="")
                        {
                           $s_date_data = date("Y-m-d", strtotime($dt));
                           $b_found_date = true; 
                           break;
                        }
                    }
                }  
                if($b_found_date===false && file_exists($url_prodcast))
                {
                    $xml = simplexml_load_file($url_prodcast);
                    foreach ($xml->slide as $entry)
                    {
                        if(isset($entry->file) && trim($entry->file)!="")
                        {
                           $s_date_data = date("Y-m-d", strtotime($dt));
                           $b_found_date = true;
                           break;
                        }
                    }
                } 
                
            }
        }   
    }
    return ($b_found_date) ? $s_date_data : false;
}


function has_gallery_data_cartoon( $name,$ci_key, $s_date, $b_issue_date = false, $i_count = 0 ) {
    if ( file_exists('gallery/xml/' . $ci_key . '-'.$name.'-' . date("Ymd", strtotime($s_date)) . '.xml')  )
        return $s_date;   
    else
    {
        $b_found_date = false;
        $s_date_data  =  "";
        for ( $i = ( $b_issue_date ) ? 0 : 1; $i < 7; $i++  )
        {
            $dt = ( $i == 0 ) ? "now" : "-" . $i . " day";
            if ( file_exists('gallery/xml/' . $ci_key . '-'.$name.'-' . date("Ymd", strtotime($dt)) . '.xml') )
            {
                 $s_date_data = $dt;   
                 $b_found_date = true;
                 break;
            }
        }   
    }
    return ($b_found_date) ? $s_date_data : false;
}

function compress_image($source_url, $destination_url, $quality) {
	$info = getimagesize($source_url);
 
	if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
	elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
	elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
 
	//save file
	imagejpeg($image, $destination_url, $quality);
 
	//return destination file
	return $destination_url;
}


function super_compress( $image_path, $dest_image_path )
{
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
   $target_url = 'http://jpgoptimiser.com/optimise';
   $file_name_with_full_path = $image_path;
   $post = array('input'=>'@'.$file_name_with_full_path);

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL,$target_url);
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
   $result=curl_exec ($ch);
   curl_close ($ch);
   

   if(file_exists($dest_image_path)){
        unlink($dest_image_path);
    }
    $fp = fopen(substr($dest_image_path, 1, strlen($dest_image_path)),'w+');
    fwrite($fp, $result);
    fclose($fp);
}

if(!function_exists('getCustomTitle')){
    function getCustomTitle($obj_data){
        if(isset($obj_data->short_title) && !empty($obj_data->short_title))
        {
            $str_title = $obj_data->short_title;
        }
        else
        {
            $str_title = '';
            if(isset($obj_data->shoulder) && !empty($obj_data->shoulder))
            {
                $str_title .= $obj_data->shoulder;
            }
            if(isset($obj_data->headline) && !empty($obj_data->headline))
            {
                if(!empty($obj_data->shoulder))
                {
                    $str_title .= ' | ';
                }
                $str_title .= $obj_data->headline;
            }
            if(isset($obj_data->sub_head) && !empty($obj_data->sub_head))
            {
                if(!empty($obj_data->headline) || !empty($obj_data->shoulder))
                {
                    $str_title .= ' | ';
                }
                $str_title .= $obj_data->sub_head;
            }
        }
        return ($str_title);
    }
}

if(!function_exists('array_orderby'))
{
    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}

if(!function_exists('getCommonTitle')){
    function getCommonTitle(){
        $str_title = WEBSITE_NAME . " | Largest education portal in Bangladesh";
        return ($str_title);
    }
}

if(!function_exists('garbage_collector_block'))
{
    function garbage_collector_block($block,$delete_all=false)
    {
		if ( $block > 3  )
		{
			return false;
		}
        $CI = & get_instance();
        $cache_to_delete = array(1=>"CARROSEL_CACHE",2=>"CARROSEL_CACHE",3=>"OTHER_CACHE");
        
       
        if($delete_all)
        {
            
            $ar_cahce_to_delete = array("CARROSEL_CACHE","OTHER_CACHE");  
        }
        else
        {
            $ar_cahce_to_delete = array($cache_to_delete[$block]);  
        }
        
        $ar_cache_content = $CI->cache->file->cache_info();
        foreach ($ar_cache_content as $a_cache_name)
        {
            
            $s_cache_name = $a_cache_name['name'];
          
            foreach( $ar_cahce_to_delete as $cache )
            {
                
                if (stripos($s_cache_name,$cache) !== FALSE )
                {
                    
                    $s_cache_name = "home/".$s_cache_name;
                   
                    $CI->cache->file->delete($s_cache_name);
                }
            }
        }
    }
}
if(!function_exists('get_curl_url'))
{
    function get_curl_url($garbagecollector = "")
    {
	$CI = & get_instance();
                
        $CI->load->config("huffas");	

        return $CI->config->config['api_url'] .  "/".$garbagecollector;
    }
}

if(!function_exists('password_change_request'))
{
    function password_change_request($password,$token)
    {
        $url = get_curl_url("resetpassword");
        $url = str_replace("/freeuser","/user", $url);
        $fields = array(
            'password' => $password,
            'token'  => $token
            
	);
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
            )                                                                       
        );  
        $result = curl_exec($ch);
        curl_close($ch);
        
        
    }
}

if(!function_exists('forget_password_request'))
{
    function forget_password_request($username,$email)
    {
        $url = get_curl_url("forgotpassword");
        $url = str_replace("/freeuser","/user", $url);
        $fields = array(
            'email' => $email,
            'username'  => $username
            
	);
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
            )                                                                       
        );  
        $result = curl_exec($ch);
        curl_close($ch);
        
        
    }
}

if(!function_exists('update_cache_single'))
{
    function update_cache_single($id,$user_view_count,$view_count)
    {
        $url = get_curl_url("createcachesinglenews");
        $cache_name = "YII-RESPONSE-HOME";
        $fields = array(
            'id' => $id,
            'delete_cache'  => "no",
            'user_view_count'  => $user_view_count,
            'view_count'  => $view_count,
            
	);
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            
        }
        rtrim($fields_string, '&');
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
            )                                                                       
        );  
        $result = curl_exec($ch);
        curl_close($ch);
        
        
    }
}

if(!function_exists('create_cache_single'))
{
    function create_cache_single($id)
    {
        $url = get_curl_url("createcachesinglenews");
        $cache_name = "YII-RESPONSE-HOME";
        $fields = array(
            'id' => $id,
            'delete_cache'  => "yes"
	);
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            
        }
        rtrim($fields_string, '&');
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
            )                                                                       
        );  
        $result = curl_exec($ch);
        curl_close($ch);
        
        
    }
}

if(!function_exists('garbage_collector'))
{
    function garbage_collector()
    {
        $url = get_curl_url("garbagecollector");
        $cache_name = "YII-RESPONSE-HOME";
        $fields = array(
            'keys_to_match' => urlencode($cache_name)
	);
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            
        }
        rtrim($fields_string, '&');
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
            )                                                                       
        );  
        $result = curl_exec($ch);
        curl_close($ch);
        
        
    }
}

if(!function_exists('garbage_collector_gallery'))
{
    function garbage_collector_gallery($home_cache = true)
    {
        $CI = & get_instance();
        $ar_cahce_to_delete []="ALL_GALLERY_CACHE";
        if($home_cache)
        $ar_cahce_to_delete[]="CONTENT_CACHE";
        
        $ar_cahce_to_delete[]="INNER_CONTENT_CACHE";
        
        
        
        $ar_cache_content = $CI->cache->file->cache_info();
        
        if($CI->cache->file->get("common/right_view"))
        {
            $CI->cache->file->delete("common/right_view");
        }
        foreach ($ar_cache_content as $a_cache_name)
        {
            $s_cache_name = $a_cache_name['name'];
            foreach( $ar_cahce_to_delete as $cache )
            {
                if (stripos($s_cache_name, $cache) !== FALSE )
                {
                    if($CI->cache->file->get($s_cache_name))
                    {
                         $CI->cache->file->delete($s_cache_name);
                    }
                    if($cache=="CONTENT_CACHE")
                    {
                       $s_cache_name = "home/".$s_cache_name;
                    } 
                    if($CI->cache->file->get($s_cache_name))
                    {
                         $CI->cache->file->delete($s_cache_name);
                    }
                }
            }
        }
    }
}



if(!function_exists('garbage_collector_category'))
{
    function garbage_collector_category($category_id = 0)
    {
        $url = get_curl_url("garbagecollector");
        $cache_name = "YII-RESPONSE-CATEGORY-".$category_id;
        $fields = array(
            'keys_to_match' => urlencode($cache_name)
	);
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            
        }
        rtrim($fields_string, '&');
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
            )                                                                       
        ); 

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        
//        //creating first page cache for visitor
//        $url = get_curl_url("getcategorypost");
//        $fields = array(
//            'category_id' => $category_id,
//            'callded_for_cache' =>true
//	);
//        foreach($fields as $key=>$value) { 
//            $fields_string .= $key.'='.$value.'&'; 
//            
//        }
//        rtrim($fields_string, '&');
//        $ch = curl_init();
//
//        //set the url, number of POST vars, POST data
//        curl_setopt($ch,CURLOPT_URL, $url);
//        curl_setopt($ch,CURLOPT_POST, count($fields));
//        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
//
//        //execute post
//        $result = curl_exec($ch);
//
//        //close connection
//        curl_close($ch);
    }
}

if(!function_exists('get_menu_category'))
{
   function get_menu_category()
   {

       $CI = & get_instance();
       $CI->load->database();
       $CI->db->where('is_active', 1);
       $CI->db->where('position', 1);
       $CI->db->where('type', 1);
       $CI->db->order_by('parent_menu_id asc, priority asc');

       $query = $CI->db->get('menu');
       $data = array();
       $total = $query->num_rows();
       if($total)
       {
           foreach ($query->result() as $row)
           {
               if($row->category_id)
               {

                   $data['category'][] = $row->category_id;
                   $data['ci_key'][$row->category_id] = $row->ci_key;
                   $data['menu'][$row->id] = $row->title;
                   $data['menu_s'][$row->title] = $row->title;
               }
           }
       }

       return  $data;

   }
}
if(!function_exists('get_menu_by_cikey'))
{
   function get_menu_by_cikey($ci_key)
   {
       if ( $ci_key == "0" )
       {
           return 0;
       }
       $CI = & get_instance();
       $CI->load->database();
       $CI->db->where('ci_key', $ci_key);
       $query = $CI->db->get('menu');
       $data = array();
       $total = $query->num_rows();
       if($total)
       {
//            foreach ($query->result() as $row)
//            {
//                if($row->category_id)
//                {
//                    $data['ci_key'] = $row->ci_key;
//                    $data['menu_id'] = $row->id;
//                }
//            }

           return  $total;
       }
       else
       {
           return 0;
       }
   }
}

if(!function_exists('show_magazine_image')){
    function show_magazine_image($obj_news_or_image_url, $dimension = array(), $s_class = 'ym-col1 gallery-content-left', $inside_div = false, $use_liquid = false, $cover = false){
        $str_image = '';
        
        if( ($cover == true) && (is_integer($obj_news_or_image_url)) ){
            $obj_category_cover = new Category_cover();
        
            $obj_post = new Post_model();
    
            $arIssueDate = $obj_post->getIssueDate();
            
            $obj_category_cover_data = $obj_category_cover->where("category_id", $obj_news_or_image_url)->where("issue_date <=", date("Y-m-d", strtotime($arIssueDate['s_issue_date'])))->order_by("issue_date DESC")->limit(1)->get();
            
            if (count($obj_category_cover_data->all) == 0 )
            {
                $CI = & get_instance();
                
                $CI->load->config("tds");
                if ( $CI->config->config['show_old_magazine_cover'] )
                {
                    $obj_category_cover_data = $obj_category_cover->where("category_id", $obj_news_or_image_url)->where("issue_date <= ", date("Y-m-d", strtotime($arIssueDate['s_issue_date'])))->order_by("issue_date DESC")->limit(1)->get();
                    if (count($obj_category_cover_data->all) == 0 )
                    {
                        $obj_news_or_image_url = NULL;
                    }
                    else
                    {
                        $obj_news_or_image_url = $obj_category_cover_data->image;
                    }
                }
                else
                {
                    $obj_news_or_image_url = NULL;
                }
            }
            else{
                $obj_news_or_image_url = $obj_category_cover_data->image;
            }
        }
        
        $image_url = (is_object($obj_news_or_image_url)) ? (!empty($obj_news_or_image_url->lead_material)) ? $obj_news_or_image_url->lead_material : $obj_news_or_image_url->image : $obj_news_or_image_url;
                
        if (stripos($image_url, "http://thedailystar.") !== FALSE )
        {
            $image_url = str_ireplace("http://thedailystar.", "http://www.thedailystar.", $image_url);
        }
        elseif (stripos($image_url, "http://bd.thedailystar.") !== FALSE )
        {
            $image_url = str_ireplace("http://bd.thedailystar.", "http://www.thedailystar.", $image_url);
        }
        elseif (stripos($image_url, "http://www.dailystarnews.dev") !== FALSE ){
            $image_url = $image_url;
        }
        else
        {
            if(substr($image_url, 0, 1) == '/'){
                $image_url = 'http://www.thedailystar.net' . $image_url;
            }
            if(!getimagesize($image_url))
            {
                $image_url = str_ireplace("http://www.thedailystar.net/", base_url(), $image_url);
            }
        }
        
        
        //$s_image_url = str_ireplace("http://www.thedailystar.net", "", $image_url);
        
        if(strlen($image_url) > 0){
            
            if($inside_div){
                $str_image .= '<div class="' . $s_class . '">';
            }
            
            if(($use_liquid === true) && (isset($dimension['width']) && $dimension['width'] > 0) && (isset($dimension['height']) && $dimension['height'] > 0)){
                $str_image .= '<div class="' . $s_class . '" style="width: '.$dimension['width'].'px; height: '.$dimension['height'].'px; margin-top: 15px; ">';
            }
            
            $str_image .= '<img ';
            
            if(!$inside_div && !empty($s_class)){
                $str_image .= 'class="' . $s_class . '" ';
            }
            
            if( isset($dimension['width']) && ($dimension['width'] == 330) && ($use_liquid === false) ){
                list($width, $height, $type, $attr) = getimagesize($image_url);
                
                if( ($height > $width) ){
                    $dimension['height'] = 230;
                    unset($dimension['width']);
                }   
            }
            
            if( isset($dimension['height']) && ($dimension['height'] == 55) && ($use_liquid === false) ){
                list($width, $height, $type, $attr) = getimagesize($image_url);
                
                if( ($height > $width) ){
                    $dimension['height'] = 100;
                }   
            }
            
            if( isset($dimension['height']) && ($dimension['height'] == 100) && ($use_liquid === false) ){
                list($width, $height, $type, $attr) = getimagesize($image_url);
                
                if( ($width >= ($height * 1.5)) ){
                    $dimension['width'] = 117;
                    unset($dimension['height']);
                }   
            }
            
            
            if(isset($dimension['width']) && $dimension['width'] > 0){
                $str_image .= 'width="'.$dimension['width'].'"';
            }
            
            if(isset($dimension['height']) && $dimension['height'] > 0){
                $str_image .= ' height="'.$dimension['height'].'"';
            }
            
            $str_image .= ' src="' . $image_url . '" alt="" />';
            
            if(($inside_div === true) || ($use_liquid === true)){
                $str_image .= '</div>';
            }
        }
        
        return (strlen($str_image) > 0) ? $str_image : '';
    }
}

if(!function_exists('print_magazine_news'))
{
    function print_magazine_news($s_ci_key, $obj_news, $btn_more = false, $cover = false, $limit = 16){
        $str_content = '';
        
        if(strlen($obj_news->content) > 0){
                        
            if(!empty($obj_news->summary)){
                $s_content = $obj_news->summary;
            }else{
                $ar_words = explode(' ', $obj_news->content);
                $i = 0;
                foreach($ar_words as $word){
                    $s_content .= $word . ' ';
                    $i++;
                    
                    if($i > $limit){
                        break;
                    }
                }
            }
            
            $str_content .= '<p>';
            $str_content .= trim($s_content);
            $str_content .= '</p>';
            
            if($btn_more){
                $str_content .= '<a href="'. create_link_url($s_ci_key, $obj_news->headline, $obj_news->id) .'">Read Now</a>';
            }   
        }
                
        return $str_content;        
    }
}

if(!function_exists('magazine_menu'))
{
    function magazine_menu($s_ci_key){
        ini_set('display_errors', 1);
        
        $obj_category = new Category_model();
        $obj_category->where(array('name' => unsanitize($s_ci_key), 'status' => 1));
        $obj_category->where('parent_id IS NULL');
        
        $obj_parent_category = $obj_category->get();
        
        $html_magazin_menu = $obj_category->get_magazine_menu($obj_parent_category->id);
        
        return $html_magazin_menu;
    }
}

if(!function_exists('visitor_country'))
{
    function visitor_country() 
    {
        if (function_exists("geoip_record_by_name") )
        {
            $ip = $_SERVER["REMOTE_ADDR"];
            if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];

            $info = geoip_record_by_name($ip);
            if($info)
            {
                $result = $info['country_name'];
                return $result <> NULL ? $result : "Bangladesh";
            }
            else
            {
             return "Bangladesh"; 
            }
        }
        else
        {
         return "Bangladesh"; 
        }
    }
}

if(!function_exists('get_id_by_country'))
{
    function get_id_by_country( $s_country ) 
    {
        $CI = & get_instance();
        $CI->db->like('name', $s_country);
        $CI->db->set_dbprefix('');

        $query = $CI->db->get($CI->db->dbprefix('countries'));
        return ( $query->num_rows() == 0 ) ? 14 : $query->row();
    }
}

if(!function_exists('check_categories_recursive'))
{
    function check_categories_recursive( $ar_categories ) 
    {
        $CI = & get_instance();
        $i_parent_id = 0;
        $i_category_id = 0;
        foreach( $ar_categories as $category_name )
        {
            $CI->db->where('name', ucwords(unsanitize($category_name)));
            if ( $i_parent_id == 0 )
            {
                $CI->db->where('(parent_id = 0 OR parent_id IS NULL)');
            }
            else
            {
                $CI->db->where("parent_id", $i_parent_id, FALSE);
            }
            $CI->db->where("status",1);
            $query = $CI->db->get('categories');
            if ( $query->num_rows() == 0 )
            {
                return false;
            }
            else
            {
                $o_category = $query->row();
                $i_parent_id = $o_category->id;
            }
        }
        return $i_parent_id;
    }
}

if(!function_exists('get_parent_category'))
{
    function get_parent_category( $i_category_id ) 
    {
        $CI = & get_instance();
        
        $CI->db->where('id',$i_category_id, FALSE);
        
        $query = $CI->db->get('categories');
        if ( $query->num_rows() == 0 )
        {
            return false;
        }
        else
        {
            $o_category = $query->row();
            if ( $o_category->parent_id == 0)
            {
                return $o_category;
            }
            else
            {
                $o_category = get_parent_category($o_category->parent_id);
                return $o_category;
            }
        }
    }
}

if(!function_exists('get_notification'))
{
    function get_notification(  ) 
    {
        $CI = & get_instance();
        if (! free_user_logged_in() )
        {
            return 0;
        }
        else
        {
            
            $i_user_id = get_free_user_session("id");
            $CI->db->where('user_id',$i_user_id, FALSE);
        
            $query = $CI->db->get('user_notification');
            if ( $query->num_rows() == 0 )
            {
                return 0;
            }
            else
            {
                return $query->num_rows();
            }
        }
        
        
    }
}

if(!function_exists('set_type_cookie'))
{
    function set_type_cookie($user_type) 
    {
        $cookie = array(
                'name'   => 'user_type',
                'value'  =>  $user_type,
                'expire' => '86500'
        );
        $CI = & get_instance();
        $CI->input->set_cookie($cookie);
        
    }
}

if(!function_exists('get_type_cookie'))
{
    function get_type_cookie() 
    {
        $CI = & get_instance();
        $user_type = $CI->input->cookie('user_type');
        if($user_type)
        {
           return $user_type; 
        }
        else
        {
           return 1;
        }    
        
        
    }
}

if(!function_exists('get_language_cookie'))
{
    function get_language_cookie() 
    {
        $CI = & get_instance();
        $lang = $CI->input->cookie('local');
        
        return ($lang) ? $lang : false;
    }
}

if(!function_exists('get_language'))
{
    function get_language( $lng = NULL) 
    {
        $ar_lang = array(
            'en' => 'ENG',
            'bn' => 'BAN',
        );
        
        return ( !empty($lng) ) ? $ar_lang[$lng] : $ar_lang;
    }
}

if(!function_exists('get_alphabets'))
{
    function get_alphabets( $index = '') 
    {
        $ar_alphas = array(
            '0' => 'a',
            '1' => 'b',
            '2' => 'c',
            '3' => 'd',
            '4' => 'e',
            '5' => 'f',
            '6' => 'g',
            '7' => 'h',
            '8' => 'i',
            '9' => 'j',
            '10' => 'k',
            '11' => 'l',
            '12' => 'm',
            '13' => 'n',
            '14' => 'o',
            '15' => 'p',
            '16' => 'q',
            '17' => 'r',
            '18' => 's',
            '19' => 't',
            '20' => 'u',
            '21' => 'v',
            '22' => 'w',
            '23' => 'x',
            '24' => 'y',
            '25' => 'z',
        );
        
        return ( $index !=='' ) ? $ar_alphas[$index] : $ar_alphas;
    }
}

if (!function_exists('set_session_cookie')) {

    function set_session_cookie($cookie_token) {
        return setcookie('c21_session', $cookie_token, time() + 2592000, '/', str_replace('www.', '', $_SERVER['SERVER_NAME']));
        $cookie = array(
            'name' => 'champs_session',
            'value' => $cookie_token,
            'expire' => 2592000,
            'domain' => str_replace('www.', '', $_SERVER['SERVER_NAME'])
        );
        
        setcookie('champs_session', $cookie_token, time() + 2592000, '/', str_replace('www.', '', $_SERVER['SERVER_NAME']));
    }

}

if (!function_exists('get_session_cookie')) {

    function get_session_cookie() {
        $cookie = $_COOKIE['champs_session'];
        if ($cookie) {
            return $cookie;
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_session_cookie_token')) {
    
    function get_session_cookie_token($obj_user, $key) {
        $str = $obj_user->id . $obj_user->user_type . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
        $str .= $key;
        return hash('sha512', $str);
    }
}

if (!function_exists('get_session_key')) {
    
    function get_session_key() {
        return md5(uniqid(rand(), true) . time() );
    }
}

if (!function_exists('get_school_page')) {
    
    function get_school_page($school_name) {
        $ch = curl_init("http://schoolpage.champs21.com/".$school_name."/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        
        return $content;
    }
}
if (!function_exists('check_token_forget_password')) {
    function check_token_forget_password($token) 
    {
        $CI = & get_instance();
        $CI->db->set_dbprefix('');
        $CI->db->where("reset_password_code",$token);
        $user_info = $CI->db->get("users")->row();
        $CI->db->set_dbprefix('tds_');
        if($user_info && date("Y-m-d H:i:s")<$user_info->reset_password_code_until)
        {
            return true;   
        }
        return false;
        
    }
}
if (!function_exists('check_user_forget_password')) {
    function check_user_forget_password($username,$email) 
    {
        $CI = & get_instance();
        $CI->db->set_dbprefix('');
        $CI->db->where("username",$username);
        $CI->db->where("email",$email);
        $user_info = $CI->db->get("users")->row();
        $CI->db->set_dbprefix('tds_');
        if($user_info)
        {
            return true;   
        }
        return false;
        
    }
}
if (!function_exists('student_limit_excessed')) {
    function student_limit_excessed($school_id) 
    {
        $CI = & get_instance();
        $CI->db->set_dbprefix('');
        $CI->db->where("school_id",$school_id);
        $subscription_info = $CI->db->get("subscription_info")->row();
        if($subscription_info)
        {
            if( $subscription_info->is_unlimited == 0 )
            {
                if( $subscription_info->current_count >= $subscription_info->no_of_student )
                {
                    return true;
                }
            }    
        }
        $CI->db->set_dbprefix('tds_');
        return false;
        
    }
}
if (!function_exists('update_subscription_current_count')) {
    function update_subscription_current_count($school_id) 
    {
        $CI = & get_instance();
        $CI->db->set_dbprefix('');
        $CI->db->set('current_count', 'current_count+1', FALSE);

        $CI->db->where("school_id",$school_id);
        $CI->db->update('subscription_info');
        
    }
}

// ------------------------------------------------------------------------

/* End of file MY_html_helper.php */
/* Location: /application/helpers/MY_html_helper.php */
