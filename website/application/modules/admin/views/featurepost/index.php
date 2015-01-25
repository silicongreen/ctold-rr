
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
        
        $widget->run('sidebar');
        ?>

       
        
        <input type="hidden" id="sorttype" value="desc" >

        <div id="main_container" class="main_container container_16 clearfix">


            
             <?php echo form_open('',array('class' => 'validate_form'));?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[0] = array("Title", "input");
                $filter_array[1] = array("Category", "form_dropdown", $categoryMenu);
                $filter_array[2] = array("Position", "form_dropdown", $position);
                
               
                
                create_filter($filter_array);
                ?>


            </div>
            
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close();?> 
        </div>






