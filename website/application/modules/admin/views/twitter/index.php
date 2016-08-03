<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
            $widget = new Widget();
            $widget->run('sidebar');
        ?>
        
        <input type="hidden" id="modelwidth" value="85%" />
        <input type="hidden" id="modelheight" value="80%" />
        
        <div id="main_container" class="main_container container_16 clearfix">
            <div class="flat_area grid_16">
                <h2>Manage Settings</h2>
                <?php echo form_open('',array('class'=>'validate_form'));?>
                    <div class="box grid_16" >
                        <?php
                            $filter_array = array();
                            
                            $filter_array[0] = array("Menu", "form_dropdown", $menus);
                            
                            create_filter($filter_array);
                        ?>
                    </div>
                    <?php if(access_check("twitter","add")){ ?>
                    <div class="flat_area grid_16">
                        <button  type="button"  class="light text_only has_text model" id="<?php echo base_url(); ?>admin/twitter/add" >
                            <span>Add Twitter Widget</span>
                        </button>
                    </div>  
                    <?php } ?>
                    <div class="box grid_16 single_datatable">
                        <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
                    </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>