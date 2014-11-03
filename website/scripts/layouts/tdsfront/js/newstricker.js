/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var newsitems;
var curritem=0;
var go = 0;
var approve = true;
var next_values = false;
var speed = 2;
var curPos;
var defaultTrickerTime = 5000;  
var newsflag = true;
            
            
$(document).ready(function(){
             
    var extra_link = "";
    if ( $("#archive").length != 0 )
    {
         extra_link = "?archive=" + $("#archive").val();
    }
    $("#ticker-to-remove").parent().remove();
    $('#js-news').ticker({
        titleText : ""
    });          
//    {
//        htmlFeed: false,
//        ajaxFeed: true,
//        feedUrl: $("#base_url").val()+'front/ajax/get_tricker_news' + extra_link,
//        feedType: 'json'
//    }
                
                
    newsitems = $("#ticker li").hide().size();
    curritem  =  newsitems-1;
    $("#ticker li:eq("+curritem+")").show();

				
    $(".news").hover(function() {
        next_values = false;
        $(this).stop(true);				
    },function(){
				
        $(this).trigger("click");				
    });


    $(".news").click(function(){
                    
        curPos = parseInt($(this).css("left"), 10);
        width = $(this).outerWidth();
                    
        //console.log("curPos = "+curPos);
                    
        durationTime = (width + curPos) * speed;

        $(this).animate({
            left: "0px"
        },{
            duration: durationTime,
            complete:function(){

                if(!next_values)
                {
                    next_values = true;
                    setTimeout(delay_wait,trickerTime);
                }

            },
            progress:function(){
                                                      
            }
            ,
            done:function(){

            // console.log(" done ::"+$(this).css("left"));
            },
            fail:function(){
                next_values = false;
            //console.log(" fail ::"+$(this).css("left"));
            }
        },"linear");
    });



    if(newsflag)
    {
        newsflag =false;
        ticknews();
    }
    
    //dataload();
    
    setInterval(dataload,60000);
                
});


function dataload()
{             
    var extra_link = "";
    if ( $("#archive").length != 0 )
    {
         extra_link = "?archive=" + $("#archive").val();
    }           
    $.ajax({
        url:$("#base_url").val()+'front/ajax/get_tricker_news' + extra_link,
        async :false,
        success:function(response){
                        
            // console.log(" tricker ajax load.");
            var data = $.parseJSON(response);

            $.each(data, function(key, val) {   
                            
                            
                // console.log(" href = "+val.href+" is_breaking ="+val.is_breaking+" ");
                          
                if(val.is_breaking == 1)
                {
                    $("ul#ticker li:eq("+key+") a").addClass("breaking-news");   
                } 
                else
                {
                    $("ul#ticker li:eq("+key+") a").removeClass("breaking-news"); 
                    $("ul#ticker li:eq("+key+") a").addClass("news"); 
                }  

                         
                $("ul#ticker li:eq("+key+") a").attr("href",val.href);
                $("ul#ticker li:eq("+key+") a").attr("title",val.headline);
                $("ul#ticker li:eq("+key+") a").html(val.headline);
                              
            });
        }
    });  
                       
}


function ticknews() {

    $("#ticker li:eq("+curritem+")").hide();
    curritem = ++curritem%newsitems;
               
               
    // $("a").css("left","600px");
    $("#ticker li:eq("+curritem+")").show();
    $("#ticker li:eq("+curritem+")").find(".news").css("left","600px");
                
                
    news_type_check($("#ticker li:eq("+curritem+")"));
    $("#ticker-"+curritem).children(".news").trigger("click");    
}

function delay_wait()
{
    if(next_values){
        next_values = false;
        ticknews();
    }
}
            
            
function news_type_check(list)
{

    if(list.children("a").hasClass("breaking-news") == true)
    {
        $("#newsticker-title").html("Breaking News");
        $("#newsticker-title").css("background","#EA0E15");
        list.children("a").css("color","#EA0E15");
    }
    else
    {
        $("#newsticker-title").html("Latest News");
        $("#newsticker-title").css("background","#4E4D4D");
        list.children("a").css("color","#000000");
    }  
                
    trickerTime =  (list.children("a").attr("rel")) ?list.children("a").attr("rel"): defaultTrickerTime;               
}
 


