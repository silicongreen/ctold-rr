<link href="<?php echo  base_url() ?>styles/plugins/polls/styles.css" rel="stylesheet" type="text/css" >
<script src="<?php echo  base_url() ?>scripts/custom/customPollsResult.js"></script>


<div id="pjax">
    <?php echo  form_open('', array('class' => 'validate_form')); ?>
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section" id="question_text"><?php echo $model->ques?></h2>
                        <input type="hidden" value="<?php echo  $id ?>" id="id" />
                        <div id="pollcontainer" >
                        </div>
                        <p id="loader" >Loading...</p> 
                    </div>
                </div>


            </div>
            <?php echo  form_close(); ?> 
        </div>
    </div>
 </div>   

