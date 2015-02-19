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
class champs21assessment_2 extends widget
{
    public $CI;
    
    public function __construct(){
        $this->CI = & get_instance();
    }

    function run($ci_key, $assessment, $score_board, $can_play, $last_played, $school_score_board = NULL)
    {   
        $userscore_data = get_icc_user_level_score();
        
        $uris = explode('/', $_SERVER['REQUEST_URI']);
        $pices = $uris[3];      $icc = 0;
        if( is_array($userscore_data))
        {   $utotal = 0;
            foreach ($userscore_data as $udata)
            {
                $utotal += $udata->mark;
                $icc++;
            }
        }
        if($pices == null || $pices == "")
        {
            if($icc == 3)
            {
                redirect(base_url().$uris[1]."/".$uris[2]."/".$icc);
            }
            else
            {
                redirect(base_url().$uris[1]."/".$uris[2]."/1");
            }
        }
        else
        {
            if($icc > 1 && $pices  != $icc)
            {
                redirect(base_url().$uris[1]."/".$uris[2]."/".$icc);
            }
            elseif($icc == 3)
            {
                $data['icc_utotal'] = "done";
            }
            else
            {
                $data['icc_utotal'] = "not";
            }
        }
        
        
        
        
        $this->CI->load->config("huffas");
        $assessment_config = $this->CI->config->config['assessment'];
        
        $data['b_explanation_popup'] = FALSE;
        
        if($assessment_config['auto_next'][strtolower($assessment_config['types'][$assessment->type])]){
            $data['b_explanation_popup'] = TRUE;
        }
        
        $data['ci_key'] = $ci_key;
        $data['assessment'] = $assessment;
        $data['score_board'] = $score_board;
        $data['school_score_board'] = $school_score_board;
        $data['can_play'] = $can_play;
        $data['last_played'] = $last_played;
        
        
        
        
        $this->render($data);
    }
}

