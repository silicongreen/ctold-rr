<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    $target = "../../compresstest/charger-hellcat-white-3";
    $target_url = "http://jpgoptimiser.com/optimise";
    $image_path = str_replace("../../", "/home/champs21/public_html/website/", $target);
    $image_path = str_replace("/./", "/", $image_path);
    $dest_path  = "/".$image_path;
    $this->super_compress($target_url, $image_path, $dest_path);

    function super_compress( $target_url, $image_path, $dest_image_path )
    {
        /*
        * To change this template, choose Tools | Templates
        * and open the template in the editor.
        */
       $file_name_with_full_path = $image_path;
       $post = array('input'=>'@'.$file_name_with_full_path);

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,$target_url);
       curl_setopt($ch, CURLOPT_POST,1);
       curl_setopt($ch, CURLOPT_HEADER, 0);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $result=curl_exec ($ch);
       curl_close ($ch);


       if(file_exists($dest_image_path)){
            unlink($dest_image_path);
        }
        $fp = fopen(substr($dest_image_path, 1, strlen($dest_image_path)),'w+');
        fwrite($fp, $result);
        fclose($fp);
    }