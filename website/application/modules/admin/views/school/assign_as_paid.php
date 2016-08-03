<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            
            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Assign as Paid (<?php echo $model->name; ?>)</h2>
                       

                        <?php echo form_open('',array('class' => 'validate_form','enctype'=>'multipart/form-data'));?>  
                            
                            <fieldset class="label_side top">
                                    <label for="required_field">School</label>
                                    <div>
                                      <?php echo form_dropdown('school', $paid_school); ?>
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
        
       