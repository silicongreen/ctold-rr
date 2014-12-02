<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<?php

?>
<div class="container" style="width: 77%;min-height:250px;">
	<div style="">
		<ul style="margin: 30px 20px 20px 35px;">
			<li>
                                <div style="background: #fff;padding: 30px 20px;font-size:35px;font-family:'Bree Serif';margin-top:-40px;">
                                    <span style="color:#dadada">Searched</span><span style="color:#60cb97">&nbsp;SCHOOLS</span>
                                </div>
                        </li>
                        <?php foreach ( $schooldata as $row ) :?>
                        <?php
                        if(isset($row['picture']) && $row['picture'])
                        {
                            $row['logo'] = $row['picture'];
                        }   
                        ?>
			<li style="list-style:none;">
				<div style="background:#FFF;padding:20px;height:200px;overflow:hidden;">
					<div style="float:left;width:25%;height:160px;overflow: hidden;">
                                            <img src="<?php echo base_url($row['logo']); ?>" width="220">
                                        </div>
					<div style="float:left;width:45%;">
						<p class="f2" style="font-size:22px;"><a style="color:#60cb97;" href="<?php echo base_url() . 'schools/' . sanitize($row['name']); ?>"><?php echo $row['name']; ?></a></p>                            
                                                <p class="f5" style="font-size:16px;color:#9CD64E;"><?php echo $row['medium']; ?><?php echo " ".$row['level']; ?></p>                            
                                                <p class="f5" style="font-size:14px;color:#000;"><?php echo $row['district']; ?><?php echo " ".$row['location']; ?></p>
                                                <p>
                                                    <button class="red" type="button" style="width:20%;">
                                                        <span class="clearfix f2">
                                                            Visit
                                                        </span>
                                                    </button>
                                                </p>
					</div>
					<div class="join-wrapper">
                            
                                            <?php if ( !isset($user_school_ids) || empty($user_school_ids) || !in_array($row['id'], $user_school_ids)) { ?>
                                            <button id="<?php echo $row['id']; ?>" data="school_join" class="red <?php echo (free_user_logged_in()) ? 'btn_user_join_school' : 'before-login-user'; ?>" type="button">
                                                <span class="clearfix f2">
                                                    Join In
                                                </span>
                                            </button>
                                            <?php } ?>

                                        </div>					
				</div>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<style>
    .join-wrapper{
        float: left;
        width: 30%;
        margin-top: 30px;
        text-align: right;
    }
    .red{
        background-color: #BBBBBB;
        border: none;
        line-height: 15px;
        color: #fff;
        height: 40px;
        width: 50%;
        box-shadow: 0 3px 2px 0 #bbb;
        -o-box-shadow: 0 3px 2px 0 #bbb;
        -ms-box-shadow: 0 3px 2px 0 #bbb;
        -moz-box-shadow: 0 3px 2px 0 #bbb;
        -webkit-box-shadow: 0 3px 2px 0 #bbb;
    }
    .red:hover{
        background-color: #60cb97;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
</style>