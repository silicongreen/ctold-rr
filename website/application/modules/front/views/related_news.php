<!--<div id="related_news_1" class="related_news_parent">-->
    <h1>RELATED NEWS </h1>

    <ul class="related_news-list">
        <?php if ( $related_news ) foreach( $related_news as $news ) : ?>
            <?php if ( isset($news->title) && strlen($news->title) > 0 ) : ?>
                <li><a href="<?php echo $news->new_link; ?>" target="<?php echo ($news->related_type == 1) ? '_blank' : '_blank';?>"><?php echo $news->title; ?></a><br /><span style="font-size:8px;"><?php echo !empty($news->published_date)?date("F j, Y", strtotime($news->published_date)):""; ?></span></li>
				<!--<li><a href="<?php echo $news->new_link; ?>" target="<?php echo ($news->related_type == 1) ? '_self' : '_blank';?>"><?php echo $news->title; ?></a><br /><span style="font-size:8px;"><?php echo !empty($news->published_date)?date("F j, Y", strtotime($news->published_date)):""; ?></span></li>-->
            <?php endif; ?>
        <?php endforeach;?>
    </ul>
<!--</div>-->