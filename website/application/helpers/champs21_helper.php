<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!function_exists('getBingTokens'))
{
function getBingTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl) {
        try {
            //Initialize the Curl Session.
            $ch = curl_init();
            //Create the request Array.
            $paramArr = array(
                'grant_type' => $grantType,
                'scope' => $scopeUrl,
                'client_id' => $clientID,
                'client_secret' => $clientSecret
            );

            //Create an Http Query.//
            $paramArr = http_build_query($paramArr);
            //Set the Curl URL.
            curl_setopt($ch, CURLOPT_URL, $authUrl);
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
            //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //Execute the  cURL session.
            $strResponse = curl_exec($ch);
            
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                throw new Exception($curlError);
            }
            //Close the Curl Session.
            curl_close($ch);
            //Decode the returned JSON string.
            $objResponse = json_decode($strResponse);
            if (isset( $objResponse->error )) {
                throw new Exception($objResponse->error_description);
            }
            return $objResponse->access_token;
        } catch (Exception $e) {
            echo "Exception-" . $e->getMessage();
        }
    }
}

    /*
     * Create and execute the HTTP CURL request.
     *
     * @param string $url        HTTP Url.
     * @param string $authHeader Authorization Header string.
     *
     * @return string.
     *
     */
if (!function_exists('curlRequest'))
{
    function curlRequest($url, $authHeader) {
        //Initialize the Curl Session.
        $ch = curl_init();
        //Set the Curl url.
        curl_setopt($ch, CURLOPT_URL, $url);
        //Set the HTTP HEADER Fields.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader));
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, False);
        //Execute the  cURL session.
        $curlResponse = curl_exec($ch);
        $curl_status = curl_getinfo($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close a cURL session.
        curl_close($ch);
        if($curl_status['http_code']==200)
        {
            return $curlResponse;
        }
        else
        {
            return false;
        } 
        
    }
}
if (!function_exists('get_post_link_url'))
{

    function get_post_link_url($news)
    {
        $link_array = array();
        if($news->post_type == 2)
        {
            if($news->lead_link!=null && $news->lead_link!="")
            {
                 $link_array['url'] = $news->lead_link;
                 $link_array['target'] = "_blank";
            }
            else
            {
                $link_array['url'] = "javascript:void(0)";
                $link_array['target'] = "_shelf";
            }    
           
        } 
        else
        {
            if($news->lead_link!=null && $news->lead_link!="")
            {
                 $link_array['url'] = $news->lead_link;
                 $link_array['target'] = "_blank";
            }
            else
            {
                $link_array['url'] = base_url() . sanitize($news->headline) . "-" . $news->post_id;
                $link_array['target'] = "_shelf";
            } 
        }
        return $link_array;
    }

}

if (!function_exists('get_post_image_type_content'))
{

    function get_post_image_type_content($news,$arCustomNews,$style="",$replace=false,$extra_class="",$show_slider = false)
    {
        $content = "";
        $extra_class.= ( $news->post_type == 2 ) ? ' no_toolbar' : '';
        if ( strlen(trim($news->embedded)) > 0 )
        {
            $content = $news->embedded;
        }
        else if(!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0)
        {
             $content = '<img src="'.base_url($news->lead_material).'" class="attachment-post-thumbnail wp-post-image '.$extra_class.'" alt="'.$news->headline.'" style="'.$style.'">';
        }    
         
        else if ( strlen(trim($arCustomNews['image'])) > 0 && (count($arCustomNews['all_image']) == 1 || $show_slider==false) ) 
        {
            $content = '<img src="'.$arCustomNews['image'].'" class="attachment-post-thumbnail wp-post-image '.$extra_class.'" alt="'.$news->headline.'" style="'.$style.'">';
        }
        else if(count($arCustomNews['all_image'])> 1)
        {
            $content = '<div class="flex-wrapper">
                            <div id="slider" class="flexslider" style="border: 1px solid #Fff;">
                                <ul class="slides">';
            
             foreach( $arCustomNews['all_image'] as $image )
             {
               $content.= '<li> <img class="'.$extra_class.'" src="'.$image.'" alt="'.$news->headline.'"  style="'.$style.'" /></li>';  
             }
             
             $content.= '</ul> </div> </div>';
        }    
        return $content;
    }

}
if (!function_exists('getFileType'))
{

    function getFileType($file)
    {
//        Deprecated, but still works if defined...<br>
        if (function_exists("mime_content_type"))
            return mime_content_type($file);
//        New way to get file type, but not supported by all yet.<br>
        else if (function_exists("finfo_open"))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $type;
        }
//        Otherwise...just use the file extension<br>
        else
        {
            $types = array(
                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',

                // images
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',

                // archives
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',

                // audio/video
                'mp3' => 'audio/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',

                // adobe
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',

                // ms office
                'doc' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',
                'docx' => 'application/msword',
                'xlsx' => 'application/vnd.ms-excel',
                'pptx' => 'application/vnd.ms-powerpoint',


                // open office
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            );
            $ext = substr($file, strrpos($file, '.') + 1);
            if (key_exists($ext, $types))
                return $types[$ext];
            return "unknown";
        }
    }

}

if (!function_exists('getclassactionbox'))
{

    function acceptableType($type)
    {
        $array = array("image/jpeg", "image/jpg", "image/png", "image/png", "image/gif");
        if (in_array($type, $array))
            return true;
        return false;
    }

}

