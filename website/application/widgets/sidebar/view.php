<div id="sidebar" class="sidebar pjax_links">

    <a class="logo"><span>Admin</span></a>
    <?php
    $sessionData = $this->session->userdata("admin");
    ?>
    <div class="user_box dark_box clearfix">
        <img src="<?php echo  base_url() ?>images/interface/profile.jpg" width="55" alt="Profile Pic" />
        <h2>Administrator</h2>
        <h3><a class="text_shadow" href="#"><?php echo  $sessionData['name']; ?><a></h3>
        <ul>
            <li><a href="<?php echo  base_url() ?>admin/login/logout" class="dialog_button" data-dialog="dialog_logout">Logout</a></li>
        </ul>
    </div><!-- #user_box -->
	<div class="user_box dark_box clearfix">
		<a href="<?php echo  base_url() ?>" style="color:#fff;font-weight:bold;" target="_blank">Go To Site</a>
	</div>
    <div class="cog">+</div>
        <ul class="side_accordion" id="nav_side"> <!-- add class 'open_multiple' to change to from accordion to toggles -->
            
            <?php foreach ($menu as $key_group => $group): ?>
                <?php if (group_check($group)) : ?>
                    <li <?php if (check_current($group)) : ?>class="open"<?php endif; ?>><a href="#"><img src="<?php echo  base_url() ?>images/icons/small/grey/<?php echo  $key_group ?>.png"/><?php echo  $key_group ?></a>
                        <ul class="drawer<?php if (check_current($group)) : ?> current active<?php endif; ?>">
                            <?php foreach ($group as $key => $value): ?>
                                <?php
                                $controller_to_check = $value[0];
                                if (isset($value[1]))
                                {
                                    $function_to_check = $value[1];
                                }
                                else
                                {
                                    $function_to_check = "";
                                }
                                ?>

                                <?php if (access_check($controller_to_check, $function_to_check)) : ?>
                                    <li ><a href="<?php echo  base_url() ?>admin/<?php echo  implode("/", $value) ?>"><?php echo  $key ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>     
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
          

        </ul>
</div><!-- #sidebar -->