<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
            $widget = new Widget();
            $widget->run('sidebar');
        ?>
        
        <input type="hidden" id="modelwidth" value="80%" />
        <input type="hidden" id="modelheight" value="70%" />
        
        <div id="main_container" class="main_container container_16 clearfix">
            <div class="flat_area grid_16">
                <h2>Gallery Management</h2>
                <?php $lib->load_browser(); ?>
            </div>
        </div>
    </div>
</div>