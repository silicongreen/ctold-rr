
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">School Information</h2>

                       
                            <fieldset class="top">
                                <label for="required_field">User Name</label>
                                <div style="margin-top:10px;">
                                   <?php echo  $model->name ?>
                                </div>
                            </fieldset>
                            <fieldset class="top">
                                <label for="required_field">User Email</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->email ?>
                                   
                                </div>
                            </fieldset>
                            <fieldset class="top">
                                <label for="required_field">User Phone</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->phone ?>
                                   
                                </div>
                            </fieldset>
                            <?php if($model->hphone):?>
                            <fieldset class="top">
                                <label for="required_field">User Home Phone</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->hphone ?>
                                   
                                </div>
                            </fieldset>
                            <?php endif; ?>
                            <fieldset class="top">
                                <label for="required_field">School Name</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->school_name ?>
                                   
                                </div>
                            </fieldset>
                        
                            <fieldset class="top">
                                <label for="required_field">School Address</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->school_address ?>
                                   
                                </div>
                            </fieldset>
                        
                            <?php if($model->about_school):?>
                            <fieldset class="top">
                                <label for="required_field">About School</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->about_school ?>
                                   
                                </div>
                            </fieldset>
                            <?php endif; ?>
                        
                            <?php if($model->admission_details):?>
                            <fieldset class="top">
                                <label for="required_field">Admission Details</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->admission_details ?>
                                   
                                </div>
                            </fieldset>
                            <?php endif; ?>
                        
                            <?php if($model->facilities):?>
                            <fieldset class="top">
                                <label for="required_field">Facilities</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->facilities ?>
                                   
                                </div>
                            </fieldset>
                            <?php endif; ?>
                        
                            <?php if($model->achievement):?>
                            <fieldset class="top">
                                <label for="required_field">Achievement</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->achievement ?>
                                   
                                </div>
                            </fieldset>
                            <?php endif; ?>
                        
                            
                            
                            <?php if(count($school_file)>0):?>
                            <fieldset class="top">
                                <label>Attachment</label>
                                <div style="margin-top:10px;">
                                    <a style="font-size:18px;font-weight: bold;" href="<?php echo  base_url(); ?>admin/userschoolinfo/download_all_file/<?php echo $model->id; ?>"> Download Attachment</a>
                                </div>
                            </fieldset>
                            <?php endif; ?>
                        
                            
                    </div>
                </div>


            </div>

        </div>

