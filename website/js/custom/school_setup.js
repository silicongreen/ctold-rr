var opened_from_check_box = false;
var form_name = '';
var last_tab = false;

$(document).ready(function () {

    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    var school_id = $('#school_id').val();

    $('.tab-content .active form #extra_vaules table tbody').html('');

    $('#rootwizard').bootstrapWizard({
        nextSelector: '.button-next',
        previousSelector: '.button-previous',
        onTabShow: function (tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index + 1;

            if ($current == 6) {
                last_tab = true;
            }

            var $percent = ($current / $total) * 100;
            $('#rootwizard .progress-bar').css({width: $percent + '%'});
            $('.emp_category_wrapper').html('');

            $.ajax({
                url: '/createschool/load-setup-form',
                type: 'POST',
                dataType: 'html',
                data: {form_id: $current, school_id: school_id},
                beforeSend: function () {
                    $('.dots-loader').css('display', 'inline-block');
                }
            }).done(function (data) {
                $('#tab' + $current).html('');
                $('#tab' + $current).html(data);

                form_name = $('#tab' + $current).find('form').attr('id');

                if (form_name == 'shift' || form_name == 'course') {
                    $('.add_txt_field').html('Add More Class <b>+</b>');
                } else {
                    $('.add_txt_field').html('Add More <b>+</b>');
                }

                if ($('#str_categories_wrapper').length == 1) {
                    $('.emp_category_wrapper').html($('#str_categories_wrapper').html());
                    $('#str_categories_wrapper').html('');
                }

                if ($('#str_courses_wrapper').length == 1) {
                    $('.class_name_wrapper').html($('#str_courses_wrapper').html());
                    $('#str_courses_wrapper').html('');
                }

                $('.dots-loader').css('display', 'none');
            }).fail(function (err) {
                $('.dots-loader').css('display', 'none');
                console.log(err);
            });
        },
//        onTabClick: function (tab, navigation, index) {
//            alert('Save or skip to go to next step.');
//            return false;
//        }
    });

    $(document).off('click', '.add_txt_field').on('click', '.add_txt_field', function () {
        opened_from_check_box = false;

        var active_form = $('.tab-content .active form').attr('id');

        if (active_form == 'shift' || active_form == 'course') {

            $('#class_name_txt_box').val('');
            $('#section_name_txt_box').val('');
            $('#classModal').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });

        } else {

            $("#uLogin").parent('div').parent('div.form-group').find('p').remove();
            if (active_form == 'employee_category') {
                $("#uLogin").parent('div').parent('div.form-group').append('<p>eg: Teacher (TE)</p>');
            } else if (active_form == 'employee_position') {
                $("#uLogin").parent('div').parent('div.form-group').append('<p>eg: Senior</p>');
            } else if (active_form == 'employee_grade') {
                $("#uLogin").parent('div').parent('div.form-group').append('<p>eg: Grade 1</p>');
            } else if (active_form == 'employee_department') {
                $("#uLogin").parent('div').parent('div.form-group').append('<p>eg: Bangla</p>');
            }

            if ($('.emp_category_wrapper').html() != '') {
                $('.emp_category_wrapper').show();
            }

            $("#uLogin").val('');
            $('#myModal').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
        }

    });

    $(document).off('click', 'i.fa-trash-o').on('click', 'i.fa-trash-o', function () {

        var cur_row_text = $(this).parent('td').siblings('td:eq(0)').text().trim();
        var active_form = $('.tab-content .active form').attr('id');

        if (active_form != 'employee_category') {
            if (cur_row_text.indexOf('(') > -1) {
                cur_row_text = cur_row_text.replace(/ *\([^)]*\) */g, "");
            }
        }

        if (cur_row_text.indexOf('Assign to class') > -1) {
            cur_row_text = cur_row_text.replace('Assign to class', '');
        }

        $(this).parent('td').parent('tr').remove();

        $('.tab-content .active form').find('input[type=checkbox]:checked').each(function (e) {
            var checkbox_of_cur_text = $(this);
            if (cur_row_text == checkbox_of_cur_text.val().trim()) {
                checkbox_of_cur_text.prop('checked', false);
            }
        });

        if ($('.tab-content .active form div#extra_vaules_classes table tbody tr').length < 1) {
            $('.tab-content .active form div#extra_vaules_classes').hide('slow');
        }

        if ($('.tab-content .active form div#extra_vaules table tbody tr').length < 1) {
            $('.tab-content .active form div#extra_vaules').hide('slow');
        }

    });

    $(document).off('click', '.button-save').on('click', '.button-save', function () {
        $('#initial_setup_alert a.close').click();
        $('form#' + form_name).submit();
    });

    $(document).off('click', '.shift_checkbox').on('click', '.shift_checkbox', function () {

        if ($(this).is(':checked')) {
            $('.additional_shift').show();
            $('.additional_shift_txt').focus();
        } else {
            var tbody = $('.tab-content .active form #extra_vaules table tbody');
            $('.additional_shift').hide();
            $('.tab-content #extra_vaules').hide();
            tbody.html('');
        }

    });

    $(document).off('click', '.my_checkbox').on('click', '.my_checkbox', function () {

        if ($(this).is(':checked') == false) {
            return false;
        }

        opened_from_check_box = true;
        var item_name = $(this).val();
        var item_name_val = item_name;
        var check_box_id = $(this).val();
        $('.check_box_id').attr('id', check_box_id);

        if ($('#myModal .modal-body #str_categories').length == 1) {
            if ($('.emp_category_wrapper').html() != '') {
                $('.emp_category_wrapper').show();
            }
            $('#myModal').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
        } else {
            var tbody = $('.tab-content .active form #extra_vaules table tbody');
            var tr = '<tr><td>';
            var item_name_td_html = '';



            if (form_name == 'subject') {
                item_name_td_html += '<input type="hidden" value="' + item_name_val + '" />' + item_name;
                item_name_td_html += '<a href="javascript:void(0);" class="assign_to_class btn">Assign to class</a>';
            } else {
                item_name_td_html += '<input type="hidden" name="' + form_name + '[]" value="' + item_name_val + '" />' + item_name;
            }

            tr += item_name_td_html + '</td><td><i class="fa fa-trash-o"></i></td></tr>;'

            var b_val_exists = false;

            if (item_name.trim() != '') {
                tbody.find('tr').each(function () {
                    if (item_name.trim() == $(this).find('td:first-child').text().trim()) {
                        b_val_exists = true;
                        return false;
                    }
                });
            }

            if (b_val_exists) {
                alert('This name is already added. Try another name.');
            } else {
                tbody.append(tr);
                $('.tab-content .tab-pane:not(.active) #extra_vaules').hide();

                if (!$('.tab-content .active #extra_vaules').is(':visible')) {
                    $('.tab-content .active #extra_vaules').show('slow');
                }
            }
        }

    });

    $(document).on('submit', '.tab-content .active form', function (e) {

        $('.dots-loader').css('display', 'inline-block');
        e.preventDefault();

        var formData = new FormData($(this)[0]);
        formData.append('school_id', school_id);

        $.ajax({
            url: '/createschool/save-initial-setup',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
        }).done(function (data) {
            $('.dots-loader').css('display', 'none');

            if (data.success == true) {

                if (last_tab) {
                    $('#successModal').modal({
                        show: true,
                        keyboard: false,
                        backdrop: 'static'
                    });
                } else {
                    $('.button-next').click();
                }

            } else {
                var err_html = '<div id="initial_setup_alert" class="alert alert-danger fade in">' +
                        '<a href="#" class="close" data-dismiss="alert">&times;</a>' +
                        '<strong>Sorry!</strong> ' + data.message +
                        '</div>';

                $('.alert-wrapper').html(err_html);
            }

        }).fail(function (err) {
            $('.dots-loader').css('display', 'none');
        });

    });

    $(document).off('click', '#add_new_item').on('click', '#add_new_item', function () {

        var item_name = $('#uLogin').val();
        var item_name_val = item_name;
        var cate_id = '';
        $('.check_box_id').attr('id', '');

        if ($('#myModal .modal-body #str_categories').length > 0) {

            cate_id = parseInt($('#myModal .modal-body #str_categories option:selected').val());

            if (cate_id <= 0) {
                alert('Please a category.');
                return false;
            }

            item_name_val = item_name_val + '==' + cate_id;
        }

        var tbody = $('.tab-content .active #extra_vaules table tbody');
        var tr = '<tr><td>';
        var item_name_td_html = '<input type="hidden" name="' + form_name + '[]" value="' + item_name_val.trim() + '" />' + item_name.trim();

        if (form_name == 'subject') {
            item_name_td_html += '<a href="javascript:void(0);" class="assign_to_class btn">Assign to class</a>';
        }

        tr += item_name_td_html + '</td><td><i class="fa fa-trash-o"></i></td></tr>;'

        var b_val_exists = false;

        if (item_name.trim() == '') {
            alert('Name is required.');
            return false;
        }

        if (item_name.trim() != '') {
            tbody.find('tr').each(function () {
                if (item_name.trim() == $(this).find('td:first-child').text().trim()) {
                    b_val_exists = true;
                    return false;
                }
            });
        }

        if (b_val_exists) {
            alert('This name is already added. Try another name.');
        } else {
            tbody.append(tr);
            $('.tab-content .tab-pane:not(.active) #extra_vaules').hide();

            if (!$('.tab-content .active #extra_vaules').is(':visible')) {
                $('.tab-content .active #extra_vaules').show('slow');
            }
            $('#myModal').modal('hide');
        }
    });

    $("#myModal").on('shown.bs.modal', function () {
        var check_box_id = $('.check_box_id').attr('id');

        if (opened_from_check_box) {
            $("#uLogin").val(check_box_id.trim());
            $("#uLogin").prop('readonly', true);
        } else {
            $("#uLogin").val('');
            $("#uLogin").prop('readonly', false);
        }

        $("#uLogin").focus();
    });

    $("#classModal").on('hide.bs.modal', function () {
        var check_box_id = $('#classModal .class_name_check_box_id').attr('id');
        var section_names = $('#classModal #section_name_txt_box').val();

        if (section_names == '') {
            $('.tab-content .active form').find('input[type=checkbox]:checked').each(function (e) {
                var checkbox_of_cur_text = $(this);
                if (check_box_id == checkbox_of_cur_text.val().trim()) {
                    checkbox_of_cur_text.prop('checked', false);
                }
            });
        }

    });


    /** Classes **/
    $(document).off('keyup', '.additional_shift_txt').on('keyup', '.additional_shift_txt', function (e) {
        e.preventDefault();

        if (e.keyCode == 13) {

            var item_field = $(this);
            var item_name = item_field.val();
            var item_name_val = item_name;

            var tbody = $('.tab-content .active form #extra_vaules table tbody');
            var tr = '<tr><td>' +
                    '<input type="hidden" name="' + form_name + '[]" value="' + item_name_val + '" />' + item_name + '</td>' +
                    '<td><i class="fa fa-trash-o"></i>' +
                    '</td></tr>;'
            var b_val_exists = false;

            if (item_name.trim() != '') {
                tbody.find('tr').each(function () {
                    if (item_name.trim() == $(this).find('td:first-child').text().trim()) {
                        b_val_exists = true;
                        return false;
                    }
                });
            }

            if (b_val_exists) {
                alert('This name is already added. Try another name.');
            } else {
                tbody.append(tr);
                $('.tab-content #extra_vaules').hide();
                $('.tab-content .active #extra_vaules').show();
            }

            item_field.val('');
            item_field.focus();
        }

    });

    $(document).off('click', '#add_new_class').on('click', '#add_new_class', function () {

        var item_name = $('#classModal #class_name_txt_box').val();
        var section_names = $('#classModal #section_name_txt_box').val();
        var item_name_val = item_name;
        var cate_id = '';
        var tbody = $('.tab-content .active #extra_vaules_classes table tbody');
        var form_name_classes = 'course';

        if (section_names != '') {
            item_name_val += ' (' + section_names + ')';
        } else {
            alert('Provide at least 1 section name.');
            return false;
        }

        $('#classModal .class_name_check_box_id').attr('id', '');

        var tr = '<tr><td>' +
                '<input type="hidden" name="' + form_name_classes + '[]" value="' + item_name_val.trim() + '" />' + item_name_val.trim() + '</td>' +
                '<td><i class="fa fa-trash-o"></i>' +
                '</td></tr>;';

        var b_val_exists = false;

        if (item_name.trim() == '') {
            alert('Name is required.');
            return false;
        }

        if (item_name.trim() != '') {
            tbody.find('tr').each(function () {
                if (item_name.trim() == $(this).find('td:first-child').text().trim()) {
                    b_val_exists = true;
                    return false;
                }
            });
        }

        if (b_val_exists) {
            alert('This name is already added. Try another name.');
        } else {
            tbody.append(tr);

            $('.tab-content .tab-pane:not(.active) #extra_vaules_classes').hide();

            if (!$('.tab-content .active #extra_vaules_classes').is(':visible')) {
                $('.tab-content .active #extra_vaules_classes').show('slow');
            }

            $('#classModal').modal('hide');
        }
    });

    $(document).off('click', '.class_checkbox').on('click', '.class_checkbox', function () {

        if ($(this).is(':checked') == false) {
            return false;
        }

        var item_name = $(this).val();
        var item_name_val = item_name;
        var check_box_id = $(this).val();

        $('#classModal .class_name_check_box_id').attr('id', check_box_id);
        $('#classModal #class_name_txt_box').val(item_name_val);
        $('#classModal #section_name_txt_box').val('');
        $('#classModal').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
    });
    /** Classes **/

    /** Subjects **/
    $(document).off('click', '.assign_to_class').on('click', '.assign_to_class', function () {

        var row_index = $(this).parent('td').parent('tr').index();
        var cel_index = $(this).parent('td').index();
        var subject_name = $(this).siblings('input[type="hidden"]').val();
        var existing_subject_class = '';

        if ($('.class_name_wrapper').html() != '') {
            $('#class_names_container').html('');
            $('.class_name_subject_id').attr('id', subject_name);
            $('.class_name_wrapper').show();
        }

        if ($(this).parent('td').parent('tr').find('div.td_class_names_container').length > 0) {
            existing_subject_class = $(this).parent('td').parent('tr').find('div.td_class_names_container').html();
        }

        $('#class_names_container').html(existing_subject_class);
        $('#subjectModal .row_index').attr('id', row_index);
        $('#subjectModal .cel_index').attr('id', cel_index);

        $('#subjectModal').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
    });

    $(document).off('change', '#str_courses').on('change', '#str_courses', function () {

        var course_txt = $(this).children('option:selected').text();
        var subject_name = $('#subjectModal .class_name_subject_id').attr('id');

        if (course_txt.trim() == 'Select') {
            return false;
        }

        var courses_html = '<div class="alert alert-default panel panel-default pull-left" style="position: relative;">' +
                '<input type="hidden" name="subject[]" value="' + subject_name + '==' + course_txt.trim() + '" />' +
                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><p>' +
                course_txt
        '<p></div>';

        var b_val_exists = false;

        if (course_txt.trim() != '') {

            if ($('#class_names_container').html() != '') {
                $('#class_names_container').find('div.alert').each(function () {

                    if (course_txt.trim() == $(this).find('p').text()) {
                        b_val_exists = true;
                        return false;
                    }
                });
            }
        }

        if (!b_val_exists) {
            $('#class_names_container').append(courses_html);
        }

    });

    $(document).off('click', '#successModal #setup_success').on('click', '#successModal #setup_success', function () {
        window.location.href = '/';
    });

    $(document).off('click', '#subjectModal #add_new_subject_class').on('click', '#subjectModal #add_new_subject_class', function () {

        var subject_classes = $('#class_names_container').html();
        var tbody = $('.tab-content .active form #extra_vaules table tbody');
        var row_index = $('#subjectModal .row_index').attr('id');
        var cel_index = $('#subjectModal .cel_index').attr('id');
        var td_extra_html = '<div class="td_class_names_container" class="form-group">';
        td_extra_html += subject_classes;
        td_extra_html += '</div>';

        if (tbody.find('tr:eq(' + row_index + ')').find('td:eq(' + cel_index + ') div.td_class_names_container').length > 0) {
            tbody.find('tr:eq(' + row_index + ')').find('td:eq(' + cel_index + ') div.td_class_names_container').html('');
            tbody.find('tr:eq(' + row_index + ')').find('td:eq(' + cel_index + ') div.td_class_names_container').html(td_extra_html);
        } else {
            tbody.find('tr:eq(' + row_index + ')').find('td:eq(' + cel_index + ')').append(td_extra_html);
        }

        $('#subjectModal').modal('hide');
    });

    /** Subjects **/

});