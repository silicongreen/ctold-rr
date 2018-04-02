
var jqss = jQuery.noConflict();
function hide_loading(batch_id)
{
    jqss('#loading_' + batch_id).hide(); 
    jqss('.batches_list').removeClass('selected');
    jqss('#batch_list_' + batch_id).addClass('selected'); 
}

function highlight_section(batch_id, course_id)
{
    jqss('#loading_' + course_id).hide(); 
    jqss('.category-name').css('color', "#000");
    jqss('.sections').removeClass('selected');
    jqss('#section_' + batch_id).addClass('selected'); 
    jqss('li.listdata .category-name').each(function(){
        jqss(this).parent().css("min-height", jqss(this).outerHeight() + 'px');
    });
}

function highlight_name(batch_id, course_id)
{
    jqss('#loading_' + course_id).hide(); 
    jqss('.sections').removeClass('selected');
    jqss('.category-name').css('color', "#000");
    jqss('#cate_name_' + course_id).css('color', "#f00"); 
    jqss('.category-name').each(function(){
        jqss(this).parent().css("min-height", jqss(this).outerHeight() + 'px');
    });
}

function success_load()
{
    jqss('#loader').hide();
    jqss('.listdata .category-name').each(function(){
        jqss(this).parent().css("min-height", jqss(this).outerHeight() + 'px');
    });
}

function success_assign()
{
    jqss('.category-name').each(function(){
        jqss(this).parent().css("min-height", jqss(this).outerHeight() + 'px');
    });
}

jqss(document).ready(function() {
    jqss('li.listelective .category-name').each(function(){
        jqss(this).parent().css("min-height", jqss(this).outerHeight() + 'px');
    });
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
