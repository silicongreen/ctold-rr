<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($question->id) ? "Edit" : "Add"; ?> Question</h2>

                        <?php echo form_open('', array('class' => 'validate_form')); ?>

                        <?php if (!empty($question->id)) { ?>
                            <input type="hidden" id="question_id" name="question_id" value="<?php echo $question->id; ?>">
                        <?php } ?>

                        <fieldset class="label_side top">
                            <label for="required_field">question</label>
                            <div>
                                <input id="title" name="question" value="<?php echo $question->question; ?>" type="text" class="required" minlength="5" required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Mark</label>
                            <div>
                                <input id="title" name="mark" value="<?php echo $question->mark; ?>" type="text" class="required" minlength="1" required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Style</label>
                            <div>
                                <?php echo form_dropdown('style', $style, $question->style); ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <div class="button_bar clearfix">
                            <button id="add_answers" class="green" type="button">
                                <span>Add Answers</span>
                            </button>
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

<script src="<?= base_url('scripts/custom/customAssesment.js'); ?>"></script>