if (!function_exists('getclassactionbox'))
{

    function getclassactionbox($count)
    {
        $value = array();
        $new_count = 0;
        $count_string = "";
        if($count<1000)
        {
            $new_count = $count;
            $checkcount = $count;
        }    
        else if($count>=1000 && $count<1000000)
        {
           $new_count = round($count/1000);
           $count_string = "k";
           $checkcount = (int)$new_count."0";
           
        } 
        else if($count>=100000)
        {
           $new_count = round($count/100000);
           $count_string = "M";
           $checkcount = (int)$new_count."0";
        }  
        
        if($checkcount<100)
        {
           $value['width'] = 100;
           $value['class1'] = 4;
           $value['class2'] = 8;
           $value['new_count'] = $new_count.$count_string;
        }
        else if($checkcount>=100 && $checkcount<1000)
        {
           $value['width'] = 100;
           $value['class1'] = 4;
           $value['class2'] = 8;
           $value['new_count'] = $new_count.$count_string;
        }
        else if($checkcount>=1000)
        {
           $value['width'] = 120;
           $value['class1'] = 4;
           $value['class2'] = 8;
           $value['new_count'] = $new_count.$count_string;
        }
        
        return $value;
        
    }

}

if (!function_exists('substr_with_unicode'))
{

    function substr_with_unicode($string, $full_length = false, $length = 80)
    {

        if ($full_length === false)
        {
            $s_target = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
            $main_string = mb_substr($s_target, 0, mb_strpos($s_target, ' ', $length,'UTF-8'), 'UTF-8');
            return trim($main_string);
        }
        else
        {
            $main_string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
            $main_string = mb_substr($main_string, 0, mb_strlen($main_string, 'UTF-8'), 'UTF-8');
            return trim($main_string);
        }
    }

}
if (!function_exists('can_sharepost'))
{
    function can_sharepost($post_id)
    {
        if (free_user_logged_in() || wow_login()==false)
        {
           
            $user_id = get_free_user_session("id");
            
            if($post_id && $user_id )
            {
                $url = get_curl_url("can_share_from_web");
                $fields = array(
                    'user_id' => $user_id,
                    'id' => $post_id
                );
                
                $fields_string = "";

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

                return $result;
               
              
                
            }
           
        }
        return 0;
          
    }
}    

if (!function_exists("getFormatedContentAll"))
{

    function getFormatedContentAll($news, $length = 800, $s_ci_key = -1)
    {
        $content = $news->content;
        $summary = $news->summary;
        $headline = $news->headline;
        $id = $news->id;
        $lead_material = $news->lead_material;
        
        $layout = "layout1";
        if ( $news->is_exclusive && date("Y-m-d H:i:s") < $news->exclusive_expired )
        {
            $layout = "layout3";
        }
        else if($news->is_breaking && date("Y-m-d H:i:s") < $news->breaking_expire )
        {
            $layout = "layout2";
        }    
        
        
        $arData = array();
        $objMainNewsContent = $content;
        $content = preg_replace('/<div (.*?)>Source:(.*?)<\/div>/', '', $content);
        $content = preg_replace('/<div class="img_caption" (.*?)>(.*?)<\/div>/', '', $content);
        
        //$content = str_replace("\n", '', trim($content));
        $content = str_replace("&nbsp;", '', $content);
        $content = str_replace("<p></p>", '', $content);
        
        $strContent = strip_tags($content);
        
        
        if( strlen(trim($summary)) > 0 ) {
            $shortContent = $summary;
        }
        else{
            if (strlen($strContent) >= $length)
            {
                $shortContent = substr_with_unicode($strContent, false, $length) . " ... ";
            }
            else
            {
                $shortContent = $strContent;
            }
        }
        
        error_reporting(0);
        $doc = new DOMDocument();
        $doc->loadHTML($objMainNewsContent);

        $arData['headline'] = $headline;
        if ($s_ci_key !== -1)
        {
            $arData['ci_key'] = create_link_url($s_ci_key, $headline, $id, false);
        }
        
        $arData['lead_material'] = getimage_link($lead_material,$layout);
       
        
        //$arData['lead_material'] = $lead_material;
        $arData['content'] = $shortContent;

        $xpath = new DOMXPath($doc);
        $s_caption = $xpath->evaluate("string(//div[contains(@class, 'img_caption')])");
        $arData['caption_inside'] = $s_caption;

        $images = $doc->getElementsByTagName('img');        
        
        $arData['image'] = "";
        $arData['all_image'] = array();
        $arData['all_image_url'] = array();
        $arData['all_image_title'] = array();
        $i = 0;
        foreach ($images as $image)
        {
            
            if (strpos($image->getAttribute('src'), "relatednews.jpg") !== FALSE)
            {
                continue;
            }
            else if (strpos($image->getAttribute('class'), "no_slider") !== FALSE)
            {
                continue;
            }
            else if ($i == 0)
            {

                $i++;
                $arData['image'] = getimage_link($image->getAttribute('src'),$layout);
                $arData['all_image'][] =  getimage_link($image->getAttribute('src'),$layout);
                $arData['all_image_url'][] = $image->getAttribute('longdesc');
                $arData['all_image_title'][] = $image->getAttribute('title');
            }
            else
            {
                $arData['all_image'][] =  getimage_link($image->getAttribute('src'),$layout);
                $arData['all_image_url'][] = $image->getAttribute('longdesc');
                $arData['all_image_title'][] = $image->getAttribute('title');
            }
        }
//        print_r($arData);
        return $arData;
    }

}
if ( !function_exists("getimage_link") )
{
    function getimage_link($image_link,$layout)
    {
        
        $url = base_url();
        $url_base = "http://www.champs21.com/";
        $main_url = "champs21.com/";
        $url_array = array("http://www.champs21.com/","http://champs21.com/","http://stage.champs21.com");
        if( $image_link )
        {
            if( strpos($image_link, $main_url) !== false)
            {
                foreach($url_array as $value)
                {
                    $image_link = str_replace($value, FCPATH, $image_link);
                }    
                 
            }
            else
            {
                if( strpos($image_link, $url) !== false)
                {
                    $image_link = str_replace($url, FCPATH, $image_link);
                }
                else
                {
                    $image_link = FCPATH . $image_link;
                }
                
            }
            
            $s_thumb_url = str_replace("gallery/", "gallery/".$layout."/", $image_link);
            
            if(! file_exists($image_link) )
            {
                
                $image_link = str_replace(FCPATH, $url_base, $image_link);
            }
            else
            {
               
                if(file_exists($s_thumb_url) )
                {
                    
                   $image_link =  $s_thumb_url;
                }

                $image_link = str_replace(FCPATH, $url, $image_link);
            }
            
        }
        
        return  $image_link; 
        
    }
}    

