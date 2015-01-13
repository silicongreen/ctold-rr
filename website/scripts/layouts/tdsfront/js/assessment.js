$(document).ready(function(){
    
    if($('#asses_id').length > 0) {
        
        var assess_score_cookie = readCookie('c21_assessment');
        if(assess_score_cookie) {
            $.ajax({
                url : $('#base_url').val() + 'save_assessment',
                type : 'post',
                dataType : 'json',
                data : {
                    data : assess_score_cookie
                },
                success : function(data) {
                    if(data.saved == true) {
                        eraseCookie('c21_assessment');
                    }
                },
                error : function() {}
            });
        }
    }
    
    var user_score = 0;
    var time_up = false;
    var assessment_time = 10;
    var total_time_taken = 0;
    var assess_finished = false;
    var num_assessments = 0;
    //        var assessment_time = parseInt($('#assess_time').val()) * 60 - 1;
        
    var clock = $('.clock').FlipClock({
        clockFace: 'MinuteCounter',
        countdown: true,
        autoStart: false,
        callbacks: {
            stop: function() {
                
                time_up = true;
                
                var html_expl_nxt_popup = '';
                var key = '';
                var btn_html = '';
                var pop_up_data = '';
                
                console.log(time_up);
                console.log(assess_finished);
                
                if( (assess_finished == true) && (time_up == false) ) {
                    
                    key = 'assessment_score';
                    
                    $('.assessment-popup-btn-wrapper').html('');
                    
                    $('.nxt-btn span').text('View Score');
                    $('.nxt-btn').removeAttr('id');
                    $('.nxt-btn').addClass('show-assessment-score');
                    $('.nxt-btn').attr('data', 'assessment_score');
                    
                } else if( (assess_finished == true) && (time_up == true) ) {
                    
                    key = 'ques_time_up';
                    
                    $('.assessment-popup-btn-wrapper').html('');
                    
                    $('.nxt-btn span').text('View Score');
                    $('.nxt-btn').removeAttr('id');
                    $('.nxt-btn').addClass('show-assessment-score');
                    $('.nxt-btn').attr('data', 'assessment_score');
                    
                } else {
                    
                    var next_question_id = 0;
                    
                    $('.materials_and_byline').each(function() {
            
                        if( ($(this).attr('style').contains('display: block;')) ||($(this).attr('style').contains('opacity: 1;')) ) {
                
                            next_question_id = parseInt($(this).attr('id').split('-')[1]) + 1;
                        }
                    });
                    
                    $('#assessment_next').attr('nxt_q_id', next_question_id.toString());
                    
                    key = 'ques_time_up';
                    
                    if(next_question_id == num_assessments) {
                        
                        $('.assessment-popup-btn-wrapper').html('');
                    
                        $('.nxt-btn span').text('View Score');
                        $('.nxt-btn').removeAttr('id');
                        $('.nxt-btn').addClass('show-assessment-score');
                        $('.nxt-btn').attr('data', 'assessment_score');
                    }
                }
                
                btn_html = $('.assessment-popup-btn-wrapper-explanation').html();
                pop_up_data = get_popup_data(key, user_score);
                
                $('#assessment-popup-wrapper').css('width', '450px');
                $('.assessment-popup-header-label').html('');
                $('.assessment-popup-header-label').html(pop_up_data.header_label);
                    
                $('.assessment-popup-icon-wrapper').html('');
                $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');
        
                $('.assessment_custom_message').html('');
                $('.assessment_custom_message').html(pop_up_data.custom_message);
            
                $('.assessment-popup-btn-wrapper').html('');
                $('.assessment-popup-btn-wrapper').html(btn_html);
        
                html_expl_nxt_popup = $('#assessment-popup-fancy').html();
                
                $.fancybox({
                    'content' : html_expl_nxt_popup,
                    'width': 450,
                    'transitionIn': 'fade',
                    'transitionOut': 'fade',
                    'openEffect': 'elastic',
                    'openSpeed' : 350,
                    'fitToView' : true,
                    'autoSize' : true,
                    'padding': 0,
                    'margin': 0
                });
            }
        }
    });
    
    $('.materials_and_byline').each(function() {
             
        num_assessments = num_assessments + 1;
        
        if( $(this).attr('style') == 'display: block;' ) {
                    
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
            
    var pop_up_data =  get_popup_data(key, '');
    
    $('#assessment-popup-wrapper').css('width', '700px');
    $('.assessment-popup-header-label').html('');
    $('.assessment-popup-header-label').html(pop_up_data.header_label);
            
    $('.assessment-popup-icon-wrapper').html('');
    $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');
            
    $('.assessment_custom_message').html('');
    $('.assessment_custom_message').html(pop_up_data.custom_message);
            
    var html_before_login_popup = $('#assessment-popup-fancy').html();
    
    $.fancybox({
        'content' : html_before_login_popup,
        'width': 700,
        'transitionIn': 'fade',
        'transitionOut': 'fade',
        'openEffect': 'elastic',
        'openSpeed' : 350,
        'fitToView' : true,
        'autoSize' : true,
        'padding': 0,
        'margin': 0
    });
        
    var assessment_next_clicked = 0;
    $(document).off('click').on('click', '#assessment_next', function(){
        
        assessment_next_clicked++;
        
        var next_q_id = $(this).attr('nxt_q_id');
        var curr_q_id = parseInt(next_q_id) - 1;
        
        var ques_time = get_ques_time(next_q_id);
        clock.setTime(ques_time);
        clock.start(function() {
            time_up = false;
        });
        
        $('#q_id-'+curr_q_id).hide('slow', function() {
            $('#q_id-'+curr_q_id).attr('style', 'display: none;');
        });
        
        $('#q_id-'+next_q_id).show('slow', function(){
            $('#q_id-'+next_q_id).attr('style', 'display: block;');
                        
            var set_height = setTimeout(function() {
                            
                if( ($('#q_id-'+next_q_id).attr('style').contains('display: block;')) || ($('#q_id-'+next_q_id).attr('style').contains('opacity: 1;')) ) {
                    
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
            
            $.fancybox.close();
            $('.assessment-popup-btn-wrapper').html('');
        });
        
    });
            
    /*$(document).on('click', '.assessment-previous', function(){
            
        $('.assessment-submit').hide('slow');
                
        var current = $(this).parent('.assessment-next-previous').parent('.answer-wrapper').parent('.content-post').parent('.materials_and_byline');
        var next = current.prev('.materials_and_byline');
        has_next = next.length;
                
        if(has_next > 0) {
            current.hide('slow', function() {
                current.attr('style', 'display: none;');
            });
            next.show('slow', function(){
                next.attr('style', 'display: block;');
                        
                var set_height = setTimeout(function() {
                            
                    if( (next.attr('style').contains('display: block;')) ||(next.attr('style').contains('opacity: 1;')) ) {
                    
                        var height = 0;
                        next.find('.content-post .answer-wrapper ul li').each(function() {
                                    
                            if ($(this).height() > $(this).prev().height()) {
                                height = $(this).height();
                            } else {
                                height = $(this).prev().height();
                            }
                        });
                            
                        next.find('.content-post .answer-wrapper ul li').height(height);
                    }
                            
                    clearTimeout(set_height);
                            
                }, 1);
                        
            });
        }
    });*/
            
    $(document).on('click', '.btn-assessment-submit', function() {
        
        var assessment = get_user_score();
        
        $.ajax({
            url : $('#base_url').val() + 'save_assessment',
            type : 'post',
            dataType : 'json',
            data : {
                data : assessment
            },
            success : function(data) {
                console.log(data)
            },
            error : function() {}
        });
           
    });
    
    $(document).on('click', '.answer-wrapper ul li', function(){
        
        if(time_up){
            return false;
        }
        
        clock.stop(function(){
            time_up = false;
            total_time_taken += parseInt(clock.time);
            return;
        });
        
        var current = $(this).parent('ul').parent('.answer-wrapper').parent('.content-post').parent('.materials_and_byline');
        var current_id = current.attr('id').split('-')[1];
        var current_q_id = parseInt(current_id);
        var next_q_id = current_q_id + 1;
        $('#assessment_next').attr('nxt_q_id', next_q_id.toString());
        
        var checked = false; 
        var this_ul = $(this).parent('ul');
        
        this_ul.find('li').each(function() {
            var this_checked = $(this).attr('checked');
            if(typeof this_checked !== typeof undefined && this_checked !== false) {
                checked = true;
                return;
            }
        });
        
        if( next_q_id == num_assessments ) {
            
            clock.stop(function(){
                time_up = false;
                assess_finished = true;
            });
        }
        
        if(!checked) {
            $(this).attr('checked', 'checked');
            $(this).css('background-color', '#FFFDDC');
            
            var correct = $(this).attr('option');
            
            var key = 'assess_wrong';
            var btn_html = $('.assessment-popup-btn-wrapper-explanation').html();
            if(correct == '1') {
                key = 'assess_correct';
                var ques_mark = parseInt($(this).attr('mark'));
                user_score += ques_mark;
            }
            
            var pop_up_data =  get_popup_data(key, '');
            
            $('#assessment-popup-wrapper').css('width', '450px');
            $('.assessment-popup-header-label').html('');
            $('.assessment-popup-header-label').html(pop_up_data.header_label);
        
            $('.assessment-popup-icon-wrapper').html('');
            $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');
        
            $('.assessment_custom_message').html('');
            $('.assessment_custom_message').html(pop_up_data.custom_message);
            
            $('.assessment-popup-btn-wrapper').html('');
            $('.assessment-popup-btn-wrapper').html(btn_html);
        
            var html_expl_nxt_popup = $('#assessment-popup-fancy').html();

            $.fancybox({
                'content' : html_expl_nxt_popup,
                'width': 450,
                'transitionIn': 'fade',
                'transitionOut': 'fade',
                'openEffect': 'elastic',
                'openSpeed' : 350,
                'fitToView' : true,
                'autoSize' : true,
                'padding': 0,
                'margin': 0
            });
        }
    });
    
    $(document).on('click', '.show-assessment-score', function(){
        
        var key = $(this).attr('data');
        
        $('.assessment-popup-btn-wrapper').html('');
        
        $('.nxt-btn').removeClass('show-assessment-score');
        $('.nxt-btn span').text('Save Score');
        $('.nxt-btn').removeAttr('id');
        
        if($('#asses_id').length < 1) {
            $('.nxt-btn').addClass('before-login-user');
            $('.nxt-btn').attr('data', 'assessment_save_score');
            
            get_user_score();
            total_time_taken += parseInt(clock.time);
        } else {
            $('.nxt-btn').addClass('btn-assessment-submit');
        }
        
        var btn_html = $('.assessment-popup-btn-wrapper-explanation').children().eq(1);
        var pop_up_data =  get_popup_data(key, user_score);
        
        $('#assessment-popup-wrapper').css('width', '450px');
        $('.assessment-popup-header-label').html('');
        $('.assessment-popup-header-label').html(pop_up_data.header_label);
        
        $('.assessment-popup-icon-wrapper').html('');
        $('.assessment-popup-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data.icon + '" width="75" />');
        
        $('.assessment_custom_message').html('');
        $('.assessment_custom_message').html(pop_up_data.custom_message);
        
        $('.assessment-popup-btn-wrapper').html(btn_html);
        
        var html_expl_nxt_popup = $('#assessment-popup-fancy').html();

        $.fancybox({
            'content' : html_expl_nxt_popup,
            'width': 450,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : true,
            'autoSize' : true,
            'padding': 0,
            'margin': 0
        });
        
    });
    
    $(document).on('click', '#assessment_explanation', function(){
        
        var key = 'assess_explanation';
        var explanation = '';
        var btn_html = $('.assessment-popup-btn-wrapper-explanation').html();
        
        $('.materials_and_byline').each(function() {
            
            if( ($(this).attr('style').contains('display: block;')) ||($(this).attr('style').contains('opacity: 1;')) ) {
                
                explanation = $(this).find('.content-post .answer-wrapper ul').attr('explanation');
            }
        });
        
        var pop_up_data =  get_popup_data(key, explanation);
        
        $('#assessment-popup-wrapper').css('width', '450px');
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
            'width': 450,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : true,
            'autoSize' : true,
            'padding': 0,
            'margin': 0
        });
    });
    
    $(document).on('click', '#start_assessment_now', function(){
        var ques_time = get_ques_time(0);
        time_up = false;
        $.fancybox.close();
        clock.setTime(ques_time);
        clock.start();
    });
    
    $(document).on('click', '.fancybox-close', function(){
        $.fancybox.close();
        clock.start();
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
            'custom_message' : '<div id="pre_assessment_details"><p>No. of Question&nbsp;: ' + $('#nos_questions').val() + '</p><p>Total Score&nbsp;: ' + $('#total_mark').val() + '</p><p>Highest Score&nbsp;: ' + $('#highest_score').val() + '</p><p>Quiz Time&nbsp;: ' + $('#assess_time').val() + ' : 00 Minute</p><p>Total Played&nbsp;: ' + $('#total_played').val() + '</p></div><div id="leader_board" >' + $('#assess_ladder_board').html() + '</div>'
        },
        'assess_wrong' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Quiz',
            'custom_message' : '<p style="text-align: center;">Oops! Wrong answer.</p>'
        },
        'assess_correct' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Quiz',
            'custom_message' : '<p style="text-align: center;">Congratulations! You got it right.</p>'
        },
        'assessment_score' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Game Over',
            'custom_message' : '<p style="color: #999; font-size: 30px; font-weight: 900; letter-spacing: 3px; text-align: center;">YOUR SCORE IS</p><p style="color: #000; font-size: 70px; font-weight: 900; letter-spacing: -1; margin: 35px 0; text-align: center; "> '+ explanation + ' / ' + $('#total_mark').val() + '</p>'
        },
        'assess_time_out' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Game Over',
            'custom_message' : '<p style="color: #999; font-size: 30px; font-weight: 900; letter-spacing: 3px; text-align: center;">YOUR SCORE IS</p><p style="color: #000; font-size: 70px; font-weight: 900; letter-spacing: -1; margin: 35px 0; text-align: center; "> '+ explanation + ' / ' + $('#total_mark').val() + '</p>'
        },
        'ques_time_up' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Time Up',
            'custom_message' : '<p style="text-align: center;">Oops! Time up</p>'
        },
        'assess_explanation' : {
            'icon' : 'assessment_popup.png',
            'header_label' : 'Explanation',
            'custom_message' : '<p>' + explanation + '</p>'
        }
    };
    
    return pop_up_data[key];
}

function get_user_score() {
    
    var assessment = $('#assessment_title').attr('data') + '_';
    $('.materials_and_byline .content-post .answer-wrapper ul li').each(function() {
        if($(this).attr('checked') == 'checked') {
            var q_id = $(this).parent('ul').parent('.answer-wrapper').parent('.content-post').siblings('.ques_id').attr('data');
            var a_id = $(this).attr('data');
                        
            assessment += q_id + '-' + a_id + ',';
        }
    });
    
    if($('#asses_id').length < 1) {
        createCookie('c21_assessment', assessment, false);
    }
    
    return assessment;
}

function get_ques_time(ques_id) {
        
    var ques_time = $('#q_id-' + ques_id).find('.content-post .answer-wrapper ul').attr('time');
    
    //    return parseInt(ques_time);
    return 5;
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}