<style>
    .good-read-box{
        background: none repeat scroll 0 0 #f7f7f7;
        border: 1px solid #ccc;
        cursor: auto;
        display: none;
        height: auto !important;
        padding: 10px;
        position: absolute;
        right: 0;
        top: 101px;
        width: 435px;
        z-index: 10000;
    }
    .good-read-box-scroll{
        height: 170px !important;
        overflow-x: auto;
        margin-bottom: 50px;
    }
    .good-read-box .folder, .folder-add{
        height: 80px;
        width: 85px;
        padding: 10px;
        border: 1px solid #ccc;
        float: left;
        margin-left: 10px;
        margin-bottom: 7px;
        text-align: center;
        font-size: 12px;
        border-bottom: 3px solid #ccc;
        border-right: 2px solid #ccc;
        cursor: pointer;
        background: #fff url(styles/layouts/tdsfront/images/social/black-folder.png) no-repeat 23px 10px;
        padding-top: 45px;
    }
    .good-read-box .selected-folder{
        height: 80px;
        width: 85px;
        padding: 10px;
        border: 1px solid #ccc;
        float: left;
        margin-left: 10px;
        margin-bottom: 7px;
        text-align: center;
        font-size: 12px;
        border-bottom: 3px solid #ccc;
        border-right: 2px solid #ccc;
        cursor: pointer;
        background: #FC3E30 url(styles/layouts/tdsfront/images/social/white-folder.png) no-repeat 23px 10px;
        padding-top: 45px;
    }
    .good-read-box .selected-folder span{
       color: #fff;
    }
    .good-read-box .folder:hover, .folder-add:hover{
        background: #FC3E30 url(styles/layouts/tdsfront/images/social/white-folder.png) no-repeat 23px 10px;
    }
    .good-read-box .folder:hover span, .folder-add:hover span{
        color: #fff;
    }
    input.folder_name{
        height: 45px;
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ccc;
    }
    div.done-folder{
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ccc;
        width: 80px;
        height: 35px;
        padding: 3px 17px;
        background: #6EBE41;
        font-size: 18px;
        color: #fff;
        cursor: pointer;
        float: left;
    }
    div.remove-folder{
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ccc;
        width: 105px;
        height: 35px;
        padding: 3px 17px;
        background: #6F7173;
        font-size: 18px;
        color: #fff;
        cursor: pointer;
        float: left;
        margin-left: 10px;
    }
    .good-read-box-new-folder{
        display: none;
        height: 100px !important;
    }
    .good-read-box-action{
        bottom: 20px;
        left: 20px;
        position: absolute;
    }
    .close-btn {
        border: 1px solid #aaa;
        border-radius: 100px;
        color: #aaaaaa;
        cursor: pointer;
        -moz-border-radius: 100px;
        -webkit-border-radius: 100px;
        -ms-border-radius: 100px;
        -o-border-radius: 100px;
        float: right;
        font-size: 15px;
        font-weight: bold;
        padding-left: 0;
        padding-right: 0;
        padding-top: 0;
        text-align: center;
        width: 25px;
    }
    .close-btn:hover {
        background-color: #93989C;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
        color: #ffffff;
    }
    .title-folder {
        clear: both;
        letter-spacing: 0;
        padding-left: 10px;
        text-align: left;
    }
</style>

<div class="good-read-box">
    
    <div class="col-lg-12 close-btn">X</div>
    
    <h4 class="title-folder">Your <span style="color: #FC3E30;" class="highlighted-title">Good Read </span>Manager</h4>
    <br class="br-folder" />
    <div class="good-read-box-scroll">
        <?php if ( $ar_user_folder ) foreach ($ar_user_folder as $folder) : ?>
        <div class="folder" id="folder_<?php echo $folder->id; ?>">
            <span class="title-span-folder"><?php echo $folder->title; ?></span>
        </div>
        <?php endforeach; ?>
        <div class="folder add">
            <span class="title-span-folder">Add Folder</span>
        </div>
    </div>
    <div class="good-read-box-new-folder">
        <label class="label-folder">Folder Name</label>
        <input type="text" name="folder_name" id="folder_name" value="" placeholder="Insert your folder name " class="folder_name" />
    </div>  
    <div class="good-read-box-action">
        <div class="done-folder">Done</div>
        <div class="remove-folder">Remove</div>
    </div>
    <div class="clear-folder" style="clear: both; height: 8px;"></div>
</div>