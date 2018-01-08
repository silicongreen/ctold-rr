<?php
$s_ci_key = (isset($ci_key)) ? $ci_key : NULL;
?>
<?php
$b_checked_cache = FALSE;
if (( list($i_type_id_cache, $i_category_id_cache, $s_category_name_cache) = get_category_type(sanitize($ci_key)))) {
    $b_checked_cache = TRUE;
}

if ((isset($_GET['archive']) && strlen($_GET['archive']) != "0") || (isset($_GET['date']) && strlen($_GET['date']) != "0" && $_GET['date'] != date("Y-m-d"))) {
    $b_checked_cache = FALSE;
}
$CI = & get_instance();
$CI->load->driver('cache', array('adapter' => 'file'));
$cache_name = "INNER_CONTENT_CACHE_" . $i_category_id_cache . "_" . str_replace(":", "-", str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");

ob_start();
?>

<div class="masthead"><h1 class="heading">Star Online Video</h1></div>

<div style="width:68%;float:left;" class="ym-grid  <?php echo ( $s_image_exists ) ? "mtab-1" : "mtab-0"; ?>" >

    <div style="display:none;margin:0 auto;" class="galleryrightwidget" data-skin="gallery" 
         data-autoplayvideo="false" data-responsive="true" 
         data-resizemode="fill" data-html5player="true" 
         data-autoslide="true" data-autoplayvideo="false"
         data-width="900" data-height="500" data-effect="fade"
         data-thumbwidth="170" data-thumbheight="100"
         data-googleanalyticsaccount="UA-29319282-1" data-onchange="onSlideChange" data-onthumbover="onThumbOver"
         data-onthumbout="onThumbOut"
         >


        <?php foreach ($data['common'] as $value): ?>
            <?php
            $s_thumb_image = $value->lead_material;

           // $s_thumb_image = str_replace("gallery/", 'gallery/main/', $s_thumb_image)
            ?> 
            <a title="<?php echo $value->headline; ?>" href="<?php echo $value->embedded ?>" ><img width="200" height="120" alt="<?php echo $value->headline; ?>" src="<?php echo base_url().$s_thumb_image ?>"></a>
        <?php endforeach; ?>

    </div>  


</div>
<div style="width:30%; float:right; margin-right:0px; margin-bottom:20px;" class="ym-grid" >
    <?php
    $c = 0;
    $style = 'display: none;';
    foreach ($data['common'] as $row):
        if ($c == 0) {
            $style = 'display: block;';
        } else {
            $style = 'display: none;';
        }
        ?>
        <div class="content-news-body" id="<?php echo $c; ?>" style="<?php echo $style; ?>">

            <div class="meta-info">
                <div class="meta-left"  ><h4 class="shoulder"><?php echo $row->shoulder; ?></h4></div>
                
                <!-- AddThis Button BEGIN -->
               
                <div  class="meta-right  share-video"><span class="icon"></span>
                    <a addthis:url="<?php echo create_link_url("index", $row->headline, $row->id); ?>" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=ra-51dc0c5478bb0e1a" class="addthis_button share-text" title="Share" href="#">Share</a>
                
                </div>
               
                <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51dc0c5478bb0e1a"></script>
                <!-- AddThis Button END -->
                
                
            </div> 
            <div style="clear:both;"></div>

            <h3 class="headline"><a href="<?php echo create_link_url("index", $row->headline, $row->id); ?>"><?php echo trim($row->headline); ?></a>
            </h3>

            <div class="video-description"> 
                <h5 class="byline"><small>Published: <?php echo date("g:i a l, F d, Y", strtotime($row->published_date)); ?></small></h5>
                <?php echo print_magazine_news($ci_key, $row, false, false, 45); ?> 
            </div>


            <!--SHARE ICONS-->

<!--            <div class="modal show-all-sharetool-modal hide" style="position: fixed; margin-left: -240px; margin-top: -134px; top: 50%; left: 50%; display: block;">
                <div class="modal-header">
                    <h4 class="modal-heading">Share This Video</h4>
                </div>
                <div class="modal-content">
                    <div class="section share">
                        <ul class="sharetools-menu">
                            <li class="shareToolsItem shareToolsItemFacebook"><a href="javascript:;" data-share="facebook"><i class="icon"></i><span>Facebook</span></a></li><li class="shareToolsItem shareToolsItemEmail"><a href="javascript:;" data-share="email"><i class="icon"></i><span>E-mail</span></a></li><li class="shareToolsItem shareToolsItemTwitter"><a href="javascript:;" data-share="twitter"><i class="icon"></i><span>Twitter</span></a></li><li class="shareToolsItem shareToolsItemLinkedin"><a href="javascript:;" data-share="linkedin"><i class="icon"></i><span>Linkedin</span></a></li><li class="shareToolsItem shareToolsItemGoogle"><a href="javascript:;" data-share="google"><i class="icon"></i><span>Google+</span></a></li><li class="shareToolsItem shareToolsItemPermalink"><a href="javascript:;" data-share="permalink" style="display: block;"><i class="icon"></i><span>Permalink</span></a><input style="display: none;" data-share="permalink" class="selectall" type="text" value="http://nyti.ms/1gOiA44" readonly="readonly"></li><li class="shareToolsItem shareToolsItemReddit"><a href="javascript:;" data-share="reddit"><i class="icon"></i><span>Reddit</span></a></li><li class="shareToolsItem shareToolsItemTumblr"><a href="javascript:;" data-share="tumblr"><i class="icon"></i><span>Tumblr</span></a></li>
                        </ul>
                    </div>
                </div>
                <button type="button" class="modal-close shareToolsDialogBoxClose"><i class="icon"></i><span class="visually-hidden">Close this modal window</span></button>
                <div class="modal-pointer modal-pointer-centered"><div class="modal-pointer-conceal"></div></div>
            </div>-->
            <!--SHARE ICONS-->


        </div>
        <?php
        $c++;
    endforeach;
    ?>
</div>
<div style="height:100px; clear:both; ">&nbsp;</div>
<style>
    .masthead .heading{
        border-bottom: 1px solid #CCCCCC;
        color: #fff;
        font-size: 24px;
        margin-bottom: 10px;
        padding:10px;
        background:#000;
        font-weight: normal;
    }
    .galleryrightwidget .html5gallery-container-0
    { 
        background: none repeat scroll 0 0 #fff !important;
    }
    .meta-info {
        padding: 20px 0;
    }
    .meta-info .share-video{

    }
    .adsbygoogle
    {
        display:none !important;
    }
    .meta-left{
        float:left; width: 70%;
    }
    .meta-right{
        float:right; width:28%;
    }
    .meta-info .share-video span.icon {
        display: inline-block;
        line-height: 0;
        vertical-align: middle;
        font-style: normal;
        background: url('http://www.thedailystar.net/upload/ads/2014/04/28/share-icon.jpg') no-repeat;
        -webkit-transition: all 0.2s ease-in-out;
        -moz-transition: all 0.2s ease-in-out;
        -o-transition: all 0.2s ease-in-out;
        -ms-transition: all 0.2s ease-in-out;
        transition: all 0.2s ease-in-out;
        width: 26px;
        height: 20px;
        padding: 0;
        margin: 0 0px 0 0;
    } 


    ::selection {
        background: #b3d4fc;
        text-shadow: none;
    }
    .meta-info .shoulder{
        font-size: 16px;
        text-transform: uppercase;
        font-weight: normal;
    }
    .content-news-body .headline  { 
        margin-bottom: 15px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 15px;
        margin-top: 10px;
    }
    .content-news-body .headline a {
        font-size: 28px;
        color: #575757; 
        font-weight: normal;
    }
    .video-description p {
        font-size: 14px;
        line-height: 20px;
    } 
    .video-description .byline{
        padding: 5px;
        font-weight: normal;
        color: #9E9B9B;
    }
    .share-text{
        color: #575757;
    }



    /*share icons css*/
 
</style>

<?php
$s_inner_content = ob_get_contents();
ob_end_clean();
if ($b_checked_cache && !isset($_GET['archive'])) {
    $CI->cache->file->save($cache_name, $s_inner_content, 86400);
}
echo $s_inner_content;
?>
