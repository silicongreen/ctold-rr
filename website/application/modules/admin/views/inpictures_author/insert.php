<script src="<?= base_url() ?>scripts/custom/customByline.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> New User (In Pictures)</h2>

                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Name</label>
                                <div>
                                    <input id="name" name="name" value="<?php echo  $model->name ?>"  type="text" class="required" minlength="3"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            
                          <fieldset class="label_side top">
                                <label for="required_field">E-mail</label>
                                <div>
                                    <input id="email" name="email" value="<?php echo  $model->email ?>"  type="text">
                                </div>
                            </fieldset>                        

                         <fieldset class="label_side top">
                             <label for="required_field">Phone</label>
                                <div>
                                    <input id="phone" name="phone" value="<?php echo  $model->phone ?>"  type="text">
                                </div>
                            </fieldset>
    
                             <fieldset class="label_side top">
                             <label for="required_field">Created Date</label>
                                <div>
                                    <input id="created_date" name="created_date" class="datetimepicker_class" value="<?php echo  $model->created_date ?>"  type="text">
                                </div>
                            </fieldset>
                        <fieldset class="label_side top">
                             <label for="required_field">Profession</label>
                                <div>
                                    <input id="profession" name="profession" value="<?php echo  $model->profession ?>"  type="text">
                                </div>
                        </fieldset>
                          <fieldset class="label_side top">
                             <label for="required_field">Address</label>
                                <div>
                                    <input id="address" name="address" value="<?php echo  $model->address ?>"  type="text">
                                </div>
                        </fieldset>
                       
  
                            <fieldset class="label_side top">
                                <label for="required_field">Active</label>
                                <div>
                                    <?php
                                    $a_active = array('1'=>'Yes','0'=>'No');
                                    echo form_dropdown('is_active', $a_active,$model->is_active);
                                    ?>
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

