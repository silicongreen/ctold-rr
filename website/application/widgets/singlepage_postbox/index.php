<?php

class singlepage_postbox extends widget
{	
    function run($post_id)
    {	
        $data_news = $this->data_news($post_id);
        $data['toprated_news'] = array();
        $data['mostpopuler_news'] = array();
        $data['mostpopuler_news'] = array();
        
        $data['toprated_news'] = $this->toprated_news($data_news->top_rated);
        $data['mostpopuler_news'] = $this->mostpopuler_news($data_news->most_popular);
        $data['editorpicks_news'] = $this->editorpicks_news($data_news->editor_picks);
        $this->render($data);
    }
	
    function toprated_news($limit)
    {
        $this->db->select("*");
        $this->db->where("status",5);
        $this->db->order_by("wow_count", "desc");        
        $toprated_news = $this->db->get("post",$limit)->result();

        $ar_news_data = array();
        $i = 0;
        foreach ($toprated_news as $r_news) {

            if (isset($r_news->content)) {				

                    $ar_news_id['id'] = $r_news->id;				
                    $ar_news = getFormatedContentAll($r_news, 150,'index');
                    $ar_news_data[$i] = $ar_news_id + $ar_news;
            }
            $i++;
        }

        return (count($ar_news_data) > 0) ? $ar_news_data : FALSE;
    }
    function mostpopuler_news($limit)
    {
        $this->db->select("*");
        $this->db->where("status",5);
        $this->db->order_by("user_view_count", "desc");        
        $mostpopuler_news = $this->db->get("post",$limit)->result();

        $ar_news_data = array();
        $i = 0;
        foreach ($mostpopuler_news as $r_news) {

                if (isset($r_news->content)) {				

                        $ar_news_id['id'] = $r_news->id;				
                        $ar_news = getFormatedContentAll($r_news, 150,'index');
                        $ar_news_data[$i] = $ar_news_id + $ar_news;
                }
                $i++;
        }

        return (count($ar_news_data) > 0) ? $ar_news_data : FALSE;
    }
    
    function editorpicks_news($limit)
    {
        $this->db->select('tds_post.*');
        $this->db->from('post');
        $this->db->join('tds_editor_picks', 'tds_editor_picks.post_id = tds_post.id');
        $this->db->limit($limit);
        $editorpicks_news = $this->db->get()->result();
        
        
//        $this->db->select("*");
//        $this->db->where("status",5);
//        $this->db->order_by("user_view_count", "desc");        
//        $editorpicks_news = $this->db->get("post",$limit)->result();

        $ar_news_data = array();
        $i = 0;
        foreach ($editorpicks_news as $r_news) {

            if (isset($r_news->content)) {				

                    $ar_news_id['id'] = $r_news->id;				
                    $ar_news = getFormatedContentAll($r_news, 150,'index');
                    $ar_news_data[$i] = $ar_news_id + $ar_news;
            }
            $i++;
        }

        return (count($ar_news_data) > 0) ? $ar_news_data : FALSE;
    }
    
    function data_news($post_id)
    {
        $this->db->select("most_popular,top_rated,editor_picks");
        $this->db->where("id",$post_id);     
        $data_news = $this->db->get("post")->row();

        

        return $data_news;
    }
	
}