$(document).ready(function(){
    
    if($('#asses_id').length > 0) {
        
        var assess_score_cookie = readCookie('c21_icc_quiz');
        
        var cur_level = 0;
        if($('#current-level').length > 0) {
            cur_level = $('#current-level').attr('data');
        }
        
        if(assess_score_cookie) {
            
            $.ajax({
                url : $('#base_url').val() + 'save_assessment',
                type : 'post',
                dataType : 'json',
                data : {
                    data : assess_score_cookie,
                    add_to_school : false,
                    cur_level : cur_level
                },
                success : function(data) {
                    if(data.saved == true) {
                        eraseCookie('c21_icc_quiz');
                        
                        var url = window.location.href;
                        var i = url.lastIndexOf('/');
                        if (i != -1) {
                            var next_level = parseInt(cur_level) + 1;
                            url = url.substr(0, i) + "/" + next_level;
                        }
                        window.location.href = url;
                    }
                    
                },
                error : function() {}
            });
        }
    }
    
    if($('#icc_utotal').val() == "done")
    {
        $("#icc-quiz-start-screen").css("display","none");
        $(".icc-quiz-game-over").css("display","block");
        $(".replay-wrapper").hide();
        $("#score_board_icc").hide();
        $("#icc_uboard").css("width","50%");
        $("#icc_uboard").css("margin","0px auto");
        $("#icc_uboard").css("text-align","center");
        $("#icc_uboard").css("float","none");
    }
    
    var user_score = 0;
    var time_up = false;
    var total_time_taken = 0;
    var assess_finished = false;
    var num_assessments = 0;
        
    var clock = $('.clock').FlipClock({
        clockFace: 'MinuteCounter',
        countdown: true,
        autoStart: false,
        callbacks: {
            stop: function() {
                
                if(clock.time == 0) {
                    time_up = true;
                    
                    var next_question_id = 0;
                    
                    $('.materials_and_byline').each(function() {
                        if( ( $(this).attr('style').indexOf('display: block;') != -1 ) ||( $(this).attr('style').indexOf('opacity: 1;') != -1 ) ) {
                
                            next_question_id = parseInt($(this).attr('id').split('-')[1]) + 1;
                        }
                    });
                    
                    clock.setOption('next_q_id', next_question_id);
                }
                
                $('#assessment_next').attr('nxt_q_id', clock.next_q_id.toString());
                
                var ques_time = get_ques_time(clock.current_q_id);
                var clock_time = parseInt(clock.time);
                var time_taken = ques_time - clock_time;
                total_time_taken += time_taken;
                
                if(clock.next_q_id == num_assessments) {
                    assess_finished = true;
                } else {
                    time_up = false;
                }
                
                if(assess_finished == false) {
                    
                    var ques_time = get_ques_time(clock.next_q_id);
                    clock.setTime(ques_time);
                    
                    $('#assessment_next').trigger('click');
                } else {
                    
                    $('.assessment-popup-btn-wrapper').html('');
                    
                    $('.nxt-btn').removeAttr('id');
                    $('.nxt-btn').addClass('show-assessment-score');
                    $('.nxt-btn').attr('data', 'assessment_score');
                    
                    var assess_score = setTimeout(function(){
                        $('.show-assessment-score').trigger('click');
                        clearTimeout(assess_score);
                    }, 50);
                }
                
            }
        }
    });
    
    $('.materials_and_byline').each(function() {
             
        num_assessments = num_assessments + 1;
        
        if( $(this).attr('style').indexOf('display: block;') != -1 ) {
                    
            var height = 0;
            $(this).find('.content-post .answer-wrapper ul li').each(function() {
                        
                if ($(this).height() > $(this).next().height()) {
                    height = $(this).height();
                } else {
                    height = $(this).next().height();
                }
            });
                    
            $(this).find('.content-post .answer-wrapper ul li').height(height);
        }
    });
    
    $('#nos_questions').val(num_assessments);
        
    var key = 'asssessment_start_now';
    
    var extra_html = '<p class="f2">Last Played: ' + $('#assessment_title_span').attr('lpd') + '</p>';
    var cp = '';
    
    if($('#assessment_title_span').attr('cp') == 0 ) {
        cp = '<p class="f2 cann_play">*** You should not participate in the same quiz within 24 hour. You&rsquo;ll not be able to save your score.</p>';
    }
    
    if(cp != '') {
        extra_html += cp;
    }
        
    $(document).off('click').on('click', '#assessment_next', function(){
        
        var next_q_id = $(this).attr('nxt_q_id');
        var curr_q_id = parseInt(next_q_id) - 1;
        
        $('#q_id-'+curr_q_id).hide('slow', function() {
            $('#q_id-'+curr_q_id).attr('style', 'display: none;');
        });
        
        $('#q_id-'+next_q_id).show('slow', function(){
            $('#q_id-'+next_q_id).attr('style', 'display: block;');
                        
            var set_height = setTimeout(function() {
                            
                if( ($('#q_id-'+next_q_id).attr('style').indexOf('display: block;') != -1 ) || ($('#q_id-'+next_q_id).attr('style').indexOf('opacity: 1;') != -1) ) {
                    
                    var height = 0;
                    $('#q_id-'+next_q_id).find('.content-post .answer-wrapper ul li').each(function() {
                                    
                        if ($(this).height() > $(this).prev().height()) {
                            height = $(this).height();
                        } else {
                            height = $(this).prev().height();
                        }
                    });
                    
                    $('#q_id-'+next_q_id).find('.content-post .answer-wrapper ul li').height(height);
                }
                clearTimeout(set_height);
            }, 1);
            
            var start_clock = setTimeout(function() {
                clock.start();
                clearTimeout(start_clock);
            }, 700);
        });
        
    });
            
    $(document).on('click', '#friends-fb', function() {
        FB.ui({
            method: 'send',
//            link: 'http://www.champs21.com' + window.location.pathname
            link: window.location.href
        });
    });
    
    $(document).on('submit', 'form#frm-invite_friend', function(event) {
        
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url : $('#base_url').val() + 'invite_friend_by_email',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success : function(data) {
                console.log(data);
            },
            error : function() {}
        });
        
    });
    
    $(document).on('click', '#friends-email', function() {
        $('.assessment_custom_message').html('');
        var html_email = '<form id="frm-invite_friend"><div style="margin: 3px 0 20px;">' +
                            '<label class="f2">Full Name&nbsp;:&nbsp;</label>' +
                            '<input id="friend_name" style="width: 75%; float: right; border-radius: 5px;" type="text" name="friend_name" value="">' +
                        '</div>' +
                        '<div style="margin: 3px 0 20px;">' +
                            '<label class="f2">Email&nbsp;:&nbsp;</label>' +
                            '<input id="friend_email" style="width: 75%; float: right; border-radius: 5px;" type="text" name="friend_email" value="">' +
                        '</div>' +
                        '<div class="friends-email-invite"><button type="submit" class="f2">Send Invitation</button></div></form>';
        $('.assessment_custom_message').html(html_email);
    });
    
    $(document).on('click', '.invite-friends', function() {
        
        var key = 'asssessment_invite_friends';
        var pop_up_data =  get_popup_data(key, '');
        
        $('.assessment-popup-btn-wrapper').html('');
        
        $('#assessment-popup-wrapper').css('width', '450px');
        $('.assessment-popup-header-label').html('');
        $('.assessment-popup-header-label').html(pop_up_data.header_label);

        $('.assessment-popup-icon-wrapper').html('');
        $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');

        $('.assessment_custom_message').html('');
        $('.assessment_custom_message').html(pop_up_data.custom_message);

        var html_before_login_popup = $('#assessment-popup-fancy').html();

        $.fancybox({
            'content' : html_before_login_popup,
            'width': 718,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : true,
            'autoSize' : true,
            'closeClick'  : false,
            'padding': 0,
            'margin': 0
        });
    });
    
    $(document).on('click', '.btn-assessment-submit', function() {
        
        var assessment = get_user_score(total_time_taken);
        var cur_level = 0;
        if($('#current-level').length > 0) {
            cur_level = $('#current-level').attr('data');
        }
        
        $(".btn-assessment-submit").attr("disabled","disabled");
        $(".btn-assessment-submit").css('background-color', '#cccccc');
        $("#loader_icc").show();
        
        $.ajax({
            url : $('#base_url').val() + 'save_assessment',
            type : 'post',
            dataType : 'json',
            data : {
                data : assessment,
                add_to_school : false,
                cur_level : cur_level
            },
            success : function(data) {
                
                var site_url_icc = $('#base_url').val();
                $('.icc-quiz-game-over .grand-score-board-summary-text').text(data.user_total_score);
                $("#loader_icc").hide();
                $(".btn-assessment-submit").css('display', 'none');
                
                if(data.next_level == 0 ) {
                    
                    $(".replay-wrapper").hide();
                    $(".congratulation_text").show();
                    
                } else {
                    $(".replay-wrapper").find("a").attr("href", site_url_icc + 'cricaddict/' + data.assessment_title + '-' + data.assessment_type + '-' + data.assessment_id + "/" + data.next_level);
                }
                
                var quiz_levels = data.assessment_levels.split(',');
                var levels_html = '';
                var i = 1;
                
                if( $('.icc-quiz-game-over .score-board-summary-text p').length < quiz_levels.length) {
                    quiz_levels.forEach(function(level) {
                        
                        if(level > cur_level) {
                            
                            var level_str = '';
                            var label_str = 'Complete '+(parseInt(level)-1);
                            
                            if(data.next_level == level) {
                                level_str = '/' + level;
                                label_str = 'Play Now';
                            }
                            
                            if(level_str == "") {
                                levels_html += '<p class="f2">Stage ' + level + ' : <span id="level-' + level + '">0</span><span class="level-status">' + label_str + '</span></p>';
                            } else {
                                levels_html += '<p class="f2">Stage ' + level + ' : <span id="level-' + level + '">0</span><a href="' + $('#base_url').val() + 'quiz/' + data.assessment_title + '-' + data.assessment_type + '-' + data.assessment_id + level_str + '"><span class="level-status">' + label_str + '</span></a></p>';
                            }
                            
                            i++;
                            
                        } else {
                            
                            if(level == cur_level) {
                                $('#level-' + cur_level).text(data.score);
                                $('#level-' + cur_level).parent('p').find('a').replaceWith('<span class="level-status">Completed</span>');
                            }
                        }
                    });
                    
                } else {
                    if( $('.icc-quiz-game-over .score-board-summary-text p').length == quiz_levels.length) {
                        quiz_levels.forEach(function(level) {
                            
                            if(level == cur_level) {   
                                $('#level-' + cur_level).text(data.score);
                                $('#level-' + cur_level).parent('p').find('a').replaceWith('<span class="level-status">Completed</span>');
                            }
                            
                            if(data.next_level == level) {   
                                var level_str = '/' + level;
                                var label_str = 'Play Now';
                                $('#level-' + data.next_level).text(0);
                                $('#level-' + data.next_level).parent('p').find('span.level-status').replaceWith('<a href="' + $('#base_url').val() + 'quiz/' + data.assessment_title + '-' + data.assessment_type + '-' + data.assessment_id + level_str + '"><span class="level-status">' + label_str + '</span></a>');
                            }
                        });
                    }
                }
                
                $('.icc-quiz-game-over .score-board-summary-text').append(levels_html);
                
            },
            error : function() {}
        });
           
    });
    
    $(document).on('click', '#score-add-to-school', function() {
        
        var assessment = get_user_score(total_time_taken);
        var cur_level = 0;
        if($('#current-level').length > 0) {
            cur_level = $('#current-level').attr('data');
        }
        
        $.ajax({
            url : $('#base_url').val() + 'save_assessment',
            type : 'post',
            dataType : 'json',
            data : {
                data : assessment, 
                add_to_school : true,
                cur_level : cur_level
            },
            success : function(data) {
                
                if(data.has_school === false) {
                    createCookie('c21_icc_quiz_level', cur_level, 1);
                    createCookie('c21_icc_quiz', assessment, 1);
                    window.location.href = $('#base_url').val() + 'schools';
                } else {
                    $('.grand-score-board-summary-text').html(data.user_total_score);
                }
                $('.score-add-to-school').text('Score added to your school');
                $('.score-add-to-school').attr('id', '');
            },
            error : function() {}
        });
           
    });
    
    $(document).on('click', '.answer-wrapper ul li', function(){
        
        if(time_up){
            return false;
        }
        
        var current = $(this).parent('ul').parent('.answer-wrapper').parent('.content-post').parent('.materials_and_byline');
        var current_id = current.attr('id').split('-')[1];
        var current_q_id = parseInt(current_id);
        var next_q_id = current_q_id + 1;
        $('#assessment_next').attr('nxt_q_id', next_q_id.toString());
        
        clock.setOptions({
            'current_q_id' : current_q_id,
            'next_q_id' : next_q_id
        });
        
        clock.stop();
        
        var checked = false; 
        var this_ul = $(this).parent('ul');
        
        this_ul.find('li').each(function() {
            var this_checked = $(this).attr('checked');
            if(typeof this_checked !== typeof undefined && this_checked !== false) {
                checked = true;
                return;
            }
        });
        
        if(!checked) {
            $(this).attr('checked', 'checked');
            $(this).css('background-color', '#FFFDDC');
            
            var correct = $(this).attr('option');
            
            if(correct == '1') {
                var ques_mark = parseInt($(this).attr('mark'));
                user_score += ques_mark;
            } 
        }
    });
    
    $(document).on('click', '.show-assessment-score', function(){
        
        var last_q_time = 0;
        var user_clicked = false;
        
        $('.materials_and_byline:visible .content-post .answer-wrapper ul li').each(function() {
            if($(this).attr('checked') == 'checked') {
                user_clicked = true;
            }
        });
        
        if(!user_clicked) {
            last_q_time = get_ques_time($('.materials_and_byline:visible').attr('id').split('-')[1]) - parseInt(clock.time);
        }
        
        total_time_taken += last_q_time;
        
        $('.nxt-btn').removeClass('show-assessment-score');
        $('.nxt-btn').removeClass('red');
        $('.nxt-btn span').text('Save');
        $('.nxt-btn').removeAttr('id');
        
        $('.nxt-btn').addClass('purple');
        
        get_user_score(total_time_taken);
        
        if($('#asses_id').length < 1) {
            $('.nxt-btn').addClass('before-login-user');
            $('.nxt-btn').attr('data', 'assessment_save_score');
            
            $('.score-add-to-school').addClass('before-login-user');
            $('.score-add-to-school').attr('data', 'assessment_save_score');
            
            $('.grand-score-board-summary-text').html(user_score);
            
        } else {
            $('.nxt-btn').addClass('btn-assessment-submit');
        }
        
        var cur_level = 0;
        if($('#current-level').length > 0) {
            cur_level = $('#current-level').attr('data');
        }
        var next_level = parseInt(cur_level) + 1;
        
        $('#level-' + cur_level).text(user_score);
        $('#level-' + cur_level).parent('p').find('a span').text('Play Again');
        
        if($('#level-' + next_level).length > 0){
            var cur_url = $('#level-' + next_level).parent('p').find('a').attr('href');
            var next_url = cur_url + '/' + next_level;
            
            $('#level-' + next_level).parent('p').find('a').attr('href', next_url);
            $('#level-' + next_level).parent('p').find('a span').text('Play Now');
        }
        
        var btn_html = $('.assessment-popup-btn-wrapper-explanation').children().eq(0);
        $('.assessment-save-score-wrapper').html(btn_html);
        
        $('#icc-quiz-content').hide('fast');
        $('.icc-quiz-game-over').show('slow');
      
    });
    
    $(document).on('click', '#assessment_explanation', function(){
        
        var key = 'assess_explanation';
        var question = '';
        var explanation = '';
        var correct_html = '';
        
        $('.materials_and_byline').each(function() {
            
            if( ($(this).attr('style').indexOf('display: block;') != -1) || ($(this).attr('style').indexOf('opacity: 1;') != -1) ) {
                
                question = '<p class="f2"><span style="color: #DC3131;">Question: </span>' + $(this).find('.ques_id .question .ques_text').text() + '</p>';
                explanation = '<p class="f2">Explanation: ' + $(this).find('.content-post .answer-wrapper ul').attr('explanation') + '</p>';
                $(this).find('.content-post .answer-wrapper ul li').each(function() {
                    var option = $(this).attr('option');
                    //                    var opt_num = $(this).find('.opt-wrapper .opt-num').text();
                    
                    if(option == 1) {
                        correct_html = '<p class="f2">Right answer is : ' + $(this).find('.opt-wrapper .opt-ans').text() + '</p>';
                    }
                    
                });
            }
        });
        
        var explanation_html = question + correct_html + explanation;
        
        var pop_up_data =  get_popup_data(key, explanation_html);
        
        $('#assessment-popup-wrapper').css('width', '450px');
        $('.assessment-popup-header-label').html('');
        $('.assessment-popup-header-label').html(pop_up_data.header_label);
        
        $('.assessment-popup-icon-wrapper').html('');
        $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');
        
        $('.assessment_custom_message').html('');
        $('.assessment_custom_message').html(pop_up_data.custom_message);
    });
    
    $(document).on('click', '#icc-quiz-start-screen', function(){
        
        $('.inner-container').css('background-color', 'rgba(1, 1, 1, 0.45)');
        $('.inner-container').css('border-radius', '5px');
        $('.inner-container').css('min-height', '530px');
        $('.inner-container').css('margin-bottom', '20px');
        $('.flip-clock-label').css('color', '#ffffff');
        $('.icc-quiz-start-screen-player').css('display', 'none');
        $('.icc-quiz-start-screen-stamp').css('display', 'none');
        
        $('.score-board-summary-text').children('p').eq(0).html('Total Score&nbsp;: '+ $('#total_mark').val());
        $('.score-board-summary-text').children('p').eq(1).html('No. of Question&nbsp;: '+num_assessments);
        
        $('#icc-quiz-start-play-screen').show('slow');
        $('#icc-quiz-start-screen').hide('fast');
    });
    
    $(document).on('click', '#show_assessment_rules', function(){
        var lb_old_content = $('#leader_board').html();
        var lb_new_content = $('.icc-quiz-rules-wrapper').html();
        $('#leader_board').html(lb_new_content);
        
    });
    
    $(document).on('click', '#start_assessment_now', function(){
        
        if($(window).width() < 768) {
            $('.icc-quiz-top-header').addClass('hide-icc-quiz-top-header');
        }
        
        if($('#assessment_title_span').attr('cp') == 0 ) {
            return false;
        }
        
        $('.inner-container').css('background-color', 'rgba(1, 1, 1, 0.65)');
        
        var ques_time = get_ques_time(0);
        time_up = false;
        clock.setTime(ques_time);
        
        $('#icc-quiz-content').show('slow');
        $('#icc-quiz-start-play-screen').hide('fast');
        
        clock.start();
    });
    
    $(document).on('click', '#full_leader_board', function(){
        
        var assessment_id = $('#assessment_title').attr('data');
        
        $.ajax({
            url : $('#base_url').val() + 'assessment_leader_board',
            type : 'post',
            dataType : 'json',
            data : {
                assessment_id : assessment_id,
                type : 2
            },
            success : function(data) {
                
                var response = JSON.parse(data.leader_board);
                var lb_rows = ''
                var s_lb_rows = ''
                
                $.each(response.data.assesment,function(k, v) {
                    
                    var time = parseInt(v.time_taken);
                    var minutes = Math.floor(time / 60);
                    var seconds = time - minutes * 60;
                    var profile_img = $('#base_url').val() + 'styles/layouts/tdsfront/image/C.png';
                    
                    if(v.profile_image != '') {
                        profile_img = v.profile_image;
                    }
                    
                    lb_rows += '<tr>' +
                    '<td>' +
                    '<div class="ladder_board_user_name f2">' +
                    '<img src="' + profile_img + '">' +
                    v.user_name +
                    '</div>' +
                    '<div class="ladder_board_school_name f2">'+ v.school +'</div>' +
                    '</td>' +
                    '<td>' +
                    '<div class="ladder_board_mark f2">'+ v.mark +'</div>' +
                    '<div class="ladder_board_time f2">' + minutes + ':' + seconds + ' Minute</div>' +
                    '</td>' +
                    '</tr>';
                    
                });
                
                $.each(response.data.school_score_board,function(k, v) {
                    
                    var school_logo = $('#base_url').val() + 'styles/layouts/tdsfront/image/C.png';
                    
                    if(v.school_logo != '') {
                        school_logo = $('#base_url').val() + v.school_logo;
                    }
                    
                    s_lb_rows += '<tr>' +
                    '<td>' +
                    '<div class="ladder_board_user_name f2">' +
                    '<img src="' + school_logo + '">' +
                    v.school_name +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<div class="ladder_board_mark f2">'+ v.mark +'</div>' +
                    '</td>' +
                    '</tr>';
                    
                });
                
                $('.ladder_board_title').text('');
                $('.ladder_board_title').html('Top 100');
                
                $('#user_leader_board table tbody').html('');
                $('#user_leader_board table tbody').html(lb_rows);
                
                $('#school_leader_board table tbody').html('');
                $('#school_leader_board table tbody').html(s_lb_rows);
                
                var pop_up_data = get_popup_data('assess_full_leader_board', '');
                
                $('.assessment-popup-btn-wrapper').html('');
                
                var btn_html = '';
                
                $('#assessment-popup-wrapper').css('width', '100%');
                $('.assessment-popup-header-label').html('');
                $('.assessment-popup-header-label').html(pop_up_data.header_label);
        
                $('.assessment-popup-icon-wrapper').html('');
                $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');
        
                $('.assessment_custom_message').html('');
                $('.assessment_custom_message').html(pop_up_data.custom_message);
            
                $('.assessment-popup-btn-wrapper').html('');
                $('.assessment-popup-btn-wrapper').html(btn_html);
        
                var html_before_login_popup = $('#assessment-popup-fancy').html();

                $.fancybox({
                    'content' : html_before_login_popup,
                    'width': '90%',
                    'transitionIn': 'fade',
                    'transitionOut': 'fade',
                    'openEffect': 'elastic',
                    'openSpeed' : 350,
                    'fitToView' : true,
                    'autoSize' : false,
                    'closeClick'  : false,
                    'padding': 0,
                    'margin': 0
                });
                
            },
            error : function() {}
        });
    });
            
