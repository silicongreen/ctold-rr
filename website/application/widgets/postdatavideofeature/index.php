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
class postdatavideofeature extends widget
{

    function run( $s_category_name, $s_category_ids = "", $target = "inner", $b_featured = FALSE, $i_featured_position = 0, $page = "index", $current_page = 0, $limit = 9,$is_game = 0)
    {
        $CI = & get_instance();        
       
        $s_priority = "";
        $a_post_params = array();       
        
        
        
        $s_priority = "DATE(tds_post.published_date),desc+postCategories.inner_priority,asc";

        if ( $b_featured )
        {
            $a_post_params = array(
                            "tds_post.referance_id" => 0,
                            "tds_post.is_featured" => 1
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
       
    
        
        $ar_post_news = $this->post->gePostNews($a_post_params, $target, "smaller", $s_priority, $s_category_ids, $limit, $current_page, $b_featured, $i_featured_position); 

      
        
        $data['obj_post_news'] = $ar_post_news["data"];
        
        
        $this->render($data);
        
    }
    
    
 
}

