<script src="<?php echo base_url()?>scripts/custom/customPolls.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> Polls</h2>
                        <?php 
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                            
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Question<span>Required</span></label>
                                <div>
                                    <textarea class="textarea required" name="ques" required><?php echo $model->ques?></textarea>
                                    
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <div class="option_box">
                                <?php 
                                $i=0;
                                foreach($options as $key=>$value): $i++; ?>
                                <fieldset class="label_side top">
                                    <label for="required_field">Option <?php echo $i?><span><?php if($i<=2): ?>Required<?php else: ?><a href="javascript:void(0)" class="removeVar">Remove</a></span><?php endif; ?></label>
                                    <div>
                                        <input id="value<?php echo $i?>" name="value_<?php echo $i?>" value="<?php echo $value->value?>"  type="text" <?php if($i<=2): ?>class="required"  required<?php endif; ?> >
                                        <input id="id_<?php echo $i?>" name="value_id_<?php echo $i?>" value="<?php echo $value->id?>"  type="hidden"  >
                                        <?php if($i<=2): ?><div class="required_tag"></div><?php endif; ?> 
                                    </div>
                                </fieldset>
                                <?php
                                    
                                    endforeach;
                                    
                                ?>
                                <script>
                                    startingNo = <?php echo $i?>;
                                    maxNo      = <?php echo $maxNumber?>;
                                </script>
                            </div> 
                            
                            <div class="button_bar clearfix" id="add_more_button" <?php if($i>=$maxNumber): ?> style="display:none;" <?php endif; ?>>
                                <button class="gray" id="addVar" type="button">
                                    <span>Add More Option</span>
                                </button>
                            </div>
                            
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

