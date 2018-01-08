/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var base_url_category_ckfinder = $("#base_url").val()+"/ckeditor/kcfinder/";
$(document).ready(function() {
    function checkURLImage(url) 
    {
        return(url.match(/\.(jpeg|jpg|gif|png)$/) != null);
    }
    function checkURLPdf(url) 
    {
        return(url.match(/\.(pdf)$/) != null);
    }
    $(document).on("click", "#select_cover_photo", function()
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
                    $title = '<img src="'+url+'" width="70">';
                }
                else
                {
                    alert("You can add only image for cover photo");
                    return false;
                }
                
                var url_main =url.replace($("#base_url").val(), '');
                
             
                
                var $html = '<div>'+$title+'<input type="hidden" name="image" value="'+url_main+'"></div>';
              
                $("#select_cover_box").html($html);
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
                    alert("You can add only image for category icon");
                    return false;
                }
                
                var url_main =url.replace($("#base_url").val(), '');
                
             
                
                var $html = '<div>'+$title+'<input type="hidden" name="icon" value="'+url_main+'"><a class="text-remove"></a></div>';
              
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
    
    
    $(document).on("click", "#select_cover", function()
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
                    alert("You can add only image for category cover");
                    return false;
                }
                
                var url_main =url.replace($("#base_url").val(), '');
                
             
                
                var $html = '<div>'+$title+'<input type="hidden" name="cover" value="'+url_main+'"><a class="text-remove"></a></div>';
              
                $("#select_cover_box").html($html);
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
    
    
    
    $(document).on("click", "#select_menu_icon", function()
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
                    alert("You can add only image for category menu icon");
                    return false;
                }
                
                var url_main =url.replace($("#base_url").val(), '');
                
             
                
                var $html = '<div>'+$title+'<input type="hidden" name="menu_icon" value="'+url_main+'"><a class="text-remove"></a></div>';
              
                $("#select_menu_icon_box").html($html);
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
    
    
    $(document).on("change", "#menu_types", function()
    {
  
         if($("#menu_types").val()==1) 
         {
             $(".show_on_parent_menu").show();
             $("#show_on_sub_menu").hide();
         } 
         else
         {
            $(".show_on_parent_menu").hide();
            $("#show_on_sub_menu").show(); 
             
         }    
       
    
    });
    
    
    $(document).on("click", "#chk_header_menu", function()
    {
  
         if($("#chk_header_menu").is(':checked')) 
         {
             $("#div_show_in_checked").show();
         } 
         else
         {
             $("#div_show_in_checked").hide(); 
             
         }    
       
    
    });
    
    $(document).on("click", "#chk_footer_menu", function()
    {
  
         if($("#chk_footer_menu").is(':checked')) 
         {
             $("#div_show_in_checked_footer").show();
         } 
         else
         {
             $("#div_show_in_checked_footer").hide(); 
             
         }    
       
    
    });
    
    
    
    $(document).on("click", "#select_pdf_photo", function()
    {
     
        window.KCFinder = 
        {
            callBack: function(url) 
            {
                
                var $title= "";
                
                var value = url.substring(url.lastIndexOf('/') + 1);
                
                if(checkURLPdf(url))
                {  
                    window.KCFinder = null;
                    $title = '<a href="'+url+'" target="_blank">Pdf</a>';
                }
                else
                {
                    alert("You can add only Pdf type file");
                    return false;
                }
                
                var url_main =url.replace($("#base_url").val(), '');
                
             
                
                var $html = '<div>'+$title+'<input type="hidden" name="pdf" value="'+url_main+'"></div>';
              
                $("#select_pdf_box").html($html);
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
});