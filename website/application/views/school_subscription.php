<div id="page" class="page" >
    <div class="item subscription" id="pricing_table2" style="margin-top:120px;">

        <div class="container" >

            <div class="row" style="margin-top:30px;">
				<div class="col-md-12">
					<?php 
						$i = 1;$letter = "A";
						foreach($subscription as $key => $value):
					?>
					<div class="col-md-3" style="padding:0px;">
						<div class="row subscription_box"><div class="col-md-12" style="padding:0px;">
							<div class="price_sub priceBG<?php echo $i;?>">
								<span class="roundBG<?php echo $i;?> round_box"><?php echo $letter;?></span>
								<span class="currency f2">$</span>
								<h2 class="f2"><?php echo $value['price'];?>/</h2>
							</div>
							<div class="details_sub detailsBG<?php echo $i;?>">
								<p><?php echo $value['student'];?> Students</p>
								<p>Pay <?php if($value['type_purchase'] == "Year"){echo "Annually";}?> </p>								
							</div>
						
							<div class="action_sub">
								<a href="<?php echo base_url()?>createschool/userregister/paid?type=<?php echo $key;?>" class="btn btn-basic actionBG<?php echo $i;?> col-md-12"><b>Buy Now</b></a>
							</div>
							<div class="border_sub borderBG<?php echo $i;?>"></div>
							
						</div></div>
					</div>
					<?php if($i%4==0): ?>
				</div>				
            </div><!-- /.row -->
			<div class="row" style="margin-top:60px;">
				<div class="col-md-12">
					<?php endif;?>
					<?php $i++;$letter++; endforeach ?>
					
				</div>				
            </div><!-- /.row -->
			
        </div><!-- /.container -->

    </div><!-- /.item -->   
</div>