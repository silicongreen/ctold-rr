<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Gallery
 *
 * @author ahuffas
 */
class Voice extends DataMapper  {
    //put your code here
    var $table = "voice_box";
    
    var $personality_name;
    var $personality_description;
    var $topic;
    
    public function getVoices($ar_issue_date = array()){
        
        $where = (!empty($ar_issue_date)) ? " AND DATE(published_date) = '". date('Y-m-d', strtotime($ar_issue_date['s_issue_date'])) ."'" : "";
        
        $sql = "SELECT
                	v.id,
                	v.voice,
                	v.published_date,
                	pre_personality.`name` AS personality_name,
                    pre_topic.topic AS topic,
                    pre_personality.`description` AS personality_description
                FROM
                    `tds_voice_box` AS v
                INNER JOIN tds_personality AS pre_personality ON pre_personality.id = v.personality_id
                LEFT JOIN tds_topic AS pre_topic ON pre_topic.id = v.topic_id
                WHERE v.is_active = 1{$where};";
        
        $obj_voice = $this->query($sql);
        
        return (count($obj_voice->all) > 0) ? $obj_voice : false;
    }
    
}

?>
