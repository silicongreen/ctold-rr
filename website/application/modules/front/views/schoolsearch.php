<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<?php

?>
<div class="container" style="width: 77%;min-height:250px;">
	<div style="">
		<ul style="margin: 30px 20px 20px 35px;">
                        <li>
                                <div class="srch_page_title">
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
				<div class="srch_item_container">
					<div class="srch_item_pic">
                                            <img src="<?php echo base_url($row['logo']); ?>" width="220">
                                        </div>
                                        <div class="srch_item_info">
						<p class="f2 s1"><a style="color:#60cb97;" href="<?php echo base_url() . 'schools/' . sanitize($row['name']); ?>"><?php echo $row['name']; ?></a></p>                            
                                                <p class="f5 s2" style="color:#9CD64E;"><?php echo $row['medium']; ?><?php echo " ".$row['level']; ?></p>                            
                                                <p class="f5 s3" style="color:#000;"><?php echo $row['district']; ?><?php echo " ".$row['location']; ?></p>
                                                <p class="btn_item_visit">
                                                    <a style="color:#60cb97;" href="<?php echo base_url() . 'schools/' . sanitize($row['name']); ?>">
                                                        <button class="red" type="button" style="width:20%;">
                                                            <span class="clearfix f2">
                                                                Visit
                                                            </span>
                                                        </button>
                                                    </a>
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
    .srch_page_title
    {
        background: #fff;padding: 30px 20px;font-size:35px;font-family:'Bree Serif';margin-top:-40px;
    }
    .srch_item_container
    {
        background:#FFF;padding:20px;height:200px;overflow:hidden;
    }
    .srch_item_pic
    {
        float:left;width:25%;height:160px;overflow: hidden;
    }
    .srch_item_info
    {
        float:left;width:45%;
    }
    .srch_item_info .s1
    {
        font-size:22px;
    }
    .srch_item_info .s2
    {
        font-size:16px;
    }
    .srch_item_info .s3
    {
        font-size:14px;
    }
    .srch_item_info .btn_item_visit
    {
        display: block;
    }
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
@media all and (min-width: 200px) and (max-width: 314px) {
    .srch_page_title
    {
        background: #fff;padding: 15px 15px;font-size:15px;font-family:'Bree Serif';margin-top:0px;
    }
    .srch_item_container
    {
        background:#FFF;padding:20px;height:auto;overflow:hidden;
    }
    .srch_item_pic
    {
        float:none;width:100%;height:160px;overflow: hidden;
    }
    .srch_item_info
    {
        float:none;width:100%;
    }
    .srch_item_info .s1
    {
        font-size:15px;
    }
    .srch_item_info .s2
    {
        font-size:12px;
    }
    .srch_item_info .s3
    {
        font-size:12px;
    }
    .srch_item_info .btn_item_visit
    {
        display: none;
    }
    .join-wrapper{
        float:none;width:100%;
        margin-top: 30px;
        text-align: right;
    }
}
</style>
