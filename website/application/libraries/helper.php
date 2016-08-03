<?php

/**
 * Description of Helper library .
 * 
 * In this library I use it various purpoes. 
 *
 * @author Mahamud
 * @email  mahamud.hasan35@gmail.com
 * @phone +8801913-28-70-32
 */

class Helper
{
   
    /* @name        : sanitize()
     * @description : create slug from given string
     * @author      : champs21[mahamud]
     * @params      : $str string, $char = string
     * @return type : string
     * 
     */ 
      public static function sanitize($str, $char = '-')
      {
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
   

    
    public static function limit_words($string, $word_limit)
    {
      $words = explode(" ",$string);
      return implode(" ", array_splice($words, 0, $word_limit));
    }	    
}

?>