//    var myEvent = window.attachEvent || window.addEventListener;
//    var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compatable
// 
//    myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox
//        var confirmationMessage = 'Do you want to leave the page? You may lose assessment progress.';
//        (e || window.event).returnValue = confirmationMessage;
//        return confirmationMessage;
//    });
   
});

function get_popup_data(key, explanation){
    
    if(explanation === '') {
        explanation = 'Sorry! No explanation available at the moment.';
    }
    
    var pop_up_data = {
        'asssessment_start_now' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Quiz',
            'custom_message' : '<div id="pre_assessment_details"><p class="f2">No. of Question&nbsp;: ' + $('#nos_questions').val() + '</p><p class="f2">Total Score&nbsp;: ' + $('#total_mark').val() + '</p><p class="f2">Highest Score&nbsp;: ' + $('#highest_score').val() + '</p><p class="f2">Quiz Time&nbsp;: ' + $('#assess_time').val() + ' : 00 Minute</p><p class="f2">Total Played&nbsp;: ' + $('#total_played').val() + '</p>' + explanation + '</div><div id="leader_board" >' + $('#assess_ladder_board').html() + '</div>'
        },
        'assess_wrong' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Quiz',
            'custom_message' : ''
        },
        'assess_correct' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Quiz',
            'custom_message' : ''
        },
        'assessment_score' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Game Over',
            'custom_message' : explanation
        },
        'assess_time_out' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Game Over',
            'custom_message' : '<p class="f2" style="color: #999; font-size: 30px; font-weight: 900; letter-spacing: 3px; text-align: center;">YOUR SCORE IS</p><p style="color: #000; font-size: 70px; font-weight: 900; letter-spacing: -1; margin: 35px 0; text-align: center; "> '+ explanation + ' / ' + $('#total_mark').val() + '</p>'
        },
        'ques_time_up' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Time Up',
            'custom_message' : '<p class="f2" style="text-align: center;">Oops! Time up</p>'
        },
        'assess_full_leader_board' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Leader Board',
            'custom_message' : '<div class="full_leader_board_wrapper" id="leader_board">' + $('#assess_ladder_board').html() + '</div>'
        },
        'asssessment_invite_friends' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Invite Friends',
            'custom_message' : '<div class="friends-fb f2" id="friends-fb">Invite Facebook Friends</div><div class="friends-email f2" id="friends-email">Email Friend</div>'
        },
        'assess_explanation' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Explanation',
            'custom_message' : '<p class="f2">' + explanation + '</p>'
        }
    };
    
    return pop_up_data[key];
}

