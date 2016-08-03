$(document).ready(function(){
    $( "#personality_search" ).autocomplete({
        type: 'post',
        dataType: "json",
        minLength: 2,
        source: function( request, response ) {
            $.getJSON( $("#base_url").val()+"admin/quote/personality_search", request, function(data, status, xhr) {
                response( $.map(data.personalities, function(item){
                    return {
                        label: item.name,
                        value: item.name,
                        personality_id: item.id
                    }
                }));
            });
        },
        select: function( event, ui ) {
            $('#Voice_personality_id').val(ui.item.personality_id);
        },
    });
})

$(document).ready(function(){
    $( "#topic_search" ).autocomplete({
        type: 'post',
        dataType: "json",
        minLength: 2,
        source: function( request, response ) {
            $.getJSON( $("#base_url").val()+"admin/voice_box/topic_search", request, function(data, status, xhr) {
                response( $.map(data.topic, function(item){
                    return {
                        label: item.topic,
                        value: item.topic,
                        topic_id: item.id
                    }
                }));
            });
        },
        select: function( event, ui ) {
            $('#Voice_topic_id').val(ui.item.topic_id);
        },
    });
})
    