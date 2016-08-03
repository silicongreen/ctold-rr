<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            
            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Add News To Home Page</h2>
                       

                        <?php echo form_open('',array('class' => 'validate_form','enctype'=>'multipart/form-data'));?>  
                            
                            <fieldset class="label_side top">
                                    <label for="required_field">Date</label>
                                    <div>
                                        <input id="issue_date" name="date" class="datepicker"  value="<?php echo  $date ?>"  type="text" >  
                                    </div>     
                            </fieldset>
                            <fieldset class="top" id="type_div">
                                <label for="required_field">For Type
                                </label>
                                <div  id="tree3rd">
                                    <?php echo $post_type;?>
                                </div>    
                            </fieldset>
                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                         <?php echo form_close();?>  
                    </div>
                </div>


            </div>

        </div>
        
        <style>
    
            #tree2nd ul 
            {
                list-style: none;
            }
            #tree2nd ul li 
            {
                float:left;
                margin: 3px 35px 6px 0;
            }
            #tree3rd ul 
            {
                list-style: none;
            }
            #tree3rd ul li 
            {
                float:left;
                margin: 3px 35px 6px 0;
            }
        </style>

