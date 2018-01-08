/* 
 * All Validation extra method will be added there
 */




jQuery.validator.addMethod("validCaptcha", function(value, element) {
   
    $.ajax({
        type: "POST",
        url: $("#base_url").val()+"admin/login/ajax_valid_captcha/",
        data: {
            tds_csrf        : $('input[name$="tds_csrf"]').val(),
            captcha        : $('#captcha').val(),
        },
        async:false,
        success: function(data) {

            isSuccess = (parseInt(data) == 1) ? true : false;

        }
    });

    return isSuccess;

}, "Invalid Captcha.");

jQuery.validator.addMethod("uniqueUser", function(value, element) {
    $.ajax({
        type: "POST",
        url: $("#base_url").val()+"admin/users/ajax_unique_user/",
        data: {
            admin_name      : $("#admin_name").val(),
            tds_csrf        : $('input[name$="tds_csrf"]').val(),
            admin_id        : $("#admin_id").val()
        },
        async:false,
        success: function(data) {

            isSuccess = (parseInt(data) == 1) ? true : false;

        }
    });

    return isSuccess;

}, "User Name already in database");


jQuery.validator.addMethod("uniqueGroup", function(value, element) {
    $.ajax({
        type: "POST",
        url: $("#base_url").val()+"admin/group/ajax_unique/",
        data: {
            group_name      : $("#group_name").val(),
            tds_csrf        : $('input[name$="tds_csrf"]').val(),
            group_id        : $("#group_id").val()
            
        },
        async:false,
        success: function(data) {

            isSuccess = (parseInt(data) == 1) ? true : false;

        }
    });

    return isSuccess;

}, "Group Name already in database");



jQuery.validator.addMethod("uniqueMenu", function(value, element) {
    $.ajax({
        type: "POST",
        url: $("#base_url").val()+"admin/menu/ajax_unique_menu/",
        data: {
            menu_name      : $("#menu_name").val(),
            tds_csrf       : $('input[name$="tds_csrf"]').val(),
            menu_id        : $("#menu_id").val(),
            parent_id      : $('select[name$="parent_id"]').val()
        },
        async:false,
        success: function(data) {

            isSuccess = (parseInt(data) == 1) ? true : false;

        }
    });

    return isSuccess;

}, "Menu Name already in database");

//jQuery.validator.addMethod("uniqueCategory", function(value, element) {
//    $.ajax({
//        type: "POST",
//        url: $("#base_url").val()+"admin/categories/ajax_unique_category/",
//        data: {
//            name           : $("#name").val(),
//            tds_csrf       : $('input[name$="tds_csrf"]').val(),
//            id             : $("#id").val(),
//            parent         : $('select[name$="parent"]').val()
//        },
//        async:false,
//        success: function(data) {
//            isSuccess = (parseInt(data) == 1) ? true : false;
//
//        }
//    });
//
//    return isSuccess;
//
//}, "Category Name already in database");


jQuery.validator.addMethod("uniqueFunction", function(value, element) {
    $.ajax({
        type: "POST",
        url: $("#base_url").val()+"admin/menu/ajax_unique_method/",
        data: {
            controller_or_function      : $("#controller_or_function").val(),
            tds_csrf        : $('input[name$="tds_csrf"]').val(),
            menu_id         : $("#menu_id").val(),
            parent_id       : $('select[name$="parent_id"]').val()
        },
        async:false,
        success: function(data) {

            isSuccess = (parseInt(data) == 1) ? true : false;

        }
    });

    return isSuccess;

}, "Controller/Method Name already in database");




jQuery.validator.addMethod("lengthPassword", function(value, element) {
    if($("#admin_password").val())
    {
        if($("#admin_password").val().length<6)
        {
          return false;   
        }    
        else
        {
          return true;  
        }    
    }   
    else
    {
        return true;
    }    
    
},"Password must be atleast 6 character");
