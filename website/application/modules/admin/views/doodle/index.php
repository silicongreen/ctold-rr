<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
       
        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="80%" >

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Doodle</h2>

            </div>
			<div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Current Doodle</h2>

                        
                        <?php echo form_open('',array('class' => 'validate_form','enctype' => "multipart/form-data"));?>
                           
                            <div style="position:relative;">
								<p style="margin:20px;">Update only PNG type file with Width : 1899px, Height : 160px, Max-size : 2000KB.</p>
								<?php 
									if (@file_exists(FCPATH."styles/layouts/tdsfront/images/doodle-f.png"))
									{	?>
										<img src="<?php echo "/styles/layouts/tdsfront/images/doodle-f.png";?>" width="100%">
										<a style="float:right;font-size:60px;color:red;position:relative;top:-80px;right:20px;border:3px solid red;line-height:60px;" href="/admin/doodle/delete" title="Delete Doodle">X</a>
								<?php  }
									else
									{?>
										<p style="margin:30px;">No Doodle Image.Please upload one.</p>
								<?php }
								?>
							</div>
							<?php if($msg !=""){ ?>
								<div style="background-color: #fff1ab;margin-top:20px;border: 1px solid #efdf95;font-weight: bold;padding: 5px;text-align: center;">
							<?php echo $msg;?>
							</div>
							<?php } ?>
                            <fieldset class="label_side top" id="js_type_image">
                                <label for="required_field">Doodle Image<span>Upload a PNG file.</span></label>
                                <div>
                                    <input id="image" type="file" size="30" name="image" required >                                    
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            


                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                       <?php echo form_close();?>  
                    </div>
                </div>


            </div>

        </div>

            
        </div>
