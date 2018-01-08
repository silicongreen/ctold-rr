<div style="
     color: #ff0000;
     float: left;
     height: 35px;
     margin-left: 20px;
     margin-top: 7px;
     width: 11%;" class="bababa">

</div>
<?php
$my_uri = $_SERVER[REQUEST_URI];
$my_uri = explode("/", $my_uri);
?>
<div class="new_top_menu" style="float: left; width: 86%; height:38px; margin-top: 5px; overflow: hidden;">
    <ul class="ca-menu-new" style="margin: 0 0 10px 10px;">
        <?php if ($slidemenu) : ?>
            <?php
            $i = 0;
            foreach ($slidemenu as $row) :
                ?>			
                <li>
                    <?php if ($row->id == 7): ?>
<!--                        <a href="https://www.youtube.com/channel/UCywQj51MiCqHzQAa0Mg4KXg" target="_blank">-->
                        <a href="<?php echo base_url('spellingbee'); ?>">
                    <?php elseif ($row->id == 81): ?>
                        <a href="<?php echo base_url('sciencerocks'); ?>">
                    <?php else: ?>
                        <a href="<?php echo base_url() . sanitize($row->name); ?>">
                    <?php endif; ?>
                        <!--<span class="ca-icon-new <?php echo ($my_uri[1] == sanitize($row->name)) ? "active_header_menu" : ""; ?>" style="background: url(<?php echo base_url($row->menu_icon); ?>) no-repeat;background-size:55px;top:0px;left:-5px;"></span>-->
                        <span id="nmicon_<?php echo $row->id;?>" class="ca-icon-new <?php echo ($my_uri[1] == sanitize($row->name)) ? "active_header_menu" : ""; ?>" style="background: url(<?php echo 'http://www.champs21.com/' . $row->menu_icon; ?>) no-repeat;background-size:55px;top:0px;left:-5px;"></span>

                    </a>
                    <div class="ca-content-main" style="display:none;">
                        <h2 class="ca-main-new f5"><?php echo (isset($row->display_name) && $row->display_name != "") ? $row->display_name : $row->name; ?></h2>
                    </div>
                </li>
                <?php
                $i++;
            endforeach;
            ?>
        <?php endif; ?>
        			<li class="schools topopup">
                                         <a href="<?php echo base_url() . "schools"; ?>">
                                                <span class="ca-icon-new <?php echo ($my_uri[1] == "schools") ? "active_header_menu" : ""; ?>" style="background: url(<?php echo base_url('styles/layouts/tdsfront/image/schools_new.png'); ?>) no-repeat;background-size:55px;top:0px;left:-5px;"></span>
                                                <div class="ca-content-main" style="display:none;">
                                                        <h2 class="ca-main-new f5">Schools</h2>
                                                </div>
                                        </a>						
                                </li>


    </ul>
</div>
<script type="text/javascript">

    jQuery(function ($) {
        var i = 0;

        $(document).off('mouseenter', ".ca-menu-new li").on('mouseenter', ".ca-menu-new li", function () {
            if (i == 0)
            {
                var h = $(this).find(".ca-content-main").html();
                $('.bababa').append(h);
                $('.bababa .ca-main-new').addClass("new_animation");
                $(".bababa").css("border-left", "4px solid #cccccc");
                i = 1;

            }
        });
        $(document).off('mouseleave', ".ca-menu-new li").on('mouseleave', ".ca-menu-new li", function () {
            $('.bababa').empty("");
            $(".bababa").css("border-left", "0px solid #cccccc");
            i = 0;


        });


//$( ".ca-menu-new li" ).mouseover(function(e) {
//  if(i == 0)
//    {       
//        
//        var h = $(this).find( ".ca-content-main" ).html();         
//        $('.bababa').append(h);
//        $('.bababa .ca-main-new').addClass("new_animation");
//        $(".bababa").css("border-left","4px solid #cccccc");
//        i = 1;
//        console.log(i);
//        
//    }
//    
//}).mouseout(function() {
//    $('.bababa').empty("");
//    $(".bababa").css("border-left","0px solid #cccccc");
//    i = 0;console.log(i);
//  
//});
    });
