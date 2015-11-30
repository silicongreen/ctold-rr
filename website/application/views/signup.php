</header>
<div class="container">
    <div class="wrapper">
    
          

            <div class="col-md-12"  style="padding:0px; margin-top: 120px;">
                <div style="margin:30px auto; width:80%">
                    <iframe id="iframe_change_height"  src="http://www.champs21.com/front/paid/select_school?back_url=<?php echo $back_url;?>&user_type=<?php echo $user_type;?>" style="border:1px solid white" style="border:0;" width="100%" scrolling="no"></iframe>
                </div>
            </div><!-- /.col-md-5 -->

            

        </div><!-- /.container -->

    </div><!-- /.wrapper -->
    
    <script src="<?php echo base_url('js/iframe-resizer/iframeResizer.min.js?v=1'); ?>"></script>
    <script type="text/javascript">

			

			iFrameResize({
				log                     : true,                  // Enable console logging
				inPageLinks             : true,
                                checkOrigin             : false,
                                bodyMargin              : 20
			});

		</script>
