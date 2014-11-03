<?php if ($posts) foreach($posts as $post) : ?>
<li class="post post_sort ui-state-default" id="post_<?php echo $post->id;?>_5" style=" margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; clear: both; cursor: move; line-height: 24px;"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
    <?php echo $post->headline; ?> - <a target="_blank" href="<?php echo base_url("admin/news/edit/" . $post->id); ?>">EDIT</a>
</li>
<?php endforeach; ?>
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #sortable li span { position: absolute; margin-left: -1.3em; }
</style>