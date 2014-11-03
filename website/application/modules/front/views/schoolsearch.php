<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<?php

?>
<div class="container" style="width: 77%;min-height:250px;">
	<div style="">
		<ul style="margin: 30px 20px 20px 35px;">
			<?php foreach ( $schooldata as $row ) :?>
                        <?php
                        if(isset($row['picture']) && $row['picture'])
                        {
                            $row['logo'] = $row['picture'];
                        }   
                        ?>
			<li style="list-style:none;">
				<div style="background:#FFF;padding:20px;height:200px;overflow:hidden;">
					<div style="float:left;width:20%;">
						<img src="<?php echo base_url($row['logo']);?>" width="160">
					</div>
					<div style="float:left;width:50%;">
						<p class="f2" style="font-size:30px;"><a href="<?php echo base_url().'schools/'.sanitize($row['name']);?>"><?php echo $row['name']; ?></a></p>
						<p class="f5" style="font-size:16px;color:#9CD64E;"><?php echo $row['district']; ?></p>
						<p class="f5" style="font-size:16px;color:#000;"><?php echo $row['medium']; ?></p>
						<p class="f5" style="font-size:16px;color:#000;"><?php echo $row['level']; ?></p>
						<p class="f5" style="font-size:14px;"><?php echo $row['location']; ?></p>
					</div>
					<div style="float:left;width:30%;margin-top:30px;">
						<p class="f5" style="font-size:14px;"><?php //echo $row['location']; ?></p>
					</div>					
				</div>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>