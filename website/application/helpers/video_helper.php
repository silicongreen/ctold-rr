<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if ( !function_exists("video_info") )
{
    function video_info($url) 
    {
        // Handle Youtube
        if (strpos($url, "youtube.com")) 
        {
            $data['url'] = $url;
            $url = parse_url($url);
            $vid = parse_str($url['query'], $output);
            $video_id = $output['v'];
            $data['video_type'] = 'youtube';
            $data['video_id'] = $video_id;
            $data['is_exists'] = True;
            $youtube_data = get_xml("http://gdata.youtube.com/feeds/api/videos?q=$video_id");
            $xml = new SimpleXMLElement($youtube_data);
            //print "http://gdata.youtube.com/feeds/api/videos?q=$video_id";
            //print '<pre>';
            //print_r($xml);
            if ( is_object($xml) ) foreach ($xml->entry as $entry) 
            {
                $data['is_exists'] = TRUE;
                
                $media = $entry->children('http://search.yahoo.com/mrss/');
                
                $attrs = $media->group->player->attributes();
                
                $watch = $attrs['url']; 

                // get video thumbnail
                foreach( $media->group->thumbnail[0]->attributes() as $key => $value )
                {
                    $data['thumb_1'][$key] = (string) $value;
                }
                foreach( $media->group->thumbnail[1]->attributes() as $key => $value )
                {
                    $data['thumb_2'][$key] = (string) $value;
                }
                foreach( $media->group->thumbnail[2]->attributes() as $key => $value )
                {
                    $data['thumb_3'][$key] = (string) $value;
                }
                foreach( $media->group->thumbnail[3]->attributes() as $key => $value )
                {
                    $data['thumb_large'][$key] = (string) $value;
                }
                
                $data['tags'] = (string) $media->group->keywords; // Video Tags
                $data['cat'] = (string) $media->group->category; // Video category
                $attrs = $media->group->thumbnail[0]->attributes();
                $thumbnail = $attrs['url']; 

                // get <yt:duration> node for video length
                $yt = $media->children('http://gdata.youtube.com/schemas/2007');
                $attrs = $yt->duration->attributes();
                $data['duration'] = (int) $attrs['seconds'];

                // get <yt:stats> node for viewer statistics
                $yt = $entry->children('http://gdata.youtube.com/schemas/2007');
                if ( $yt->statistics && $yt->statistics->attributes() )
                {
                    $attrs = $yt->statistics->attributes();
                    $data['views'] = $viewCount = (string) $attrs['viewCount']; 
                }
                $data['title'] = (string) $entry->title;
                $data['info'] = (string) $entry->content;

                // get <gd:rating> node for video ratings
                $gd = $entry->children('http://schemas.google.com/g/2005'); 
                if ($gd->rating) {
                    $attrs = $gd->rating->attributes();
                    $data['rating'] = $attrs['average']; 
                } else { $data['rating'] = 0;}
            } // End foreach
        } // End Youtube

        // Handle Vimeo
        else if (strpos($url, "vimeo.com")) 
        {
            $video_id=explode('vimeo.com/', $url);
            $video_id=$video_id[1];
            $data['video_type'] = 'vimeo';
            $data['video_id'] = $video_id;
            $data['is_exists'] = FALSE;
            libxml_use_internal_errors(true);
            $vimeo_data = get_xml("http://vimeo.com/api/v2/video/$video_id.xml");
            
            if ( $vimeo_data )
            {
                $xml = new SimpleXMLElement($vimeo_data);
            }
            else
            {
                $xml = false;
            }
            
            if (is_object($xml) ) foreach ($xml->video as $video) 
            {
                $data['is_exists'] = TRUE;
                $data['id']=$video->id;
                $data['title'] = (String)$video->title;
                $data['info']=$video->description;
                $data['url']=(String)$video->url;
                $data['upload_date']=$video->upload_date;
                $data['mobile_url']=$video->mobile_url;
                $data['thumb_small']=(string) $video->thumbnail_small;
                $data['thumb_medium']=(string) $video->thumbnail_medium;
                $data['thumb_large']=(string) $video->thumbnail_large;
                $data['user_name']=$video->user_name;
                $data['urer_url']=$video->urer_url;
                $data['user_thumb_small']=$video->user_portrait_small;
                $data['user_thumb_medium']=$video->user_portrait_medium;
                $data['user_thumb_large']=$video->user_portrait_large;
                $data['user_thumb_huge']=$video->user_portrait_huge;
                $data['likes']=(string)$video->stats_number_of_likes;
                $data['views']=(string)$video->stats_number_of_plays;
                $data['comments']=(string)$video->stats_number_of_comments;
                $data['duration']=(string)$video->duration;
                $data['width']=(string)$video->width;
                $data['height']=(string)$video->height;
                $data['tags']=(string)$video->tags;
            } // End foreach
        } // End Vimeo

        // Set false if invalid URL
        else 
        { 
            $data = false; 
        }

        return $data;

    }
}

if ( !function_exists("check_video") )
{
    function check_video($url) 
    {
        $video_exists = FALSE;
        $unsupported_video = false;
        // Handle Youtube
        if (strpos($url, "youtube.com")) 
        {
            $url = parse_url($url);
            $vid = parse_str($url['query'], $output);
            $video_id = $output['v'];
            $youtube_data = get_xml("http://gdata.youtube.com/feeds/api/videos?q=$video_id");
            $xml = new SimpleXMLElement($youtube_data);
            if ( is_object($xml) ) foreach ($xml->entry as $entry) 
            {
                $video_exists = TRUE;
            } // End foreach
            $video_exists = TRUE;
        } // End Youtube

        // Handle Vimeo
        else if (strpos($url, "vimeo.com")) 
        {
            $video_exists = TRUE;
        } // End Vimeo

        // Set false if invalid URL
        else 
        { 
            $unsupported_video = true;
        }

        return array($video_exists, $unsupported_video);

    }
}

if ( !function_exists("save_video_image") )
{
    function save_video_image( $image_url, $folder_name )
    {
        $path = ( strtolower($folder_name) == "video" ) ? "video" : "video/" . $folder_name;
        $filename = getfilename();
        $s_filename = "upload/gallery/" . strtolower($path) . "/";
        
        if ( !file_exists($s_filename) )
        {
            @mkdir(FCPATH . $s_filename, 0777, true);
        }
        $s_filename .= $filename;
        $s_thumb_filename = "upload/gallery/thumbs/" . strtolower($path) . "/";
        if ( !file_exists($s_thumb_filename) )
        {
            @mkdir(FCPATH . $s_thumb_filename, 0777, true);
        }
        $s_thumb_filename .= $filename;
        $s_upload_path = FCPATH . $s_filename;
        $s_thumb_path = FCPATH . $s_thumb_filename;
        
        
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$image_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1); 
	//curl_setopt($ch, CURLOPT_FILE, $fp);
        
	$retValue = curl_exec($ch);	
        curl_close($ch);
        
        $fp = fopen($s_upload_path, 'x');
        fwrite($fp, $retValue);
	
        
        fclose($fp);
        copy($s_upload_path, $s_thumb_path);
        return $s_filename;
    }
}

if ( !function_exists('getfilename') )
{
    function getfilename()
    {
        $strNewName = substr(md5(uniqid(rand(), true)), 0, 7);
        $filename = $strNewName . ".png";
        return $filename;
    }
}

if ( !function_exists("get_xml") )
{
    function get_xml( $url )
    {
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FAILONERROR,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$retValue = curl_exec($ch);			 
	curl_close($ch);
	return $retValue;
    }
}

