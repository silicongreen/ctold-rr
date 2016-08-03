
<h1>
    Columnists
</h1>
<div class="ym-grid"  >
   <ul class="show_columinst_list">
    <?php foreach ($bylines as $value): ?>
       <li>
           <?php if($value->image): ?>
           <img src="<?php echo base_url() . $value->image; ?>" width="50" />
           <?php endif; ?>  
           <?php $s_link = ""; ?>
           <?php if ( isset($archive) &&  strlen($archive) != "0"  ) : ?>
                <?php $s_link .= "?archive=" . $archive; ?>
           <?php endif; ?>
           <span><a href="<?php echo base_url();?>columnist/<?php echo sanitize($value->title);?><?php echo $s_link;?>"><?=$value->title?></span>
        </li>   
    <?php endforeach; ?>
    </ul> 
</div>     
