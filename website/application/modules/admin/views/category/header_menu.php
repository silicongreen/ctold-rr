<script src="<?php echo  base_url() ?>scripts/custom/customCategory.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Assign As Header Menu</h2>


                        <?php echo  form_open('', array('class' => 'validate_form', 'enctype' => 'multipart/form-data')); ?> 


                        <div id="div_show_for_header" >
                            <fieldset class="label_side top">
                                <label for="required_field">Assign As Header Menu</label>
                                <div>
                                    <input type="checkbox" value="1" id="chk_header_menu" name="chk_header_menu" <?php echo (isset($menu->id) && $menu->id != "") ? "checked" : ""; ?> >  
                                </div>     
                            </fieldset>

                            <div id="div_show_in_checked" style="<?php if (!isset($menu->id) || $menu->id == ""): ?>display:none<?php endif; ?>">

                                <fieldset class="label_side top">
                                    <label for="required_field">Title<span>If left blank then use category name to auto generate</span></label>
                                    <div>
                                        <input id="name" name="title" value="<?php echo  (isset($menu->title)) ? $menu->title : ""; ?>"  type="text"  >
                                        <div class="required_tag"></div>
                                    </div>
                                </fieldset>


                                <fieldset class="label_side top">
                                    <label for="required_field">Type</label>
                                    <div>
                                        <?php
                                        $menu_types = array('1' => 'Parent Menu', '2' => 'Sub Menu');

                                        $current_type = 2;
                                        if (!isset($menu->parent_menu_id) || $menu->parent_menu_id == null)
                                            $current_type = 1;

                                        $js = " id='menu_types'";
                                        echo form_dropdown('menu_types', $menu_types, $current_type, $js);
                                        ?>

                                    </div>
                                </fieldset>

                                <fieldset id="show_on_sub_menu" class="label_side top"  style="<?php if (!isset($menu->parent_menu_id) || $menu->parent_menu_id == null): ?>display:none<?php endif; ?>">
                                    <label for="required_field">Parent Menu</label>
                                    <div>
                                        <?php
                                        if (!isset($menu->parent_menu_id))
                                            $parent_menu_id = 0;
                                        else
                                            $parent_menu_id = $menu->parent_menu_id;

                                        echo form_dropdown('parent_menu_id', $parent_menu, $parent_menu_id);
                                        ?>

                                    </div>
                                </fieldset>

                                <fieldset  class="label_side top show_on_parent_menu" style="<?php if (isset($menu->parent_menu_id) && $menu->parent_menu_id != null): ?>display:none<?php endif; ?>">
                                    <label for="required_field">Number Of Recent News Shows</label>
                                    <div>
                                        <?php
                                        $number_of_news = array(null => 'None', '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six');

                                        if (!isset($menu->news_num))
                                            $news_num = 0;
                                        else
                                            $news_num = $menu->news_num;

                                        echo form_dropdown('news_num', $number_of_news, $news_num);
                                        ?>

                                    </div>
                                </fieldset>


                                <fieldset  class="label_side top show_on_parent_menu" style="<?php if (isset($menu->parent_menu_id) && $menu->parent_menu_id != null): ?>display:none<?php endif; ?>">
                                    <label for="required_field">Has Ad</label>
                                    <div>
                                        <?php
                                        $has_ad = array('0' => 'No', '1' => 'Yes');

                                        if (!isset($menu->has_ad))
                                            $has_ad_current = 0;
                                        else
                                            $has_ad_current = $menu->has_ad;



                                        echo form_dropdown('has_ad', $has_ad, $has_ad_current);
                                        ?>

                                    </div>
                                </fieldset>

                                <fieldset class="label_side top">
                                    <label for="required_field">Status</label>
                                    <div>
                                        <?php
                                        $status = array('0' => 'Inactive', '1' => 'Active');



                                        if (!isset($menu->is_active))
                                            $is_active = 1;
                                        else
                                            $is_active = $menu->is_active;

                                        echo form_dropdown('is_active', $status, $is_active);
                                        ?>

                                    </div>
                                </fieldset>

                            </div>
                        </div>
                        
                        
                        <div id="div_show_for_footer" >
                            <fieldset class="label_side top">
                                <label for="required_field">Assign As footer Menu</label>
                                <div>
                                    <input type="checkbox" value="1" id="chk_footer_menu" name="chk_footer_menu" <?php echo (isset($menu_footer->id) && $menu_footer->id != "") ? "checked" : ""; ?> >  
                                </div>     
                            </fieldset>

                            <div id="div_show_in_checked_footer" style="<?php if (!isset($menu_footer->id) || $menu_footer->id == ""): ?>display:none<?php endif; ?>">

                                <fieldset class="label_side top">
                                    <label for="required_field">Title<span>If left blank then use category name to auto generate</span></label>
                                    <div>
                                        <input id="name_footer" name="title_footer" value="<?php echo  (isset($menu_footer->title)) ? $menu_footer->title : ""; ?>"  type="text"  >
                                        <div class="required_tag"></div>
                                    </div>
                                </fieldset>


                                <fieldset class="label_side top"  >
                                    <label for="required_field">Parent Menu</label>
                                    <div>
                                        <?php
                                        if (!isset($menu_footer->parent_menu_id))
                                            $parent_menu_id = 0;
                                        else
                                            $parent_menu_id = $menu_footer->parent_menu_id;

                                        echo form_dropdown('parent_menu_id_footer', $parent_menu_footer, $parent_menu_id);
                                        ?>

                                    </div>
                                </fieldset>

                                

                                <fieldset class="label_side top">
                                    <label for="required_field">Status</label>
                                    <div>
                                        <?php
                                        $status = array('0' => 'Inactive', '1' => 'Active');



                                        if (!isset($menu_footer->is_active))
                                            $is_active = 1;
                                        else
                                            $is_active = $menu_footer->is_active;

                                        echo form_dropdown('is_active_footer', $status, $is_active);
                                        ?>

                                    </div>
                                </fieldset>

                            </div>
                        </div>



                        <div class="button_bar clearfix">
                            <button class="green" type="submit">
                                <span>Submit</span>
                            </button>
                        </div>
                       <?php echo  form_close(); ?>  
                    </div>
                </div>


            </div>

        </div>

