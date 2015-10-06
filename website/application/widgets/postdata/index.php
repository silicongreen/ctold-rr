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
class postdata extends widget
{

    function run( $s_category_name, $s_category_ids = "", $target = "inner", $b_featured = FALSE,
            $i_featured_position = 0, $page = "index", $current_page = 0, $limit = 9,$is_game = 0,
            $q = '', $mix_category = NULL, $b_get_related = false, $post_id = 0, $exclude = array(),
            $lang = '')
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
                
                $ar_post_params = ( $category_config['3rd-column']['force_limit'] ) ? array('force_limit' => $category_config['3rd-column']['force_limit']) : 0;
                
                if(!empty($lang)) {
                    $a_post_params['lang'] = $lang;
                }
                
                $ar_post_news_additional = $this->post->gePostNews( $ar_post_params, "inner", "smaller", "tds_post.published_date, asc", $data['ar_extra_config']['category_id'], $data['ar_extra_config']['count'], 0, false, 0);
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
        } else {
            $s_priority = "post.priority, ASC";
        }
        if($target == "school" || $target=="teacher")
        {
            $a_post_params = array();
            $a_post_params['stbid'] =$s_category_ids;

            if(!empty($lang)) {
                $a_post_params['lang'] = $lang;
            }

            $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", $s_priority, 0, $limit, $current_page, $b_featured, $i_featured_position);
        }
        else
        {
            if($post_id > 0 && !empty($q)) {
                $a_post_params['postCategories.post_id'] = $post_id;
            } 
            else if($post_id > 0 && empty($q) && $b_get_related) {
                $a_post_params['post_id'] = $post_id;
            }
            else  if($post_id > 0 && empty($q) && $b_get_related == false) {
                $a_post_params["NOT_IN"] = array("tds_post.id",array($post_id));
            }
            
            if($b_get_related) {
                $a_post_params['b_get_related'] = $b_get_related;
            }
            if($b_get_related == false)
            {
//            echo '<pre>';
//            print_r($a_post_params) . '<br/>';
//            print_r($target) . '<br/>';
//            print_r($s_priority) . '<br/>';
//            print_r($s_category_ids) . '<br/>';
//            
//            exit;
            }
            if(!empty($lang)) {
                $a_post_params['lang'] = $lang;
            }

            $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", $s_priority, $s_category_ids, $limit, $current_page, $b_featured, $i_featured_position); 
        }
        $data['target'] = $target;
        
        $data['is_game'] = $is_game;
        $data['page'] = $page;
        
        $data['category'] = (int) $s_category_ids;
        $data['related'] = $b_get_related;
        
        $data['ecl'] = FALSE;
        $data['ecl_top_banner'] = array();
        if(isset($CI->config->config[sanitize($s_category_name)]['ecl_ids'])) {
            $data['ecl'] = in_array($data['category'],  $CI->config->config[sanitize($s_category_name)]['ecl_ids'] ) ? TRUE : FALSE;
            
            if($data['ecl']) {
                $data['ecl_top_banner'] = $CI->config->config[sanitize($s_category_name)]['top_banner'];
            }
            
        }
        
        $data['opinion'] = FALSE;
        if(isset($CI->config->config[sanitize($s_category_name)]['op_ids'])) {
            $data['opinion'] = in_array($data['category'],  $CI->config->config[sanitize($s_category_name)]['op_ids'] ) ? TRUE : FALSE;
        }
        
        $data['candle_category_id'] = NULL;
        if(isset($CI->config->config[sanitize($s_category_name)]['candle_category_id'])) {
            $data['candle_category_id'] = $CI->config->config[sanitize($s_category_name)]['candle_category_id'];
        }
        
        if(is_string($mix_category)) {
            $data['category_banner_title'] = $mix_category;
        } else {
            $i = 0;
            foreach ($mix_category as $category) {
                if($i == 0) {
                    $data['category_banner_title'] = (isset($category->display_name) && $category->display_name != "") ? $category->display_name : $category->name;
                }
            }
        }
        
        $data['obj_post_news'] = $ar_post_news["data"];
        $data['s_category_name'] = $s_category_name;
        
        $data['obj_selected_post_news'] = array();
        
        if(isset($ar_post_news["selected_data"]) && count($ar_post_news["selected_data"])>0)
        {
            $data['obj_selected_post_news'] = $ar_post_news["selected_data"];
        }
        
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
        
        if (get_free_user_session('paid_id') && get_free_user_session('paid_school_code')) {
            $user_school = new User_school();

            $user_school_data = $user_school->get_user_school(get_free_user_session("id"));
            $widget_title = 'My School';
            if ($user_school_data !== FALSE) {
                $school_obj = new schools($user_school_data[0]->school_id);
                $widget_title = '<a href="'. base_url() . 'schools/' . sanitize($school_obj->name) .'" style="color: #ffffff;">My Diary21</a>';
                if ($school_obj->is_paid == 1) {
                    $data['school_icon_class'] = 'icon-diary21-school';
                }
            }

            $data['widget_title'] = $widget_title;
        }
        
        $data['exclude'] = $exclude;
        
//        print '<pre>';
//        print_r($data);
//        exit;
        $this->render($data);
        
    }
    
    
 
}

