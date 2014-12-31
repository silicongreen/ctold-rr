<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($question->id) ? "Edit" : "Add"; ?> Question</h2>

                        <?php echo form_open('', array('class' => 'validate_form form-inline')); ?>

                        <?php if (!empty($question->id)) { ?>
                            <input type="hidden" id="question_id" name="question_id" value="<?php echo $question->id; ?>">
                        <?php } ?>

                        <fieldset class="label_side top">
                            <label for="required_field">Question</label>
                            <div>
                                <textarea class="ckeditor" id="question" name="question"><?php echo $question->question; ?></textarea>
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
                            <label for="required_field">Style</label>
                            <div>
                                <?php echo form_dropdown('style', $style, ($question->style) ? $question->style : '2' ); ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <div class="button_bar clearfix">
                            <button id="add_answers" class="green" type="button">
                                <span>Add Answers</span>
                            </button>

                            <fieldset id="answers_wrapper" class="label_side top" style="display: none;">
                                <label for="required_field">Answer Type</label>
                                <div>
                                    <?php
                                    $ans_type_selected = '0';
                                    if (count($answers) > 2) {
                                        $ans_type_selected = '1';
                                    }
                                    echo form_dropdown('ans_type', $ans_type, $ans_type_selected, 'id="ans_type"');
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                        </div>

                        <div id="answers_form_container"></div>

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

<div id="ans_enum" style="display: none;">

    <?php for ($i = 0; $i <= 1; $i++) { ?>

        <fieldset class="label_side top row">
            <label for="required_field">Answer <?php echo $i + 1; ?></label>
            <div>
                <input name="answer[]" value="<?php echo ($edit) ? $answers[$i]->answer : ''; ?>" type="text" class="required form-control " minlength="1">
            </div>
            <div>
                <input name="correct[]" value="<?php echo ( $edit && ($answers[$i]->correct == 1) ) ? '1' : '0'; ?>" type="checkbox" class="form-control correct-chk" minlength="1" <?php echo ( $edit && ($answers[$i]->correct == 1) ) ? 'checked="checked"' : ''; ?>>
            </div>
        </fieldset>

    <?php } ?>

</div>

<div id="ans_mcq" style="display: none;">

    <?php for ($i = 0; $i <= 3; $i++) { ?>

        <fieldset class="label_side top">
            <label for="required_field">Answer <?php echo $i + 1; ?></label>
            <div>
                <textarea class="ckeditor" id="answer" name="answer[]"><?php echo ($edit) ? $answers[$i]->answer : ''; ?></textarea>
            </div>
            <div>
                <input name="correct[]" value="<?php echo ( $edit && ($answers[$i]->correct == 1) ) ? '1' : '0'; ?>" type="checkbox" class="form-control correct-chk" minlength="1" <?php echo ( $edit && ($answers[$i]->correct == 1) ) ? 'checked="checked"' : ''; ?>>
            </div>
        </fieldset>

    <?php } ?>

</div>

<script src="<?= base_url('scripts/custom/customAssesment.js'); ?>"></script>