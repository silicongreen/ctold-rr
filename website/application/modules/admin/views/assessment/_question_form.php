<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($question->id) ? "Edit" : "Add"; ?> Question</h2>

                        <?php echo form_open('', array('class' => 'validate_form form-inline', 'id' => 'assessments_q')); ?>

                        <?php if ( $edit && !empty($question->id)) { ?>
                            <input type="hidden" id="question_id" name="question_id" value="<?php echo $question->id; ?>">
                            <input type="hidden" id="assesment_id" name="assesment_id" value="<?php echo $question->assesment_id; ?>">
                        <?php } ?>

                        <fieldset class="label_side top">
                            <label for="required_field">Question</label>
                            <div>
                                <textarea class="ckeditor" id="question" name="question"><?php echo $question->question; ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                            
                        <fieldset class="label_side top">
                            <label for="required_field">Explanation</label>
                            <div>
                                <textarea class="ckeditor" id="explanation" name="explanation"><?php echo $question->explanation; ?></textarea>
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
                            <label for="required_field">Time</label>
                            <div>
                                <input id="time" name="time" value="<?php echo $question->time; ?>" type="text" class="required" minlength="1">
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Style</label>
                            <div>
                                <?php echo form_dropdown('style', $style, ($question->style) ? $question->style : '2' ); ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                            
                        <div id="answers_form_container">
                            <?php for ($i = 0; $i <= 3; $i++) { ?>

                                <fieldset class="label_side top">
                                    <label for="required_field">Answer <?php echo $i + 1; ?></label>
                                    <div>
                                        <textarea class="ckeditor" id="answer_<?php echo $i; ?>" name="answer[]"><?php echo ($edit) ? $answers[$i]->answer : ''; ?></textarea>
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