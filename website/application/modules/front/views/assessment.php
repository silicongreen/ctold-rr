<?php
    $widget = new Widget;
    
    if(!property_exists($assessment, 'id')) {
        $assessment->type = 1;
    }
    
    $widget->run('champs21assessment_' . $assessment->type, $ci_key, $assessment, $score_board, $can_play, $last_played, $school_score_board);
?>