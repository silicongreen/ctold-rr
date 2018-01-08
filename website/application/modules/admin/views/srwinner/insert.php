<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo  ($model->id) ? "Edit" : "Add"; ?> Science Rocks Episode</h2>
                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>

                        <?php echo  form_open('', array('class' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
                        

                                                  

                        <fieldset class="label_side top">
                            <label for="required_field">Episode Name</label>
                            <div>
                                <input id="name" name="name" value="<?php echo  $model->name ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Details</label>
                            <div>
                                <textarea  name="description" id="description" ><?= $model->description ?></textarea>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Date</label>
                            <div id="publish_date_div" >
                                <input  type="text" name="date" value="<?= $model->date ?>" id="date" class="datepicker required" required    >
                                <div class="required_tag"></div>
                            </div>

                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Winner One</label>
                            <div>
                                <input id="winner1" name="winner1" value="<?php echo  $model->winner1 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                         <fieldset class="label_side top">
                            <label for="required_field">Winner One District</label>
                            <div>
                                <input id="winner1_district" name="winner1_district" value="<?php echo  $model->winner1_district ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                         <fieldset class="label_side top">
                            <label for="required_field">Winner One Occupation</label>
                            <div>
                                <input id="winner1_occupation" name="winner1_occupation" value="<?php echo  $model->winner1_occupation ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Winner Two</label>
                            <div>
                                <input id="winner2" name="winner2" value="<?php echo  $model->winner2 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                         <fieldset class="label_side top">
                            <label for="required_field">Winner Two District</label>
                            <div>
                                <input id="winner2_district" name="winner2_district" value="<?php echo  $model->winner2_district ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                         <fieldset class="label_side top">
                            <label for="required_field">Winner Two Occupation</label>
                            <div>
                                <input id="winner2_occupation" name="winner2_occupation" value="<?php echo  $model->winner2_occupation ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Winner Three</label>
                            <div>
                                <input id="winner3" name="winner3" value="<?php echo  $model->winner3 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                         <fieldset class="label_side top">
                            <label for="required_field">Winner Three District</label>
                            <div>
                                <input id="winner3_district" name="winner3_district" value="<?php echo  $model->winner3_district ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                         <fieldset class="label_side top">
                            <label for="required_field">Winner Three Occupation</label>
                            <div>
                                <input id="winner3_occupation" name="winner3_occupation" value="<?php echo  $model->winner3_occupation ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Question One</label>
                            <div>
                                <input id="question1" name="question1" value="<?php echo  $model->question1 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Answer : </label>
                            <div>
                                <input id="ans1" name="ans1" value="<?php echo  $model->ans1 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Question Two</label>
                            <div>
                                <input id="question2" name="question2" value="<?php echo  $model->question2 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Answer : </label>
                            <div>
                                <input id="ans2" name="ans2" value="<?php echo  $model->ans2 ?>"  type="text" class="required" minlength="3"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        
                        

                        

                        

                        <div class="button_bar clearfix">
                            <button class="green" type="submit">
                                <span>Submit</span>
                            </button>
                        </div>
                        <?php echo  form_close(); ?>  
                    </div>
                </div>


            </div>

        </div>

