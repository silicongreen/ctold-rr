<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of kcfinder
 *
 * @author ahuffas
 */
class kcfinder {
    //put your code here
    public function load_browser()
    {
        require "kcfinder/core/autoload.php";
        require "kcfinder/core/uploader.php";
        require "kcfinder/core/browser.php";
        require "kcfinder/core/types/type_img.php";
        require "kcfinder/core/types/type_mime.php";
        require "kcfinder/lib/class_gd.php";
        require "kcfinder/lib/class_input.php";
        require "kcfinder/lib/class_zipFolder.php";
        require "kcfinder/lib/helper_dir.php";
        require "kcfinder/lib/helper_file.php";
        require "kcfinder/lib/helper_httpCache.php";
        require "kcfinder/lib/helper_path.php";
        require "kcfinder/lib/helper_text.php";
        $browser = new browser();
        $browser->action();
    }
}

?>
