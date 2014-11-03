<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $caption;?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="The Daily Star" />
        <meta property="og:title" content="<?php echo $caption;?>" />
        <meta property="og:image" content="<?php echo base_url().str_replace("gallery/","gallery/facebook/",$material_url);?>" />
        <meta property="og:url" content="<?php echo base_url()."socialPage/".basename($_SERVER['REQUEST_URI'])?>" />
        <meta property="og:description" content="<?php echo $caption;?> <?php echo $source;?>" />
    </head>
    <body>
        <div>
            <img src="<?php echo base_url().$material_url;?>" alt="<?php echo $caption;?>" />
        </div>
        <div>
            <?php echo $caption;?>
        </div>    
    </body>
</html>
