<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="" />
<meta name="keywords" content=""/>
<?php
	// Add any keywords
	echo ( isset( $keywords ) ) ? meta('keywords', $keywords) : '';

	// Add a discription
	echo ( isset( $description ) ) ? meta('description', $description) : '';

	// Add a robots exclusion
	echo ( isset( $no_robots ) ) ? meta('robots', 'noindex,nofollow') : '';
?>
<?php
	// Always add the main stylesheet
        echo link_tag( array( 'href' => 'styles/style.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";

	// Add any additional stylesheets
	if( isset( $css ) )
	{
		foreach( $css as $href => $media )
		{
			echo link_tag( array( 'href' => $href, 'media' => $media, 'rel' => 'stylesheet' ) ) . "\n";
		}
	}
?>
<title><?php echo $title; ?></title>
</head>
<body>
    <div id="header">
       <h1>Header is Here</h1>
    </div>
    <?php echo $content; ?>
    <div id="footer">
       <h1>Footer is Here</h1>
    </div>
</body>
<?php
	// jQuery is always loaded
	echo script_tag( 'http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js' ) . "\n";
        echo script_tag( 'http://code.jquery.com/ui/1.10.3/jquery-ui.js' ) . "\n";
       // echo script_tag( $protocol . '://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js' ) . "\n";
        
	// Add any additional javascript
	if( isset( $javascripts ) )
	{
		for( $x=0; $x<=count( $javascripts )-1; $x++ )
		{
			echo script_tag( $javascripts["$x"] ) . "\n";
		}
	}

	// Add anything else to the head
	echo ( isset( $extra_head ) ) ? $extra_head : '';
?>
</html>