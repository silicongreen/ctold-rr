/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
    
    $(document).on("click", "#select_media", function()
    {
        
        window.KCFinder = 
        {
            callBackMultiple: function(files) 
            {
                window.KCFinder = null;
                var $title= "";
                var $html = "";
               
                for (var i = 0; i < files.length; i++)
                {
                    var url = files[i];
                  
                    
                    var a_caption_source;
                    var value = url.substring(url.lastIndexOf('/') + 1);

                    if(checkURLImage(url))
                    {  
                        $title = '<img src="'+url+'" width="70">';
                        
                    }    
                    else
                    {
                        $title = '<a href="'+url+'">'+value+'</a>';
                    }
                    
                    
                    
                    


                    $html= '<div class="gallery_image"><fieldset class="label_side top"><label for="required_field">'+$title+'</label></fieldset><input type="hidden" name="related_img[]" value="'+url+'"><a class="text-remove"></a></div>';
                    $("#gallery_box").append($html);
                }
               
                $.fancybox.close();
               
            }
        };
         window.open(base_url + 'browse.php?type=files&dir=files/public',
        'kcfinder_multiple', 'status=0, toolbar=0, location=0, menubar=0, ' +
        'directories=0, resizable=1, scrollbars=0, width=800, height=700'
        );
        

       
        
        
    });
    $(document).on("click", ".text-remove", function()
    {
           
        $(this).parents('div:eq(0)').remove();
    
    });
    
    
//    
//
});