<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Widget Plugin 
 * 
 * Install this file as application/plugins/widget_pi.php
 * 
 * @version:     0.1
 * $copyright     Copyright (c) Wiredesignz 2009-03-24
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class postvideos extends widget
{

    function run( $s_category_name, $s_category_ids = "", $target = "inner", $b_featured = FALSE, $i_featured_position = 0, $page = "index", $current_page = 0, $limit = 9,$is_game = 0, $q = '')
    {
        $CI = & get_instance();        
        $CI->load->config("huffas");
        
        if ( ($category_config = $CI->config->config[sanitize($s_category_name)]  ) )
        {
            $data['exclude_category'] = $category_config['exclude_category'];
            $data['position_div'] = TRUE;
            $data['s_width'] = "";
            $data['li_class_name'] = "col-md-6";
            $data['ar_extra_config'] = "";
            $data['has_3rd_column'] = FALSE;
//            if ( isset($category_config['width']) )
//            {
//                $data['s_width'] = "width: " . $category_config['width'] . ";";
//            }
//            if ( isset($category_config['li-class-name']) )
//            {
//                $data['li_class_name'] = $category_config['li-class-name'];
//            }
            if ( isset($category_config['li-class-name']) )
            {
                $data['has_3rd_column'] = TRUE;
                $data['ar_extra_config'] = $category_config['3rd-column'];
                $ar_post_news_additional = $this->post->gePostNews( 0, "inner", "smaller", "tds_post.published_date, asc", $data['ar_extra_config']['category_id'], $data['ar_extra_config']['count'], 0, false, 0); 
                $data['ar_3rd_column_extra_data'] = $ar_post_news_additional['data'];
                $data['extra_column_name'] = $ar_post_news_additional['data'][0]->name;
            }
        }
        else
        {
            $data['exclude_category'] = 0;
            $data['position_div'] = FALSE;
            $data['s_width'] = "";
            $data['li_class_name'] = "col-md-6";
            $data['ar_extra_config'] = "";
            $data['has_3rd_column'] = FALSE;
        }
        
        $this->load->model('post');
        $this->load->model("user_folder");
        $s_priority = "";
        $a_post_params = array();       
        
        if ( $target == "index" )
        {
            $s_priority = "MAX(t.priority),DESC";
        }
        else if ( $target == "good_read" )
        {            
            
            $i_user_id = get_free_user_session("id");
            $ar_folder_id_data = $this->user_folder->get_folder_data($i_user_id, $s_category_ids);
 
            $s_priority = "ugr.folder_id,asc+ugr.id,desc";
            
            if ( $s_category_ids > 0 )
            {
                
                $is_read = ($ar_folder_id_data->visible == 0 )?0:1;
                $a_post_params = array(
                                "ugr.folder_id" => $s_category_ids,
                                "ugr.user_id" => $i_user_id
                );
            }
        }
        else if($target == "good_read_unread")
        {
            $i_user_id = get_free_user_session("id");
            $ar_folder_id_data = $this->user_folder->get_folder_data($i_user_id, $s_category_ids);
 
            $s_priority = "ugr.folder_id,asc+ugr.id,desc";
            
            if ( $s_category_ids > 0 )
            {
                
                $is_read = ($ar_folder_id_data->visible == 0 )?0:1;
                $a_post_params = array(
                                "ugr.user_id" => $i_user_id
                );
            }
            
        }    
        else if ( $target == "inner" )
        {
            $s_priority = "DATE(tds_post.published_date),desc+postCategories.inner_priority,asc";
            
            if ( $b_featured )
            {
                $a_post_params = array(
                                "tds_post.referance_id" => 0
                );
            }
            else
            {
                $a_post_params = array(
                                "tds_post.referance_id" => 0,
                                "CUSTOM"                => "( tds_post.is_featured = 0 OR tds_post.is_featured IS NULL)"
                );
            }
            if ( $data['exclude_category'] > 0 )
            {
                $a_post_params['CUSTOM'] = "category.id NOT IN (". $data['exclude_category'] . ")"; 
            }
        }
        else if ( $target == "inner-popular" )
        {
            $s_priority = "tds_post.user_view_count,desc";
            
            if ( $b_featured )
            {
                $a_post_params = array(
                                "tds_post.referance_id" => 0
                );
            }
            else
            {
                $a_post_params = array(
                                "tds_post.referance_id" => 0,
                                "CUSTOM"                => "( tds_post.is_featured = 0 OR tds_post.is_featured IS NULL)"
                );
            }
            if ( $data['exclude_category'] > 0 )
            {
                $a_post_params['CUSTOM'] = "category.id NOT IN (". $data['exclude_category'] . ")"; 
            }
        }
        
        $data['layout_type'] = "1";
        
        if ( $target == "index" && ! $b_featured )
        {
            $CI->db->where('key', 'layout');
            $query = $CI->db->get('settings');
            $layout_settings = $query->row();

            $layout = $layout_settings->value;
            if ( $layout == "3-block-default" || $layout == "3-block-with-featured-in-two-block" )
            {
                $b_featured = 2;
                $i_featured_position = "1,2,3,4";
            }
            else if ( $layout == "3-block-with-featured-with-two-block" )
            {
                $b_featured = 3;
                $i_featured_position = "2,3,4";
            }
            $data['layout_type'] = "1";
            if ( $layout == "3-block-with-featured-in-two-block" )
            {
                $data['layout_type'] = "2";
            }
            else if ( $layout == "3-block-with-featured-with-two-block" )
            {
                $data['layout_type'] = "3";
            }
        }
        
        $data['q'] = '';
        
        $q = trim($q);
        if(!empty($q)) {
            $a_post_params['q'] = $q;
            $data['q'] = $q;
            $s_priority = "DATE(tds_post.published_date), DESC";
        }
        
        $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", $s_priority, $s_category_ids, $limit, $current_page, $b_featured, $i_featured_position); 

        $data['target'] = $target;
        
        $data['is_game'] = $is_game;
        $data['page'] = $page;
        $data['category'] = (int) $s_category_ids;
        
        $data['obj_post_news'] = $ar_post_news["data"];
        
//        foreach($data['obj_post_news'] as $value)
//        {
//            echo $value->headline."<br/>";
//        } 
//        exit;
        
        $data['total_data']  = $ar_post_news['total'];
        $data['page_size'] = $limit;
        $data['current_page'] = $current_page;
        
        $data['featured'] = $b_featured;
        $data['swf_external_url'] =  $CI->config->config['swf']['external_url'];
        
        $this->render($data);
        
    }
    
    
 
}