if ( ! function_exists("get_post_time"))
{
    function get_post_time( $published_date )
    {
        $datediff = get_diff_date($published_date);
        $datestring = "";
        $findvalue = false;
        if ($datediff['Years'] > 0 )
        {
            if($datediff['Years']>1)
            {
                $datestring.= $datediff['Years'] . " Years";
            }
            else
            {
                $datestring.= $datediff['Years'] . " Year";
            }
            $findvalue = true;
        }
        if ($datediff['Months'] > 0 && $findvalue===false)
        {
            if ($findvalue)
            {
                $datestring.= ", ";
            }
            if($datediff['Months']>1)
            {
                $datestring.= $datediff['Months'] . " Months";
            }
            else
            {
                $datestring.= $datediff['Months'] . " Month";
            }
           
            $findvalue = true;
        }
        if ($datediff['Days'] > 0 && $findvalue===false)
        {
            if ($findvalue)
            {
                $datestring.= ", ";
            }
            if($datediff['Days']>1)
            {
                $datestring.= $datediff['Days'] . " Days";
            }
            else
            {
                $datestring.= $datediff['Days'] . " Day";
            }
            
            $findvalue = true;
        }
        if ($datediff['Hours'] > 0 && $findvalue===false)
        {
            if ($findvalue)
            {
                $datestring.= ", ";
            }
            if($datediff['Hours']>1)
            {
                $datestring.= $datediff['Hours'] . " Hours";
            }
            else
            {
                $datestring.= $datediff['Hours'] . " Hour";
            }
            
            $findvalue = true;
        }
        if ($datediff['Minutes'] > 0 && $findvalue===false)
        {
            if ($findvalue)
            {
                $datestring.= ", ";
            }
            if($datediff['Minutes']>1)
            {
                $datestring.= $datediff['Minutes'] . " Minutes";
            }
            else
            {
                $datestring.= $datediff['Minutes'] . " Minute";
            }
           
            $findvalue = true;
        }
        if ($datediff['Seconds'] > 0 && $findvalue===false)
        {
            if ($findvalue)
            {
                $datestring.= ", ";
            }
            if($datediff['Seconds']>1)
            {
                $datestring.= $datediff['Seconds'] . " Seconds";
            }
            else
            {
                $datestring.= $datediff['Seconds'] . " Second";
            }
           
            $findvalue = true;
        }
        
        return $datestring;
    }
}

if ( ! function_exists("show_summary"))
{
    function show_summary($conetnt, $news=null, $return = false)
    {
        $str = "";
        if($news)
        {
           $str = '<a class="summary_link" href="'.base_url() . sanitize($news->headline) . "-" . $news->post_id.'" title="'.$news->headline.'">'; 
        }
        $str .= $conetnt;
        if($news)
        {
           $str .= "</a>"; 
        }
        if($return)
        {
            return $str;
        }
        else
        {
            echo $str;
        }    
        
    }
}

if ( ! function_exists("get_diff_date"))
{
    function get_diff_date($end, $out_in_array = true)
    {
        $intervalo = date_diff(date_create($end), date_create());
        $out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
        if (!$out_in_array)
            return $out;
        $a_out = array();
        $outs = explode(',', $out);
        foreach ($outs as $val) {
            $v = explode(':', $val);
            $a_out[$v[0]] = $v[1];
        }
        return $a_out;
    }
}
if(!function_exists("get_parent_children"))
{
    function get_parent_children($str,$user_data)
    {
     
        $CI = &get_instance();
        $CI->db->dbprefix = '';
        $CI->db->select('*');
        $CI->db->from('students');
        $CI->db->where('admission_no', trim($str));
        $CI->db->where('school_id',$user_data->paid_school_id); 
        $std = $CI->db->get()->row();
        $CI->db->dbprefix = 'tds_';
        if($std)
        {
            return $std;
        }
        else
        {
            return FALSE;
        }
        
    }
}

