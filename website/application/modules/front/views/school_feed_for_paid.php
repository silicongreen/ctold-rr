<script type="text/javascript" src="<?php echo base_url('js/top-main.js'); ?>"></script> 
<?php
$widget = new Widget;
$widget->run('postdata', "index", $s_category_ids, 'index');
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
</style>