<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Post_category
 *
 * @author ahuffas
 */
class Post_category extends DataMapper{
    //put your code here
    var $table = "post_category";
    
    public function update_priority($post_ids, $cate_id, $post_publish){
        
        $i = 1;
        $cur_publish_date = "";
           
        foreach($post_ids as $post_id){
           
          $data = array( 'inner_priority' => $i);          
          $this->db->where(array('category_id' => $cate_id, 'post_id' => $post_id));
          
          $this->db->update($this->table, $data);
          $i++;
        }
        return ($i-1);
    }
    
//    public function update_priority($post_ids, $cate_id, $post_publish){
//        
//        $i = 1;
//        $cur_publish_date = "";
//           
//        foreach($post_ids as $key=>$post_id){
//           
//          if($cur_publish_date!=$post_publish[$key])
//          {
//                $cur_publish_date = $post_publish[$key];
//                $date_string = "i_".str_replace("-","",$cur_publish_date);
//                
//                if(!isset($$date_string) && $$date_string<1)
//                    $$date_string= 1;
//          }
//          $data = array( 'inner_priority' => $$date_string);          
//          $this->db->where(array('category_id' => $cate_id, 'post_id' => $post_id));
//          
//          $this->db->update($this->table, $data);
//          $$date_string++; 
//          $i++;
//        }
//        return ($i-1);
//    }
}

?>
