<script src = "<?php echo base_url();?>merapi/script/jquery.iosslider.js"></script>
<style type="text/css">
			body {
				/* overflow: hidden; */
				margin: 0;
			}
			
			.responsive_height {
				height: 0;
				padding: 0 0 0 0; /* responsive slider height = 40% of the browser width; ie. slide image aspect ratio: w10xh4 */				
				overflow: visible;
			}
			
			.responsive_height > .container {
				position: absolute;
				width: 100%;
				height: 100%;
				overflow: hidden;
			}
			
			.iosSlider {
				width: 100%;
				height: 100%;
				background: #f7f7f7;
                                z-index: 1000;
                                top: 100px;
			}
			
			.iosSlider .slider {
				width: 100%;
				height: 100%;
			}
			
			.iosSlider .slider .item {
				position: relative;
				top: -5px;
				left: 0;
				
				width: 20%;
				height: 100%;
				margin: 0 0 0 0;
			}
			
			.iosSlider .slider .item img {
				width: 100%;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				
				var SNAP_MULTIPLE = 5;
				
				$('.iosSlider').iosSlider({
					desktopClickDrag: true,
					snapToChildren: false
				});
				
				up();
				
				function up() {
					
					$('.iosSlider').one('mousedown', function(e) {
					
						down();
						
					});
					
				}
				
				function down() {
				
					$('.iosSlider').one('mouseup', function(e) {
					
						var data = $('.iosSlider').data('args');
						var round = SNAP_MULTIPLE * Math.round((data.currentSlideNumber - 1) / SNAP_MULTIPLE) + 1;
						
						setTimeout(function() {
							$('.iosSlider').iosSlider('goToSlide', round);
						}, 10);
						
						up();
						
					});
				
				}
				
			});
		</script>
		<div class = 'responsive_height'>
			<div class = 'container'>
			
				<div class = 'iosSlider'>
					<div class = 'slider'>
					<?php if ($slidemenu) : ?>
					<?php
					$i = 0;
					foreach ($slidemenu as $row) :
						?>	
						<div class = 'item'>
							<a href="<?php echo base_url() . sanitize($row->name); ?>">
								<span class="ca-icon" style="background: url(<?php echo base_url($row->menu_icon); ?>) no-repeat;background-size:40px;top:10px;left:20px;"></span>
								<div class="ca-content">
									<h2 class="ca-main f5"><?php echo (isset($row->display_name) && $row->display_name != "") ? $row->display_name : $row->name; ?></h2>
								</div>
							</a>
						</div>
						<?php
								$i++;
							endforeach;
							?>
						<?php endif; ?>
						
						<div class = 'item'>
							<a href="<?php echo base_url() . "schools"; ?>">
								<span class="ca-icon" style="background: url(<?php echo base_url('styles/layouts/tdsfront/image/schools.png'); ?>) no-repeat;background-size:40px;top:10px;left:20px;"></span>
								<div class="ca-content">
									<h2 class="ca-main f5">Schools</h2>
								</div>
							</a>
						</div>
						
					</div>
				</div>
			</div>
		</div>
                
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