function populate_assessment_summary() {
    
    var assessment_summary_html = '<table><thead><tr><th class="f2">Question</th><th></th><th class="f2">Correct Answer</th><th class="f2">Explanation</th></tr></thead><tbody>';
    $('.ques_text');
    //.content-post .answer-wrapper ul li
    $('.materials_and_byline').each(function() {
        var ques = '<p class="f2">' + $(this).find('.ques_text').text() + '</p>';
        
        var explanation = '<p class="f2">' + $(this).find('.content-post .answer-wrapper ul').attr('explanation') + '</p>';
        var correct_ans = '';
        var user_ans = '';
        var mark = 0
        
        $(this).find('.content-post .answer-wrapper ul li').each(function() {
            var option = $(this).attr('option');
            mark = '<p class="f2">Mark: ' + $(this).attr('mark') + '</p>';

            if(option == 1) {
                correct_ans = '<p class="f2">' + $(this).find('.opt-wrapper .opt-ans').text() + '</p>';
                
                if ($(this).attr('checked') == 'checked') {
                    user_ans = 'right.png';
                } else {
                    user_ans = 'wrong.png';
                }
            }
            
        });
        console.log(user_ans);
        assessment_summary_html += '<tr><td><div class="ladder_board_user_name f2">' + ques + '</div></td><td><img src="/styles/layouts/tdsfront/image/' + user_ans + '"></td><td><div class="ladder_board_mark f2">' + correct_ans + '</div></td><td><div class="ladder_board_mark f2">' + explanation + '</div></td></tr>';
        
    });
    
    assessment_summary_html += '</tbody></table>';
    
    return assessment_summary_html;
}

function get_user_score(total_time_taken) {
    
    var assessment = $('#assessment_title').attr('data') + '_';
    
    if(total_time_taken > 0) {
        assessment += total_time_taken + '_';
    }
    
    $('.materials_and_byline .content-post .answer-wrapper ul li').each(function() {
        if($(this).attr('checked') == 'checked') {
            var q_id = $(this).parent('ul').parent('.answer-wrapper').parent('.content-post').siblings('.ques_id').attr('data');
            var a_id = $(this).attr('data');
            
            assessment += q_id + '-' + a_id + ',';
        }
    });
    
    if($('#asses_id').length < 1) {
        createCookie('c21_icc_quiz', assessment, false);
    }
    
    return assessment;
}

function get_ques_time(ques_id) {
        
    var ques_time = $('#q_id-' + ques_id).find('.content-post .answer-wrapper ul').attr('time');
    
    return parseInt(ques_time);
//    return 5;
}