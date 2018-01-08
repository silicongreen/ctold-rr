<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($question->id) ? "Edit" : "Add"; ?> Question (<?php echo $topic->name; ?>)</h2>

                        <?php echo form_open('', array('class' => 'validate_form form-inline', 'id' => 'assessments_q')); ?>

                        <?php if ( $edit && !empty($question->id)) { ?>
                            <input type="hidden" id="question_id" name="question_id" value="<?php echo $question->id; ?>">
                            <input type="hidden" id="topic_id" name="topic_id" value="<?php echo $question->topic_id; ?>">
                        <?php } ?>

                        <fieldset class="label_side top">
                            <label for="required_field">Question</label>
                            <div>
                                <textarea  id="question" name="question" required><?php echo $question->question; ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                            
                        <fieldset class="label_side top">
                            <label for="required_field">English Question</label>
                            <div>
                                <textarea  id="en_question" name="en_question" required><?php echo $question->en_question; ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                            
                        <fieldset class="label_side top">
                            <label for="required_field">Explanation</label>
                            <div>
                                <textarea  id="explanation" required name="explanation"><?php echo $question->explanation; ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                            
                        <fieldset class="label_side top">
                            <label for="required_field">English Explanation</label>
                            <div>
                                <textarea  id="en_explanation" required name="en_explanation"><?php echo $question->en_explanation; ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>    

                        <fieldset class="label_side top">
                            <label for="required_field">Mark</label>
                            <div>
                                <input id="mark" name="mark" value="<?php echo $question->mark; ?>" type="text" class="required" minlength="1" required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                            
                        
                            
                        
                            
                        <fieldset class="label_side top">
                            <label for="required_field">Time (Second)</label>
                            <div>
                                <input id="time" name="time" value="<?php echo $question->time; ?>" type="text" class="required" minlength="1" required>
                            </div>
                        </fieldset>

                        
                            
                        <div id="answers_form_container">
                            <?php for ($i = 0; $i <= 3; $i++) { ?>

                                <fieldset class="label_side top">
                                    <label for="required_field">Answer <?php echo $i + 1; ?></label>
                                    <div>
                                        <p> Bangla </p>
                                        <textarea  id="answer_<?php echo $i; ?>" required name="answer[]"><?php echo ($edit) ? $answers[$i]->answer : ''; ?></textarea>
                                        <br/><br/>
                                        <p> English </p>
                                        <textarea  id="en_answer_<?php echo $i; ?>" required name="en_answer[]"><?php echo ($edit) ? $answers[$i]->en_answer : ''; ?></textarea>
                                    </div>
                                    <div>
                                        <input name="correct[]" id="correct-<?php echo $i; ?>" value="<?php echo ( $edit && ($answers[$i]->correct == 1) ) ? $i : '0'; ?>" type="checkbox" class="form-control correct-chk" minlength="1" <?php echo ( $edit && ($answers[$i]->correct == 1) ) ? 'checked="checked"' : ''; ?>>
                                    </div>
                                </fieldset>

                            <?php } ?>
                        </div>
                            
                        <div class="button_bar clearfix">
                            <button class="green" type="submit">
                                <span>Submit</span>
                            </button>
                        </div>

                        <?php echo form_close(); ?>  
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url('scripts/custom/customAssesment.js'); ?>"></script>