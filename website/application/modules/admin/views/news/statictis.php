<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Home : <?php echo $home; ?> views , Aboard : <?php echo $aboard; ?> 
                            <?php 
                                    $title_array = array();
                                    
                                    if(isset($aboard_country))
                                    foreach($aboard_country as $value)
                                    {
                                        $title_array[] = $value->country." : ".$value->view;
                                    }  
                                    $title = implode(" , ", $title_array);
                                        
                                    ?>
                                    <?php if($title): ?><a class="tooltip" href="javascript:void(0);" title="<?php echo $title;?>"><?php endif; ?>views 
                                    <?php if($title): ?></a><?php endif; ?>  
                            
                         </h2> 
                        <h2 class="section">Today View</h2> 
                            <fieldset class="label_side top">
                                <label >Home</label>
                                <div>
                                  <?php echo $daily_home; ?> views
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label >Abroad</label>
                                <div>
                                  <?php echo $daily_aboard; ?>
                                    <?php 
                                    $title_array = array();
                                    
                                    if(isset($daily_aboard_country))
                                    foreach($daily_aboard_country as $value)
                                    {
                                        $title_array[] = $value->country." : ".$value->view;
                                    }  
                                    $title = implode(" , ", $title_array);
                                        
                                    ?>
                                    <?php if($title): ?><a class="tooltip" href="javascript:void(0);" title="<?php echo $title;?>"><?php endif; ?>views 
                                    <?php if($title): ?></a><?php endif; ?>  
                                </div>
                            </fieldset>
                        
                        <h2 class="section">Weekly View</h2> 
                            <fieldset class="label_side top">
                                <label >Home</label>
                                <div>
                                  <?php echo $weekly_home; ?> Views
                                    
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label >Abroad</label>
                                <div>
                                  <?php echo $weekly_aboard; ?> 
                                    <?php 
                                    $title_array = array();
                                    
                                    if(isset($weekly_aboard_country))
                                    {
                                        
                                        foreach($weekly_aboard_country as $value)
                                        {
                                           
                                            $title_array[] = $value->country." : ".$value->view;
                                        }  
                                    }
                                    $title = implode(" , ", $title_array);
                                        
                                    ?>
                                    <?php if($title): ?><a class="tooltip" href="javascript:void(0);" title="<?php echo $title;?>"><?php endif; ?>views 
                                    <?php if($title): ?></a><?php endif; ?>   
                                </div>
                            </fieldset>
                        
                        <h2 class="section">Monthly View</h2> 
                            <fieldset class="label_side top">
                                <label >Home</label>
                                <div>
                                  <?php echo $monthly_home; ?> views 
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label >Abroad</label>
                                <div>
                                  <?php echo $monthly_aboard; ?>
                                  <?php 
                                    $title_array = array();
                                    
                                    if(isset($monthly_aboard_country))
                                    {
                                        
                                        foreach($monthly_aboard_country as $value)
                                        {
                                           
                                            $title_array[] = $value->country." : ".$value->view;
                                        }  
                                    }
                                    $title = implode(" , ", $title_array);
                                        
                                    ?>
                                    <?php if($title): ?><a class="tooltip" href="javascript:void(0);" title="<?php echo $title;?>"><?php endif; ?>views 
                                    <?php if($title): ?></a><?php endif; ?>
                                </div>
                            </fieldset>
                             
                    </div>
                </div>


            </div>

        </div>