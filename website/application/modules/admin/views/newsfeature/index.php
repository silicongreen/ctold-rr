
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
        
        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="73%" >
        
        <input type="hidden" id="model2width2" value="30%" >

        <input type="hidden" id="modelheight2" value="45%" >
        
        
        <input type="hidden" id="school_id_feed" value="<?php echo $school_id ?>" >
        
        

        <input type="hidden" id="sorttype" value="desc" >

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2><?php if(isset($school_name)): ?><?php echo $school_name; ?> Feeds <?php else :?> Manage News <?php endif; ?></h2>

            </div>
             <?php echo form_open('',array('class' => 'validate_form'));?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[0] = array("Date", "input_daterange");
                $filter_array[1] = array("Title", "input");
                $filter_array[2] = array("Category", "form_dropdown", $categoryMenu,'group_concate');
                //$filter_array[4] = array("Tags", "form_dropdown", $tags,'group_concate');
               
              
                $status = array(NULL=>"Select",1=>"Draft",5=>"Published");
                $filter_array[3] = array("Status", "form_dropdown", $status);
                
                $status = array(1=>"Admin",2=>"Web User");
                $filter_array[6] = array("User Type", "form_dropdown", $status);
                
                
                
                
                
                create_filter($filter_array);
                ?>


            </div>
            
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close();?> 
        </div>






