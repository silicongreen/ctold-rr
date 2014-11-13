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
				background: #E3E3E3;
			}
			
			.iosSlider .slider {
				width: 100%;
				height: 100%;
			}
			
			.iosSlider .slider .item {
				position: relative;
				top: 0;
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