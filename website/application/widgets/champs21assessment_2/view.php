<link rel="stylesheet" href="<?php echo base_url('scripts/FlipClock/compiled/flipclock.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/css/quiz.css') ?>">
<div style="width: 100%; min-height:250px;" class="quiz-cointainer">
    
    <?php
        $uris = explode('/', $_SERVER['REQUEST_URI']);
        $cur_level = $uris[count($uris) - 1];
    ?>
    
    <div class="sports-inner-news yesPrint">
        <div class="icc-quiz-top-header">
            <img src="/styles/layouts/tdsfront/image/icc-quiz-top-header.png">
        </div>
        <div class="clearfix"></div>
    </div>
    
    <div class="clearfix"></div>
   
    <div class="container" style="width: 77%; min-height:250px !important;">

        <?php if (free_user_logged_in()) { ?>
            <input type="hidden" value="<?php echo get_free_user_session('id'); ?>" id="asses_id">
        <?php } ?>
        <input type="hidden" value="<?php echo (!empty($assessment->use_time) ) ? $assessment->time : 0; ?>" id="assess_time">
        <input type="hidden" value="<?php echo (!empty($assessment->higistmark) ) ? $assessment->higistmark : 0; ?>" id="highest_score">
        <input type="hidden" value="<?php echo (!empty($assessment->played) ) ? $assessment->played : 0; ?>" id="total_played">
        <?php if($icc_utotal=="done"){?>
        <input type="hidden" value="<?php echo $icc_utotal; ?>" id="icc_utotal">
        <?php }?>
        <?php if (!$b_explanation_popup) { ?>
            <input type="hidden" value="" id="b_explanation_popup">
        <?php } ?>

        <input type="hidden" value="" id="nos_questions">

        <div id="post_uri" style="display: none;">
            <?php echo $post_uri; ?>
        </div>

        <div class="inner-container">
            <div id="icc-quiz-start-screen">                
                <div class="icc-quiz-start-screen-wrapper">
                    <div class="icc-quiz-logo-wrapper">
                        <img src="/styles/layouts/tdsfront/image/icc-quiz-logo.png">
                    </div>
                    <div class="icc-quiz-btn-wrapper">
                        <button class="element-animation" type="button" id="start_assessment_play"></button>
                        <div class="f2 icc-quiz-start-screen-stage-text">
                            Stage <?php echo $cur_level; ?>
                        </div>
                    </div>
                    <div class="icc-quiz-jersey-wrapper">
                        <img src="/styles/layouts/tdsfront/image/icc-quiz-jersey.png">
                    </div>
                </div>                
                <div class="clearfix"></div>
                <div class="icc-quiz-start-screen-text">
                    <p class="f2">The stage is set for the greatest cricketers… and quizzers!! Win a whole set of Cricket gears for your school! 10 jerseys are up for grabs for the top ten high-scorers everyday as well. So make sure you play every day and win as many jerseys as you want.<br />
                        So what are you waiting for? Start playing!!</p>
                </div>
            </div>
            
            <div id="icc-quiz-start-play-screen">
                <div id="pre_assessment_details">
                    <div class="score-board-summary">
                        <div class="score-board-header f2">
                            Today's Quiz
                        </div>
                        <?php 
                            $actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            $pieces = explode("/", $actual_link);
                        ?>
                        <div class="score-board-summary-text">
                            <div class="f2" style="margin-bottom:5px;font-size:18px;color: #616161;">Stage <?php echo (!empty($pieces[count($pieces) - 1]))?$pieces[count($pieces) - 1]:1 ;?></div>
                            <p class="f2"></p>
                            <p class="f2"></p>
                            <p class="f2">Highest Score&nbsp;: <?php echo (!empty($assessment->higistmark) ) ? $assessment->higistmark : 0; ?></p>
                            <p class="f2">Quiz Time&nbsp;: <?php echo (!empty($assessment->use_time) ) ? $assessment->time : 0; ?> Minute</p>
                            <p class="f2">Total Played&nbsp;: <?php echo (!empty($assessment->played) ) ? $assessment->played : 0; ?></p>
                        </div>

                        <div class="assessment-popup-btn-wrapper">
                            <button class="icc-quiz-play-2" type="button" id="start_assessment_now"></button>
                            <button class="icc-quiz-rules f2" type="button" id="show_assessment_rules">
                                <span>Rules</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="leader_board">
                    <div class="individual">
                        <div class="leader_board_header f2">
                            Top Player
                        </div>
                        <div class="clearfix"></div>
                        <div class="leader_board_content">
                            <ul>
                                <?php foreach ($score_board as $sb) { ?>
                                    <li><span class="user_name f5"><?php echo $sb->user_name; ?></span>&nbsp;<span class="mark f5"><?php echo $sb->mark; ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="school">
                        <div class="leader_board_header f2">Top School</div>
                        <div class="clearfix"></div>
                        <div class="leader_board_content">
                            <ul>
                                <?php foreach ($school_score_board as $ssb) { ?>
                                    <li><span class="user_name f5"><?php echo $ssb->school_name; ?></span>&nbsp;<span class="mark f5"><?php echo $ssb->mark; ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="icc-quiz-rules-wrapper" >
                    <div style="background-color: #fff; border-radius: 5px; min-height: 530px;margin-top:30px; margin-bottom: 20px;padding:20px;">
                    <h3>Quiz Rules</h3>   
                 1 . Anybody can participate in this competition, not just students<br><br>
2 . You can win a jersey every day by being in the top ten scorers. So make sure you play every day and win as many jerseys as you want.<br><br>
3 . You can add your scores to your school. The school with the highest score (scores contributed by players) will win a set of cricket gears.<br><br>
4 . The competition has three stages. You can play Stage 1 without any registration, to save your score please register.<br><br>
5 . After saving your score, Stages 2 & 3 will be unlocked for you.<br><br>
6 . Your total score is a compilation of your scores from Stage 1, 2 & 3. So make sure you play all three stages to be in the top 10 scorers. <br>
                </div>
                    </div>                
            </div>

            <div class="icc-quiz-game-over">
                <div class="icc-quiz-game-over-header f2"><?php echo ($icc_utotal == "done") ? 'You have already played for today. Visit tomorrow.' : 'Game Over' ?></div>
                <div class="score-board-summary-wrapper" id="score_board_icc">
                    <div class="score-board-summary">
                        <div class="score-board-header f2">
                            My Score Today
                        </div>
                        <div class="score-board-summary-text">
                            <?php
                            $ar_assess_levels = explode(',', $assessment->levels);
                            $ar_user_score_board = get_object_vars($assessment->user_score_board);
                            
                            $userscore_data = get_icc_user_level_score($assessment->id);
                            ?>

                            <?php
                            unset($uris[count($uris) - 1]);

                            $url = implode('/', $uris);

                            foreach ($ar_assess_levels as $level) {                                
                                ?>
                                <p class="f2">Stage <?php echo $level; ?> : 
                                    <span id="level-<?php echo $level; ?>">
                                        <?php 
                                            
                                            if($userscore_data == false)                                            {                                          
                                               echo (property_exists($ar_user_score_board[$level], 'mark') ) ? $ar_user_score_board[$level]->mark : 0;
                                            }else
                                            {
                                                echo (int) $userscore_data[$level]->mark;
                                            }
                                        ?>
                                    </span>
                                    <?php
                                    if (!empty($assessment->next_level)) {
                                        if ($level > $assessment->next_level) {
                                            $str_level_status = 'Complete '.$assessment->next_level;
                                            $url_level = '';
                                    ?>        <span class="level-status"><?php echo $str_level_status; ?></span>
                                    <?php    } else if ($level == $assessment->next_level) {
                                            $str_level_status = 'Play Now';
                                            $url_level = '/'.$assessment->next_level;
                                    ?>        <a href="<?php echo base_url($url . $url_level); ?>"><span class="level-status"><?php echo $str_level_status; ?></span></a>
                                    <?php    } else {
                                            $str_level_status = 'Completed';
                                            $url_level = '/' . $level;
                                    ?>        <span class="level-status"><?php echo $str_level_status; ?></span>
                                    <?php    }
                                        ?>

<!--                                        <a href="<?php echo base_url($url . $url_level); ?>"><span class="level-status"><?php echo $str_level_status; ?></span></a>-->

                                        <?php
                                    } else {
                                        $str_level_status = 'Play Again';
                                        $url_level = '/1';
                                        ?>
                                        <a href="<?php echo base_url($url . $url_level); ?>"><span class="level-status"><?php echo $str_level_status; ?></span></a>
                                        <?php break;
                                    } ?>
                                </p>
                            <?php } ?>
                            <div style="display: none;" id="current-level" data="<?php echo $cur_level; ?>"></div>
                            
                        </div>
                        <div style="margin-left: 50px; margin-top: 22px;position: absolute;display:none;" id="loader_icc">
                            <img src="/styles/layouts/tdsfront/images/loader.gif"></div>
                        <div class="assessment-save-score-wrapper"></div>
                        <div class="congratulation_text" style="width:80%;text-align:center;font-size: 20px;display:none;">Congratulation<br />All Stages Finished.Play Tomorrow.</div>
                    </div>
                </div>

                <div class="score-board-summary-wrapper" id="icc_uboard">
                    <div class="grand-score-board-summary">
                        <div class="grand-score-board-header f2">
                            My Total Score
                        </div>
                        <div class="grand-score-board-summary-text f2">
                            <?php
                                if($userscore_data!=false)
                                {   $utotal = 0;
                                    foreach ($userscore_data as $udata)
                                    {
                                        $utotal += $udata->mark;
                                    }
                                    echo $utotal;
                                }
                                else
                                {
                                    echo 0;//echo $assessment->total_score;
                                }
                                
                                ?>
                            
                            <?php  ?>                            
                        </div>
                        <div id="<?php echo (!$b_score_added_to_school) ? 'score-add-to-school' : ''; ?>" class="score-add-to-school f2"><?php echo (!$b_score_added_to_school) ? 'Add score to my school' : 'Score added to your school'; ?></div>
                        <div id="full_leader_board" class="school-position f2">Leader Board</div>
                        <div class="invite-friends f5">
                            <img src="/styles/layouts/tdsfront/image/icc-quiz-invite.png">
                            <p>Invite friends</p>
                        </div>
                    </div>
                </div>

                <div class="replay-wrapper">
                    <a href="<?php echo base_url($_SERVER['REQUEST_URI']); ?>"><img src="/styles/layouts/tdsfront/image/icc-quiz-play.png"></a>
                </div>

            </div>

            <div id="icc-quiz-content">
                <div class="col-md-9" style="display: none;">
                    <h1 data="<?php echo $assessment->id; ?>" style="font-size: 30px;" class="f2" id="assessment_title">
                        <span id="assessment_topic"><?php echo (!empty($assessment)) ? $assessment->topic : 'No Assessment'; ?></span> <span id="assessment_title_span" lpd="<?php echo (!$last_played) ? 'Not Available' : date("M j, y, g:i A", strtotime($last_played)); ?>" cp="<?php echo (!$can_play) ? '0' : '1'; ?>"><?php echo (!empty($assessment)) ? $assessment->title : 'No Assessment'; ?></span>
                    </h1>
                    <div style="clear: both;"></div>
                </div>
                <div class="col-md-3 social-bar">
                    <div class="clock"></div>
                </div>

                <!--            <div class="clearfix"></div>-->

                <div class="ques_id">
                    <?php
                    $i = 0;
                    $total_mark = 0;
                    foreach ($assessment->question as $question) {
                        $total_mark += $question->mark;
                        ?>

                        <div id="q_id-<?php echo $i; ?>" class="post materials_and_byline" style="display: <?php echo ($i == 0 ) ? 'block;' : 'none;'; ?>">

                            <div class="col-md-12 ques_id" data="<?php echo $question->id; ?>">
                                <h5 class="f2 question">
                                    <?php
                                    $k = ( strlen($i + 1) < 2 ) ? '0' . ($i + 1) : ($i + 1);
                                    ?>
                                    <span class="ques_no"><?php echo 'Question ' . $k; ?></span><span class="ques_text"><?php echo $question->question; ?></span>
                                </h5>
                            </div>

                            <div id="content" class="content-post">
                                <div class="answer-wrapper">
                                    <ul explanation="<?php echo (!empty($question->explanation)) ? $question->explanation : ''; ?>" time="<?php echo $question->time; ?>">
                                        <?php
                                        $j = 0;

                                        $li_style = ($question->style == 1) ? '' : 'style="float: none; width: 65%;"';

                                        foreach ($question->option as $option) {
                                            ?>
                                            <li data="<?php echo $option->id; ?>" <?php echo $li_style; ?> option="<?php echo $option->correct; ?>" mark="<?php echo $question->mark; ?>">
                                                <div class="opt-wrapper f2">
                                                    <div class="opt-num f2"><?php echo ucfirst(get_alphabets($j)); ?>.</div>
                                                    <div class="opt-ans f2"><?php echo $option->answer_webview; ?></div>
                                                </div>
                                            </li>
                                            <?php $j++;
                                        } ?>
                                    </ul>

                                </div>

                                <div style="clear: both; height: 2px;"></div>
                            </div>

                            <div style="clear:both;margin-top:20px;"></div>

                        </div>

                        <?php $i++;
                    } ?>

                    <input type="hidden" value="<?php echo $total_mark; ?>" id="total_mark">

                </div>
            </div>

        </div>
    </div>
    
    <div class="icc-quiz-start-screen-player">
        <img src="/styles/layouts/tdsfront/image/icc-quiz-bottom-player.png">
    </div>

    <div class="icc-quiz-start-screen-stamp">
        <img src="/styles/layouts/tdsfront/image/icc-quiz-stamp.png">
    </div>

</div>

<!--   Assessment Pop up     -->
<div id="assessment-popup-fancy" style="display: none;">
    <div id="assessment-popup-wrapper">

        <div class="assessment-popup-header">
            <div class="f2 assessment-popup-header-label"></div>
            <div class="assessment-popup-icon-wrapper">
                <img src="/styles/layouts/tdsfront/image/good_read_red_icon.png" width="75" />
            </div>

        </div>

        <div class="assessment-popup-body">

            <div class="assessment_custom_message"></div>
            <div class="assessment_common_message"></div>
            <div class="clearfix"></div>
            <div class="assessment-popup-btn-wrapper">
                <button class="red" type="button" id="start_assessment_now">
                    <span class="clearfix f2">
                        Start Now
                    </span>
                </button>
                <button class="red" type="button" id="full_leader_board">
                    <span class="clearfix f2">
                        Full Leader Board
                    </span>
                </button>
            </div>
        </div>

    </div>
</div>

<div class="assessment-popup-btn-wrapper-explanation" style="display: none;">
    <button class="red nxt-btn" type="button" id="assessment_next">
        <span class="clearfix f2">
            Next
        </span>
    </button>
</div>
<!--   Assessment Pop up     -->

<div id="assess_ladder_board">
    <div class="col-lg-12 ladder_board_title f2">
        Top 5
    </div>
    
    <div id="user_leader_board" class="ladder_board_wrapper">
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="f2">Individual</th>
                </tr>
                <tr>
                    <th class="f2">Name</th>
                    <th class="f2">Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($score_board as $sb) { ?>
                    <tr>
                        <td>
                            <div class="ladder_board_user_name f2">
                                <?php
                                $profile_img = base_url('styles/layouts/tdsfront/image/C.png');

                                if (!empty($sb->profile_image)) {
                                    $profile_img = $sb->profile_image;
                                }
                                ?>
                                <img src="<?php echo $profile_img; ?>">
                                <?php echo $sb->user_name; ?>
                            </div>
                            <div class="ladder_board_school_name f2"><?php echo $sb->school; ?></div>
                        </td>
                        <td>
                            <div class="ladder_board_mark f2"><?php echo $sb->mark; ?></div>
                            <div class="ladder_board_time f2"><?php echo gmdate('i:s', (int) $sb->time_taken); ?> Minute</div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <div id="school_leader_board" class="ladder_board_wrapper">
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="f2">School</th>
                </tr>
                <tr>
                    <th class="f2">Name</th>
                    <th class="f2">Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($school_score_board as $ssb) { ?>
                    <tr>
                        <td>
                            <div class="ladder_board_user_name f2">
                                <?php
                                $school_logo = base_url('styles/layouts/tdsfront/image/C.png');

                                if (!empty($ssb->school_logo)) {
                                    $school_logo = $ssb->school_logo;
                                }
                                ?>
                                <img src="<?php echo $school_logo; ?>">
                                <?php echo $ssb->school_name; ?>
                            </div>
                        </td>
                        <td>
                            <div class="ladder_board_mark f2"><?php echo $ssb->mark; ?></div>
                            <!--div class="ladder_board_time f2"><?php //echo gmdate('i:s', (int) $ssb->time_taken); ?> Minute</div-->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
</div>

<div class="clearfix"></div>
<script src="<?php echo base_url('scripts/FlipClock/compiled/flipclock.min.js') ?>"></script>
<script src="<?php echo base_url('scripts/layouts/tdsfront/js/quiz.js') ?>"></script>