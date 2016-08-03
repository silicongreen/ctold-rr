<?php INCLUDE APPPATH . "libraries/kcfinder/tpl/tpl_css.php" ?>
<?php INCLUDE APPPATH . "libraries/kcfinder/tpl/tpl_javascript.php" ?>
<div id="gallery_list" style="display: none;">
    <div class="box about" style="max-width: 800px;">
        <iframe height="430" width="700" frameborder="0" scrolling="no"  src="<?php echo base_url();?>admin/gallery/gallery_list"></iframe>
    </div>
</div>

<div id="video_upload" style="display: none;">
    <div class="box about" style="max-width: 800px;">
        <div class="head">Upload Video</div>
        <iframe id="add_video_frame" height="400" width="700" frameborder="0" scrolling="no" src=""></iframe>
    </div>
</div>
    
<div id="add_caption" style="display: none;">
    <div class="box about" style="max-width: 800px;">
        <div class="head">Add Caption & Source</div>
        <iframe id="add_caption_frame" height="380" width="700" frameborder="0" scrolling="no" src=""></iframe>
    </div>
</div>

<div id="assign_to_menu" style="display: none;">
    <div class="box about" style="max-width: 1000px;">
        <iframe id="add_assign_frame" height="500" width="1000" frameborder="0" scrolling="no" src="<?php echo base_url();?>admin/gallery/assign_to_menu"></iframe>
    </div>
</div>

<div id="assign_to_menu_single" style="display: none;">
    <div class="box about" style="max-width: 340px;">
        <iframe id="add_assign_single_frame" height="300" width="340" frameborder="0" scrolling="no" src="<?php echo base_url();?>admin/gallery/assign_to_menu_single"></iframe>
    </div>
</div>
    
<input type="hidden" name="from_gallery" id="from_gallery" value="1" />
<input type="hidden" name="base_url_ck" id="base_url_ck" value="<?php echo base_url();?>/ckeditor/kcfinder/" />
<input type="hidden" name="base_url_ci" id="base_url_ci" value="<?php echo base_url();?>" />
<div id="resizer"></div>
<div id="shadow"></div>
<div id="dialog"></div>
<div id="alert"></div>
<div id="clipboard"></div>
<div id="all">
<div id="left">
    <div id="folders"></div>
</div>
<div id="right">
    <div id="toolbar">
        <div>
            <a href="kcact:upload"><?php echo $this->label("Upload") ?></a>
            <a href="kcact:gallery"><?php echo $this->label("Gallery") ?></a>
            <a href="kcact:assign"><?php echo $this->label("Assign to Menu") ?></a>
            <a href="kcact:refresh"><?php echo $this->label("Refresh") ?></a>
            <a href="kcact:settings"><?php echo $this->label("Settings") ?></a>
            <!--a href="kcact:maximize"><?php //echo $this->label("Maximize") ?></a-->
            <a href="kcact:about"><?php echo $this->label("About") ?></a>
            <div id="search" class="pull-right" style="width: 311px; background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                <label for="srch">Search</label>
                <input type="text" name="srch" id="srch" value="" style="float: left; width: 264px;"  />
                <img src="<?php echo base_url();?>images/icons/search-button.png" id="srch-btn" name="srch-btn" height="25" style="float: left; margin: -1px; cursor: pointer;" />
            </div>
            <div id="reportrange" class="pull-right" style="clear:both; background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                  <i class="glyphicon glyphicon-calendar icon-calendar icon-large"></i>
                  <span id="range_data"></span> <b class="caret"></b>
            </div>
        <div id="loading"></div>
        </div>
    </div>
    <div id="settings">

    <div>
    <fieldset>
    <legend><?php echo $this->label("View:") ?></legend>
        <table summary="view" id="view"><tr>
        <th><input id="viewThumbs" type="radio" name="view" value="thumbs" /></th>
        <td><label for="viewThumbs">&nbsp;<?php echo $this->label("Thumbnails") ?></label> &nbsp;</td>
        <th><input id="viewList" type="radio" name="view" value="list" /></th>
        <td><label for="viewList">&nbsp;<?php echo $this->label("List") ?></label></td>
        </tr></table>
    </fieldset>
    </div>

    <div>
    <fieldset>
    <legend><?php echo $this->label("Show:") ?></legend>
        <table summary="show" id="show"><tr>
        <th><input id="showName" type="checkbox" name="name" /></th>
        <td><label for="showName">&nbsp;<?php echo $this->label("Name") ?></label> &nbsp;</td>
        <th><input id="showSize" type="checkbox" name="size" /></th>
        <td><label for="showSize">&nbsp;<?php echo $this->label("Size") ?></label> &nbsp;</td>
        <th><input id="showTime" type="checkbox" name="time" /></th>
        <td><label for="showTime">&nbsp;<?php echo $this->label("Date") ?></label></td>
        </tr></table>
    </fieldset>
    </div>

    <div>
    <fieldset>
    <legend><?php echo $this->label("Order by:") ?></legend>
        <table summary="order" id="order"><tr>
        <th><input id="sortName" type="radio" name="sort" value="name" /></th>
        <td><label for="sortName">&nbsp;<?php echo $this->label("Name") ?></label> &nbsp;</td>
        <th><input id="sortType" type="radio" name="sort" value="type" /></th>
        <td><label for="sortType">&nbsp;<?php echo $this->label("Type") ?></label> &nbsp;</td>
        <th><input id="sortSize" type="radio" name="sort" value="size" /></th>
        <td><label for="sortSize">&nbsp;<?php echo $this->label("Size") ?></label> &nbsp;</td>
        <th><input id="sortTime" type="radio" name="sort" value="date" /></th>
        <td><label for="sortTime">&nbsp;<?php echo $this->label("Date") ?></label> &nbsp;</td>
        <th><input id="sortOrder" type="checkbox" name="desc" /></th>
        <td><label for="sortOrder">&nbsp;<?php echo $this->label("Descending") ?></label></td>
        </tr></table>
    </fieldset>
    </div>

    </div>
    <div id="files">
        <div id="content"></div>
    </div>
</div>
<div id="status"><span id="fileinfo">&nbsp;</span></div>
</div>
