/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

!function( $ ){
    //    "use strict"
    
    $(document).ready(function() {
        var dropdown = $(document).find('.droppify');
        dropdown.droppify();

        $(document).mouseup(function (e)
        {
            var container = $('.droppify_wrapper');

            if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
                {
                container.removeClass('droppify_wrapper_active');
            }
        });
    });
    
    
    $.fn.droppify = function( options ) {
        
        return this.each(function() {
            // Do something to each element here.
            var _this = $(this);
            var settings = $.extend({
                changeBgOnClick: true,
                keepSelected: true
            }, options );
        
            if (!_this.is('select')) {
                console.log('Invalid Element');
                return false;
            }
            
            init(_this, settings);
            
            $(document).off('click', '.droppify_wrapper').on('click', '.droppify_wrapper', function() {
                if (settings.changeBgOnClick) {
                    $('form .droppify_wrapper').removeClass('droppify_wrapper_active_bg');
                    $(this).addClass('droppify_wrapper_active_bg');
                }
                $('form div').removeClass('droppify_wrapper_active');
                $(this).addClass('droppify_wrapper_active');
            });
            
            $(document).off('click', '.droppify_dropdown li').on('click', '.droppify_dropdown li', function(e) {
                var events = [];
                var parent = $(this).parent('ul').parent('div');
                var select = parent.children('select');
                parent.children('span').text($(this).text());
                select.children('option:eq('+$(this).index()+')').prop('selected', true);
                
                if (select.children('option:eq('+$(this).index()+')').val() !== '') {
                    var attributes = parent.children('select')[0].attributes;
                    var aLength = attributes.length;
                    for(i = 0; i < aLength; i++) {
                        if ( attributes[i].name.indexOf('on') == 0 ) {
                            events.push(attributes[i].name.replace('on', ''));
                        }
                    }
                    aLength = events.length;
                    if (aLength > 0) {
                        for(i = 0; i < aLength; i++) {
                            select.trigger(events[i]);
                        }
                    }
                }
                $('form div').removeClass('droppify_wrapper_active');
                e.stopPropagation();
            });
 
        });
    };
    
    function init(elm, settings) {
        
        elm.css('display', 'none');
        elm.parent('div').removeClass('custom_drop_wrapper');
        elm.parent('div').addClass('droppify_wrapper');
        var options = elm.find('option');
        var html = '<span class="droppify_prompt">'+elm.children('option:eq(0)').text()+'</span>';
        
        if (settings.keepSelected) {
            var selected = elm.children('option:selected').index();
            html = '<span class="droppify_prompt">'+elm.children('option:eq('+selected+')').text()+'</span>';
        }
        
        html += '<ul class="droppify_dropdown">'; 
        
        options.each(function(k, v) {
            html += '<li id="'+k+'">'+elm.children('option:eq('+k+')').text()+'</li>';
        });
        
        html += '</ul>';
        
        elm.nextAll('span, ul').remove();
        elm.parent('div.droppify_wrapper').append(html);
    }
    
}( window.jQuery );