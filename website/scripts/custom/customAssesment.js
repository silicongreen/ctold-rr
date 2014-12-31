/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    
    $('#answers_form_container').html('');
    $('#answers_wrapper').slideDown('slow');
    $('#answers_form_container').html($('#ans_enum').html());
    
    if($('#ans_type').val() == '0'){
        $('#answers_form_container').html($('#ans_enum').html());
    } else if ($('#ans_type').val() == '1') {
        $('#answers_form_container').html($('#ans_mcq').html());
    } else {
        $('#answers_form_container').html('');
    }
    
    $(document).on('click', '#add_answers', function() {
        $('#answers_wrapper').slideDown('slow');
    });
    
    $(document).on('change', '#ans_type', function() {
        
        $('#answers_form_container').html('');
        
        if($(this).val() == '0'){
            $('#answers_form_container').html($('#ans_enum').html());
        } else if ($(this).val() == '1') {
            $('#answers_form_container').html($('#ans_mcq').html());
        } else {
            $('#answers_form_container').html('');
            return false;
        }
        
    });
    
    $(document).on('click','.correct-chk', function(){
       
        $('#answers_form_container').find('input[type=checkbox]').val('0');
        $('#answers_form_container').find('input[type=checkbox]').prop('checked', false);
       
        $(this).prop('checked', true);
        $(this).val('1');
    });
    
    
    
});
