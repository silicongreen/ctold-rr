<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <input type="hidden" id="modelwidth" value="50%" />
            <input type="hidden" id="modelheight" value="10%" />

            <div class="flat_area grid_16">
                <h2>Assessment Link</h2>
            </div>
            
            <fieldset class="label_side top">
                <label for="required_field">Link</label>
                <div>
                    <?php echo $assess_url; ?>
                </div>
            </fieldset>
            
        </div>
    </div>
</div>

<style type="text/css">
    #dt1 .members_table > tbody > tr > td { 
        vertical-align: middle;
        text-align: center;
    }
    
    #dt1 .members_table > tbody > tr > td > button { 
        float: none !important;
    }
    
    #dt1 .members_table > tbody > tr > td > img { 
        display: initial;
    }
</style>