$(document).ready(function(){
    $( "#channel_search" ).autocomplete({
        type: 'post',
        dataType: "json",
        minLength: 2,
        source: function( request, response ) {
            $.getJSON( $("#base_url").val()+"admin/watch/channel_search", request, function(data, status, xhr) {
                response( $.map(data.channels, function(item){
                    return {
                        label: item.name,
                        value: item.name,
                        channel_id: item.id
                    }
                }));
            });
        },
        select: function( event, ui ) {
            $('#WhatsOn_channel_id').val(ui.item.channel_id);
        },
    });
    
    $(document).on('change','#tree input:checkbox', function(){
        $('#tree').find('input:checkbox').prop('checked', false);
        $(this).prop('checked',true);
    });
    
})
    