</script>
<style>
    .ca-menu-new{
        padding:0;
        float:left;
        text-align:center;
    }
    .ca-menu-new li{
        float:left;
        width: 48px;
        height: 44px;
        overflow: hidden;
        position: relative;
        display: block;

        margin-bottom: 4px;
        -webkit-transition: all 300ms linear;
        -moz-transition: all 300ms linear;
        -o-transition: all 300ms linear;
        -ms-transition: all 300ms linear;
        transition: all 300ms linear;
    }
    .ca-menu-new li:last-child{
        margin-bottom: 0px;
    }
    .ch-img-3 { 
        background-image: url(<?php echo base_url('styles/layouts/tdsfront/image/schools.png'); ?>);	
    }
    .ca-menu-new li a{
        text-align: left;
        width: 100%;
        height: 100%;
        display: block;
        color: #333;
        position: relative;
    }
    .ca-icon-new{            
        height:80px;
        position: absolute;
        width: 48px;    
        text-align: center;
        -webkit-transition: all 300ms linear;
        -moz-transition: all 300ms linear;
        -o-transition: all 300ms linear;
        -ms-transition: all 300ms linear;
        transition: all 300ms linear;
    }
    .ca-content-main{
        position: absolute;
        width: 80px;
        height: 60px;
        top: 1px;
    }
    .ca-main-new{
        font-size: 13px;
        color:red;
        line-height:13px;
        margin-left:10px;
        white-space:nowrap;
        text-align:left;
        /*    margin-top:10px;
            -webkit-transition: all 300ms linear;
            -moz-transition: all 300ms linear;
            -o-transition: all 300ms linear;
            -ms-transition: all 300ms linear;
            transition: all 300ms linear;*/
        opacity:1;
        font-weight:bold;
    }
    .ca-sub-new{
        font-size: 14px;
        color: #666;
        -webkit-transition: all 300ms linear;
        -moz-transition: all 300ms linear;
        -o-transition: all 300ms linear;
        -ms-transition: all 300ms linear;
        transition: all 300ms linear;
        opacity:0;
        text-align: center;
    }
    .ca-menu-new li:hover{    
        /*border-left:3px solid red;*/
    }
    .ca-menu-new li:hover .ca-icon-new{

        color: #93989C;
        opacity: 1;
        text-shadow: 0px 0px 13px #fff;

        /*	-webkit-transform: scale(1.4);
            -moz-transform: scale(1.4);
            -o-transform: scale(1.4);
            -ms-transform: scale(1.4);
            transform: scale(1.4);*/
    }
    .ca-menu-new li:hover .ca-icon-new{
        opacity: 1;
        color:#93989C;    
        top:-36px !important;


        -webkit-animation: moveFromTop 300ms ease-in-out;
        -moz-animation: moveFromTop 300ms ease-in-out;
        -ms-animation: moveFromTop 300ms ease-in-out;
    }
    .new_animation{


        -webkit-animation: moveFromTop 300ms ease-in-out;
        -moz-animation: moveFromTop 300ms ease-in-out;
        -ms-animation: moveFromTop 300ms ease-in-out;
    }
    .ca-menu-new li:hover .ca-sub-new{
        opacity: 1;
        -webkit-animation: moveFromBottom 300ms ease-in-out;
        -moz-animation: moveFromBottom 300ms ease-in-out;
        -ms-animation: moveFromBottom 300ms ease-in-out;
    }

    .active_header_menu
    {
        top:-36px !important;
    }
    @-webkit-keyframes moveFromBottom {
        from {
            opacity: 0;
            -webkit-transform: translateY(200%);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0%);
        }
    }
    @-moz-keyframes moveFromBottom {
        from {
            opacity: 0;
            -moz-transform: translateY(200%);
        }
        to {
            opacity: 1;
            -moz-transform: translateY(0%);
        }
    }
    @-ms-keyframes moveFromBottom {
        from {
            opacity: 0;
            -ms-transform: translateY(200%);
        }
        to {
            opacity: 1;
            -ms-transform: translateY(0%);
        }
    }

    @-webkit-keyframes moveFromTop {
        from {
            opacity: 0;
            -webkit-transform: translateY(-200%);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0%);
        }
    }
    @-moz-keyframes moveFromTop {
        from {
            opacity: 0;
            -moz-transform: translateY(-200%);
        }
        to {
            opacity: 1;
            -moz-transform: translateY(0%);
        }
    }
    @-ms-keyframes moveFromTop {
        from {
            opacity: 0;
            -ms-transform: translateY(-200%);
        }
        to {
            opacity: 1;
            -ms-transform: translateY(0%);
        }
    }


</style>
