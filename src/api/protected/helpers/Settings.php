<?php

class Settings {

    public static $domain_name = 'http://www.champs21.com/';
    public static $image_path = 'http://www.champs21.com/';
    public static $url_array  = array("http://www.champs21.com/","http://champs21.com/","http://stage.champs21.com");
    public static $real_path = '/home/champs21/public_html/website/';
    public static $main_path = "../website/";
    public static $inner_post_to_show = 15;
    public static $api_llicence_key = "fa@#25896321";
    public static $count_update_by = 3;
    public static $school_join_approved = array(
        1 => false,
        2 => false,
        3 => false,
        4 => false
    );
    public static $post_type = 4;
    public static $allclass = 10;
    public static $school_candle_publish = array(
        1 => false,
        2 => false,
        3 => true,
        4 => false
    );
    public static $news_in_index = array(
        'show_old_news' => TRUE,
        'days_to_retrieve_news' => "-10 days"
    );
    public static $reset_password = array(
        'token_salt' => TRUE,
        'expire_time_limit' => 1800
    );
    public static $ar_weekdays = array(
        '0' => 'sunday',
        '1' => 'monday',
        '2' => 'tuesday',
        '3' => 'wednesday',
        '4' => 'thursday',
        '5' => 'friday',
        '6' => 'saturday',
    );
    public static $ar_default_folder = array(
        '0' => 'unread',
        '1' => 'articles',
        '2' => 'recipes',
        '3' => 'resources'
    );
    public static $ar_weekdays_key = array(
        'sunday' => '0',
        'monday' => '1',
        'tuesday' => '2',
        'wednesday' => '3',
        'thursday' => '4',
        'friday' => '5',
        'saturday' => '6',
    );
    public static $ar_notice_type = array(
        '1' => 'general',
        '2' => 'circular',
        '3' => 'announcement',
        '4' => 'event',
    );
    public static $ar_notice_acknowledge_status = array(
        '0' => 'Not Acknowledged',
        '1' => 'Acknowledged',
    );
    public static $ar_event_status = array(
        '0' => 'Not Going',
        '1' => 'Join In',
    );
    public static $ar_club_status = array(
        '0' => 'Applied To Join',
        '1' => 'Successfully Joined',
    );
    public static $ar_notice_acknowledge_by = array(
        '0' => 'Students',
        '1' => 'Guardians',
    );
    public static $ar_exam_category = array(
        '1' => 'Class Test',
        '2' => 'Project',
        '3' => 'Term',
    );
    public static $ar_event_origins = array(
        '0' => array(
            'name' => 'Exam',
            'condition' => "t.origin_type = 'Exam' AND t.is_holiday != '1'",
            'operator' => "AND",
        ),
        '1' => array(
            'name' => 'Events',
            'condition' => "(t.origin_id IS NULL OR t.origin_id = '') AND (t.origin_type IS NULL OR t.origin_type = '') AND (t.is_holiday != '1')",
            'operator' => "AND",
        ),
        '2' => array(
            'name' => 'Holidays',
            'condition' => "t.is_holiday = '1' ",
            'operator' => "AND",
        ),
        '3' => array(
            'name' => 'Others',
            'condition' => "t.is_holiday != '1' AND t.is_exam != '1' ",
            'operator' => "AND",
        ),
    );

    public static function getCurrentDay($date = '') {

        $date = (!empty($date)) ? $date : \date('Y-m-d', \time());

        $day = strtolower(date('l', strtotime($date)));
        return $day;
    }

    public static function formatTime($time, $b_12_hour = TRUE) {

        return $time = ($b_12_hour) ? date('h:i a', strtotime($time)) : $time;
    }

