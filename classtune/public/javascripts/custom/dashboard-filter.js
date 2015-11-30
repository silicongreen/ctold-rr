/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * Author: N!K
 */

var jq = jQuery.noConflict();
jq(document).ready(function() {
    jq(document).on("click","#filter-btn",function(){
        jq(this).toggleClass('button-filter-active');
        if(jq('#filter-div').is(":visible")) {
            jq('#filter-div').hide('fast');
        } else {
            jq('#filter-div').show('fast');
        }
        
    });
    jq(document).on('click', '.clear-date', function() {
        jq('#assignment_duedate').val('');
        jq('#assignment_duedate').trigger('change');
    });
});