/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    
    $(document).on('click','.correct-chk', function(){
       
        $('#answers_form_container').find('input[type=checkbox]').val('0');
        $('#answers_form_container').find('input[type=checkbox]').prop('checked', false);
       
        $(this).prop('checked', true);
        
        var ans_id = $(this).attr('id').split('-')[1];
        
        $(this).val( ans_id );
        
    });
    
});
