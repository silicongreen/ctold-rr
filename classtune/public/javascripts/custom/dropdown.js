var j = jQuery.noConflict();
j(document).ready(function() {
    j('form#new_student select').addClass('droppify');
});

j( document ).ajaxComplete(function( event, xhr, settings) {
    var subject_html = j('#syllabus').html();
    if(subject_html.trim().length > 0) {
        var label_html = j(".label-field-pair > label").text();
        var dropdown_html = j(".label-field-pair > .text-input-bg").html();
        j('#subjects_drops > .row_label > div').html(label_html);
        j('#subjects_drops > .row_data > div > div').html(dropdown_html);
        j('#subjects_drops').show();
        j('#syllabus').html('');
    } else {
        j('#subjects_drops').hide();
    }
    
    j('form select').not('droppify').each(function(e){
        j('#' + j(this).attr('id')).droppify();
    });
    
});

Ajax.Responders.register({
    onComplete: function() {
        
        var subject_html = $('syllabus').innerHTML;
        var section_html = $('section_assingment').innerHTML;
        
        if(section_html.trim().length == 0) {
            $('section_assingment').up(2).hide();
        } else {
            $('section_assingment').up(2).show();
        }
        
        if(subject_html.trim().length > 0) {
            var label_html = $$(".label-field-pair > label")[0].innerHTML;
            var dropdown_html = $$(".label-field-pair > .text-input-bg")[0].innerHTML;
            $$('#subjects_drops > .row_label > div')[0].update(label_html);
            $$('#subjects_drops > .row_data > div > div')[0].update(dropdown_html);
            $('subjects_drops').show();
            $('syllabus').update('');
        } else {
            $('subjects_drops').hide();
        }
        
        $$('form select:not([class~=droppify])').each(function(e){
            j('#' + e.readAttribute('id')).droppify();
        });
    }
});