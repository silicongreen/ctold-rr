<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <input type="hidden" id="modelwidth" value="85%" >

            <input type="hidden" id="modelheight" value="80%" >
            <div class="flat_area grid_16">
                <h2>Manage Category Photo</h2>

            </div>
            <input type="hidden" name="category_id" id="category_id_photo" value="<?php echo $category_id?>" />
             <?php echo form_open('',array('class' => 'validate_form'));?>
            

            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[1] = array("Issue Date", "input_date");
                create_filter($filter_array);
                ?>


            </div>
            <?php if(access_check("categories","add_images")): ?>
            <div class="flat_area grid_16">
                <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/categories/add_images/<?php echo $category_id?>" >
                    <span>Add Images</span>
                </button>
            </div>  
            <?php endif; ?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close();?> 

        </div>

