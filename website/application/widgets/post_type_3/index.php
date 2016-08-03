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
class post_type_3 extends widget
{

    function run( $obj_post_news, $style, $s_post_class, $li_class_name, $i, $count_show, $is_exclusive_found, $target, $from = "main",$category_id = 0)
    {
        $data['news'] = $obj_post_news;
        $data['style'] = $style;
        $data['s_post_class'] = $s_post_class;
        $data['li_class_name'] = $li_class_name;
        $data['i'] = $i;
        $data['count_show'] = $count_show;
        $data['is_exclusive_found'] = $is_exclusive_found;
        $data['target'] = $target;
        $data['category_id'] = $category_id;
        $data['from'] = $from;
        //$this->load->model('post');
        //$obj_post_gallery = $this->post->get_post_gallery($obj_post_news->post_id);
        
        $data['images'] = $obj_post_news->web_images;
        $this->render($data);
    }
    
    
 
}
