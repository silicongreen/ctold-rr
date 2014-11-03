<?php
class upload_files{
    
    public $file_type = '';
    public $file_name = '';
    public $file_size = ''; // size of the current file
    public $file_field = ''; // name of the file field attribute
    public $max_allowed_size  = ''; // to upload file of any size do not set this property
    public $file_mime = '';
    public $error_msg;

    # image dimenstion relted variables
    private $max_width = 1024; // maximum image width
    private $max_height = 768; // maximum image height
    private $width; // image width
    private $height; // image height
    private $exact; // image height
    # image dimenstion relted variables
    
    private $file;
    
    private $allowed_types = array(
                                'images'=>array(
                                    'image/tiff', // .tiff
                                    'image/jpeg', // .jpg, jpeg
                                    'image/png', // .png
                                    'image/gif', // .gif
                                ),
                                'docs'=>array(
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx (2007 or >)
                                    'application/msword', // .doc (< 2007)
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx (2007 or >)
                                    'application/vnd.ms-excel', // .xls (< 2007)
                                    'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx (2007 or >)
                                    'application/vnd.ms-powerpoint', // .ppt (< 2007)
                                    'application/pdf',
                                ),
                            );
    /**
     * @function __construct construction funciton for file validation
     * @var $file the resource file to be validated
     * @var max allowed file sise
    */
    public function __construct($file){
        if(!empty($file)){
            
            $mod_name = key($file);
            $mod = new $mod_name();
            $file_fields = $mod->file_fields();
            $this->file = $file[$mod_name];
            $this->file_field = $file_fields[0];
            
            foreach($file as $fil){
                if(is_array($fil)){
                    $this->file_size = $fil['size'][$this->file_field];
                    $this->file_name = $fil['name'][$this->file_field];
                    break;
                }else{
                    $this->file_size = $fil['size'];
                    $this->file_name = $fil['name'];
                    break;
                }
            }
        }else{
            $this->error_msg = "No valid file found.";
        }
    }
    
    /**
     * @function upload_file is successfull then it will return the file name as string.
     * @param string $file_name userdeifned name of the file. If provided then file will be saved this name
     * @param resource $file
     * @param integer $max_size is the maximum file size in KB.
    */
    public function upload_file($path, $file_name = NULL, $file = NULL, $max_size = NULL){
        $file = (empty($file)) ? $this->file : $file;
        $max_allowed_size = (empty($max_size)) ? $this->max_allowed_size : $max_size;
        if($this->validate_file() ===  true){
            $file_name = (empty($file_name)) ? $this->file_name() : $file_name;
            if(!@move_uploaded_file($file['tmp_name'][$this->file_field], UPLOADPATH.$path.'/'.$file_name)){
                $this->error_msg = "Upload directory may not found or read only permission.";
                return false;
            }else{
                return $file_name;
            }
        }
    }
    
    /**
     * @function validateFile validates file for uploading
     * currently this function validates image and documents
    */
    public function validate_file($file = NULL, $max_size = NULL){
        $file = (empty($file)) ? $this->file : $file;
        $max_allowed_size = (empty($max_size)) ? $this->max_allowed_size : $max_size;
        
        $finfo = new finfo(FILEINFO_MIME); # FILEINFO_MIME_TYPE
        $type = $finfo->file($file['tmp_name'][$this->file_field]);
        
        $mime = substr($type, 0, strpos($type, ';'));
        
        # Checks for allowed file types.
        if($this->check_file_type($mime) === true){
            
            if(strstr($mime,'image/') !== false){
                # get width & height from image dimension
                $image_size = getimagesize($file['tmp_name'][$this->file_field]);
                $this->width = $image_size[0];
                $this->height = $image_size[1];
                # get width & height from image dimension
                
                if($this->check_image_dimension() !== true){
                    return false;
                }
            }
            
            # Checks for maximum allowed file size if $max_allowed_size attribute is set.
            if(!empty($max_allowed_size) && (int)$max_allowed_size > 0){
                if($this->check_file_size() !== true){
                    return false;
                }
            }
            
            # Checks file for XSS threats.
            if($this->file_xss_clean() === true){
                return true;
            }else{
                $this->error_msg = "XSS threats detected.";
                return false;
            }
        }
        
        return false;
        exit;
    }
    
    /**
     * @function file_name returns generated new file name
    */
    public function file_name($file = NULL, $file_field = NULL, $name = NULL){
        if(empty($file)){
            $file = $this->file;
            $file_name = $this->file_name;
        }else{
            $file = $file;
            $file_name = $file[$file_field]['name'];
        }
        
        if(!empty($name)){
            $new_file_name = $name;
        }else{
            $ext = explode('.',$file_name);
            $new_file_name = time().'.'.$ext[1];
        }
        return $new_file_name;
    }
    
    /**
     * @function check_file_type checks allowed file type array for valid file types
     * @var string $mime type
    */
    public function check_file_type($mime){
        $allowed_types = $this->allowed_types;
        $this->error_msg = "Invalid file type.";
        foreach($allowed_types as $allowed_type){
            if(in_array($mime,$allowed_type)){
                $this->error_msg = "";
                return true;
            }
        }
        return false;
    }
    
