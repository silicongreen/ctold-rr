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
class thirdcolumninnernews extends widget
{

    function run( $ar_3rd_column_extra_data, $extra_column_name, $ar_extra_config)
    {
        $CI = & get_instance();
        $CI->load->config("huffas");
        
        $data['ar_3rd_column_extra_data'] = $ar_3rd_column_extra_data;
        $data['extra_column_name'] = $extra_column_name;
        $data['ar_extra_config'] = $ar_extra_config;
        
        $ecl_config = $CI->config->config['education-changes-life'];
        $ecl = in_array((int) $ar_extra_config['category_id'], $ecl_config['ecl_ids'] ) ? TRUE : FALSE;
        
        if(!$ecl) {
            $ecl_config = $CI->config->config['nation-builder'];
            $ecl = in_array((int) $ar_extra_config['category_id'], $ecl_config['ecl_ids'] ) ? TRUE : FALSE;
        }
        
        $candle_banner = '';
        $candle_button = '';
        if($ecl) {
            $candle_banner = $ecl_config['3rd-column']['candle_banner'];
            $candle_button = $ecl_config['3rd-column']['candle_button'];
        }
        
        $data['ecl'] = $ecl;
        $data['ecl_banner'] = $candle_banner;
        $data['ecl_button'] = $candle_button;
        
        $this->render($data);
    }
    
    
 
}

