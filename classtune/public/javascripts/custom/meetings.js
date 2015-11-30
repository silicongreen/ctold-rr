var jqss = jQuery.noConflict();
jqss(document).ready(function() {
    jqss(document).on('submit', '#new_student', function() {
        var has_error = false;
        var err_html = '';
        
        jqss("#new_student :text, #new_student select, #new_student textarea").each(function() {
            if(jqss(this).val() === "") {
                has_error = true;
                jqss(this).addClass('has_error');
                var lbl = jqss(this).parent('div').parent('div').find('label').text();
                lbl = lbl.replace('*', '');
                lbl = lbl.replace(':', '');
                lbl = lbl.replace('Select ', '');
               
                err_html += '<div class="error-text">';
                err_html += lbl + ' cannot be empty.';
                err_html += '</div>';
            }
        });
        
        if(jqss("#new_student #student_batch_name").length > 0) {
            
            if(jqss("#new_student :checkbox:checked").length < 1) {
                has_error = true;
                err_html += '<div class="error-text">';
                err_html += 'Select at least one student from Student List.';
                err_html += '</div>';
                jqss('#students_list_view').addClass('has_error');
            }
        }
        
        if(has_error) {
            jqss('.error-box').html('');
            jqss('.error-box').html(err_html);
            jqss('.error-box').show();
        
            return false;
        }
    });
    
    jqss(document).on('focus', ':text, select, textarea', function() {
        jqss(this).removeClass('has_error');
    });
    
    jqss(document).on('click', '#select_all', function() {
        var check = '';
        if (this.checked) {
            check = 'checked';
        }
        jqss('.students').prop('checked', check);
    });
    
    jqss(document).on('change', '.students', function() {
        var check = '';
        if (jqss('.students').filter(":checked").length == jqss('.students').length) {
            check = 'checked';
        }
        jqss('#select_all').prop("checked", check);
    });
    
});

