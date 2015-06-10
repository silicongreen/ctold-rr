<?php

class socialbar extends widget
{
    function run($post_id, $main_post_id, $main_headline, $headline, $user_view_count, $wow_count, $language, $other_language, $good_read_single, $s_lang = '')
    {
        $data['post_id'] = $post_id;
        $data['main_post_id'] = $main_post_id;
        $data['main_headline'] = $main_headline;
        $data['headline'] = $headline;
        $data['user_view_count'] = $user_view_count;
        $data['wow_count'] = $wow_count;
        $data['language'] = $language;
        $data['other_language'] = $other_language;
        $data['good_read_single'] = $good_read_single;
        $data['s_lang'] = $s_lang;
        $this->render($data);
    }
}