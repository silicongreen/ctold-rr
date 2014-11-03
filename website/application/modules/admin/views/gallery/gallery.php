<script lang="" type="text/javascript" src="<?php echo base_url();?>scripts/gallery/gallery.js"></script>

<div id="pjax"><div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
<div id="main_container" class="container_16 clearfix">
    <?php echo form_open('',array('class' => 'validate_form_gallery'));?>
    <div class="box grid_16" >
        <?php
        $filter_array = array();

        $filter_array[0] = array("Gallery Name", "input");
        $filter_array[1] = array("Gallery Type", "form_dropdown", $type);


        create_filter($filter_array);
        ?>


    </div>
    <?php if(access_check("gallery","add")): ?>
    <div class="flat_area grid_16">
        <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/gallery/add/" >
            <span>Add Gallery</span>
        </button>
    </div> 
    <?php endif; ?>
    <div class="box grid_16 single_datatable">

        <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
    </div>
    <?php echo form_close();?> 
</div></div>
</div>





