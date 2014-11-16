<script src="<?php echo base_url()?>scripts/jquery/jquery.tree.js" type="text/javascript"></script>
<link href="<?php echo base_url()?>scripts/tree/jquery.tree.css" rel="stylesheet" type="text/css" >
<script src="<?php echo base_url()?>scripts/custom/customTree.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css"
          href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css"/>

<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Acl Management</h2>
                         <?php echo form_open('',array('class' => 'validate_form'));?>
                            <input type="hidden" id="group_id" value="0" />
                            
                            <div class="button_bar clearfix">
                              
                                   
                                    
                                    <ul id="tree">
                                        <?php foreach($arLinkData as $value): ?>
                                        <li><input type="checkbox" value="<?php echo $value['id']?>" <?php echo ($value['checked'])?"checked='checked'":"";?> name="controller[]"><span><?php echo $value['title']?></span>
                                            <?php if(count($value['children'])>0): ?>
                                            <ul>
                                                <?php foreach($value['children'] as $childrens): ?>
                                                    <li>
                                                    <input type="checkbox" <?php echo ($childrens['checked'])?"checked='checked'":"";?> value="<?php echo $childrens['id']?>" name="function[]"><span><?php echo $childrens['title']?></span>
                                                   
                                                <?php endforeach; ?>
                                            </ul>    
                                            <?php endif; ?> 
                                          
                                        <?php endforeach; ?>
                                    </ul>     
                              
                                <br/>
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                       <?php echo form_close();?>  
                    </div>
                </div>


            </div>

        </div>

