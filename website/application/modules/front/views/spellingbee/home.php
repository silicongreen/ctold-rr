<!--<div class="flying_bee"></div>-->
<div class="home_box">    
    <div style="width: 100%;">
        <div style="width: 41%;float:left;">
            <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/BG.png'); ?>" style="width:100%;">
        </div>
        <div style="width: 58%;float:left;">
            <div class="f5" style="float:left;width: 100%;font-size: 20px;">
                <center><img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/join_msg.png'); ?>" style="width:75%;"></center>
            </div>
            <div style="clear: both;float:left;width: 100%;margin-top:10px;">
                <nav>
                <a style="float: left;width:110px;" href="<?php echo base_url('leaderboard'); ?>">
                    <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play.png'); ?>" style="width:100%;">
                </a>
                <?php if( free_user_logged_in() ) { ?>
                    <a  href="#" id="join_spellbee_reg" class="f2 button-filter1">Join</a>
                <?php } else { ?>
                    <a  href="#" class="f2 button-filter1 login-user">Join</a>
                <?php } ?>
                <a  href="<?php echo base_url('leaderboard'); ?>" class="f2 button-filter2">Leaderboard</a>
                <a  href="https://www.facebook.com/spellbangladesh" target="_blank" class="f2 button-filter3">Facebook Like</a>
                </nav>
            </div>
            <div class="f5" style="float:left;width: 100%;font-size: 13px;">
                Spelling Bee is back with its 4th season in Bangladesh! Join the Spelling Bee Competition and join the top spellers of the country to fight for the trophy and the prestigious title of Spelling Bee Champion.
            </div>
        </div>
    </div>
</div>



<style>
.flying_bee {
  background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/bugs_27.gif'); ?>");
  background-position: left top;
  background-repeat: no-repeat;
  background-size: 80% auto;
  height: 40px;
  left: 150px;
  position: absolute;
  top: 132px;
  width: 60px;
}

.home_box {  
  display: block;
  padding: 0px;
  height:410px;
}

.button-filter1 {
  background-color: #F4A91C;
  border: 1px solid #b3b3b3;  
  color: #fff;
  cursor: pointer;
  display: block;
  float: left;
  font-size: 17px;
  font-weight: normal;
  padding: 10px 17px;
  margin-top:39px;
  text-decoration: none;
  transition: all 0.25s ease-in 0s;
}

.button-filter1:hover, .button-filter1:active {
  background-color: #FF8F35;
  color: #ffffff;
  transition: all 0.25s linear 0s;
}
.button-filter2 {
  background-color: #63BF8E;
  border: 1px solid #b3b3b3;  
  color: #fff;
  cursor: pointer;
  display: block;
  float: left;
  font-size: 17px;
  font-weight: normal;
  padding: 10px 17px;
  margin-top:39px;
  text-decoration: none;
  transition: all 0.25s ease-in 0s;
}

.button-filter2:hover, .button-filter2:active {
  background-color: #61A581;
  color: #ffffff;
  transition: all 0.25s linear 0s;
}
.button-filter3 {
  background-color: #2E7EB1;
  border: 1px solid #b3b3b3;  
  color: #fff;
  cursor: pointer;
  display: block;
  float: left;
  font-size: 17px;
  font-weight: normal;
  padding: 10px 17px;
  margin-top:39px;
  text-decoration: none;
  transition: all 0.25s ease-in 0s;
}

.button-filter3:hover, .button-filter3:active {
  background-color: #0F547F;
  color: #ffffff;
  transition: all 0.25s linear 0s;
}

</style>