<style>
#main_container .grid_16 .decor{
    text-align:center;
    opacity:1;
    filter:alpha(opacity=1);
 }

.grid_16 .grid_4:hover{
    cursor: pointer;
    opacity:1;
    filter:alpha(opacity=1);
 }
 .grid_16 .grid_4
 {
     width:22.8%;
     opacity:0.5;
     filter:alpha(opacity=0.5);
 }
</style>
<script>
    $(document).ready(function() {
        $(document).on("click", "#main_container .grid_16 .grid_4 img", function()
        {
            var value = this.id;
            $("#value").val(value);
            $("#main_container .grid_16 .grid_4").removeClass("decor");
            $(this).parent().addClass("decor");
         
        });
    });
</script>    
<div id="pjax">
    <?php echo form_open('', array('class' => 'validate_form')); ?>
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">
            <div class="flat_area grid_16" >

            </div>
            <div class="flat_area grid_16">
                <h2 class="section">Layout</h2>

            </div>

                        <div class="box  grid_16" style="padding:15px 0px;" >
                            
                            <input type="hidden" value="<?php echo $model->value;?>" name="value" id="value" />
                                <div class="grid_4 <?php if($model->value == "3-block-default"): ?>decor<?php endif; ?>"   >
                                    
                                    <img id="3-block-default"  width="96%" height="98%" src="<?php echo base_url();?>images/layout/3-block-default.jpg" />
                                   
                                </div>
                                <div class="grid_4 <?php if($model->value == "3-block-with-featured"): ?>decor<?php endif; ?>"   >
                                    
                                        <img id="3-block-with-featured"  width="96%" src="<?php echo base_url();?>images/layout/3-block-with-featured.jpg" />
                                    
                                </div>
                                 <div class="grid_4 <?php if($model->value == "3-block-with-featured-in-two-block"): ?>decor<?php endif; ?>"   >
                                    
                                        <img id="3-block-with-featured-in-two-block" width="96%" src="<?php echo base_url();?>images/layout/3-block-with-featured-in-two-block.jpg" />
                                   
                                </div>
                                <div class="grid_4 <?php if($model->value == "3-block-with-featured-with-two-block"): ?>decor<?php endif; ?>"   >
                                    
                                        <img id="3-block-with-featured-with-two-block" width="96%" src="<?php echo base_url();?>images/layout/3-block-with-featured-with-two-block.jpg" />
                                   
                                </div>
                            </div>    
                        
                        
                            
                        <div class="flat_area grid_16">
                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                        </div>    
                        
                    </div>
                </div>


<?php echo form_close(); ?> 
        </div>
 
