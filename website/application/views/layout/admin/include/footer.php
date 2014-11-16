<div class="display_none">						
    <div id="dialog_logout" class="dialog_content narrow" title="Logout">
        <div class="block">
            <div class="section">
                <h1>Thank you</h1>
                <div class="dashed_line"></div>	
                <p>We will now log you out of Daily Star in a 10 seconds...</p>
            </div>
            <div class="button_bar clearfix">
                <button class="dark blue no_margin_bottom link_button" data-link="<?php echo  base_url() ?>admin/login/logout">
                    <div class="ui-icon ui-icon-check"></div>
                    <span>Ok</span>
                </button>
                <button class="light send_right close_dialog">
                    <div class="ui-icon ui-icon-closethick"></div>
                    <span>Cancel</span>
                </button>
            </div>
        </div>
    </div>
</div> 


<div class="display_none">						
    <div id="dialog_news_saved" class="dialog_content narrow" title="Saved">
        <div class="block">
            <div class="section">
                <h1>News</h1>
                <div class="dashed_line"></div>	
                <p>News Successfully Saved </p>
                <p>Press The Ok button for continue</p>
            </div>
            <div class="button_bar clearfix">
                <button class="dark blue no_margin_bottom link_button" data-link="<?php echo  base_url() ?>admin/news/edit/">
                    <div class="ui-icon ui-icon-check"></div>
                    <span>Ok</span>
                </button>
            </div>
        </div>
    </div>
</div>


<div class="display_none">						
    <div id="dialog_delete" class="dialog_content narrow no_dialog_titlebar" title="Delete Confirmation">
        <div class="block">
            <div class="section">
                <h1>Delete File</h1>
                <div class="dashed_line"></div>	
                <p>Please confirm that you want to delete this file.</p>
            </div>
            <div class="button_bar clearfix">
                <button class="delete_confirm dark red no_margin_bottom close_dialog">
                    <div class="ui-icon ui-icon-check"></div>
                    <span>Delete</span>
                </button>
                <button class="light send_right close_dialog">
                    <div class="ui-icon ui-icon-closethick"></div>
                    <span>Cancel</span>
                </button>
            </div>
        </div>
    </div>
</div> 

<div id="loading_overlay">
    <div class="loading_message round_bottom">
        <img src="<?php echo  base_url() ?>images/interface/loading.gif" alt="loading" />
    </div>
</div>

</div>
</body>
</html>