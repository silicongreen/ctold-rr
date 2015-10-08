<?php //echo "<pre>";

//print_r($toprated_news);
?>
<div class="row postlist-tab">  
                    
	<div class="col-md-12 col-sm-12">
		<div class="tabbable"> <!-- Only required for left/right tabs -->
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab1" data-toggle="tab">Top Rated</a></li>
				<li><a href="#tab2" data-toggle="tab">Most Populer</a></li>
				<li><a href="#tab3" data-toggle="tab" style="">Editor's Pick</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
					<?php foreach($toprated_news as $toprated){ 					
					
					?>
						<?php
							$image_related = "";
							if (isset($toprated['lead_material']) && $toprated['lead_material']) {
								$image_toprated = $toprated['lead_material'];
							} else if (isset($toprated['image']) && $toprated['image']) {

								$image_toprated = $toprated['image'];
							}
                                                        
                                                        $link_top =  base_url() . sanitize($toprated['headline']) . "-" . $toprated['id'];
						?>
						<div class="media">
						  <div class="media-left">
						  <?php if ($image_toprated): ?>
							<!--a href="#">
							  <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="<?php echo $toprated?>" data-holder-rendered="true" style="width: 64px; height: 64px;">
							</a-->
							<a href="<?php echo $link_top; ?>" style="border:0px;"><img src="<?php echo $image_toprated; ?>" class="media-object" data-holder-rendered="true" style="width: 64px; height: 64px;" /></a>
						  <?php endif; ?>
						  </div>
						  <div class="media-body">
							<p class="media-heading"><a style="color:#000;" href="<?php echo $link_top; ?>"><?php echo $toprated['headline']; ?></a></p>                                            
						  </div>
						</div>
					<?php } ?>	
					
				</div>
				<div class="tab-pane" id="tab2">
					<?php foreach($mostpopuler_news as $mostpopuler){ 					
					
					?>
						<?php
							$image_related = "";
							if (isset($mostpopuler['lead_material']) && $mostpopuler['lead_material']) {
								$image_mostpopuler = $mostpopuler['lead_material'];
							} else if (isset($mostpopuler['image']) && $mostpopuler['image']) {

								$image_mostpopuler = $mostpopuler['image'];
							}
                                                        
                                                         $link_most =  base_url() . sanitize($mostpopuler['headline']) . "-" . $mostpopuler['id'];
						?>
						<div class="media">
						  <div class="media-left">
						  <?php if ($image_mostpopuler): ?>
							<!--a href="#">
							  <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="<?php echo $image_mostpopuler?>" data-holder-rendered="true" style="width: 64px; height: 64px;">
							</a-->
							<a href="<?php echo $link_most; ?>" style="border:0px;"><img src="<?php echo $image_mostpopuler; ?>" class="media-object" data-holder-rendered="true" style="width: 64px; height: 64px;" /></a>
						  <?php endif; ?>
						  </div>
						  <div class="media-body">
							<p class="media-heading"><a style="color:#000;" href="<?php echo $link_most; ?>"><?php echo $mostpopuler['headline']; ?></a></p>                                            
						  </div>
						</div>
					<?php } ?>	
				</div>
				<div class="tab-pane" id="tab3">
					<?php foreach($editorpicks_news as $editorpicks){ 					
					
					?>
						<?php
							$image_related = "";
							if (isset($editorpicks['lead_material']) && $editorpicks['lead_material']) {
								$image_editorpicks = $editorpicks['lead_material'];
							} else if (isset($editorpicks['image']) && $editorpicks['image']) {

								$image_editorpicks = $editorpicks['image'];
							}
                                                        $link_picks =  base_url() . sanitize($editorpicks['headline']) . "-" . $editorpicks['id'];
						?>
						<div class="media">
						  <div class="media-left">
						  <?php if ($image_editorpicks): ?>
							<!--a href="#">
							  <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="<?php echo $image_editorpicks?>" data-holder-rendered="true" style="width: 64px; height: 64px;">
							</a-->
							<a href="<?php echo $link_picks; ?>" style="border:0px;"><img src="<?php echo $image_editorpicks; ?>" class="media-object" data-holder-rendered="true" style="width: 64px; height: 64px;" /></a>
						  <?php endif; ?>
						  </div>
						  <div class="media-body">
							<p class="media-heading"><a style="color:#000;" href="<?php echo $link_picks; ?>"><?php echo $editorpicks['headline']; ?></a></p>                                            
						  </div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.postlist-tab{padding:20px 15px;}
	.postlist-tab #grid_1{margin: 0px auto !important;}
	.postlist-tab ul.nav{margin:0px;}
	.postlist-tab ul.nav-tabs{border:0px;}
	.postlist-tab ul.nav-tabs li{letter-spacing:0px;width:33.33%;height:62px;float: left;line-height: 20px;    display: list-item;list-style: none;margin-bottom:0px;}
	.postlist-tab ul.nav-tabs > .active > a, .nav-tabs > .active > a:hover, .nav-tabs > .active > a:focus {
						color: #fff;
						background: #DC3434;						
						cursor: default;
						padding-bottom: 12px;
						line-height: 20px;
                                                padding:10%;
						border: 1px solid #DEE9EB;
						font-family:Arial;
						}
	.postlist-tab ul.nav-tabs > li > a {                                        
						color: #000;
						padding:10%;
						margin:0px;
						line-height: 20px;
						border: 1px solid #DEE9EB;
						border-right-color: #DEE9EB;
						border-bottom-color: #DEE9EB;
						-webkit-border-radius: 0px 0px 0 0;
						-moz-border-radius: 0px 0px 0 0;
						border-radius: 0px 0px 0 0;
						background: #DEE9EB;
						font-family:Arial;
						font-size:13px;
						font-weight:normal;
					}
	.postlist-tab .tabbable{background-color:#fff;}
	.postlist-tab .tab-content{padding:0px;}
	.postlist-tab .media-heading-p{color:#000;font-family:sans-serif;}
	.postlist-tab .media-heading-p a{color:#000;font-size:15px;}
        
        .media {
  margin-top: 0px;
  padding:15px;
  border-bottom: 1px solid #ccc;
}
.media:first-child {
  margin-top: 0;
}
.media,
.media-body {
  overflow: hidden;
  zoom: 1;
}
.media:hover{
	background-color:#F4F8F9;
	cursor: pointer;
}
.media-body {
  width: 10000px;
}
.media-object {
  display: block;
}
.media-object.img-thumbnail {
  max-width: none;
}
.media-right,
.media > .pull-right {
  padding-left: 10px;
}
.media-left,
.media > .pull-left {
  padding-right: 10px;
}
.media-left,
.media-right,
.media-body {
  display: table-cell;
  vertical-align: top;
}
.media-middle {
  vertical-align: middle;
}
.media-bottom {
  vertical-align: bottom;
}
.media-heading {
  margin-top: 0;
  margin-bottom: 5px;
}
.media-list {
  padding-left: 0;
  list-style: none;
}
</style>