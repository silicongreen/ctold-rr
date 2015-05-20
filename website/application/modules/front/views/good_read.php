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

$widget = new Widget;
?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<style>
    .good-read-box{
        width: 100%;
        cursor: auto;
        padding: 25px;
        margin-left: 30px;
    }
    .good-read-box2{        
        right: 115px;
        height: 200px !important;
        width: 500px;
        background: #F7F7F7;
        border: 1px solid #ccc;
        position: absolute;
        cursor: auto;
        padding: 25px;
        display: none;
    }
    .good-read-box-scroll{
        overflow-x: auto;
    }
    .good-read-box .folder{
        height: 150px;
        width: 155px;
        padding: 10px;
        border: 1px solid #ccc;
        float: left;
        margin-left: 0px;
        margin-bottom: 7px;
        text-align: center;
        font-size: 20px;
        color:#969799;
        border-bottom: 3px solid #ccc;
        border-right: 2px solid #ccc;
        cursor: pointer;
        background: #fff url(<?php echo base_url('styles/layouts/tdsfront/images/social/black-folder.png'); ?>) no-repeat 50px 38px;
        background-size: 50px 50px;
        padding-top: 95px;
    }
    .good-read-box .folder-add{
        height: 150px;
        width: 155px;
        padding: 10px;
        border: 1px solid #ccc;
        float: left;
        margin-left: 5px;
        margin-bottom: 7px;
        text-align: center;
        font-size: 20px;
        color:#969799;
        border-bottom: 3px solid #ccc;
        border-right: 2px solid #ccc;
        cursor: pointer;
        background: #fff url(<?php echo base_url('styles/layouts/tdsfront/images/social/black-folder.png'); ?>) no-repeat 50px 38px;
        background-size: 50px 50px;
        padding-top: 95px;
        position: relative;
    }
    .good-read-box .selected-folder{
        height: 150px;
        width: 155px;
        padding: 10px;
        border: 1px solid #ccc;
        float: left;
        margin-left: 0px;
        margin-bottom: 7px;
        text-align: center;
        font-size: 20px;
        color:#969799;
        border-bottom: 3px solid #ccc;
        border-right: 2px solid #ccc;
        cursor: pointer;
        background: #FC3E30 url(<?php echo base_url('styles/layouts/tdsfront/images/social/white-folder.png'); ?>)no-repeat 50px 38px;
        background-size: 50px 50px;
        padding-top: 90px;
    }
    .good-read-box .selected-folder span{
        color: #fff;
    }
    .good-read-box .folder:hover, .folder-add:hover {
        background-image: url(<?php echo base_url('styles/layouts/tdsfront/images/social/black-folder.png'); ?>);
        background-position: 50px 38px;
        background-repeat: no-repeat;
        background-color: #3d3d3b;
        background-size: 50px 50px;
        transition: all 0.25s ease-out 0s;
        -webkit-transition: all 0.25s ease-out 0s;
        -ms-transition: all 0.25s ease-out 0s;
        -moz-transition: all 0.25s ease-out 0s;
        -o-transition: all 0.25s ease-out 0s;
    }
    .good-read-box .folder:hover span, .folder-add:hover span{
        color: #93989c;
    }
    .good-read-box .folder a {
        background: transparent none repeat scroll 0 0;
        border: 1px solid #93989c;
        border-radius: 50%;
        color: #93989c;
        font-size: 10px;
        font-weight: bold;
        height: 22px;
        line-height: 17px;
        opacity: 0;
        position: absolute;
        right: 10px;
        text-align: center;
        text-decoration: none;
        top: 10px;
        visibility: hidden;
        width: 20px;
        transition: all 0.25s ease-out 0s;
        -webkit-transition: all 0.25s ease-out 0s;
        -ms-transition: all 0.25s ease-out 0s;
        -moz-transition: all 0.25s ease-out 0s;
        -o-transition: all 0.25s ease-out 0s;
    }
    .good-read-box .folder-add a {
        background: transparent none repeat scroll 0 0;
        border: 1px solid #ffffff;
        border-radius: 50%;
        color: #ffffff;
        height: 18px;
        position: absolute;
        right: 69px;
        text-align: center;
        text-decoration: none;
        top: 64px;
        width: 18px;
    }
    .good-read-box .folder-add a span {
        color: #ffffff;
        font-size: 20px;
        margin-left: -5px;
        margin-top: -7px;
        position: absolute;
    }
    .good-read-box .folder:hover a {
        opacity: 1;
        visibility: visible;
    }
    input.folder_name{
        height: 45px;
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ccc;
    }
    div.done-folder{
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ccc;
        width: 80px;
        height: 35px;
        padding: 3px 17px;
        background: #6EBE41;
        font-size: 18px;
        color: #fff;
        cursor: pointer;
        float: left;
    }
    div.remove-folder{
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ccc;
        width: 105px;
        height: 35px;
        padding: 3px 17px;
        background: #6F7173;
        font-size: 18px;
        color: #fff;
        cursor: pointer;
        float: left;
        margin-left: 10px;
    }
    .good-read-box-new-folder{
        height: 100px !important;
    }
    .good-read-box-action{
        position: absolute;
        bottom: 0;
        height: 100px !important;

    }
    /*************************************
     * generic styling for ALS elements
     ************************************/

    .als-container {
        position: relative;
        width: 100%;
        margin: 0px auto;
        z-index: 1000;
    }

    .als-viewport {
        position: relative;
        overflow: hidden;
        margin: 0px auto;
        margin-left:7px;
    }

    .als-wrapper {
        position: relative;
        list-style: none;
        margin: 0 0 10px 0px;
    }

    .als-item {
        position: relative;
        display: block;
        text-align: center;
        cursor: pointer;
        float: left;
    }

    .als-prev, .als-next {
        position: absolute;
        cursor: pointer;
        clear: both;
    }
    #demo2 {
        margin: 40px auto;
    }

    #demo2 .als-item {
        margin: 0px 5px;
        padding: 0px 0px;
        min-height: 120px;
        min-width: 100px;
        text-align: center;
    }

    #demo2 .als-item img {
        display: block;
        margin: 0 auto;
        vertical-align: middle;
    }

    #demo2 .als-prev, #demo2 .als-next {
        top: 26px;
    }

    #demo2 .als-prev {
        left: 200px;
    }

    #demo2 .als-next {
        right: 30px;
        width:60px;
        height:72%;
        border:1px solid #ccc;
        background: url("./styles/layouts/tdsfront/images/nextarrow.png") no-repeat 10px 55px;
    }




