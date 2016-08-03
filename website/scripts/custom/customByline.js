/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var base_url_category_ckfinder = $("#base_url").val()+"ckeditor/kcfinder/";
$(document).ready(function() {
   
    
    function checkURLImage(url) 
    {
        return(url.match(/\.(jpeg|jpg|gif|png)$/) != null);
    }
    $('.datetimepicker_class').datetimepicker
    ({
        timeFormat: "HH:mm",
        dateFormat: "yy-mm-dd"
    });
    $(document).on("click", "#select_icon", function()
    {
     
        window.KCFinder = 
        {
            callBack: function(url) 
            {
                
                var $title= "";
              
                
                var value = url.substring(url.lastIndexOf('/') + 1);
                
                if(checkURLImage(url))
                {  
                    window.KCFinder = null;
                    $title = '<img src="'+url+'" width="50">';
                }
                else
                {
                    alert("You can add only image");
                    return false;
                }
                
                var url_main =url.replace($("#base_url").val(), '');
                
          
                var $html = '<div>'+$title+'<input type="hidden" name="image" value="'+url_main+'"><a class="text-remove"></a></div>';
              
                $("#select_icon_box").html($html);
                
                $.fancybox.close();
               
            }
        };
        
        $.fancybox({
            'width'		        : "85%",
            'height'                    : "90%",
            'autoScale'                 : true,
            'href'			: base_url_category_ckfinder+ 'browse.php?type=files',
            'title'                     : false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'		        : 'iframe'
                    
        });
       
        
        
    });
    
    $(document).on("click", ".text-remove", function()
    {
           
        $(this).parents('div:eq(0)').remove();
    
    });
    
    
    
});