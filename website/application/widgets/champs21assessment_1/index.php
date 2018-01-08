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
class champs21assessment_1 extends widget
{
    public $CI;
    
    public function __construct(){
        $this->CI = & get_instance();
    }

    function run($ci_key, $assessment, $score_board, $can_play, $last_played, $school_score_board = NULL)
    {   
        $this->CI->load->config("huffas");
        $assessment_config = $this->CI->config->config['assessment'];
        
        $data['b_explanation_popup'] = FALSE;
        
        if($assessment_config['auto_next'][strtolower($assessment_config['types'][$assessment->type])]){
            $data['b_explanation_popup'] = TRUE;
        }
        
        $data['ci_key'] = $ci_key;
        $data['assessment'] = $assessment;
        $data['score_board'] = $score_board;
        $data['can_play'] = $can_play;
        $data['last_played'] = $last_played;
        
        $this->render($data);
    }
}

