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
class whattowatch extends widget
{

    function run($ci_key)
    {           
        
        //$data['magazines'] = $this->format_magazines($magazines);
        $data['whattowatch'] = $this->getData($ci_key);
        
        $this->render($data);
    }
    private function getData($ci_key, $byDate = "")
    {
        $this->load->model( "menus" );
        $obj_menus_data = $this->menus->get_menu_by_ci_key( $ci_key );
        $obj_menus_data->category_id;
        
        if($byDate == "")
        {
            $obj_posts = new Post_model();
            $arIssueDate = $obj_posts->getIssueDate();
        }        
        
        $this->db->select('whats_on.*,ch.name' )
                ->from('whats_on')
                ->join("channels as ch", "whats_on.channel_id = ch.id", 'LEFT')
                ->where("tds_whats_on.is_active",1)                
                ->where("tds_whats_on.category_id",$obj_menus_data->category_id)                                
                ->order_by("show_date", "desc");
        
        $news_query = $this->db->get();
        $data = array();
        foreach($news_query->result() as $row)
        {
            $data[] = $row;
        }
        return $data;
    }
    
 
}

