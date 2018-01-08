
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
                                <label for="required_field">Name</label>
                                <div style="margin-top:10px;">
                                   <?php echo  $model->school_name ?>
                                </div>
                            </fieldset>
                            <fieldset class="top">
                                <label for="required_field">Contact</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->contact ?>
                                   
                                </div>
                            </fieldset>
                            <fieldset class="top">
                                <label for="required_field">Address</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->address ?>
                                   
                                </div>
                            </fieldset>
                            <fieldset class="top">
                                <label for="required_field">Zip Code</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->zip_code ?>
                                   
                                </div>
                            </fieldset>
                            <fieldset class="top">
                                <label for="required_field">About</label>
                                <div style="margin-top:10px;">
                                    <?php echo  $model->about ?>
                                   
                                </div>
                            </fieldset>
                        
                            
                            
                        
                            <fieldset class="top">
                                <label>School Picture</label>
                                <div style="margin-top:10px;">
                                    <div>
                                            <?php
                                            if ($model->picture):
                                            $title = '<img src="' . base_url() . $model->picture . '">';
                                            ?>
                                        <div><a target="_blank" href="<?php echo base_url() . $model->picture; ?>"><?= $title ?></a></div>
                                            <?php
                                            endif;
                                            ?>
                                    </div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="top">
                                <label>School Logo</label>
                                <div style="margin-top:10px;">
                                    <div>
                                            <?php
                                            if ($model->logo):
                                            $title = '<img src="' . base_url() . $model->logo . '">';
                                            ?>
                                                <div><a  target="_blank" href="<?php echo base_url() . $model->logo; ?>"><?= $title ?></a></div>
                                            <?php
                                            endif;
                                            ?>
                                    </div>
                                </div>
                            </fieldset> 
                    </div>
                </div>


            </div>

        </div>

