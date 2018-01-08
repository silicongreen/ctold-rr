<?php $i=0; if ( $images ) foreach( $images as $image) :?>
<div class="file from_menu" id="imagediv_<?php echo $image['id'];?>">
      <img id="close_<?php echo $image['id'];?>" src='<?php echo base_url(); ?>styles/layouts/tdsfront/images/close.png' class="close" />
      <div style="background-image:url('<?php echo base_url() . "ckeditor/kcfinder/browse.php?type=image&lng=en&act=thumb&file=" . $image['filename'] . "&dir=" . urlencode($image['dir']);?>')" class="thumb"></div>
      <div class="name"><?php echo $image['filename']; ?></div>
</div>
<?php $i++; endforeach; ?>