<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
        $extra_class.= ( $news->post_type == 2 ) ? ' ad' : '';
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
        if( $image_link )
        {
            if( strpos($image_link, $url_base) !== false)
            {
                
            }
            else
            {
                if( strpos($image_link, $url) !== false)
                {
                    $image_link = str_replace($url, FCPATH, $image_link);
                    //$image_link = base_url().$image_link;
                }
                else
                {
                    $image_link = FCPATH . $image_link;
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

if ( !function_exists("get_api_data_from_yii") )
{
    function get_api_data_from_yii($a_exclude_id,$page_number,$link="",$category_id = 0,$popular = false,$page_size = 9,$game_type=false,$fetaured=0)
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
        
        if(count($a_exclude_id)>0)
        {
           $fields['already_showed'] = implode(",", $a_exclude_id); 
           
        }    
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
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        
        $a_data = format_data(json_decode($result));
        
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
        foreach( $a_raw_data->data->post as $post )
        {
            $post->title = $post->author_title;
            unset($post->author_title);
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
        return $a_data;
    }
}    
