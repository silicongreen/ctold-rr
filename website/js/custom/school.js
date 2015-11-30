$(document).ready(function () {

    if ($('#school_code').length > 0) {
        var school_code = $('#school_code').val();
        var school_type = $('#school_type').val();
        var i_tmp_school_created_data_id = $('#i_tmp_school_created_data_id').val();
        var i_free_user_id = $('#i_free_user_id').val();

        $.ajax({
            url: '/createschool/notify-user',
            type: 'POST',
            dataType: 'json',
            data: {notify: true, i_free_user_id: i_free_user_id, i_tmp_school_created_data_id: i_tmp_school_created_data_id}
        }).done(function (data) {
            console.log(data);
        }).fail(function (err) {
            console.log(err);
        });

//        $('#school_code_wrp').html('');

        $.ajax({
            url: '/createschool/finalize',
            type: 'POST',
            dataType: 'json',
            data: {code: school_code, type: school_type}
        }).done(function (data) {
            console.log(data);
        }).fail(function (err) {
            console.log(err);
        });

    }

});