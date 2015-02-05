<link rel="stylesheet" href="<?php echo base_url('scripts/FlipClock/compiled/flipclock.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/css/quiz.css') ?>">
<div style="width: 77%; min-height:250px;" class="container">

    <?php if (free_user_logged_in()) { ?>
        <input type="hidden" value="<?php echo get_free_user_session('id'); ?>" id="asses_id">
    <?php } ?>
    <input type="hidden" value="<?php echo (!empty($assessment->use_time) ) ? $assessment->time : 0; ?>" id="assess_time">
    <input type="hidden" value="<?php echo (!empty($assessment->higistmark) ) ? $assessment->higistmark : 0; ?>" id="highest_score">
    <input type="hidden" value="<?php echo (!empty($assessment->played) ) ? $assessment->played : 0; ?>" id="total_played">

    <?php if (!$b_explanation_popup) { ?>
        <input type="hidden" value="" id="b_explanation_popup">
    <?php } ?>

    <input type="hidden" value="" id="nos_questions">

    <div id="post_uri" style="display: none;">
        <?php echo $post_uri; ?>
    </div>

    <div style="padding: 5px 25px 0 25px;" class="sports-inner-news yesPrint">    
        <div style="float:left;">
            <a href="<?php echo '#'; ?>">
<!--            <a href="<?php //echo create_link_url(sanitize('assessment'));   ?>">-->
                <h1 style="color:#93989C;" class="title noPrint f2">
                    &nbsp;
                </h1>
            </a>
        </div>
        <div style="clear:both;"></div>
    </div>

    <div style="margin: 10px 20px;" class="inner-container">

        <div id="icc-quiz-start-screen">

            <div class="start_assessment_play_wrapper assessment-popup-btn-wrapper">
                <button type="button" id="start_assessment_play"></button>
            </div>

            <div class="icc-quiz-start-screen-text">
                <p class="f2">The ICC Cricket World Cup. The stage for greatest batsman, bowler, all rounders, wicket-keepers...and quizzers.<br />
                    Sign in now for a chance to grab the spotlight.</p>
            </div>

        </div>

        <div id="icc-quiz-start-play-screen">

            <div id="pre_assessment_details">

                <div class="score-board-summary">

                    <div class="score-board-header f2">
                        Today's Quiz
                    </div>

                    <div class="score-board-summary-text">
                        <p class="f2"></p>
                        <p class="f2">Total Score</p>
                        <p class="f2">Highest Score&nbsp;: <?php echo (!empty($assessment->higistmark) ) ? $assessment->higistmark : 0; ?></p>
                        <p class="f2">Quiz Time&nbsp;: <?php echo (!empty($assessment->use_time) ) ? $assessment->time : 0; ?> Minute</p>
                        <p class="f2">Total Played&nbsp;: <?php echo (!empty($assessment->played) ) ? $assessment->played : 0; ?></p>
                    </div>

                    <div class="assessment-popup-btn-wrapper">
                        <button class="icc-quiz-play-2" type="button" id="start_assessment_now"></button>
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
                            <?php foreach ($score_board as $sb) { ?>
                                <li><span class="user_name f5"><?php echo $sb->user_name; ?></span>&nbsp;<span class="mark f5"><?php echo $sb->mark; ?></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="icc-quiz-game-over">
            <div class="icc-quiz-game-over-header f2">Game Over</div>
            
            <div class="score-board-summary-wrapper">
                <div class="score-board-summary">

                    <div class="score-board-header f2">
                        Your Score Today
                    </div>

                    <div class="score-board-summary-text">
                        <?php
                            $ar_assess_levels = explode(',', $assessment->levels);
                            foreach ($ar_assess_levels as $level) { ?>
                                <p class="f2">Stage <?php echo $level; ?> : 
                                    <?php
                                        $score = 0;
                                        $str_level_status = 'Play Again';
                                        if($level > $assessment->next_level) {
                                            $str_level_status = 'Locked';
                                        }
                                        else if($level == $assessment->next_level) {
                                            $str_level_status = 'Play Now';
                                            $score = '';
                                        }
                                    ?>
                                    <?php echo $score; ?>
                                    <span class="level-status"><?php echo $str_level_status; ?></span>
                                </p>
                        <?php } ?>
                    </div>

                    <div class="assessment-popup-btn-wrapper"></div>

                </div>
            </div>
            
            
            <div class="score-board-summary-wrapper">
                <div class="grand-score-board-summary">

                    <div class="grand-score-board-header f2">
                        Your Total Score
                    </div>

                    <div class="grand-score-board-summary-text f2">
                        <?php echo $assessment->total_score; ?>
                    </div>

                    <div class="score-add-to-school f2">Add score to your school   </div>
                    <div class="school-position f2">Your schools's position</div>
                    <div class="invite-friends f5">
                        <img src="/styles/layouts/tdsfront/image/icc-quiz-invite.png">
                        <p>Invite friends</p>
                        <p>to play</p>
                    </div>

                </div>
            </div>
            
            <div class="replay-wrapper">
                <img src="/styles/layouts/tdsfront/image/icc-quiz-play.png">
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

                                    $li_style = ($question->style == 1) ? '' : 'style="float: none; width: 90%;"';

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
    <table>
        <thead>
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

<script src="<?php echo base_url('scripts/FlipClock/compiled/flipclock.min.js') ?>"></script>
<script src="<?php echo base_url('scripts/layouts/tdsfront/js/quiz.js') ?>"></script>