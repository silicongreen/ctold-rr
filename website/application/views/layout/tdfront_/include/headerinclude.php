<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php $ar_segmens = $this->uri->segment_array();?>		
		<?php if (count($ar_segmens) == 0 ):?>
		<meta http-equiv="refresh" content="300" />		
        <?php endif; ?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--meta http-equiv="Expires" content="<?php echo gmdate("D, d M Y H:i:s", time() + 86400) . " GMT"; ?>" /-->
        <!--meta http-equiv="Cache-control" content="max-age=86400" /-->
        <title><?php echo $title; ?></title>
        <link href="<?= base_url() ?>styles/layouts/tdsfront/images/favicon.ico" type="image/x-icon" rel="shortcut icon" />
        
        <link rel="canonical" href="<?php echo current_url();?>"/>
        <?php
        // Add any keywords
        echo ( isset($keywords) ) ? meta('keywords', $keywords) : '';

        // Add a discription
        if ( isset($description) && strlen(trim($description)) > 0 )
        {
            echo ( isset($description) ) ? meta('description', $description) : '';
        }
        else if ( isset($fb_contents) && !is_null($fb_contents) )
        {
            echo ( isset($fb_contents['description']) ) ? meta('description', $fb_contents['description']) : '';
            if ( isset($fb_contents['image']) && strlen($fb_contents['image']) > 0 )
            {
                echo '<link rel="image_src" href="' . $fb_contents['image']  . '" />';
            }
        }

        // Add a robots exclusion
        echo ( isset($no_robots) ) ? meta('robots', 'noindex,nofollow') : '';
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/all.css?v=<?php echo $js_version; ?>" type="text/css" media="screen"/>

        <?php
        // Always add the main stylesheet
        //echo link_tag( array( 'href' => 'styles/layouts/tdsfront/css/style.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
        // Add any additional stylesheets
        if (isset($css)) {
            foreach ($css as $href => $media) {
                if ( $href != "0" )
                     echo link_tag(array('href' => $href . '?v=' . $js_version, 'media' => $media, 'rel' => 'stylesheet')) . "\n";
            }
        }
        ?>
        <style media="print">
            .noPrint{ display: none; }
            .yesPrint{ display: block !important; }
             body {
                    -webkit-font-smoothing: antialiased;
                    text-rendering: optimizeLegibility;
                }
        </style>
        <!--[if lte IE 7]>
        <link href="<?php echo base_url(); ?>styles/layouts/tdsfront/css/yaml/core/iehacks.css" rel="stylesheet" type="text/css" />
        <![endif]-->

        <!--[if lt IE 9]>
        <script src="<?php echo base_url(); ?>js/ie9.js"></script>
        <![endif]-->
        
        <?php if ( isset($fb_contents) && !is_null($fb_contents) ) : ?>
            <?php 
                    foreach($fb_contents as $key => $value) : 
                        
                        if($key=="image")
                        {
                            if(strpos($value,"http://")===false)
                            {
                                if($value=="")
                                {
                                   $value = "styles/layouts/tdsfront/images/no_image/fb.jpg"; 
                                }    
                                $value = base_url().$value;
                                
                                
                            }
                            list($width_main, $height_main, $type_main, $attr_main) = getimagesize($value);
                            if(!isset($width_main))
                            {
                                $value = str_replace("facebook/", "", $value);
                                
                            }   
                            if(strpos($value,"bd.")!==false)
                            {
                                $value = str_replace("bd.", "www.", $value);
                            }
                        }
                
                    ?>
        <meta property="og:<?php echo $key; ?>" content="<?php echo str_replace('"', "", $value); ?>" />
              <meta name="twitter:<?php echo $key; ?>" content="<?php echo str_replace('"', "", $value); ?>" />
            <?php endforeach; ?>
            <meta name="twitter:card" content="summary" />
            <meta name="twitter:site" content="@dailystarnews" />
        <?php endif; ?>
</head>