if(!function_exists("get_paid_employee_position_droupdown"))
{
    function get_paid_employee_position_droupdown($school_id=0,$category_id=0,$selected='')
    {
        $s_array = array(NULL=>"select position");
        if($category_id)
        {
            $CI = &get_instance();
            $CI->db->dbprefix = '';
            $CI->db->select('id,name');
            $CI->db->from('employee_positions');
            if($school_id>0)
            {
                $CI->db->where('school_id',$school_id);
            }
            $CI->db->where('employee_category_id',$category_id);
            $CI->db->where('status',1);
            $position = $CI->db->get()->result();
            $CI->db->dbprefix = 'tds_';
            
            foreach($position as $value)
            {

                $s_array[$value->id] = $value->name;

            }
        } 
        $class='class="cd-input f5 form-control" style="height:30"  required="" ';
        $droup_down = form_dropdown('employee_position_id', $s_array, $selected,$class);
        return $droup_down;
    }
}

if(!function_exists("get_paid_employee_category_droupdown"))
{
    function get_paid_employee_category_droupdown($school_id,$selected='')
    {
        $CI = &get_instance();
        $CI->db->dbprefix = '';
        $CI->db->select('id,name');
        $CI->db->from('employee_categories');
        $CI->db->where('school_id',$school_id);
        $CI->db->where('status',1);
        $category = $CI->db->get()->result();
        $CI->db->dbprefix = 'tds_';
        $s_array = array(NULL=>"select category");
        foreach($category as $value)
        {
            
            $s_array[$value->id] = $value->name;
            
        }
        $class='class="cd-input f5 form-control" id="change_position" style="height:30"  required="" ';
        $droup_down = form_dropdown('employee_category', $s_array,$selected,$class);
        return $droup_down;
    }
}

if(!function_exists("get_paid_employee_grade_droupdown"))
{
    function get_paid_employee_grade_droupdown($school_id,$selected='')
    {
        $CI = &get_instance();
        $CI->db->dbprefix = '';
        $CI->db->select('id,name');
        $CI->db->from('employee_grades');
        $CI->db->where('school_id',$school_id);
        $CI->db->where('status',1);
        $grade = $CI->db->get()->result();
        $CI->db->dbprefix = 'tds_';
        $s_array = array(NULL=>"Select Grade");
        foreach($grade as $value)
        {
            
            $s_array[$value->id] = $value->name;
            
        }
        $class='class="cd-input f5 form-control" style="height:30" ';
        $droup_down = form_dropdown('employee_grade_id', $s_array,$selected,$class);
        return $droup_down;
    }
}

