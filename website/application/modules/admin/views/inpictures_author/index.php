<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="80%" >

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage In Pictures Users</h2>

            </div>

            <?php echo form_open('', array('class' => 'validate_form')); ?>
            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[0] = array("name", "input");
                $filter_array[1] = array("email", "input");
                $filter_array[1] = array("phone", "input");
                $filter_array[1] = array("created_date", "input");
                $filter_array[1] = array("profession", "input");
                $filter_array[1] = array("address", "input");           
                create_filter($filter_array);
                ?>
            </div>
            <?php if (access_check("inpicture_author", "add")): ?>
                <div class="flat_area grid_16">
                    <button  type="button"  class="light text_only has_text model" id="<?php echo base_url(); ?>admin/inpictures_author/add/" >
                        <span>Add New User (In Pictures)</span>
                    </button>
                </div> 
            <?php endif; ?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close(); ?> 
        </div>