    public static function get_diff_date($end, $out_in_array = true) {
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

    public static function get_post_time($published_date) {
        $datediff = self::get_diff_date($published_date);
        $datestring = "";
        $findvalue = false;
        if ($datediff['Years'] > 0) {
            if ($datediff['Years'] > 1) {
                $datestring.= $datediff['Years'] . " Years";
            } else {
                $datestring.= $datediff['Years'] . " Year";
            }
            $findvalue = true;
        }
        if ($datediff['Months'] > 0 && $findvalue === false) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Months'] > 1) {
                $datestring.= $datediff['Months'] . " Months";
            } else {
                $datestring.= $datediff['Months'] . " Month";
            }

            $findvalue = true;
        }
        if ($datediff['Days'] > 0 && $findvalue === false) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Days'] > 1) {
                $datestring.= $datediff['Days'] . " Days";
            } else {
                $datestring.= $datediff['Days'] . " Day";
            }

            $findvalue = true;
        }
        if ($datediff['Hours'] > 0 && $findvalue === false) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Hours'] > 1) {
                $datestring.= $datediff['Hours'] . " Hours";
            } else {
                $datestring.= $datediff['Hours'] . " Hour";
            }

            $findvalue = true;
        }
        if ($datediff['Minutes'] > 0 && $findvalue === false) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Minutes'] > 1) {
                $datestring.= $datediff['Minutes'] . " Minutes";
            } else {
                $datestring.= $datediff['Minutes'] . " Minute";
            }

            $findvalue = true;
        }
        if ($datediff['Seconds'] > 0 && $findvalue === false) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Seconds'] > 1) {
                $datestring.= $datediff['Seconds'] . " Seconds";
            } else {
                $datestring.= $datediff['Seconds'] . " Second";
            }

            $findvalue = true;
        }

        return $datestring;
    }

    public static function formatDateTime($date_time, $b_12_hour = TRUE) {

        return $time = ($b_12_hour) ? date('Y-m-d h:i a', strtotime($date_time)) : $time;
    }

    public static function get_mobile_image($url, $replace_url = "gallery/mobile/") {
        $image = str_replace("gallery/", $replace_url, $url);

        foreach(self::$url_array as $value)
        {
           $image = str_replace($value, self::$real_path, $image); 
        }
        
        if (!file_exists($image)) {
            return $url;
        }
        return str_replace(self::$real_path, self::$image_path, $image);
    }

    public static function sanitize($str, $char = '-') {
        // Lower case the string and remove whitespace from the beginning or end
        $str = trim(strtolower($str));

        // Remove single quotes from the string
        $str = str_replace("'", '', $str);

        $str = str_replace("?", '', $str);
        $str = str_replace("!", '', $str);

        // Every character other than a-z, 0-9 will be replaced with a single dash (-)
        //$str = mb_ereg_replace("/[^a-z0-9]+/", $char, $str);
        $str = str_replace(" ", "-", $str);

        // Remove any beginning or trailing dashes
        $str = trim($str, $char);

        return $str;
    }

    public static function get_simple_post_layout($postValue) {
        $post_type = 0;
        if ($postValue->post_layout == 1 && $postValue->inside_image != "" && $postValue->inside_image != null) {
            $post_type = 2;
        } else if ($postValue->post_layout == 2 && $postValue['postGalleries'] && count($postValue['postGalleries']) > 2) {
            $post_type = 3;
        } else if ($postValue->post_layout == 3) {
            $post_type = 1;
        } else if ($postValue->short_title != "") {
            if ($postValue->sort_title_type == 2 && $postValue['postGalleries'] && count($postValue['postGalleries']) > 1) {
                $post_type = 6;
            } else if ($postValue->sort_title_type == 3) {
                $post_type = 7;
            } else if ($postValue->sort_title_type == 4 && isset($postValue['postAuthor']) && $postValue['postAuthor']->image != "") {
                $post_type = 4;
            } else if ($postValue->sort_title_type == 5) {
                $post_type = 5;
            }
        }
        return $post_type;
    }

    public static function get_post_link_url($news) {
        $link_array = array();
        if ($news->post_type == 2) {
            if ($news->lead_link != null && $news->lead_link != "") {
                $link_array['link'] = $news->lead_link;
                $link_array['use_link'] = 1;
            } else {
                $link_array['link'] = "";
                $link_array['use_link'] = 1;
            }
        } else {
            if ($news->lead_link != null && $news->lead_link != "") {
                $link_array['link'] = $news->lead_link;
                $link_array['use_link'] = 1;
            } else {
                $link_array['link'] = self::$image_path . self::sanitize($news->headline) . "-" . $news->id;
                $link_array['use_link'] = 0;
            }
        }
        return $link_array;
    }

    public static function add_caption_and_link($postValue) {

        $all_image = array();
        if ($postValue->lead_material && strlen(trim($postValue->lead_material)) > 0) {
            $all_image[0]['ad_image'] = self::get_mobile_image(self::$image_path . $postValue->lead_material);
            $all_image[0]['ad_image_link'] = $postValue->lead_source;
            $all_image[0]['ad_image_caption'] = $postValue->lead_caption;
        } else {
            $doc = new DOMDocument();
            @$doc->loadHTML($postValue->content);
            $images = $doc->getElementsByTagName('img');
            $i = 0;
            foreach ($images as $image) {
                if (strpos($image->getAttribute('src'), "relatednews.jpg") !== FALSE) {
                    continue;
                } else if (strpos($image->getAttribute('class'), "no_slider") !== FALSE) {
                    continue;
                } else {
                    $all_image[$i]['ad_image'] = self::get_mobile_image($image->getAttribute('src'));
                    $all_image[$i]['ad_image_link'] = $image->getAttribute('longdesc');
                    $all_image[$i]['ad_image_caption'] = $image->getAttribute('title');
                    $i++;
                }
            }
        }
        return $all_image;
    }

    public static function get_embeded_url($content) {
        preg_match('/src="([^"]+)"/', $content, $match);
        $url = $match[1];
        return $url;
    }

    public static function get_solution($content) {
        $value = preg_match_all('/<div(.*?)id=\"solution\-text\"(.*?)>(.*?)<\/div>/s', $content, $estimates);
        $soultion = "";
        if ($value) {
            $soultion = str_replace("<hr />", "", $estimates[count($estimates) - 1][0]);
            $soultion = str_replace("<hr/>", "", $soultion);
            $soultion = str_replace("\n", "", $soultion);
        }

        return $soultion;
    }
    public static function formatData($postValue)
    {
        $post_array = array();
        if($postValue)
        {
            $post_array['title']     = $postValue->headline;
            
            $post_array['post_type'] = $postValue->post_type;
            
            //need to change into single news
            $post_array['post_type_mobile'] = $postValue->mobile_view_type;
            
            $post_array['video_file'] = "";

            if ($postValue->video_file)
                $post_array['video_file'] = Settings::$image_path . $postValue->video_file;
            
            $post_array['seen'] = $postValue->view_count;
            
            $post_array['title_color'] = $postValue->headline_color;
            
            $post_array['id'] = $postValue->id;
            
            $post_array['post_layout'] = $postValue->post_layout;

            $post_array['sort_title_type'] = $postValue->sort_title_type;
            $post_array['inside_image'] = "";

            if ($postValue->inside_image)
                $post_array['inside_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue->inside_image);


            $post_array['normal_post_type'] = Settings::get_simple_post_layout($postValue);


            $post_array['author'] = "";
            $post_array['author_image'] = "";
            if (isset($postValue['postAuthor']))
            {
                $post_array['author'] = $postValue['postAuthor']->title;
                if ($postValue['postAuthor']->image)
                    $post_array['author_image'] = Settings::$image_path . $postValue['postAuthor']->image;
            }
            
            $post_array['post_id'] = $postValue->id;
            
            $post_array['headline']= $postValue->headline;
            
            $post_array['content'] = $postValue->content;
            
            $post_array['is_featured'] = $postValue->is_featured;
            $post_array['show_byline_image'] = $postValue->show_byline_image;
            $post_array['headline_color'] = $postValue->headline_color;


            $post_array['short_title'] = $postValue->short_title;
            $post_array['shoulder'] = $postValue->shoulder;
            $post_array['other_language'] = $postValue->other_language;

            $post_array['sub_head'] = $postValue->sub_head;
            $post_array['lead_material'] = $postValue->lead_material;

            $post_array['lead_caption'] = $postValue->lead_caption;
            $post_array['is_breaking'] = $postValue->is_breaking;
            $post_array['breaking_expire'] = $postValue->breaking_expire;
            $post_array['is_exclusive'] = $postValue->is_exclusive;
            $post_array['exclusive_expired'] = $postValue->exclusive_expired;


            $post_array['language'] = $postValue->language;
            $post_array['lead_link'] = $postValue->lead_link;
            $post_array['view_count'] = $postValue->view_count;

            $post_array['user_view_count'] = $postValue->user_view_count;
            $post_array['embedded'] = $postValue->embedded;
            
            $post_array['embedded_url'] = "";
            if($postValue->embedded)
            $post_array['embedded_url'] = Settings::get_embeded_url($postValue->embedded);
          
            $post_array['layout_color'] = $postValue->layout_color;

            $post_array['referance_id'] = $postValue->referance_id;
            $post_array['attach'] = $postValue->attach;
            $post_array['layout'] = $postValue->layout;
            
            $post_array['images'] = array();
            $post_array['add_images'] = array();
            $post_array['web_images'] = array();
            if ($postValue['postGalleries'])
            {
                $j = 0;
                $k = 0;
                foreach ($postValue['postGalleries'] as $value)
                {
                    if (trim($value['material']->material_url) && $value->type==2)
                    {
                        $post_array['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                    
                        $post_array['add_images'][$j]['ad_image'] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                        $post_array['add_images'][$j]['ad_image_link'] = $value->source;
                        $post_array['add_images'][$j]['ad_image_caption'] = $value->caption;
                        $j++;
                    }
                    else if(trim($value['material']->material_url) && $value->type==1)
                    {
                        $post_array['web_images'][$k]['image'] = Settings::$image_path . $value['material']->material_url;
                        $post_array['web_images'][$k]['source'] = $value->source;
                        $post_array['web_images'][$k]['caption'] =  $value->caption;
                        $k++;
                    }    
                }
            }
            
            
            //need to change 2
            if (isset($postValue->mobile_content) && strlen(Settings::substr_with_unicode($postValue->mobile_content, true)) > 0)
            {
                $post_array['mobile_content'] = $postValue->mobile_content;
                $post_array['full_content'] = Settings::substr_with_unicode($postValue->mobile_content, true);
                $post_array['solution'] = Settings::get_solution($postValue->mobile_content);
            }
            else
            {
                $post_array['mobile_content'] = $postValue->content;
                $post_array['full_content'] = Settings::substr_with_unicode($postValue->content, true);
                $post_array['solution'] = Settings::get_solution($postValue->content);
            }
            
            $post_array['summary'] = "";
            
            
            if ($postValue->summary)
            {
                $post_array['has_summary'] = 1;
                $post_array['summary'] = $postValue->summary;
            }
            else
            {
                $post_array['has_summary'] = 0;
                $post_array['summary'] = Settings::substr_with_unicode($postValue->content);
            }
            
            $post_array['share_link'] = Settings::get_post_link_url($postValue);
            $post_array['mobile_image'] = "";
            if ($postValue->mobile_image)
                $post_array['mobile_image'] = Settings::$image_path . $postValue->mobile_image;

            $datestring = Settings::get_post_time($postValue->published_date);

            $post_array['published_date'] = $postValue->published_date;
            //$post_array['attachment'] = $postValue->attach_file;
            $post_array['current_date'] = date("Y-m-d H:i:s");
            $post_array['published_date_string'] = $datestring;

            $post_array['category_menu_icon'] = "";
            $post_array['category_icon'] = "";


            if ($postValue['postCategories'][0]['category']->menu_icon)
                $post_array['category_menu_icon'] = Settings::$image_path . $postValue['postCategories'][0]['category']->menu_icon;

            if ($postValue['postCategories'][0]['category']->icon)
                $post_array['category_icon'] = Settings::$image_path . $postValue['postCategories'][0]['category']->icon;

            $post_array['category_name'] = $postValue['postCategories'][0]['category']->name;
            $post_array['category_id'] = $postValue['postCategories'][0]['category']->id;

            $post_array['inner_priority'] = $postValue['postCategories'][0]->inner_priority;

            $post_array['second_category_name'] = "";
            $post_array['second_category_id'] = $postValue['postCategories'][0]['category']->id;

            if (isset($postValue['postCategories'][1]['category']->name))
            {
                $post_array['second_category_name'] = $postValue['postCategories'][1]['category']->name;
                $post_array['second_category_id'] = $postValue['postCategories'][1]['category']->id;
            }
            $post_array['tags'] = array();

            $j = 0;
            if ($postValue['postTags'])
                foreach ($postValue['postTags'] as $value)
                {
                    $post_array['tags'][$j]['name'] = $value['tag']->tags_name;
                    $post_array['tags'][$j]['id'] = $value['tag']->id;
                    $j++;
                }
                
            $post_array['attach'] = "";
            $post_array['attach_content'] = "";
            $post_array['attach_download_link'] = "";
            $post_array['attachment'] = array();

            if ($postValue['postAttachment'] && count($postValue['postAttachment']) > 0)
            {
                $ai = 0;
                foreach ($postValue['postAttachment'] as $avalue)
                {
                    $post_array['attachment'][$ai]['attach'] = Settings::$image_path . $avalue->file_name;

                    $post_array['attachment'][$ai]['content'] = '<iframe frameborder="0" style="width: 100%; height: 500px;" src="http://docs.google.com/gview?url=' . Settings::$image_path . $avalue->file_name . '&embedded=true"></iframe>';
                    $post_array['attachment'][$ai]['download_link'] = 'http://www.champs21.com/download?f_path=' . $avalue->file_name;

                    $post_array['attachment'][$ai]['caption'] = $avalue->caption;
                    $post_array['attachment'][$ai]['show'] = $avalue->show;
                    $ai++;
                }
            }
            
            return $post_array;
            
        }
        else
        {
            return false;
        }    
    }

    public static function content_images($content, $first_image = true, $lead_material = false) {
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $images = $doc->getElementsByTagName('img');
        $all_image = array();

        if ($lead_material) {
            $all_image[] = self::get_mobile_image(self::$image_path . $lead_material);
        }
        $i = 1;
        foreach ($images as $image) {
            if (strpos($image->getAttribute('src'), "relatednews.jpg") !== FALSE) {
                continue;
            } else if (strpos($image->getAttribute('class'), "no_slider") !== FALSE) {
                continue;
            } else if ($i == 1 && $first_image === false) {
                continue;
            } else {
                $all_image[] = self::get_mobile_image($image->getAttribute('src'));
            }
            $i++;
        }
        return $all_image;
    }

    public static function substr_with_unicode($string, $full_length = false, $length = 400) {
        $string = preg_replace('/<div (.*?)>Source:(.*?)<\/div>/', '', $string);
        $string = preg_replace('/<div class="img_caption" (.*?)>(.*?)<\/div>/', '', $string);

        $string = str_replace("\n", '', trim($string));
        $string = str_replace("&nbsp;", '', $string);
        $string = str_replace("<p></p>", '', $string);

        if ($full_length === false) {

            $main_string = mb_substr(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')), 0, $length, 'UTF-8');
            return trim($main_string);
        } else {
            $main_string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
            $main_string = mb_substr($main_string, 0, mb_strlen($main_string, 'UTF-8'), 'UTF-8');
            return trim($main_string);
        }
    }

    public static function getProfileModel() {

        $mod_name = 'Employees';

        if (Yii::app()->user->isStudent) {
            $mod_name = 'Students';
        }

        if (Yii::app()->user->isParent) {
            $mod_name = 'Guardians';
        }

        return $mod_name;
    }

    public static function extractIds($array_or_obj, $key = 'id') {

        $ar_ids = array();

        foreach ($array_or_obj as $value) {
            if (is_object($array_or_obj)) {
                $ar_ids[] = $value->$key;
            }

            if (is_array($array_or_obj)) {
                $ar_ids[] = $value[$key];
            }
        }

        return $ar_ids;
    }

}
