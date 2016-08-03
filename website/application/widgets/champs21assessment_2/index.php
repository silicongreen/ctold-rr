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
        $this->CI->load->config("huffas");
        $this->load->config('user_register');
        $user_id = get_free_user_session('id');
        
        $b_mulit_school_join = $this->config->config['multi_school_join'];
        $assessment_config = $this->CI->config->config['assessment'];
        
        $url_prefix = $assessment_config['url_prefix'][$assessment->type];
        
        $ar_uris = $this->CI->uri->segment_array();
        
        $redirect_url = '';
        if(!empty($assessment->unplayed_levels) && !empty($assessment->next_level) ) {
            $redirect_url = base_url($url_prefix . sanitize($assessment->title) . '-' . $assessment->type . '-' . $assessment->id) . '/' . $assessment->next_level;
            
        } elseif ( is_array($assessment->unplayed_levels) && empty($assessment->unplayed_levels) && empty($assessment->next_level) ) {
            $redirect_url = base_url($url_prefix . sanitize($assessment->title) . '-' . $assessment->type . '-' . $assessment->id) . '/' . max($assessment->ar_assessment_levels);
        }
        
        if( !empty($assessment->next_level) && (end($ar_uris) != $assessment->next_level )) {
            redirect($redirect_url);
        }
        
        $data['b_explanation_popup'] = FALSE;

        if($assessment_config['auto_next'][strtolower($assessment_config['types'][$assessment->type])]){
            $data['b_explanation_popup'] = TRUE;
        }
        
        $user_school = new User_school();
        $obj_assessment_school_mark = new Assessment_school_mark();
        
        $user_school_data = ($b_mulit_school_join) ? $user_school->get_user_school($user_id, $school_id) : $user_school->get_user_school($user_id);
        $assessment_school_mark = $obj_assessment_school_mark->find_assessment_school_mark($user_id, $assessment_id, 0, $user_school_data[0]->school_id);
        
        $b_score_added_to_school = FALSE;
        if($assessment_school_mark !== false) {
            $b_score_added_to_school = TRUE;
        }
        
        $data['ci_key'] = $ci_key;
        $data['assessment'] = $assessment;
        $data['score_board'] = $score_board;
        $data['school_score_board'] = $school_score_board;
        $data['b_score_added_to_school'] = $b_score_added_to_school;
        $data['can_play'] = $can_play;
        $data['last_played'] = $last_played;
        $data['icc_utotal'] = ( !empty($assessment->next_level) > 0 ) ? 'not' : 'done';
        
        $this->render($data); 
    }
}