</style>
<script type="text/javascript" src="<?php echo base_url('scripts/jquery/jquery.als-1.6.min.js'); ?>"></script>
<div class="container" style="width: 77%;min-height:250px;">

    <div style="padding: 0px 22px 0 35px;" class="sports-inner-news yesPrint">

        <div style="float:left;">
            <a href="<?php echo base_url('good-read'); ?>">
                <h1 style="color:#93989C;" class="title noPrint f2">
                    Good Read
                </h1>
            </a>
        </div>

    </div>

    <div class="als-container good-read-box" id="demo2">
        <div class="good-read-box-scroll">

            <div <?php if ($i_user_folder_count > 3): ?>class="als-viewport"<?php endif; ?> style="float:left; overflow: hidden; width: auto;">
                <ul class="als-wrapper">
                    <?php
                    if ($ar_user_folder)
                        $j = 0;
                    foreach ($ar_user_folder as $folder) :
                        ?>   
                        <?php if ($folder->title == trim(strip_tags($folder->title))): ?>
                            <li class="als-item" id="folderli_<?php echo $folder->id; ?>"  <?php if ($selected_folder_id == $folder->id): ?>style="left:-505px;"<?php endif; ?>>            
                                <div class="folder folder_div  <?php echo ($selected_folder_id == $folder->id) ? 'selected-folder' : ''; ?>" id="folder_<?php echo $folder->id; ?>" data-link="<?php echo base_url('good-read/' . $folder->title); ?>" >
                                    <span class="title-span-folder f2"><?php echo ucfirst(strip_tags($folder->title)); ?></span>
                                    <?php
                                    ?>
                                    <?php if (strtolower(strip_tags($folder->title)) !== 'unread'): ?>
                                        <a href="javascript:void(0);" id="delete_folder_<?php echo $folder->id; ?>" class="f2 delete_folder">x</a>
                                    <?php endif; ?>      
                                </div>            
                            </li>
                            <?php
                            $j++;
                        endif;
                        ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="folder-add add-folder add">
                <span class="title-span-folder f2">Add Folder</span>
                <a href="javascript:void(0);" class="f2"><span>+</span></a>
            </div>

            <?php if ($i_user_folder_count > 3): ?>
                            <span class="als-next" style='background: url("../styles/layouts/tdsfront/images/nextarrow.png") no-repeat 10px 55px white !important;height: 149px; float:left; clear:none; position: static; margin-left:10px;'><!-- <img src="<?php echo base_url('styles/layouts/tdsfront/images/nextarrow.png'); ?>" alt="next" title="next" />--></span>
            <?php endif; ?>
        </div>

        <div class="good-read-box2">
            <div class="good-read-box-new-folder">
                <label class="label-folder">Folder Name</label>
                <input type="text" name="folder_name" id="folder_name" value="" placeholder="Insert your folder name " class="folder_name" />
            </div> 
            <div class="good-read-box-action">
                <div class="done-folder">Add</div>
                <div class="remove-folder">Cancel</div>
            </div>
        </div>
    </div>


    <?php if ($totalpost > 0): ?>
        <?php //if( $selected_folder_name!="Unread" && $selected_folder_name!="unread" ): ?>
        <?php $widget->run('postdata', "good-read", $selected_folder_id, 'good_read'); ?>
        <?php //else: ?>
        <?php //$widget->run('postdata', "good-read", $selected_folder_id, 'good_read_unread');  ?>
        <?php //endif;  ?>
    <?php else: ?>
        <div class="good-read-box" id="demo2">  <h1 class="f2">No News Found</h1></div>
    <?php endif; ?>
</div>



<script type="text/javascript">
//    $('#wrapper1').cycle({
//    fx:     'scrollHorz',
//    next:   '#next',
//    timeout: 0
//    });

    $("#demo2").als({
        visible_items: 3,
        scrolling_items: 1,
        orientation: "horizontal",
        circular: "yes",
        autoscroll: "no"
    });
    var preventLink = false;
    $(document).ready(function () {
        $(document).on("click", ".delete_folder", function (event) {
            preventLink = true;
            if (confirm("Do you realy want to delete this folder"))
            {

                var folder = this.id;
                $.get($('#base_url').val() + 'front/ajax/delete_user_folder', {name: folder}, function (data) {
                    if (data !== 0)
                    {

                        window.location = $('#base_url').val() + "good-read"

                    }
                    else
                    {
                        alert("folder delete unsuccessful");
                    }


                });

            }
            else
            {
                preventLink = false;
            }
        });
        $(document).on("click", ".folder_div", function (event) {
            var linkwindows = $(this).attr("data-link");
            if (preventLink)
            {
                event.preventDafualt();
                return false;
            }
            else
            {
                window.location = linkwindows;
            }
        });

    });
</script>