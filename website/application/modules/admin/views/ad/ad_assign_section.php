<input type="hidden" id="base_url" value="<?= base_url() ?>" />
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;        
        $widget->run('sidebar');
        ?>
        <input type="hidden" id="controllername" value="ad" >


        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="97%" >

        <div id="main_container" class="main_container container_16 clearfix">
            <div class="flat_area grid_16">
                <h2>Section Ads</h2>
            </div>
            <?php echo form_open('',array('class' => 'validate_form'));?>        
            
            <div class="box grid_8" >
                <?php
                    $filter_array = array();
                    $filter_array[1] = array("Name", "input");                    
                    $filter_array[2] = array("Menus", "form_dropdown",$menu_s );
                    $status = array(NULL=>"Select",1 => 'Active', 0 => 'Inactive');
                    $filter_array[3] = array("Status", "form_dropdown", $status);
                    create_filter($filter_array);
                ?>
            </div>
            <div class="box grid_8" style="float:right;">
                
                
                <div class="block">
                        <h2 class="section">Assign Ad To Section</h2>
                </div>
                <div class="flat_area grid_16">
                   <?php
                        //echo date('d M y');
                       // echo $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
                        $aError = array('type_id','url_link','name','html_code');
                        create_validation($aError);
                        ?>
                        <?=form_open('',array('class' => 'validate_form','enctype' => "multipart/form-data"));?>
                       
                            <input type="hidden" id="admin_id" value="0" />
                            <fieldset id="show_on_sub_menu" class="label_side top">
                                <label for="required_field">Campaign Link Location:</label>
                                <div>
                                    <?php
                                        $ad_link_location = array(NUll => 'Select', 'section' => 'Section', 'details' => 'Details');
                                        $current_type = 1;     
                                        $js = " id='link_location'";
                                        echo form_dropdown('menu_ci_key', $ad_link_location, $current_type, $js);
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset id="show_on_ad_plans" class="label_side top"   style="display:none;">
                                <label for="required_field">Campaign Plans:</label>
                                <div>
                                    <?php                                    
                                    echo form_dropdown('plan_id_section', $ad_plans['section'], '', " id='ad_section' style='display:none;'");
                                    echo form_dropdown('plan_id_details', $ad_plans['details'], '', " id='ad_details' style='display:none;'");
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="plan_ad_div" style="display: none;" >
                                <label for="required_field">Ad Title</label>
                                <div>
                                    <select id="menu_ads" name="menu_ads">
                                        
                                    </select>
                                    <?php
                                    //echo form_dropdown('menu_ads', $menu_ads, '', " id='menu_ads' style=''");
                                    ?>                                
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="js_type_image">
                                <label for="required_field">Menu</label>
                                <div>
                                    <?php
                                    echo form_dropdown('ci_key', $menus, '', " id='ad_menu' style=''");
                                    ?>                                
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                        <?=form_close();?>   
                </div>
                
            </div> 
            <div class="box grid_8 single_datatable">
                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            
            
        </div>
        <?php echo form_close();?> 


<script  type="text/javascript">
$(document).ready(function(){   
    $(document).on('change','#link_location',function(){
        var value = $( "select#link_location option:selected").val();
        if(value == 'section')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").hide();
            $("#ad_section").show();                 
            $("#ad_details").hide();  
        }
        if(value == 'details')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").hide();
            $("#ad_section").hide();                 
            $("#ad_details").show();
        }
    });
    $(document).on('change','#ad_section',function(){
        var value = $( "select#ad_section option:selected").val();
 
        $.post( $("#base_url").val()+"admin/ad/get_plans_by",{
                'plan_id'     :   value,
                'tds_csrf': $('input[name$="tds_csrf"]').val()
            },function(data){
               $("#menu_ads").html(data);
               $("#plan_ad_div").show();
            });
        
    });
    $(document).on('change','#ad_details',function(){
        var value = $( "select#ad_details option:selected").val();

        $.post( $("#base_url").val()+"admin/ad/get_plans_by",{
                'plan_id'     :   value,
                'tds_csrf': $('input[name$="tds_csrf"]').val()
            },function(data){
                 $("#menu_ads").html(data);
                 $("#plan_ad_div").show();
                 
                 
            });
    });
});

</script>



