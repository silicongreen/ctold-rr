<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        
                         <h2 class="section"><?php echo "View"; ?> User</h2>
                         
                         <?php foreach($_attributes as $k => $v) { ?>
                         
                            <fieldset class="label_side top">
                                <label for="required_field"><?php echo $v; ?></label>
                                <div>
                                    <?php if($k == 'dob') {?>
                                        <?php echo ( !empty($model->$k) && ($model->$k != '0000-00-00') ) ? date('d-m-Y', strtotime($model->$k)) : 'N/A'; ?>
                                    <?php } else if ($k == 'medium') { ?>
                                        <?php echo ( !is_null($model->$k) ) ? $medium[$model->$k] : 'N/A'; ?>
                                    <?php } else if($k == 'user_type') { ?>
                                        <?php echo $user_type[$model->$k]; ?>
                                    <?php } else if (strpos($model->$k, 'image') !== false) { ?>
                                        <img src="<?php echo $model->$k; ?>" alt="No image found" height="120" />
                                    <?php } else if($k == 'status') { ?>
                                        <?php echo ($model->$k == 1) ? 'Active' : 'Inactive'; ?>
                                    <?php } else if($k == 'gender') { ?>
                                        <?php echo ( !is_null($model->$k) ) ? ($model->$k == 1) ? 'Male' : 'Female' : 'N/A'; ?>
                                    <?php } else { ?>
                                        <?php echo $model->$k;?>
                                    <?php } ?>
                                </div>
                            </fieldset>
                         
                         <?php } ?>
                        
                    </div>
                </div>


            </div>

        </div>

    </div>
</div>