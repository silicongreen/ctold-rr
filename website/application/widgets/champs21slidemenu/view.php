<div id="tz_mainmenu" style="top: 0px; margin-left: 0px; width: 210px;" class="col-xs-3">
    <div class="scrollbar" style="height: 136px;">
        <div class="track" style="height: 136px;">
            <div class="thumb" style="top: 0px; height: 100.7091988130563px;">
                <div class="end"></div><!--end class end-->
            </div><!--end class thumb-->
        </div><!--end class track-->
    </div><!--end class scrollbar-->
    <div class="viewport">
        <div class="overview" style="top: 0px;">
            <nav id="plazart-mainnav" class="wrap plazart-mainnav navbar-collapse-fixed-top vertical-nav">
                <div class="navbar">
                    <div class="navbar-inner">

                        <div class="nav-collapse always-show">
                            <div class="plazart-megamenu" style="padding: 0px;">
<ul class="ca-menu">
    <?php if ($slidemenu) : ?>
        <?php
        $i = 0;
        foreach ($slidemenu as $row) :
            ?>			
				<li>
                                        <?php if($row->id==7):?>
                                            <a href="https://www.youtube.com/channel/UCywQj51MiCqHzQAa0Mg4KXg" target="_blank">
                                        <?php else: ?>
                                            <a href="<?php echo base_url() . sanitize($row->name); ?>">
                                        <?php endif; ?>
						<span class="ca-icon" style="background: url(<?php echo base_url($row->menu_icon); ?>) no-repeat;background-size:40px;top:10px;left:20px;"></span>
						<div class="ca-content">
							<h2 class="ca-main f5"><?php echo (isset($row->display_name) && $row->display_name != "") ? $row->display_name : $row->name; ?></h2>
						</div>
					</a>
				</li>
            <?php
            $i++;
        endforeach;
        ?>
    <?php endif; ?>
			<li class="schools topopup">
				 <a href="<?php echo base_url() . "schools"; ?>">
					<span class="ca-icon" style="background: url(<?php echo base_url('styles/layouts/tdsfront/image/schools_new.png'); ?>) no-repeat;background-size:40px;top:10px;left:20px;"></span>
					<div class="ca-content">
						<h2 class="ca-main f5">Schools</h2>
					</div>
				</a>						
			</li>
           

</ul>
<div style="height:100px;"></div>
</div><!--end class plazart-megamenu-->
                        </div><!--end class nav-collapse-->
                    </div><!--end class navbar-inner-->
                </div><!--end class navbar-->
            </nav><!--end id plazart-mainnav-->
        </div><!--end class overview--> 
    </div><!--end class viewport-->
</div><!--end id tz_mainmenu-->	
<script type="text/javascript">
    jQuery(function($) {

        $('#tz_mainmenu').tinyscrollbar();
    });
</script>
<style>
.ca-menu{
    padding:0;
    margin:0px auto;
}
.ca-menu li{
    float:left;
    width: 80px;
    height: 70px;
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
.ca-menu li:last-child{
    margin-bottom: 0px;
}
.ch-img-3 { 
	background-image: url(<?php echo base_url('styles/layouts/tdsfront/image/schools.png'); ?>);	
}
.ca-menu li a{
    text-align: left;
    width: 100%;
    height: 100%;
    display: block;
    color: #333;
    position: relative;
}
.ca-icon{            
    height:40px;
    position: absolute;
    width: 40px;    
    text-align: center;
    -webkit-transition: all 300ms linear;
    -moz-transition: all 300ms linear;
    -o-transition: all 300ms linear;
    -ms-transition: all 300ms linear;
    transition: all 300ms linear;
}
.ca-content{
    position: absolute;
    width: 80px;
    height: 60px;
    top: 35px;
	
}
.ca-main{
	font-size: 11px;
	line-height:13px;
    -webkit-transition: all 300ms linear;
    -moz-transition: all 300ms linear;
    -o-transition: all 300ms linear;
    -ms-transition: all 300ms linear;
    transition: all 300ms linear;
	opacity:0;
	text-align: center;
}
.ca-sub{
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
.ca-menu li:hover{    
	border-left:3px solid red;
}
.ca-menu li:hover .ca-icon{

    color: #93989C;
    opacity: 1;
    text-shadow: 0px 0px 13px #fff;
	
	-webkit-transform: scale(1.4);
	-moz-transform: scale(1.4);
	-o-transform: scale(1.4);
	-ms-transform: scale(1.4);
	transform: scale(1.4);
}
.ca-menu li:hover .ca-main{
    opacity: 1;
    color:#93989C;
	
	
	
    -webkit-animation: moveFromTop 300ms ease-in-out;
    -moz-animation: moveFromTop 300ms ease-in-out;
    -ms-animation: moveFromTop 300ms ease-in-out;
}
.ca-menu li:hover .ca-sub{
    opacity: 1;
    -webkit-animation: moveFromBottom 300ms ease-in-out;
    -moz-animation: moveFromBottom 300ms ease-in-out;
    -ms-animation: moveFromBottom 300ms ease-in-out;
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