if(!function_exists("get_paid_employee_department_droupdown"))
{
    function get_paid_employee_department_droupdown($school_id,$selected='')
    {
        $CI = &get_instance();
        $CI->db->dbprefix = '';
        $CI->db->select('id,name');
        $CI->db->from('employee_departments');
        $CI->db->where('status',1);
        $CI->db->where('school_id',$school_id);
        $department = $CI->db->get()->result();
        $CI->db->dbprefix = 'tds_';
        $s_array = array(NULL=>"select department");
        foreach($department as $value)
        {
            
            $s_array[$value->id] = $value->name;
            
        }
        $class='class="cd-input f5 form-control" style="height:30"  required="" ';
        $droup_down = form_dropdown('employee_department_id', $s_array, $selected,$class);
        return $droup_down;
    }
}
if(!function_exists("check_school_code_paid"))
{
    function check_school_code_paid($activation_code)
    {
        $CI = &get_instance();
        $CI->db->dbprefix = '';
        $CI->db->select('id,name');
        $CI->db->from('schools');        
        $CI->db->where('activation_code',$activation_code);
        $schools = $CI->db->get()->row();
        $CI->db->dbprefix = 'tds_';
        if($schools)
        {
            return $schools;
        }    
    
        return false;
    }
}
if(!function_exists("get_paid_school_droupdown"))
{
    function get_paid_school_droupdown()
    {
        $CI = &get_instance();
       
        $CI->db->select('paid_school_id,name,code');
        $CI->db->from('school');
        $CI->db->where('is_paid',1);
        $CI->db->where('status',1);
        $schools = $CI->db->get()->result();
        $s_array = array(NULL=>"select school");
        foreach($schools as $value)
        {
            if($value->paid_school_id)
            {
                $s_array[$value->paid_school_id] = $value->name;
            }
        }
        $class='id="paid_school_dropdown" class="form-control selectpicker" required=""';
        $droup_down = form_dropdown('paid_school_id', $s_array, '',$class);
        return $droup_down;
    }
}
if(!function_exists("make_paid_username"))
{
    function make_paid_username($user_data,$admission_no,$parent=false,$from_std=false)
    {
        $extra = "";
        if($parent || $from_std)
        {
            $extra = "p1";
            $CI = &get_instance();
            $CI->db->dbprefix = '';
            $CI->db->select('count(id) as tp');
            $CI->db->from('users');
            $CI->db->like('username', trim($admission_no), 'before');
            $CI->db->where('school_id',$user_data->paid_school_id);
            $CI->db->where('parent',1);
            $std = $CI->db->get()->row();
            if($std && $std->tp)
            {
                $p_number = $std->tp+1;
                $extra = "p".$p_number;
            }
            $CI->db->dbprefix = 'tds_';
        }
        $use_id = true;
        $admission_no = $extra.$admission_no;
        
        if ($use_id) {
            if ($user_data->paid_school_id < 10) {
                $idchange = "0" . $user_data->paid_school_id;
            } else {
                $idchange = $user_data->paid_school_id;
            }
            $username = $idchange . "-" . $admission_no;
        } else {
            $CI = &get_instance();
            $CI->db->dbprefix = '';
            $CI->db->select('code');
            $CI->db->from('schools');
            $CI->db->where('id', $user_data->paid_school_id);
            $school = $CI->db->get()->row();
            $CI->db->dbprefix = 'tds_';
            $username = $school->code . "-" . $admission_no;
        }
        return $username;
        
    }
}
if(!function_exists("get_paid_school_class"))
{
    function get_paid_school_class($school_id,$selected='',$default_value=false)
    {
        $CI = &get_instance();
        $CI->db->dbprefix = '';
        $CI->db->select('batches.id as bid,batches.name as bname,courses.course_name,courses.section_name');
        $CI->db->from('batches');
        $CI->db->join('courses', 'courses.id = batches.course_id');
        $CI->db->where('batches.is_deleted',0);
        $CI->db->where('courses.is_deleted',0);
        $CI->db->where('batches.is_active',1);
        $CI->db->where('batches.school_id',$school_id);
        $CI->db->order_by("bname ASC,courses.course_name ASC,courses.section_name ASC");
        $course_batch = $CI->db->get()->result();
        $CI->db->dbprefix = 'tds_';
        $s_array = array();
        if($default_value)
        {
            $s_array = array(NULL => "Select Class");
        }
        foreach($course_batch as $value)
        {
            
          $s_array[$value->bid] = $value->bname." ".$value->course_name." ".$value->section_name;
          
        }
        $class='class="cd-input f5 form-control" style="height:30" id="batch_id" ';
        $droup_down = form_dropdown('batch_id', $s_array, $selected,$class);
        return $droup_down;
    }
}
if( !function_exists("sendMessageFcm"))
{
    function sendMessageFcm($data,$target){
    //FCM api URL
    $url = 'https://fcm.googleapis.com/fcm/send';
    //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
    $server_key = 'AAAAhnf27-U:APA91bGrMonoWs8V1O_Zc993WfZP_ED60vjIe8vdPqD2P1-e5ZHG7motB_IPiI7VEs8NXXs-hhimMWANdkPhUYcyd-CotnsygVa5aT3pUDBoDnHwoeUAuOOQBg2cVfRnAVEU6qFdCdO5PHoWlztWJbTudl3Of1mWlw';

    $fields = array();
    $fields['data'] = $data;
    if(is_array($target)){
            $fields['registration_ids'] = $target;
    }else{
            $fields['to'] = $target;
    }
    //header with content_type api key
    $headers = array(
            'Content-Type:application/json',
      'Authorization:key='.$server_key
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
    }
}

if( !function_exists("send_notification_paid_eddozz"))
{
    function send_notification_paid_eddozz($notification_id,$user_id)
    {
        $CI = &get_instance();
        
        $dbeddozz = $CI->load->database('eddozz', TRUE);
        //getting notification details
        
        $dbeddozz->select('*');
        $dbeddozz_>from('reminders');
        $dbeddozz->where('id',$notification_id);
        $notification = $dbeddozz->get()->row();
        
        //getting notification details
        $total_unread = 0;
       
        $dbeddozz->select('count(id) as total_unread');
        $dbeddozz->from('reminders');
        $dbeddozz->where('recipient',$user_id);
        $dbeddozz->where('is_deleted_by_sender',0);
        $dbeddozz->where('is_deleted_by_recipient',0);
        $dbeddozz->where('is_read',0);
        $count_notification = $dbeddozz->get()->row();
        
        if($count_notification)
        {
            $total_unread = $count_notification->total_unread;
        }
        
        //getting user information
        
        $dbeddozz->select('*');
        $dbeddozz->from('users');
        $dbeddozz->where('id',$user_id);
        $user = $dbeddozz->get()->row();
        
        $user_type = 0;
        
        if($user->admin)
        {
           $user_type = 1; 
        } 
        if($user->student)
        {
           $user_type = 3; 
        } 
        if($user->employee)
        {
           $user_type = 2; 
        } 
        if($user->parent)
        {
           $user_type = 4; 
        } 
        
        if(!$total_unread)
        {
            $total_unread = 0;
        }    
        //getting all user gcm_ids   
        $dbeddozz->select('gcm_ids.gcm_id as gcmid');
        $dbeddozz->where('user_gcm.user_id',$user_id);
        $dbeddozz->from('user_gcm');
        $dbeddozz->join('gcm_ids', 'gcm_ids.id = user_gcm.gcm_id');
        $all_gcm_user = $dbeddozz->get()->result();
        
        if($user_type && $total_unread && count($all_gcm_user)>0 && count($notification)>0)
        {
            $data = array("key" => "paid",'total_unread'=>$total_unread,"user_type"=>$user_type,"subject"=>$notification->subject,'messege'=>$notification->body, "rtype" => $notification->rtype, "rid" => $notification->rid, "semester_section_id" => $notification->semester_section_id, "student_id" => $notification->student_id);
            $gcm_ids = array();
            foreach($all_gcm_user as $value)
            {
               $gcm_ids[] = $value->gcmid;
            }
            sendMessageFcm($data, $gcm_ids);
            return true;
        }
        else
        {
            return array("return"=>"false");
        }    
           
    }
}

if( !function_exists("send_notification_paid"))
{
    function send_notification_paid($notification_id,$user_id)
    {
        $CI = &get_instance();
        
        
        //getting notification details
        $CI->db->dbprefix = '';
        $CI->db->select('*');
        $CI->db->from('reminders');
        $CI->db->where('id',$notification_id);
        $notification = $CI->db->get()->row();
        
        //getting notification details
        $total_unread = 0;
        $CI->db->dbprefix = '';
        $CI->db->select('count(id) as total_unread');
        $CI->db->from('reminders');
        $CI->db->where('recipient',$user_id);
        $CI->db->where('is_deleted_by_sender',0);
        $CI->db->where('is_deleted_by_recipient',0);
        $CI->db->where('is_read',0);
        $count_notification = $CI->db->get()->row();
        
        if($count_notification)
        {
            $total_unread = $count_notification->total_unread;
        }
        
        //getting user information
        $CI->db->dbprefix = '';
        $CI->db->select('*');
        $CI->db->from('users');
        $CI->db->where('id',$user_id);
        $user = $CI->db->get()->row();
        
        $user_type = 0;
        
        if($user->admin)
        {
           $user_type = 1; 
        } 
        if($user->student)
        {
           $user_type = 3; 
        } 
        if($user->employee)
        {
           $user_type = 2; 
        } 
        if($user->parent)
        {
           $user_type = 4; 
        } 
        
        if(!$total_unread)
        {
            $total_unread = 0;
        }    
        
        
        //getting all user gcm_ids
        $CI->db->dbprefix = 'tds_';
        $CI->db->select('gcm_ids.gcm_id as gcmid');
        $CI->db->where('user_gcm.user_id',$user_id);
        $CI->db->from('user_gcm');
        $CI->db->join('gcm_ids', 'gcm_ids.id = user_gcm.gcm_id');
        $all_gcm_user = $CI->db->get()->result();
        
        if($user_type && $total_unread && count($all_gcm_user)>0 && count($notification)>0)
        {
            $data = array("key" => "paid",'total_unread'=>$total_unread,"user_type"=>$user_type,"subject"=>$notification->subject, "rtype" => $notification->rtype, "rid" => $notification->rid, "batch_id" => $notification->batch_id, "student_id" => $notification->student_id);
            $messege = $notification->body;
            $CI->load->library('gcm');
            $CI->gcm->clearRecepient();
            $CI->gcm->setMessage($messege);
            $CI->gcm->setData($data);
            foreach($all_gcm_user as $value)
            {
               $CI->gcm->addRecepient($value->gcmid);
            }
            $response = $CI->gcm->send();
            $response['ststaus'] = $CI->gcm->status;
            $response['msg'] = $CI->gcm->messagesStatuses;
            return $response;
        }
        else
        {
            return array("return"=>"false");
        }    
           
    }
} 
if( !function_exists("send_notification"))
{
    function send_notification($messege, $data = array())
    {
        $CI = &get_instance();
        $CI->load->library('gcm');
        $CI->gcm->setMessage($messege);
        $CI->gcm->setData($data);
        $url = get_curl_url("getallgcm");
        $fields_string = "request_llicence=fa@#25896321";  
        
        //start curl
        
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
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        
        //end curl
        
        $registrationids = json_decode($result);
        
        if(count($registrationids)>0)
        {
            $regi_id_chunk = array_chunk($registrationids, 1000);
            
            foreach($regi_id_chunk as $registrationdevice)
            {
                $CI->gcm->setRecepients($registrationdevice);
                $CI->gcm->send();
                
            }    
        
            
               
        }
        else
        {
            return false;
        }    
    }
}    
    

if ( !function_exists("get_api_data_from_yii") )
{
    function get_api_data_from_yii($a_exclude_id, $page_number, $link="", $category_id = 0,
            $popular = false, $page_size = 9, $game_type = false, $fetaured = 0, $stbid = 0,
            $target = false, $b_get_related = false, $i_post_id = 0, $lang = '')
    {
       
        $url = get_curl_url($link);
           
        //print $url;
        $fields = array(
            'page_number' => $page_number,
            'page_size' => $page_size
        );
        if ( free_user_logged_in() )
        {
            $fields['user_id'] = get_free_user_session("id");
           
        }
        
        $fields['fetaured'] = $fetaured;
        
        if($category_id)
        {
            $fields['category_id'] = $category_id;
        }  
        if($popular)
        {
            $fields['popular_sort'] = 1;
        }  
        
        if($game_type)
        {
            $fields['game_type'] = $game_type;
        }
        
        if(!empty($lang))
        {
            $fields['lang'] = $lang;
        }
        
        if(count($a_exclude_id)>0)
        {
           $fields['already_showed'] = implode(",", $a_exclude_id); 
           
        } 
        if($target)
        {
            $fields['id'] = $stbid;
            $fields['target'] = $target;
        }
        
        if($b_get_related && $stbid > 0)
        {
            $fields['id'] = $stbid;
        }
        
        if($link=="")
        {
            $type_cookie = get_type_cookie();
            $fields['user_type'] = $type_cookie;
        } 
        
        $fields['website_only'] = 1;
        
        $fields_string = http_build_query($fields);

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
        
        $a_data = format_data(json_decode($result));
//        echo '<pre>';
//        var_dump($a_data);
//        exit;
        return $a_data;
    }
}

if ( !function_exists("format_data") )
{
    function format_data( $a_raw_data )
    {
        $a_data = array();
        $a_data['total'] = $a_raw_data->data->total;
        $a_data['data'] = array();
        $a_data['selected_data'] = array();
        $selected_post_id = array();
        $CI = & get_instance();
                
        $CI->load->config("huffas");
        
        foreach( $a_raw_data->data->selected_post as $post )
        {
            if($CI->config->config['education_changes_life']===FALSE && isset($post->education_changes_life) && $post->education_changes_life==1)
            {
                continue;
            }  
            
            $post->title = $post->author;
            unset($post->author);
            $post->id = $post->category_id;
            unset($post->category_id);
            $post->name = $post->category_name;
            unset($post->category_name);
            $post->menu_icon = $post->category_menu_icon;
            unset($post->category_menu_icon);
            $post->icon = $post->category_icon;
            unset($post->category_icon);
            if ( $post->has_summary == 0 )
            {
                $post->summary = "";
            }
            unset($post->has_summary);
            $a_data['selected_data'][] = $post;
            $selected_post_id[] = $post->post_id;
        }
       
       
        $selected_post_deleted = 0;
        foreach( $a_raw_data->data->post as $post )
        {
            if(in_array($post->id, $selected_post_id))
            {
                $selected_post_deleted++;
                continue;
            }        
            if($CI->config->config['education_changes_life']===FALSE && isset($post->education_changes_life) && $post->education_changes_life==1)
            {
                continue;
            }  
            
            $post->title = $post->author;
            unset($post->author);
            $post->id = $post->category_id;
            unset($post->category_id);
            $post->name = $post->category_name;
            unset($post->category_name);
            $post->menu_icon = $post->category_menu_icon;
            unset($post->category_menu_icon);
            $post->icon = $post->category_icon;
            unset($post->category_icon);
            if ( $post->has_summary == 0 )
            {
                $post->summary = "";
            }
            unset($post->has_summary);
            $a_data['data'][] = $post;
        }
        $a_data['total'] = $a_data['total']-$selected_post_deleted;
        return $a_data;
    }
}

if ( !function_exists("send_mail") )
{
    function send_mail( $ar_email )
    {
        $CI = &get_instance();
        
        $CI->load->config('champs21');
        
        $headers   = array();
        $headers[] = "MIME-Version: 1.0";
        
        $headers   = array();
        $headers[] = "MIME-Version: 1.0";
        
        if(!$ar_email['html']){
            $headers[] = "Content-type: text/plain; charset=utf-8";
        }else{
            $headers[] = "Content-type: text/html; charset=utf-8";
        }
        
        $headers[] = "To: " . $ar_email['to_name'] . " <" . $ar_email['to_email'] . ">";
        $headers[] = "From: " . $ar_email['sender_full_name'] . " <" . $ar_email['sender_email'] . ">";
        
        if( isset($ar_email['cc_email']) && !empty($ar_email['cc_email']) ) {
            $cc_name = ( isset($ar_email['cc_name']) && !empty($ar_email['cc_name']) ) ? $ar_email['cc_name'] : $ar_email['cc_email'];
            $headers[] = "Cc: " . $cc_name . " <" . $ar_email['cc_email'] . ">";
        }
        
        if( isset($ar_email['bcc_email']) && !empty($ar_email['bcc_email']) ) {
            $bcc_name = ( isset($ar_email['bcc_name']) && !empty($ar_email['bcc_name']) ) ? $ar_email['bcc_name'] : $ar_email['bcc_email'];
            $headers[] = "Bcc: " . $bcc_name . " <" . $ar_email['bcc_email'] . ">";
        }
        
        $headers[] = "Reply-To: ".$ar_email['sender_full_name']." <".$ar_email['sender_email'].">";
        $headers[] = "Subject: {$ar_email['subject']}";
        $headers[] = "X-Mailer: PHP/".phpversion();
        
        if($CI->config->config['mail_mode']['test']){
            return save_mail($ar_email, $headers);
        }else{
            return mail($ar_email['to_email'], $ar_email['subject'], $ar_email['message'], implode("\r\n", $headers));
        }
    }
}

if(!function_exists('save_mail')){
    function save_mail($ar_email, $headers)
    {
        $filename = date('YmdHis') . '_' . uniqid() . '.eml';
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'email';
        
        // Create a directory
        if (!is_dir($dir)) {
            $oldmask = @umask(0);
            $result = @mkdir($dir, 0777);
            @umask($oldmask);
            if (!$result) {
                throw new Exception('Unable to create the directory ' . $dir);
            }
        }
        
        try {
            $file = fopen($dir . DIRECTORY_SEPARATOR . $filename, 'w+');
            fwrite($file, implode("\r\n", $headers) . "\r\n" . $ar_email['message']);
            
            fclose($file);

            return true;
        } catch (Exception $e) {
            $e->getMessage();

            return false;
        }
    }
}
if(!function_exists('plus_api2')){
    
    function plus_api2(){
        
        $CI = &get_instance();
        
        $CI->load->library('plus_api');
        
        $ar_params = array(
            'username' => get_free_user_session('paid_username'),
            'password' => get_free_user_session('paid_password'),
            'school_code' => get_free_user_session('paid_school_code')
        );     
        
        $int_response = $CI->plus_api->init($ar_params, false);
        if($int_response != FALSE){
             //
             
            //$ar_params = array("username"=>"nbs-ST0001","password"=>"123456"); 
            //echo $res = $this->plus_api->login($ar_params, 'users/loginhook');
            
            return $res = $CI->plus_api->call__("get", 'reminders','get_data_reminder');
         }
         exit;
    }
}
if(!function_exists('plus_api3')){
    
    function plus_api3(){
        
        $CI = &get_instance();
        
        $CI->load->library('plus_api');
        
        $ar_params = array(
            'username' => get_free_user_session('paid_username'),
            'password' => get_free_user_session('paid_password'),
            'school_code' => get_free_user_session('paid_school_code')
        );     
        
        $int_response = $CI->plus_api->init($ar_params, false);
        if($int_response != FALSE){
             //
             
            //$ar_params = array("username"=>"nbs-ST0001","password"=>"123456"); 
            //echo $res = $this->plus_api->login($ar_params, 'users/loginhook');
            
            return $res = $CI->plus_api->call__("get", 'timetables','get_data_timetables');
            
             
         }
         exit;
    }
}

if(!function_exists('get_rand_images')){
    
    function get_rand_images( $ar_images, $i_first_image = "" ){
        
        $i_count_image = count($ar_images); 
        if ( strlen($i_first_image) == 0 )
        {
            $i_first_image = rand(0, $i_count_image - 1);
        }
        $i_second_image = rand(0, $i_count_image - 1);
        
        if ( $i_second_image == $i_first_image )
        {
            get_rand_images($ar_images, $i_first_image);
        }
        else
        {
            return array($i_first_image, $i_second_image);
        }
    }
}

if(!function_exists('get_icc_user_level_score')){
    
    function get_icc_user_level_score($assessment_id){
        
        $CI = &get_instance();
        
        //echo get_free_user_session('id');exit;
        if(get_free_user_session('id'))
        {
            $user_id = get_free_user_session('id');
            $CI->db->dbprefix = 'tds_';
            $CI->db->select('*');
            $CI->db->from('assesment_mark');
            $CI->db->where('user_id',$user_id);
            $CI->db->where('assessment_id', $assessment_id);
            $results = $CI->db->get()->result();

            if(count($results)>0)
            {   $datas = array()   ;      
                foreach($results as $data)
                {               
                    $datas[$data->level] = $data;
                }                     

                return $datas;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}


if( !function_exists("get_assessment"))
{
    function get_assessment($assesment_id, $user_id = 0, $webview = 1, $level = 0, $type = 0)
    {
        $CI = &get_instance();
        
        $url = get_curl_url("getassesment");
        $fields_string = "assesment_id=" . $assesment_id . "&webview=" . $webview . "&limit=5";
        
        if($user_id > 0) {
            $fields_string .= "&user_id=" . $user_id;
        }
        
        if($level > 0) {
            $fields_string .= "&level=" . $level;
        }
        
        if($type > 0) {
            $fields_string .= "&type=" . $type;
        }
        
        //start curl
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
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        //end curl
        
        $assesments = json_decode($result);
        
        if(count($assesments->data->assesment) > 0)
        {
            return $assesments->data;
        }
        
        return false;
    }
}
    
if( !function_exists("assessment_update_played"))
{
    function assessment_update_played($assesment_id)
    {
        $CI = &get_instance();
        
        $url = get_curl_url("updatePlayed");
        $fields_string = "assessment_id=" . $assesment_id;
        
        //start curl
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
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        //end curl
        
        $assesments = json_decode($result);
        
        if($assesments->status->code == 200){
            return TRUE;
        }
        
        return FALSE;
    }
}
    
if( !function_exists("get_assessment_leader_board"))
{
    function get_assessment_leader_board($assesment_id, $limit = 100, $type = 0)
    {
        $CI = &get_instance();
        
        $url = get_curl_url("assesmenttopscore");
        $fields_string = "id=" . $assesment_id . "&limit=" . $limit;
        
        if($type > 0) {
             $fields_string .= "&type=" . $type;
        }
        
        //start curl
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
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        //end curl
        
        $assesments = json_decode($result);
        
        if(count($assesments->data->assesment) > 0)
        {
            return $result;
        }
        
        return false;
    }
}

if( !function_exists("get_single_post_custom_banner"))
{
    function get_single_post_custom_banner($ar_params = array())
    {
        $CI = &get_instance();
        $CI->load->config("huffas");
        
        $post_configs = $CI->config->config['single_post_cover']['post'];
        $category_configs = $CI->config->config['single_post_cover']['category'];
        
        $banner = array();
        foreach ($post_configs as $k => $v) {
            if($ar_params['post_id'] == $k) {
                $banner = $v;
            }
        }
        
        if( empty($banner) && !is_array($ar_params['category_id']) ) {
            foreach ($category_configs as $k => $v) {
                if($ar_params['category_id'] == $k) {
                    $banner = $v;
                }
            }
        }
        
        return (!empty($banner)) ? $banner : FALSE;
    }
}

if( !function_exists("get_paid_domain"))
{
    function get_paid_domain()
    {
        $CI = &get_instance();
        
        $CI->db->dbprefix = '';
        $CI->db->from('schools');
        $CI->db->join('school_domains', 'school_domains.linkable_id = schools.id',"INNER");
        $CI->db->where('code', get_free_user_session('paid_school_code'));
        $CI->db->where('linkable_type', 'School');
        $CI->db->limit(1);
        $result = $CI->db->get()->row();
        $school_dn = $result->domain;
        
        return (!empty($school_dn)) ? $school_dn : FALSE;
    }
}