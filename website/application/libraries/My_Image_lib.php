<?php

/**
 * Here I extended CI_Image_lib.
 * 
 * In this library I use it various purpoes. 
 *
 * @author Mahamud
 * @email  mahamud.hasan35@gmail.com
 * @phone +8801913-28-70-32
 */





class My_Image_lib  extends CI_Image_lib
{
   
    public $width;
    public $height;
    public $originalCopy;
    public $type;
    public $fqpn;
    public $dir;
    public $sanitizedFilename;
    public $sanitizedFQPN;
    public $originalFilename;
    public $workingCopy;
          
    const QUALITY     = 70; #max 100
    const QUALITY_PNG = 5;  #max 9
    
    
    public function my_Image_lib( $fqpn = null )
    {
       $this->originalFilename     = basename($fqpn);
       $this->dir                  = dirname($fqpn);
       $this->fqpn                 = $fqpn;
     
   
         
     
         
       if(!empty($fqpn))
       {  

          
         list($this->width,
              $this->height,
              $this->type) = getimagesize( $fqpn ); 
       }  
          
       if($this->type== IMAGETYPE_JPEG)
       {
          $this->originalCopy  = imagecreatefromjpeg($fqpn);  
       }
       elseif( $this->type == IMAGETYPE_GIF ) 
       {
          $this->originalCopy  = imagecreatefromgif ($fqpn);
       }
       elseif( $this->type == IMAGETYPE_PNG )
       { 
          $this->originalCopy  = imagecreatefrompng($fqpn);
       }

      }  
      
      
      public function resize( $data = null)
      {
         $newWidth          = $data["width"];
         $newHeight         = $data["height"];

          
         if($this->width < $this->height) 
         {     
            $newWidth   =   $this->width/$this->height * $newHeight;
            $newHeight  =   $newHeight;             
         }
         else 
         {             
            $newWidth  =   $newWidth;  
            $newHeight =   $this->height/$this->width * $newWidth;
         }              
               
         $this->workingCopy = imagecreatetruecolor($newWidth,$newHeight);       
              
         imagecopyresampled($this->workingCopy, $this->originalCopy, 0,0,0,0, 
                               $newWidth, $newHeight, $this->width, $this->height);
            
      } 
      
      
      
      
      
      
      
      
      public function reduce_quality()
      {
         $this->workingCopy = $this->originalCopy;       
              
         imagecopyresampled($this->workingCopy, $this->originalCopy, 0,0,0,0, 
                               $this->width, $this->height, $this->width, $this->height);          
          
      }
      
      
      public function createThumbnail( $params = null )
      {          
           
         if(!$params['crop'])
         {             
            if($this->width < $this->height) 
            {     
               $thumbWidth   =   $this->width/$this->height * $params['thumbHeight'];
               $thumbHeight  =   $params['thumbHeight'];             
            }
            else 
            {             
               $thumbWidth  =   $params['thumbWidth']; 
               $thumbHeight =   $this->height/$this->width * $thumbWidth;
            }              
               
            $this->workingCopy = imagecreatetruecolor($thumbWidth,$thumbHeight);         
            imagecopyresampled($this->workingCopy, 
                                    $this->originalCopy,
                                    0,0,0,0, 
                                    $thumbWidth, 
                                    $thumbHeight, 
                                    $this->width, 
                                    $this->height);   
         }  
         else
         {            
            $centreX = round($this->width / 2);
            $centreY = round($this->height / 2);
            
            $cropWidth  = $params['thumbWidth']; 
            $cropHeight = $params['thumbHeight']; 
            $cropWidthHalf  = round($cropWidth / 2); 
            $cropHeightHalf = round($cropHeight / 2);
            
            $x1 = max(0, $centreX - $cropWidthHalf);
            $y1 = max(0, $centreY - $cropHeightHalf);
            
            $this->workingCopy  = imagecreatetruecolor($cropWidth, $cropHeight);        
            imagecopy($this->workingCopy, 
                        $this->originalCopy, 
                        0, 0,
                        $x1, $y1,  
                        $cropWidth, 
                        $cropHeight);                        
         }    

      }          
      

     public function cropFromCenter( $cropWidth = null, $cropHeight = null )
     {
		    
         $centreX = round($this->width / 2);
         $centreY = round($this->height / 2);

         $cropWidthHalf    = round($cropWidth / 2); 
         $cropHeightHalf   = round($cropHeight / 2);

         $x1 = max(0, $centreX - $cropWidthHalf);
         $y1 = max(0, $centreY - $cropHeightHalf);
        
         $this->workingCopy = imagecreatetruecolor($cropWidth, $cropHeight);        
         imagecopy($this->workingCopy, 
                   $this->originalCopy, 
                   0, 0, 
                   $x1, $y1,  
                   $cropWidth, 
                   $cropHeight);
            
     } 
           
    /* @name        : overlayPNG()
     * @description : Overlay a transparent PNG on top of the current image
     *                Default left = 0, top = 0
     * @author      : champs21[mahamud]
     * @params      : $params array()  
     * @return type : 
     * 
     */  
     public function overlayPNG( $params = null )
     {      
         $overlayImage = imagecreatefrompng($params['fqpn']);
         $destWidth     = $this->width;
         $destHeight    = $this->height;
         $srcWidth      = imagesx($overlayImage);
         $srcHeight     =  imagesy ($overlayImage);  
         $this->workingCopy  =  $this->originalCopy;

         imagecopy( $this->workingCopy, 
                    $overlayImage, 
                    $params['left'] ? $params['left'] : 0, 
                    $params['top']  ? $params['top'] :  0 , 
                    0, 0, 
                    $srcWidth, 
                    $srcHeight);         
     }              
      

      
      
      public function write( $destFQPN = null )
      {
         $destFile = ($destFQPN) ? $destFQPN : $this->fqpn;
	                 
         if ($this->type == IMAGETYPE_JPEG)
         { 
            imagejpeg($this->workingCopy, $destFile,self::QUALITY);
         }
         elseif($this->type == IMAGETYPE_GIF)
         {
            imagegif($this->workingCopy, $destFile,self::QUALITY);
         }
         elseif($this->type == IMAGETYPE_PNG)
         {
            imagepng($this->workingCopy, $destFile,self::QUALITY_PNG);
         }
           
      }
      
 /*
  * Need testing 
  * 
  * 
  */     
      
      
      
    public function writeMessage( $params = null, $rgbColor = null)
    {   
       
        $textcolor = imagecolorallocate($this->originalCopy, 
                                        $rgbColor[0], 
                                        $rgbColor[1], 
                                        $rgbColor[2] ); 

        imagettftext($this->originalCopy, 
                     $params['fontSize'], 0, 
                     $params['x'], 
                     $params['y'], 
                     $textcolor, 
                     $params['font'],  
                     $params['text']);       
        
    }        
      
    
    
    
    public static function upload($source,$destination)
    {
        move_uploaded_file($source,$destination);   
        return $destination;       
    } 
    
    public static function delete($source = '')
    {
        //$source .=base_url().$source;
        unlink($source);
    }        
}

?>