    /**
     * @function check_file_size checks maximum allowed file size
     * @var string $file file resource
     * @var string $file_field name of the file filed or attribute
    */
    public function check_file_size($file = NULL, $file_field = NULL, $max_allowed_size = NULL){
        if(empty($file)){
            $file = $this->file;
            $file_size = $this->file_size;
            $max_allowed_size = $this->max_allowed_size;
        }else{
            $file = $file;
            $file_size = $file[$file_field]['size'];
            $max_allowed_size = $max_allowed_size;
        }
        
        $this->error_msg = "File size is larger than allowed.";
        
        /*
         * If file size is 0 (Zeor) means invalid, file size must be greater than 0 (Zero) 
        */
        if((int)$file_size == 0){
            $this->error_msg = "Invalid file.";
            return false;
		}
        
        /*
        * Checks if the file size is less than or equal to maximum allowed file size.
        * if not then returns false.
        */
        if((int)$file_size <= (int)$max_allowed_size){
            $this->error_msg = "";
            return true;
        }
        
        return false;
    }
    
    /**
     * @function set_allowed_dimension sets amximum allowed image width & height
     * @param integer $width sets maximum width of image. Default value 1024
     * @param integer $height sets maximum height of image. Default value 768
     * @param boolean $exact. If true then max width and height must match image width and height
     * if false then image width and height must be less then max width and height. Default false. 
    */
    public function set_allowed_dimension($width = null, $height = null, $exact = false){
        $this->max_width = (empty($width)) ? $this->max_width : $width;
        $this->max_height = (empty($height)) ? $this->max_height : $height;
        $this->exact = $exact;
    }
    
    public function check_image_dimension(){
        if($this->exact === true){
            if(((int)$this->width != (int)$this->max_width) || ((int)$this->height != (int)$this->max_height)){
                $this->error_msg = "Image width & height invalid.";
                return false;
            }
        }else{
            if(((int)$this->width > (int)$this->max_width) || ((int)$this->height > (int)$this->max_height)){
                $this->error_msg = "Image width & height invalid.";
                return false;
            }
        }
        return true;
    }
    
    /**
	 * Sanitize file name
	 *
	 * @param	string
	 * @return	string
	 */
	private function clean_file_name($filename)
	{
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						'.',
						',',
						'%2c',    // ,
						'%252C',  // ,
						'%25',
						"%20",
						"%22",
						"%3c",		// <
						"%253c",	// <
						"%3e",		// >
						"%0e",		// >
						"%28",		// (
						"%29",		// )
						"%2528",	// (
						"%26",		// &
						"%24",		// $
						"%3f",		// ?
						"%3b",		// ;
						"%3d"		// =
					);

		$filename = str_replace($bad, '', $filename);

		return stripslashes($filename);
	}
    
    // --------------------------------------------------------------------

	/**
	 * Runs the file through the XSS clean function
	 *
	 * This prevents people from embedding malicious code in their files.
	 * I'm not sure that it won't negatively affect certain files in unexpected ways,
	 * but so far I haven't found that it causes trouble.
	 *
	 * @return	void
	 */
	public function file_xss_clean($file = NULL, $file_field = NULL)
	{
        $_file = (empty($file)) ? $this->file : $file;
        $file_field = (empty($file_field)) ? $this->file_field : $file_field;
        $file = $_file['tmp_name'][$file_field];
        
		if(function_exists('memory_get_usage') && memory_get_usage() && ini_get('memory_limit') != '')
		{
			$current = ini_get('memory_limit') * 1024 * 1024;

			// There was a bug/behavioural change in PHP 5.2, where numbers over one million get output
			// into scientific notation.  number_format() ensures this number is an integer
			// http://bugs.php.net/bug.php?id=43053
			$new_memory = number_format(ceil(filesize($file) + $current), 0, '.', '');
			ini_set('memory_limit', $new_memory); // When an integer is used, the value is measured in bytes. - PHP.net
		}

		// If the file being uploaded is an image, then we should have no problem with XSS attacks (in theory), but
		// IE can be fooled into mime-type detecting a malformed image as an html file, thus executing an XSS attack on anyone
		// using IE who looks at the image.  It does this by inspecting the first 255 bytes of an image.  To get around this
		// CI will itself look at the first 255 bytes of an image to determine its relative safety.  This can save a lot of
		// processor power and time if it is actually a clean image, as it will be in nearly all instances _except_ an
		// attempted XSS attack.
        
		if(function_exists('getimagesize') && @getimagesize($file) !== false){
			if(($file = @fopen($file, 'rb')) === false){ // "b" to force binary
				return false; // Couldn't open the file, return false
			}

			$opening_bytes = fread($file, 256);
			fclose($file);
            
            // These are known to throw IE into mime-type detection chaos
			// <a, <body, <head, <html, <img, <plaintext, <pre, <script, <table, <title
			// title is basically just in SVG, but we filter it anyhow
            
			if(!preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', $opening_bytes)){
				return TRUE; // its an image, no "triggers" detected in the first 256 bytes, we're good
			}
		}/*else{ // If not an image file
            var_dump(fopen($file,'r'));
            exit;
            if(($data = @file_get_contents($_file)) === false){
              var_dump($data);
              exit;
            	//return false;
            }
            echo 'hi';exit;
		}*/

		if(($data = @file_get_contents($_file)) === false){
        	return false;
        }
        #echo 'he';exit;
		$CI =& get_instance();
		return $CI->security->xss_clean($data, TRUE);
	}
}
?>