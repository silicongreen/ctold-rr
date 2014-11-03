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
            $('#Quotes_personality_id').val(ui.item.personality_id);
        },
    });
})
    