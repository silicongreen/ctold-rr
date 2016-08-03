<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo  ($model->id) ? "Edit" : "Add"; ?> Spelling Bee Word</h2>
                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>

                        <?php echo  form_open('', array('class' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
                        
                        <fieldset class="label_side top">
                            <label for="word">Word<span>Unique field</span></label>
                            <div>
                                <input id="word" name="word" value="<?php echo  $model->word; ?>" type="text" class="required" minlength="4" required>
                                <div>
                                    <audio id="speech" rel="noreferrer"   style="display:none;" src="" ></audio>
                                    <a id="speech-href" href="javascript:void(0);" onclick="playtext();"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAABD0lEQVRYw+3XsQqDMBAG4L6suogIIoq7ToIuDi4i6C6+gSDo6OjjXEkhoUNibcCYtPdDFk3MB8Z4eTwwGAzG7FiWBVEUwbIsoC2QtqIo7kGGYSiceBxH8DyPIZMkAeU4MvGnfmVZqkdS3BkgSdd1rH+e56AMdxZIUlUVGzNNEyjBfQN8H+/7PijB8YDbtgknX9eVjZvnGS7H8YDkWl3XwsnJ3kj6pGkqD4zjGGhzXVeIEwFJa5qGCxiG4XXftm154BHoLFC0Pvd9l1q/lwDbtoWj598OzLKMi3AcB4G/8Yov+0i032a036iN+9UZUSwYUW7JFqx936srWI0o+WmCIND30GTEsdPogzsGg/n3PAGH4GdMo71vewAAAABJRU5ErkJggg==">
                                    </a>
                                </div>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="type">Type</label>
                            <div>
                                <input id="wtype" name="wtype" value="<?php echo $model->wtype; ?>" type="text" class="required" minlength="4" required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="sentence">Sentence</label>
                            <div>
                                <textarea name="sentence" id="sentence" class="required" required="required"><?php echo $model->sentence;?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="definition">Definition</label>
                            <div>
                                <textarea id="definition" name="definition" class="required" required="required"><?php echo $model->definition; ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="bangla_meaning">Bangla Meaning</label>
                            <div>
                                <input id="bangla_meaning" class="required" name="bangla_meaning" value="<?php echo $model->bangla_meaning; ?>" type="text" minlength="4" required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="year">Year</label>
                            <div>
                                <?php
                                    $c_year = date('Y');
                                    $c_year = $c_year-10;
                                    $years = array(NULL => "Selcet"); 
                                    $years[$c_year]= $c_year;
                                    for($i = 0; $i < 10; $i++){
                                        $c_year += 1;
                                        $years[$c_year] = $c_year;
                                    }
                                    if(!$model->year)
                                    {
                                        $model->year = date('Y');
                                    }
                                    
                                    echo form_dropdown('year', $years, $model->year, 'id="year"');
                                ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label>Word Strength</label>
                            <div class="jqui_radios">
                                <input type="radio" class="required" name="level" value="0" id="easy" <?php echo ($model->level == 0) ? 'checked="checked"' : ''; ?>/><label for="easy">Easy</label>
                                <input type="radio" class="required" name="level" value="1" id="medium" <?php echo ($model->level == 1) ? 'checked="checked"' : ''; ?>/><label for="medium">Medium</label>
                                <input type="radio" class="required" name="level" value="2" id="hard" <?php echo ($model->level == 2) ? 'checked="checked"' : ''; ?>/><label for="hard">Hard</label>
                                <input type="radio" class="required" name="level" value="3" id="extreme" <?php echo ($model->level == 3) ? 'checked="checked"' : ''; ?>/><label for="extreme">Extreme Hard</label>
                                <div class="required_tag"></div>
                            </div>
                            
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label>Word Source</label>
                            <div class="jqui_radios">
                                <input type="radio" class="required" name="source" value="1" id="word_bank" <?php echo ($model->source == 1 || $model->source == 0) ? 'checked="checked"' : ''; ?>/><label for="word_bank">Word Bank</label>
                                <input type="radio" class="required" name="source" value="2" id="others" <?php echo ($model->source == 2) ? 'checked="checked"' : ''; ?>/><label for="others">others</label>
                                <input type="radio" class="required" name="source" value="3" id="daily_star" <?php echo ($model->source == 3) ? 'checked="checked"' : ''; ?>/><label for="daily_star">Daily Star</label>
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

    </div>
</div>
<script>
    
    
    $(document).ready(function(){
        var text = $("#word").val();
        if(text!="" && text.length>3)
        {
            $("#speech-href").show();
        }
        else
        {
            $("#speech-href").hide();
        } 
        
        $('body').on('keydown','#word', function(event) {
            var text = $("#word").val();
            if(text!="" && text.length>3)
            {
                $("#speech-href").show();
            }
            else
            {
                $("#speech-href").hide();
            }
        });
        $('body').on('keyup','#word', function(event) {
            var text = $("#word").val();
            if(text!="" && text.length>3)
            {
                $("#speech-href").show();
            }
            else
            {
                $("#speech-href").hide();
            }
        });
        
        
        
    })
    function playtext()
    {
        var text = $("#word").val();
        if(text!="" && text.length>3)
        {
            var url =$("#base_url").val()+"admin/spellingbee/playaudio?q=" +text+ "&tl=en";
            $("audio").attr("src",url).get(0).play();
          
        }
    }
</script>    