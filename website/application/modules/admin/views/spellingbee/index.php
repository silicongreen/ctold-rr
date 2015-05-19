<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
        
        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" />

        <input type="hidden" id="modelheight" value="90%" />
        
        
        <input type="hidden" id="sortIndex" value="<?php echo $datatableSortBy;?>" />
        <input type="hidden" id="sorttype" value="<?php echo $datatableSortDirection;?>" />

        
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Spelling Bee Words</h2>

            </div>
             <?php echo form_open('',array('class' => 'validate_form'));?>

                <div class="box grid_16" >
                    <?php
                        $filter_array = array();
                        $filter_array[1] = array("Word", "input");

                        $level = array(NULL => "Selcet", 1 => "Easy", 2 => "Medium", 3 => "Hard");
                        $filter_array[4] = array("Level", "form_dropdown", $level);

                        $status = array(NULL => "Selcet", 0 => "Disabled", 1 => "Enabled");
                        $filter_array[5] = array("Status", "form_dropdown", $status);
                        $c_year = date('Y');
                        $c_year = $c_year-10;
                        $years = array(NULL => "Selcet"); 
                        $years[$c_year]= $c_year;
                        for($i = 0; $i < 10; $i++){
                            $c_year += 1;
                            $years[$c_year] = $c_year;
                        }
                       
                        $filter_array[6] = array("Year", "form_dropdown", $years);
                        
                        $status = array(NULL => "Selcet", 1 => "Yes", 0 => "No");
                        $filter_array[7] = array("Sound Problem", "form_dropdown", $status);

                        create_filter($filter_array);
                    ?>
                </div>

                <?php if(access_check("spellingbee", "add")): ?>
                <div class="flat_area grid_16">
                    <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/spellingbee/add/" >
                        <span>Add Words</span>
                    </button>
                </div>  
                <?php endif; ?>
                <div class="box grid_16 single_datatable">

                    <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
                </div>
            
            <?php echo form_close();?>
            
        </div>
