<?php
    
//    echo "<pre>";
//    print_r($whattowatch);
    ?>

<input type="hidden" id="size_of_what_to_watch" value="<?if(count($whattowatch) >0 ){ echo count($whattowatch); }else {echo 0;}?>" />
<?php if(count($whattowatch) >0 ):?>
<div class="ym-grid bottom-slider">    
    <div style="float:left;width:100px;color:#fff;margin-left:60px;line-height:19px;font-family:serif;font-size: 18px;margin-top:65px; ">WHAT TO <h1 style="font-size:23px;">WATCH</h1>
    </div>
    <ul class="bxslider">
     <?php foreach($whattowatch as $row):?>
	<li>            
            <div style="width: 205px; height: 158px;border-left: 2px solid #494949;padding-left: 20px;">
                <p class="channel"><?php echo $row->name;?></p>
                <div class="details"><?php echo $row->program_details;?></div>
            </div>            
        </li>
     <?php endforeach;?>  
    </ul>
</div>
<?php endif;?>


<style type="text/css">
p.channel{color:#57A943;font-weight:bold;font-size:17px;} 
div.details p{color:#FFFFFF;font-size:12px;} 
.bx-wrapper .bx-viewport{width:700px;}
</style>