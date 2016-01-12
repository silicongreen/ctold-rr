jQuery(function ($) {

    function collectData(form) {

        var formData = {};
        var valid = false;

        form.find('input[type="text"], input[type="hidden"], select').each(function () {

            var key = $(this).attr('name');
            var val = $(this).val();

            if ($(this).attr('required') !== undefined && val == '') {
                $(this).parent('div').append('<div class="error">This field is required.</div>');
                valid = false;
                return false;
            } else {
                valid = true;
                formData[key] = val;
            }
        });

        return (valid) ? formData : valid;
    }

    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        sideBySide: true,
    });

    $(document).off('submit', 'form#citrix_base').on('submit', 'form#citrix_base', function (e) {
        e.preventDefault();

        var postData = collectData($(this));
        if (postData !== false) {
            postData['action'] = 'citrix_save_base_info';

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: postData,
            }).done(function (response) {
                if (response.saved == true) {
                    window.location.reload();
                }
            }).fail(function (error) {
                console.log(error);
            });
        }
        return false;
    });

    $(document).off('submit', 'form#citrix_create_meeting').on('submit', 'form#citrix_create_meeting', function (e) {
        e.preventDefault();

        var postData = collectData($(this));

        if (postData !== false) {
            postData['action'] = 'citrix_create_meeting';

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: postData,
            }).done(function (response) {
                var msg_controll_class = (response.saved == true) ? 'success' : 'danger';
                $('.message-wrapper').html('<div class="alert alert-' + msg_controll_class + '">' + response.msg + '</div>');
                $('.message-wrapper').show('slow');
            }).fail(function (error) {
                console.log(error);
            });
        }
        return false;
    });

    $(document).off('submit', 'form#citrix_start_meeting').on('submit', 'form#citrix_start_meeting', function (e) {
        e.preventDefault();

        var postData = collectData($(this));

        if (postData !== false) {
            postData['action'] = 'citrix_start_meeting';

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: postData,
            }).done(function (response) {
                if (response.saved == true) {
                    window.location.reload();
                }
            }).fail(function (error) {
                console.log(error);
            });
        }
        return false;
    });

    $(document).off('submit', 'form#citrix_webinar').on('submit', 'form#citrix_webinar', function (e) {
        e.preventDefault();

        var postData = collectData($(this));

        if (postData !== false) {
            postData['action'] = 'citrix_create_webinar';

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: postData,
            }).done(function (response) {
                var msg_controll_class = (response.saved == true) ? 'success' : 'danger';
                $('.message-wrapper').html('<div class="alert alert-' + msg_controll_class + '">' + response.msg + '</div>');
                $('.message-wrapper').show('slow');
                
                $('form#citrix_webinar')[0].reset();
                setTimeout(function () {
                    $('.message-wrapper').hide('slow');
                }, 5000);

            }).fail(function (error) {
                console.log(error);
            });
        }
        return false;
    });

    $(document).off('submit', 'form#citrix_webinar_registrant').on('submit', 'form#citrix_webinar_registrant', function (e) {
        e.preventDefault();

        var postData = collectData($(this));

        if (postData !== false) {
            postData['action'] = 'citrix_create_webinar_registrant';

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: postData,
            }).done(function (response) {
                var msg_controll_class = (response.saved == true) ? 'success' : 'danger';
                $('.message-wrapper').html('<div class="alert alert-' + msg_controll_class + '">' + response.msg + '</div>');
                $('.message-wrapper').show('slow');
                $('form#citrix_webinar_registrant')[0].reset();
                setTimeout(function () {
                    $('.message-wrapper').hide('slow');
                }, 5000);

            }).fail(function (error) {
                console.log(error);
            });
        }
        return false;
    });

    $(document).off('click', '.add-registrant').on('click', '.add-registrant', function (e) {

        var title = $(this).attr('title');
        var height = $(this).attr('height');
        var width = $(this).attr('width');
        var content = '#TB_inline?width='+width+'&height='+height+'&inlineId=thickbox_content';
        
        var data_id_and_type = $(this).attr('data-id');
        var ar_data_id_and_type = data_id_and_type.split('-');
        var id = ar_data_id_and_type[0];
        var type = ar_data_id_and_type[1];
        $('#' + type + '_id').val(id);

        $('form').each(function () {
            this.reset();
        });

        if ($('.message-wrapper').length > 0) {
            $('.message-wrapper').hide();
        }

        tb_show(title, content);
    });

    $(document).ready(function () {

        if ($('.pull-ajax').length > 0) {

            var ajax_method = $('.pull-ajax').attr('data');

            var postData = {};
            postData['action'] = ajax_method;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: postData,
            }).done(function (response) {
                $('.pull-ajax').html(response);
//                console.log(response);
            }).fail(function (error) {
                console.log(error);
            });

        }

    });

});
// URL encode plugin
jQuery.extend({URLEncode: function (c) {
        var o = '';
        var x = 0;
        c = c.toString();
        var r = /(^[a-zA-Z0-9_.]*)/;
        while (x < c.length) {
            var m = r.exec(c.substr(x));
            if (m != null && m.length > 1 && m[1] != '') {
                o += m[1];
                x += m[1].length;
            } else {
                if (c[x] == ' ')
                    o += '+';
                else {
                    var d = c.charCodeAt(x);
                    var h = d.toString(16);
                    o += '%' + (h.length < 2 ? '0' : '') + h.toUpperCase();
                }
                x++;
            }
        }
        return o;
    }
});
