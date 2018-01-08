<script src="<?= base_url() ?>scripts/custom/customSchool.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> GK</h2>

                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Question<span>Unique field</span></label>
                                <div>
                                    <input id="question" name="question" value="<?php echo  $model->question ?>"  type="text" class="required" minlength="3"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Layout</label>
                                <div class="clearfix">

                                    <?php
                                    $f_array = array(1 => "Box", 2 => "List");


                                    echo form_dropdown('layout', $f_array, $model->layout);
                                    ?>
                                    <div class="required_tag"></div>
                                </div>

                             </fieldset>
                            <fieldset class="label_side top">
                               <label for="required_field">Layout Color</label>
                               <div>
                                   <?php
                                    $f_array = array("#DDB0C4|#F06EAA" => "Color 1", "#52BE80|#ABD373" => "Color 2");


                                    echo form_dropdown('layout_color', $f_array, $model->layout_color);
                                    ?>
                                  
                                   
                               </div>
                           </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Answer 1</label>
                                <div>
                                   <input id="ans1"  name="ans1" value="<?php echo  $model->ans1 ?>"  class="required" required  type="text">
                                   <br/><br/>
                                   
                                   <input type="radio" id="correct" <?php if($model->correct==1): ?> checked="checked" <?php endif; ?> name="correct" value="1" >
                                   Correct
                                   <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Answer 2</label>
                                <div>
                                   <input id="ans2"  name="ans2" value="<?php echo  $model->ans2 ?>"  class="required" required  type="text">
                                   <br/><br/>
                                   
                                   <input type="radio" id="correct" name="correct" <?php if($model->correct==2): ?> checked="checked" <?php endif; ?>  value="2" >
                                   Correct
                                   <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Answer 3</label>
                                <div>
                                   <input id="ans3"  name="ans3" value="<?php echo  $model->ans3 ?>"  class="required" required  type="text">
                                   <br/><br/>
                                   
                                   <input type="radio" id="correct" name="correct" <?php if($model->correct==3): ?> checked="checked" <?php endif; ?>  value="3" >
                                   Correct
                                   <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Answer 4</label>
                                <div>
                                   <input id="ans4"  name="ans4" value="<?php echo  $model->ans4 ?>"  class="required" required  type="text">
                                   <br/><br/>
                                   
                                   <input type="radio" id="correct" name="correct" <?php if($model->correct==4): ?> checked="checked" <?php endif; ?>  value="4" >
                                   Correct
                                   <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                    <label for="required_field">Post Date</label>
                                    <div>
                                        <input id="post_date" name="post_date" class="datepicker"  value="<?php echo  $model->post_date ?>"  type="text" >  
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

