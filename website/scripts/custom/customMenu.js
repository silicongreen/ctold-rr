
$(document).ready(function(){
    
    $("#menu_type").change(function(){
        
        if($(this).val() == 2)
        {
         $("#icon_fieldset").hide();   
         $("#text_fieldset").show();
         $("#permalink_fieldset").show();
        }
        else if($(this).val() == 3)
        {
         $("#icon_fieldset").show()   
         $("#text_fieldset").hide();  
         $("#permalink_fieldset").show();
         $("#show_on_sub_menu").hide();
         $("#show_on_sub_menu_footer").hide();
         $("#sub_menu").hide();
        }    
       else 
        {
         $("#icon_fieldset").hide()   
         $("#text_fieldset").hide();
         $("#permalink_fieldset").hide();
         $("#show_on_sub_menu").hide();
         $("#show_on_sub_menu_footer").hide(); 
         $("#sub_menu").hide();
        }
        
        
        
    });
    
    $(document).on("change", "#menu_types", function()
    {
  
         if($("#menu_types").val()==2 && $('#position_1').is(':checked')) 
         {
             $("#show_on_sub_menu").show();
             $("#show_on_sub_menu_footer").hide();
         } 
         else if($("#menu_types").val()==2 && $('#position_2').is(':checked')) 
         {
            $("#show_on_sub_menu").hide();
            $("#show_on_sub_menu_footer").show();
             
         } 
         else
         {
           $("#show_on_sub_menu").hide();
           $("#show_on_sub_menu_footer").hide();  
         }    
       
    
    });
    
    
    $(document).on("click", "#position_1", function()
    {
  
         if($("#menu_types").val()==2 && $('#position_1').is(':checked')) 
         {
             $("#show_on_sub_menu").show();
             $("#show_on_sub_menu_footer").hide();
         } 
         else if($("#menu_types").val()==2 && $('#position_2').is(':checked')) 
         {
            $("#show_on_sub_menu").hide();
            $("#show_on_sub_menu_footer").show();
             
         } 
         else
         {
           $("#show_on_sub_menu").hide();
           $("#show_on_sub_menu_footer").hide();  
         }    
       
    
    });
    
    
    
    $(document).on("click", "#position_2", function()
    {
  
         if($("#menu_types").val()==2 && $('#position_1').is(':checked')) 
         {
             $("#show_on_sub_menu").show();
             $("#show_on_sub_menu_footer").hide();
         } 
         else if($("#menu_types").val()==2 && $('#position_2').is(':checked')) 
         {
            $("#show_on_sub_menu").hide();
            $("#show_on_sub_menu_footer").show();
             
         } 
         else
         {
           $("#show_on_sub_menu").hide();
           $("#show_on_sub_menu_footer").hide();  
         }    
       
    
    });
    
    
    
    
});





