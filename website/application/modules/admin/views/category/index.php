<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
        
        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" />

        <input type="hidden" id="modelheight" value="99%" />
        
        <input type="hidden" id="categoryphotomodelwidth" value="95%" />

        <input type="hidden" id="categoryphotomodelheight" value="95%" />
        
        <?php if(is_integer(($datatableSortBy)) && $datatableSortBy >= 0 && !empty($datatableSortDirection)){?>
            <input type="hidden" id="sortIndex" value="<?php echo $datatableSortBy;?>" />
            <input type="hidden" id="sorttype" value="<?php echo $datatableSortBy;?>" />
        <?php }?>
        
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Category</h2>

            </div>
             <?php echo form_open('',array('class' => 'validate_form'));?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();

                $filter_array[1] = array("Category Name", "input");
               
              
                $status = array(NULL=>"Selcet",0=>"Inactive",1=>"Active");
                $filter_array[2] = array("Status", "form_dropdown", $status);
                $filter_array[3] = array("Parent", "form_dropdown", $categoryMenu);
                
                
                create_filter($filter_array);
                ?>


            </div>
            <?php if(access_check("categories","add")): ?>
            <div class="flat_area grid_16">
                <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/categories/add/" >
                    <span>Add Category</span>
                </button>
            </div>  
            <?php endif; ?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close();?> 
        </div>






