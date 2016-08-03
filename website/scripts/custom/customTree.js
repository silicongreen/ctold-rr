
  

$(document).ready(function() {
     
        $('#tree').tree({
            onCheck: {  }, 
            onUncheck: { ancestors: '', descendants: '', others: '' }}
        );
        
        if ( $('.tab_tree').length > 0 )
        {
            $('.tab_tree').tree({});
        }
 
 });