var jq = jQuery.noConflict();
jq(document).ready(function () {
    var new_batch_id = jq('#std_batch_id').attr('data');
    var new_batch_name = jq('#std_batch_name').attr('data');
    
    var old_batch_option = jq('select#student_batch_name option').filter(function () { return jq(this).html() == new_batch_name; });
    
    old_batch_option.val(new_batch_id);    
    old_batch_option.prop('selected', true);
});