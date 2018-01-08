<script src="<?php echo  base_url() ?>scripts/custom/customCategory.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> Category Cover</h2>
                        <?php 
                        if($_POST)
                        create_validation($model);
                        ?>

                        <?php echo form_open('',array('class' => 'validate_form','enctype'=>'multipart/form-data'));?>  
                            
                            <fieldset class="label_side top">
                                    <label for="required_field">Issue Date</label>
                                    <div>
                                        <input id="issue_date" name="issue_date" class="datepicker"  value="<?php echo  $model->issue_date ?>"  type="text" >  
                                    </div>     
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                    <label for="required_field">Cover Photo</label>
                                    <div>
                                        <button class="green" id="select_cover_photo"  type="button">
                                            <span>Select Image</span>
                                        </button>
                                        <div  id="select_cover_box">
                                            <?php
                                             if($model->image):
                                                 $title = '<img src="'.base_url().$model->image.'" width="120">';
                                            ?>
                                            <div><?php echo $title?><input type="hidden" name="image" value="<?php echo $model->image?>"></div>
                                            <?php
                                            endif;
                                            ?>
                                        </div>
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

