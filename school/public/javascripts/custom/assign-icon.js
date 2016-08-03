var jqss = jQuery.noConflict();
jqss(document).ready(function() {
    jqss(document).on('click', '.custom_icon', function() {
        var image = jqss(this);
        var image_name = image.attr('id');
        var form_id = jqss('input[type=submit]').closest("form").attr('class');
        var pref = '';
        
        if (form_id.indexOf("edit_") > -1){
            pref = form_id.substring(5);
        }else{
            pref = form_id.substring(4);
        }
        
        image.closest('li').siblings().find('img').css("background-color", "");
        image.css("background-color", "#ccc");
        
        jqss('#' + pref + '_icon_number').val(image_name);
    });
});
