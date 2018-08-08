var jqss = jQuery.noConflict();
jqss(document).ready(function() {
    
    jqss(document).on('click', '.clear-date', function() {
        jqss('#assignment_duedate').val('');
        jqss('#assignment_duedate').trigger('change');
    });
    jqss(document).on('click', '.clear-date2', function() {
        jqss('#assignment_publish_date').val('');
        jqss('#assignment_publish_date').trigger('change');
    });
    
 
    
});
function show_project(j)
{
    jqss('.single_exam_result_'+j).css('display','none');
    jqss('.single_project_result_'+j).css('display','block');
    jqss('.show_all_project_'+j).css('display','none');
    jqss('.hide_all_project_'+j).css('display','block');
    
    jqss('.show_all_class_test_'+j).css('display','block');
    jqss('.hide_all_class_test_'+j).css('display','none');
}

function hide_project(j)
{
    jqss('.single_exam_result_'+j).css('display','none');
    jqss('#single_exam_'+j+'_0').css('display','block');
    
    jqss('.single_project_result_'+j).css('display','none');
    jqss('.show_all_project_'+j).css('display','block');
    jqss('.hide_all_project_'+j).css('display','none');
    
    jqss('.show_all_class_test_'+j).css('display','block');
    jqss('.hide_all_class_test_'+j).css('display','none');
    
    
}


function show_class_test(j)
{
    jqss('.single_exam_result_'+j).css('display','block');
    jqss('.single_project_result_'+j).css('display','none');
    jqss('.show_all_class_test_'+j).css('display','none');
    jqss('.hide_all_class_test_'+j).css('display','block');
    jqss('.show_all_project_'+j).css('display','block');
    jqss('.hide_all_project_'+j).css('display','none');
}

function hide_class_test(j)
{
    jqss('.single_exam_result_'+j).css('display','none');
    jqss('.show_all_class_test_'+j).css('display','block');
    jqss('.hide_all_class_test_'+j).css('display','none');
    jqss('.single_project_result_'+j).css('display','none');
    jqss('#single_exam_'+j+'_0').css('display','block');
    
    jqss('.show_all_project_'+j).css('display','block');
    jqss('.hide_all_project_'+j).css('display','none');
}
