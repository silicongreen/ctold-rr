<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<div class="container" style="width: 77%;min-height:250px;">	 
	<div style="margin:30px 20px;height:60px;">
			<div style="float:left">
				<h2 class="f2">School Information</h2>
			</div>
			<div class="header-bg" style="display:block;float:right;  margin-top:5px;">
				<div style="float: left;margin:5px;">                
					<form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">                    
						<input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
						<input class="submit search-button" value="" type="submit" />
					</form>                
				</div>
			</div>
	</div>
	<?php
			$widget = new Widget;
			$widget->run('champs21schoolsearch', $ci_key);
	?>
	<div style="">
		<ul style="margin: 30px 20px;">
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

<style>
#backgroundPopup { 
	z-index:5000;
	position: fixed;
	display:none;
	height:100%;
	width:100%;
	background:#000000;	
	top:0px;
	left:0px;
}
#toPopup {
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
    background: none repeat scroll 0 0 #FFFFFF;
    padding: 40px 20px !important;	
    border-radius: 3px 3px 3px 3px;
    color: #333333;
    display: block !important;
    font-size: 14px;
    position: relative !important;
    left: 0px !important;
    top: 0px !important;
    width: 96% !important;
    z-index: 6000 !important;
	margin:30px 20px !important;
}
div.loader {
    background: url("../merapi/img/bx_loader.gif") no-repeat scroll 0 0 transparent;
    height: 32px;
    width: 32px;
	display: none;
	z-index: 9999;
	top: 40%;
	left: 50%;
	position: absolute;
	margin-left: -10px;
}
div.close {
    background: url("../merapi/img/close.png") no-repeat scroll 0 0 transparent;
    bottom: 30px;
    cursor: pointer;
    float: right;
    height: 30px;
    left: 10px;
    position: relative;
    width: 31px;
	display:none !important;
}
</style>