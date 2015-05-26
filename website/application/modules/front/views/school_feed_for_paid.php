<script type="text/javascript" src="<?php echo base_url('js/top-main.js'); ?>"></script> 
<input type="hidden" name="cookie_check" id="cookie_check" value="false">
<?php
$widget = new Widget;
if($school_id>0)
{
    $widget->run('postdataforpaid', "school", $school_id, 'school');
}
else
{
    $widget->run('postdataforpaid', "index", $s_category_ids, 'index'); 
}    


?>
<script type="text/javascript" src="<?php echo base_url('js/main-bottom.js'); ?>"></script>
<script type="text/javascript">

$(window).load(function ()
{
    UpdateDimensions();
});
 
function UpdateDimensions()
{
try
{
var height = $("#main").height() + 130;
var width = $("body").outerWidth() + 100;
var ReferalUrl = "" 
if (window.postMessage)
    parent.postMessage(height + "," + width, '*');
}
catch (c)
{
alert(c.message);
}
}
</script>

<style>
body
{
    background:#e7e7e7 !important;
}
a.summary_link:hover {
            color: #fb3c2d;
}
a.summary_link {
            color: #666;
}
</style>