/*
$(document).ready(function(){
    $('#lbl_menus_position_1').on('click',function(){
        $('#f_set_footer_groups').hide();
        $('#f_set_menu_types').show();
    });
    
    $('#menus_type').live('change',function(){
        if($('#menus_type :selected').val() !== '' && ($('#menus_type :selected').val() == 1 || $('#menus_type :selected').val() == 4)){
            $('#menus_title').val('');
            $('#f_set_menus_ci_key').hide();
            $('#f_set_menus_link_type').hide();
            $('#f_set_menus_link_text').hide();
            $('#f_set_menus_icon_name').hide();
            $('#f_set_drp_categories').show();
            $('#f_set_sub_categories').show();
            
            $('#menus_permalink').val('');
            $('#menus_ci_key').val('');
            $('#menus_link_text').val('');
            $('#menus_icon_name').val('');
            
            $('#menus_icon_name').removeClass('required');
            $('#menus_link_text').removeClass('required');
            
            if($('#menus_type :selected').val() == 1){
                $('#menus_title').attr('readonly',true);
                $('#f_set_news_list').hide();
                $('#f_set_news_num').show();
                $('#div_sub_categories').html('');
                $('#div_sub_categories').html('Please select categories to get sub categories.');
                $('#news_num').prop('disabled',false);
            }
            
            if($('#menus_type :selected').val() == 4){
                $('#menus_title').attr('readonly',false);
                $('#f_set_news_num').hide();
                $('#f_set_news_list').hide();
                $('#menus_title').val('');
                $('#div_sub_categories').html('');
                $('#div_sub_categories').html('Please select categories to get sub categories.');
                $('#news_num').prop('disabled',true);
            }
            
            $.ajax({
                type: 'post',
                url: $('#base_url').val()+'admin/menu/categoryListDrop',
                data: {tds_csrf : $('input[name$="tds_csrf"]').val(),},
                success: function(data){
                    $('#drp_categories').html('');
                    $('#drp_categories').append(data);
                    $('#div_news_list').html('');
                    if($('#menus_type :selected').val() == 4){
                        $('#f_set_news_list').show();
                    }
                },
                error: function(e){
                    if(window.console){
                        console.log(e);
                    }
                },
            });
        }else if($('#menus_type :selected').val() !== '' && ($('#menus_type :selected').val() == 2 || $('#menus_type :selected').val() == 3)){
            $('#f_set_drp_categories').hide();
            $('#f_set_sub_categories').hide();
            $('#f_set_news_num').hide();
            $('#f_set_news_list').hide();
            $('#drp_categories').html('');
            $('#menus_title').val('');
            $('#menus_title').attr('readonly',false);
            
            $('#f_set_menus_ci_key').show();
            $('#f_set_menus_link_type').show();
            
            if($('#menus_type :selected').val() == 2){
                $('#f_set_menus_link_text').show();
                $('#f_set_categories_or_text').show();
                $('#f_set_menus_icon_name').hide();
                $('#menus_link_text').addClass('required');
                $('#menus_icon_name').removeClass('required');
            }else{
                $('#f_set_menus_link_text').hide();
                $('#f_set_menus_icon_name').show();
                $('#menus_icon_name').addClass('required');
                $('#menus_link_text').removeClass('required');
            }
        }
        else{
            $('#menus_title').attr('readonly',false);
            $('#menus_title').val('');
            $('#f_set_drp_categories').hide();
            $('#f_set_sub_categories').hide();
            $('#f_set_news_num').hide();
            $('#f_set_news_list').hide();
        }
    });
    
    var var_categories_or_text = 0;
    $('#chk_categories_or_text').live('change',function(e){
        if($(this).is(':checked')){
            if(var_categories_or_text == 0){
                $.ajax({
                    type: 'post',
                    url: $('#base_url').val()+'admin/menu/categoryListDrop',
                    data: {tds_csrf : $('input[name$="tds_csrf"]').val(),},
                    success: function(data){
                        var_categories_or_text = 1;
                        $('#drp_categories').html('');
                        $('#drp_categories').append(data);
                        $('#f_set_drp_categories').show();
                        $('#f_set_menus_link_text').hide();
                        $('#f_set_menus_ci_key').hide();
                    },
                    error: function(e){
                        if(window.console){
                            console.log(e);
                        }
                    },
                });
            }
            
            if(var_categories_or_text == 1){
                $('#f_set_drp_categories').show();
                $('#f_set_sub_categories').show();
            }
        }else{
            $('#f_set_drp_categories').hide();
            $('#f_set_sub_categories').hide();
            $('#f_set_menus_link_text').show();
            $('#f_set_menus_ci_key').show();
        }
    });
    
    var strSelectedVals;
    $('#drp_categories').live('change',function(){
        strSelectedVals = '';
        $.ajax({
                type: 'post',
                url: $('#base_url').val()+'admin/menu/categoryListDrop',
                data: {tds_csrf : $('input[name$="tds_csrf"]').val(), id: $('#drp_categories :selected').val(), menus_type: $('#menus_type :selected').val()},
                success: function(data){
                    $('#div_sub_categories').html('');
                    $('#div_sub_categories').html(data);
                    $('#f_set_sub_categories').show();
                    $('#menus_title').val($('#drp_categories :selected').text());
                    if($('#div_sub_categories').find('table').length == 0){
                        $.ajax({
                            type: 'post',
                            url: $('#base_url').val()+'admin/menu/newsList',
                            data: {tds_csrf : $('input[name$="tds_csrf"]').val(), cat_ids: $('#drp_categories :selected').val()},
                            success: function(data){
                                $('#div_news_list').html('');
                                $('#div_news_list').html(data);
                            },
                            error: function(e){
                                if(window.console){
                                    console.log(e);
                                }
                            },
                        });
                    }else{
                        $('#div_news_list').html('');
                        if($('#menus_type :selected').val() == 1){
                            $('#div_news_list').html('Please select Sub Categories and Number of News, you want to display at the menu to get the news list. If you do not select Number of News then 2 latest news will be displayed.');
                        }else{
                            $('#div_news_list').html('Please select Sub Categories, to get the News. You can select only one news to display at the menu.');
                        }
                    }
                },
                error: function(e){
                    if(window.console){
                        console.log(e);
                    }
                },
            });
    });
    
    $('#all_sub_cat').die('change').live('change',function(){
        if($(this).is(':checked')){
            $('#div_sub_categories table tbody').find(':checkbox').prop('checked',true);
            $('#news_num').prop('disabled',true);
        }else{
            $('#div_sub_categories table tbody').find(':checkbox').prop('checked',false);
            $('#news_num').prop('disabled',false);
        }
    });
    
    $('#div_sub_categories input:checkbox').die('change').live('change',function(e){
        strSelectedVals = $('#div_sub_categories input:checkbox:checked').map(function(){
                return $(this).val();
            }).get().join(',');
        if($('#div_sub_categories table tbody').find(':checkbox:checked').length > 1){
            $('#news_num').prop('disabled',true);
        }else{
            $('#news_num').prop('disabled',false);
        }
        
        if($('#menus_type :selected').val() == 4){
            if($('#div_sub_categories table tbody').find(':checkbox:checked').length > 1){
                alert('Please select only one Sub Category.');
                $(this).prop('checked', false);
                e.preventDefault();
                return false;
            }
        }
        
        if($('#all_sub_cat').is(':checked')){
            strSelectedVals = $('#div_sub_categories input:checkbox').map(function(){
                return $(this).val();
            }).get().join(',');
            strSelectedVals = strSelectedVals.replace(/^,|,$/g,'');
        }
        
        $.ajax({
            type: 'post',
            url: $('#base_url').val()+'admin/menu/newsList',
            data: {tds_csrf : $('input[name$="tds_csrf"]').val(), cat_ids: $('#drp_categories :selected').val()},
            success: function(data){
                $('#div_news_list').html('');
                $('#div_news_list').html(data);
            },
            error: function(e){
                if(window.console){
                    console.log(e);
                }
            },
        });
        
    });
    
    $('#news_num').die('change').live('change',function(){
        if(strSelectedVals == '' || strSelectedVals == undefined ){
            strSelectedVals = $('#drp_categories :selected').val();
        }else{
            strSelectedVals = $('#drp_categories :selected').val()+','+strSelectedVals;
        }
        
        $.ajax({
            type: 'post',
            url: $('#base_url').val()+'admin/menu/newsList',
            data: {tds_csrf : $('input[name$="tds_csrf"]').val(), cat_ids: strSelectedVals},
            success: function(data){
                $('#div_news_list').html('');
                $('#div_news_list').html(data);
            },
            error: function(e){
                if(window.console){
                    console.log(e);
                }
            },
        });
    });
    
    $('.chk_menus_news_list').live('change',function(e){
        if($(this).is(':checked')){
            if($('#menus_type :selected').val() == 1){
                $(this).parent('td').parent('tr').find('td').eq(2).html('<input name="menus[priority][]" class="text menus_priority required" type="text" style="width: auto !important;" />');
            }            
            if($('#menus_type :selected').val() == 4 && $('.static').find(':checkbox:checked').length > 1){
                alert('Please select only one news.');
                $(this).prop('checked', false);
                $(this).parent('td').parent('tr').find('td').eq(2).html('');
            }else if($('#menus_type :selected').val() == 1 && $('.static').find(':checkbox:checked').length > $('#news_num :selected').val()){
                alert('Maximum number selectable news is '+ $('#news_num :selected').val());
                $(this).prop('checked', false);
                $(this).parent('td').parent('tr').find('td').eq(2).html('');
            }
        }else{
            $(this).parent('td').parent('tr').find('td').eq(2).html('');
        }
    });
    
    $('.menus_priority').live('keydown',function(event){
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 97 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
});

$(window).load(function(){
    if($('#menus_form').find('input[id$="menus_id"]').length > 0){
        $.ajax({
            type: 'post',
            url: $('#base_url').val()+'admin/menu/categoryListDrop',
            data: {tds_csrf : $('input[name$="tds_csrf"]').val(), menus_id: $('#menus_form').find('input[id$="menus_id"]').val()},
            success: function(data){
                $('#drp_categories').html('');
                $('#drp_categories').append(data);
                $('#div_news_list').html('');
                if($('#menus_type :selected').val() == 1 || ($('#menus_type :selected').val() == 2 && $('#chk_categories_or_text').is(':checked'))){
                    $.ajax({
                        type: 'post',
                        url: $('#base_url').val()+'admin/menu/categoryListDrop',
                        data: {tds_csrf : $('input[name$="tds_csrf"]').val(), id: $('#drp_categories :selected').val(), row_id: $('#menus_form').find('input[id$="menus_id"]').val(), edit: true},
                        success: function(data){
                            $('#div_sub_categories').html('');
                            $('#div_sub_categories').html(data);
                            $('#f_set_sub_categories').show();
                        },
                        error: function(e){
                            if(window.console){
                                console.log(e);
                            }
                        },
                    });
                }
                
                if($('#menus_type :selected').val() == 4){
                    $('#f_set_news_list').show();
                }
            },
            error: function(e){
                if(window.console){
                    console.log(e);
                }
            },
        });
    }
});

*/