<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    /* @name        : sanitize()
     * @description : create slug from given string
     * @author      : champs21[mahamud]
     * @params      : $str string, $char = string
     * @return type : string
     * 
     */ 

    if(!function_exists('sanitize')){
          function sanitize($str, $char = '-'){
             // Lower case the string and remove whitespace from the beginning or end
             $str = trim(strtolower($str));
             
             // Remove single quotes from the string
             $str = str_replace("'", '', $str);
             
             // Every character other than a-z, 0-9 will be replaced with a single dash (-)
             $str = preg_replace("/[^a-z0-9]+/", $char, $str);
             
             // Remove any beginning or trailing dashes
             $str = trim($str, $char);
             
             return $str;
          } 
      }
      
      if(!function_exists('unsanitize')){
          function unsanitize($str, $char = '-')
          {
             // Lower case the string and remove whitespace from the beginning or end
             $str = trim(strtolower($str));
             
             // Remove single quotes from the string
             $str = str_replace("-"," ", $str);
             
             
             // Remove any beginning or trailing dashes
             $str = trim($str, $char);
             
             return $str;
          } 
    } 
   
   
    /* @name        : limit_words()
     * @description : show limit word from given string
     * @author      : champs21[mahamud]
     * @params      : $string, $word_limit = Integer Number
     * @return type : string
     * 
     */ 
      
    if(!function_exists('limit_words')){
        function limit_words($string, $word_limit)
        {
          $words = explode(" ",$string);
          return implode(" ", array_splice($words, 0, $word_limit));
        }
    }
    
    if(!function_exists('limit_string')){
        function limit_string($string,$limit=50)
        {
            if(count($string)>$limit)
                $string = substr ($string, 0,$limit)."...";
            return $string;
        }
    }        
 

?>
