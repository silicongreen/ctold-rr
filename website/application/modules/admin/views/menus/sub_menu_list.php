<ul class="sortable">
    <?php if ($sub_menus) foreach($sub_menus as $sub_menu){ ?>
    <li class="post post_sort ui-state-default" id="post_<?php echo $sub_menu->id;?>" style=" margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; clear: both; cursor: move; line-height: 24px;"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
        <?php echo $sub_menu->title; ?>
    </li>
    <?php } ?>
</ul>
<style>
    .sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    .sortable li span { position: absolute; margin-left: -1.3em; }
</style>