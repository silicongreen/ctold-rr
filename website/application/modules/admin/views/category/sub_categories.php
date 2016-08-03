 <style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #sortable li span { position: absolute; margin-left: -1.3em; }
</style>
<ul id="sortable" class="sortable">
    <?php if ($categories) foreach($categories as $category) : ?>
        <li class="sub_cat ui-state-default" id="category_<?php echo $category->id;?>_<?php echo $category->parent_id;?>"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $category->name; ?></li>
    <?php endforeach; ?>
</ul>