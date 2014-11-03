

<ul id="sortable" class="sortable_news" style="min-height: 100px;">
    <?php foreach($home_page_post as $post): ?>
    <li class="post post_sort ui-state-default" id="post_<?php echo $post->id;?>" style=" margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; clear: both; cursor: move; line-height: 24px;"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
        <?php echo $post->headline; ?>
        <?php if($post->show): ?>
         - <a target="_blank" href="<?php echo base_url("admin/news/edit/" . $post->id); ?>">EDIT</a>
        <label class="del_post" style="font-size: 14px; font-weight: bold; color: #555; cursor: pointer;">DELETE</label>
        <?php endif;?>
    </li>
    <?php endforeach; ?>
</ul>
                    
                       