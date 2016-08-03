
<?php //echo "-POPOPO";?>
<div class="ym-grid" style="margin:3px 0px; ">
    <ul class="top-header-nav">
        <li style="padding-top: 16px;"><a class="logo" href="<?= base_url()?>"></a></li>
        <li style="width: 220px; margin-left: 5px;text-align:right;"> 
            <span class="fb-like-div">
                <!--<img src="<?= base_url()?>styles/layouts/tdsfront/images/facebook.jpg" alt="Facebook Icon" align="right" />--> 
				<div class="fb-like" data-href="https://www.facebook.com/dailystarnews" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                <br/><br/><br/>
                <?php if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  ) : ?>
                <p> <?php echo date( "l, F d, Y", strtotime($_GET['archive']) );?> </p>
                <?php elseif ( isset($_GET['date']) &&  strlen($_GET['date']) != "0"  ) : ?>
                <p> <?php echo date( "l, F d, Y", strtotime($_GET['date']) );?> </p>
                <?php else : ?>
                    <p> <?php echo date( "l, F d, Y" );?> </p>
                <?php endif; ?>
                 
            </span>

        </li>
        <li style="margin-right:10px; float:right;">
            <div class='ym-gbox adds-header'>
                <?php
                    $ci_key = (isset($ci_key)) ? $ci_key : 'index';
                    $s_ci_key = (isset($ci_key)) ? $ci_key : 'index';
                    $menu_data = false;
                    if ( $ci_key != "index" )
                    {
                        $menu_data = get_menu_by_cikey($ci_key);
                    }
                    //$this->uri->segment_array();                    
                    //echo "<pre>";
                    //print_r($menu_data);
                    
                    if($this->uri->segment(1)=="" || $ci_key == "index")
                    {
                        $adplace_helper = new Adplace;
                        $adplace_helper->printAds( 1, null, FALSE, $s_ci_key ,'index');
                        
                    }
                    else if($this->uri->segment(1) != "" && $this->uri->segment(2) == "" && $menu_data > 0)
                    {
                        $adplace_helper = new Adplace;
                        $adplace_helper->printAds( 17, 0, FALSE, $s_ci_key,'section' );
                    }
                    else if((get_menu_by_cikey($this->uri->segment(1)) > 0 && $this->uri->total_segments() == 2) || ($menu_data == 0 && $this->uri->total_segments() == 1))
                    {
                        $adplace_helper = new Adplace;    
                        $adplace_helper->printAds( 37, 0, FALSE, "0",'details' );
                    }
                    else
                    {
                        $s_ci_key = "0";
                    }
				?>
            </div>
        </li>
    </ul>
</div> 

<!-- Start navigation -->
<div class="ym-grid navigation">     <!-- navigation -->
    <?php
        $widget = new Widget;
        $widget->run( 'menuheader' );
    ?>	
</div>                            

<!-- End navigation -->


<!-- Start tricker news -->

<?php
    //if ( isset( $newstricks ) )
    //{
        $widget = new Widget;
        $widget->run( 'newstricker', "", $s_ci_key );
    //}
?>   

<!-- End tricker news -->


