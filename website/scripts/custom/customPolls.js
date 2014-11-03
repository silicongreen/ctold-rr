/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var startingNo = 0;
var $node      = "";
var maxNo        =0;
$(document).ready(function() {
    
    
 
    
    

    $(document).on("click", "a.removeVar", function()
    {
        if(startingNo<=2)
        {
            alert("You must be add atleast 2 option");
        }
        {
            startingNo--;
            $(this).parents('fieldset:eq(0)').remove();
            $("#add_more_button").show();
        }
 
    });
    //add a new node
    $('#addVar').on('click', function(){
        
        startingNo++;
        if(startingNo>maxNo)
        {
          alert("You can't add more then "+maxNo+" option");
           
        }
        else
        {
          $node = '<fieldset class="label_side top"><label>Option '+startingNo+'<span  ><a href="javascript:void(0)" class="removeVar">Remove</a></span></label><div><input id="value'+startingNo+'" name="value_'+startingNo+'" class="text" type="text"></div></div></fieldset>'
  
          $(".option_box").append($node); 
        } 
        if(startingNo==maxNo)
        {
            $("#add_more_button").hide();
        }    
        
        
    });
});
