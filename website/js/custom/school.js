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
    
    $(document).off('click', '.country-list li').on('click', '.country-list li', function() {
        
        var country_id = $(this).attr('id');
        var country_call_code = $(this).attr('data-call-code');
        var country_text = $(this).text();
        country_text += '<span class="caret"></span>';
        
        console.log(country_id);
        console.log(country_call_code);
        console.log(country_text);
        
        $('#dropdownMenu1').attr('data-coutntry-id', country_id);
        $('#dropdownMenu1').html(country_text);
        $('#country_call_code').val(country_call_code);
        
        if(country_call_code == '') {
            $('#country_call_code').prop('readonly', false);
        } else {
            $('#country_call_code').prop('readonly', true);
        }
        
    });

});