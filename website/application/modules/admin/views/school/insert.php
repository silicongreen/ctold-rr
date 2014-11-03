<script src="<?= base_url() ?>scripts/custom/customSchool.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> School</h2>

                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Name<span>Unique field</span></label>
                                <div>
                                    <input id="title" name="name" value="<?php echo  $model->name ?>"  type="text" class="required" minlength="3"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Location</label>
                                <div>
                                    <input id="title" name="location" value="<?php echo  $model->location ?>"  type="text">
                                   
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">District</label>
                                <div>
                                    <input id="title" name="district" value="<?php echo  $model->district ?>"  type="text">
                                   
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Girls</label>
                                <div>
                                    <input id="title" name="girls" value="<?php echo  $model->girls ?>"  type="text">
                                   
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Boys</label>
                                <div>
                                    <input id="title" name="boys" value="<?php echo  $model->boys ?>"  type="text">
                                   
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Gender</label>
                                <div>
                                    <?php
                                    $a_gender = array(0=>'All',1=>'Boys',2=>'Girls');
                                    
                                    if(!$model->gender)
                                        $model->gender = 0;
                                    echo form_dropdown('gender', $a_gender,$model->gender);
                                    ?>
                                  
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Medium </label>
                                <div>
                                    <?php
                                    $medium = array("Bangla & English"=>"Bangla & English","Bangla"=>"Bangla","English"=>"English");
                                    
                                    if(!$model->medium)
                                        $model->medium = "Bangla & English";
                                    echo form_dropdown('medium', $medium,$model->medium);
                                    ?>
                                  
                                </div>
                            </fieldset>
                        
                           <fieldset class="label_side top">
                                <label for="required_field">Level</label>
                                <div>
                                    <input id="title" name="level" value="<?php echo  $model->level ?>"  type="text">
                                   
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Shift</label>
                                <div>
                                    <input id="title" name="shift" value="<?php echo  $model->shift ?>"  type="text">
                                   
                                </div>
                            </fieldset>
                            
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Cover Image<span>Size must be 954px×310px</span></label>
                                <div>
                                    <button class="green" id="select_cover"  type="button">
                                        <span>Select Cover</span>
                                    </button>
                                    <div  id="select_cover_box">
                                        <?php
                                           
                                            if ($model->cover):
                                            $title = '<img src="' . base_url() . $model->cover . '" width="50">';
                                            ?>
                                            <div><?= $title ?><input type="hidden" name="cover" value="<?= $model->cover ?>"><a class="text-remove"></a></div>
                                            <?php
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Picture<span>Size must be 300px×200px(USE FOR SEARCH RESULT)</span></label>
                                <div>
                                    <button class="green" id="select_picture"  type="button">
                                        <span>Select Picture</span>
                                    </button>
                                    <div  id="select_picture_box">
                                        <?php
                                           
                                            if ($model->picture):
                                            $title = '<img src="' . base_url() . $model->picture . '" width="50">';
                                            ?>
                                            <div><?= $title ?><input type="hidden" name="picture" value="<?= $model->picture ?>"><a class="text-remove"></a></div>
                                            <?php
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Logo<span>Size must be 225px×225px</span></label>
                                <div>
                                    <button class="green" id="select_logo"  type="button">
                                        <span>Select Logo</span>
                                    </button>
                                    <div  id="select_logo_box">
                                        <?php
                                           
                                            if ($model->logo):
                                            $title = '<img src="' . base_url() . $model->logo . '" width="50">';
                                            ?>
                                            <div><?= $title ?><input type="hidden" name="logo" value="<?= $model->logo ?>"><a class="text-remove"></a></div>
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

