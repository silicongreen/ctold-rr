<?php

/** This file is part of KCFinder project
  *
  *      @desc Browser actions class
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

class browser extends uploader {
    protected $action;
    protected $thumbsDir;
    protected $thumbsTypeDir;
    protected $current_dir = "image";
    protected $obj_con;
    public $post_dir;

    public function __construct() {
        parent::__construct();
        
        if ( isset($_POST['dir']) )
        {
            $this->post_dir = $_POST['dir']; 
        }
        
        if (isset($this->post['dir'])) {
            $dir = $this->checkInputDir($this->post['dir'], true, false);
            if ($dir === false) unset($this->post['dir']);
            $this->post['dir'] = $dir;
        }
        if (isset($this->get['dir'])) {
            //$dir = $this->checkInputDir($this->get['dir'], true, false);
            
            //if ($dir === false) unset($this->get['dir']);
            //$this->get['dir'] = $dir;
        }

        $thumbsDir = str_ireplace("//", "/", strtolower($this->config['uploadDir'] . "/" . $this->config['thumbsDir']));
        if ((
                !is_dir($thumbsDir) &&
                !@mkdir($thumbsDir, $this->config['dirPerms'])
            ) ||

            !is_readable($thumbsDir) ||
            !dir::isWritable(str_ireplace("//", "/", strtolower($thumbsDir))) ||
            (
                !is_dir("$thumbsDir/{$this->type}") &&
                !@mkdir("$thumbsDir/{$this->type}", $this->config['dirPerms'])
            )
        )
            $this->errorMsg("Cannot access or create thumbnails folder.");

        $this->thumbsDir = $thumbsDir;
        $this->thumbsTypeDir = "$thumbsDir/{$this->type}";

        // Remove temporary zip downloads if exists
        $files = dir::content($this->config['uploadDir'], array(
            'types' => "file",
            'pattern' => '/^.*\.zip$/i'
        ));
        if (is_array($files) && count($files)) {
            $time = time();
            foreach ($files as $file)
                if (is_file($file) && ($time - filemtime($file) > 3600))
                    unlink($file);
        }

        if (isset($this->get['theme']) &&
            ($this->get['theme'] == basename($this->get['theme'])) &&
            is_dir("themes/{$this->get['theme']}")
        )
            $this->config['theme'] = $this->get['theme'];
    }

    public function action() {
        $act = isset($this->get['act']) ? $this->get['act'] : "browser";
        if (!method_exists($this, "act_$act"))
            $act = "browser";
        $this->action = $act;
        $method = "act_$act";

        if ($this->config['disabled']) {
            $message = $this->label("You don't have permissions to browse server.");
            if (in_array($act, array("browser", "upload")) ||
                (substr($act, 0, 8) == "download")
            )
                $this->backMsg($message);
            else {
                header("Content-Type: text/plain; charset={$this->charset}");
                die(json_encode(array('error' => $message)));
            }
        }

        if (!isset($this->session['dir']))
            $this->session['dir'] = $this->type;
        else {
            $type = $this->getTypeFromPath($this->session['dir']);
            $dir = $this->config['uploadDir'] . "/" . $this->session['dir'];
            if (($type != $this->type) || !is_dir($dir) || !is_readable($dir))
                $this->session['dir'] = $this->type;
        }
        $this->session['dir'] = path::normalize($this->session['dir']);

        if ($act == "browser") {
            header("X-UA-Compatible: chrome=1");
            header("Content-Type: text/html; charset={$this->charset}");
        } elseif (
            (substr($act, 0, 8) != "download") &&
            !in_array($act, array("thumb", "upload"))
        )
            header("Content-Type: text/plain; charset={$this->charset}");
        $return = $this->$method();
        echo ($return === true)
            ? '{}'
            : $return;
    }

    protected function act_browser() {
        if (isset($this->get['dir']) &&
            is_dir("{$this->typeDir}/{$this->get['dir']}") &&
            is_readable("{$this->typeDir}/{$this->get['dir']}")
        )
            $this->session['dir'] = path::normalize("{$this->type}/{$this->get['dir']}");

        return $this->output();
    }

    protected function act_init() {
        $tree = $this->getDirInfo($this->typeDir);
        $tree['dirs'] = $this->getTree(""); //$this->session['dir']
        if (!is_array($tree['dirs']) || !count($tree['dirs']))
            unset($tree['dirs']);
        
        
//        $db_config = $this->config['db_config'];
//        $this->db_connect();
//        
//        $strSQL = "SELECT gallery_name FROM " . $db_config['prefix'] . "gallery"; 
//        $hRes = mysqli_query($strSQL);
//        $g_array = array();
//        while($gallery_name = mysqli_fetch_assoc($hRes))
//        {
//            $g_array[] = $gallery_name['gallery_name'];
//        }
        
        
        $reset_tree = array();
        foreach ($tree['dirs'] as $key=>$t)
        {
            
            if($t['name']!="Image" && $t['name']!="video" && $t['name']!="podcast" )
            {
               // unset($tree['dirs'][$key]);
                continue;
            }
            if (isset($t['dirs']) )
            { 
                $s_path = $this->typeDir . strtolower($t['name']) . "/post";
            }
               
        }
        foreach($tree as $key=>$value)
        {
            if($key!="dirs")
            {
                $reset_tree[$key] = $value;
            }
            else
            {
                foreach ($value as $vkey=>$t)
                {

                    if($t['name']!="Image"  && $t['name']!="video" && $t['name']!="podcast")
                    {
                       // unset($tree['dirs'][$key]);
                        continue;
                    }
                    else
                    {
                          
                        $reset_tree[$key][] = $t;
                    }    
                   

                }
            }    
        }    
        
        $files = $this->getFiles( $s_path ); //$this->session['dir']
        
        $dirWritable = dir::isWritable(str_ireplace("//","/",strtolower("{$this->config['uploadDir']}/{$this->current_dir}")));
//        
        
        $data = array(
            'tree' => &$reset_tree,
            'files' => &$files,
            'dirWritable' => $dirWritable
        );
        return json_encode($data);
    }

    protected function act_thumb() {
        
        $this->getDir($this->get['dir'], true);
        
        if (!isset($this->get['file']) || !isset($this->get['dir']))
            $this->sendDefaultThumb();
        $file = $this->get['file'];
        if (basename($file) != $file)
            $this->sendDefaultThumb();
        $file = strtolower("{$this->thumbsDir}/{$this->type}/{$this->get['dir']}/$file");
        $file = str_ireplace(".thumbs/", "", $file);
        if (!is_file($file) || !is_readable($file)) {
            $file = "{$this->config['uploadDir']}/{$this->type}/{$this->get['dir']}/" . basename($file);
            if (!is_file($file) || !is_readable($file))
                $this->sendDefaultThumb($file);
            $image = new gd($file);
            if ($image->init_error)
                $this->sendDefaultThumb($file);
            $browsable = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
            if (in_array($image->type, $browsable) &&
                ($image->get_width() <= $this->config['thumbWidth']) &&
                ($image->get_height() <= $this->config['thumbHeight'])
            ) {
                $type =
                    ($image->type == IMAGETYPE_GIF) ? "gif" : (
                    ($image->type == IMAGETYPE_PNG) ? "png" : "jpeg");
                $type = "image/$type";
                httpCache::file($file, $type);
            } else
                $this->sendDefaultThumb($file);
        }
        httpCache::file($file, "image/jpeg");
    }

    protected function act_expand() 
    {
        return json_encode(array('dirs' => $this->getDirs($this->postDir())));
    }

    protected function act_chDir() {
        $this->postDir(); // Just for existing check
        $this->current_dir = $this->type . "/" . $this->post['dir'];
        
        $dirWritable = dir::isWritable(str_ireplace("//","/",strtolower("{$this->config['uploadDir']}/{$this->current_dir}")));
        
        return json_encode(array(
            'files' => $this->getFiles(str_ireplace("//","/",strtolower($this->current_dir))),
            'dirWritable' => $dirWritable
        ));
    }

    protected function act_newDir() {
        if (!$this->config['access']['dirs']['create'] ||
            !isset($this->post['dir']) ||
            !isset($this->post['newDir'])
        )
            $this->errorMsg("Unknown error.");

        $dir = $this->postDir();
        $newDir = $this->normalizeDirname(trim($this->post['newDir']));
        if (!strlen($newDir))
            $this->errorMsg("Please enter new folder name.");
        if (preg_match('/[\/\\\\]/s', $newDir))
            $this->errorMsg("Unallowable characters in folder name.");
        if (substr($newDir, 0, 1) == ".")
            $this->errorMsg("Folder name shouldn't begins with '.'");
        if (file_exists("$dir/$newDir"))
            $this->errorMsg("A file or folder with that name already exists.");
        if (!@mkdir("$dir/$newDir", $this->config['dirPerms']))
            $this->errorMsg("Cannot create {dir} folder.", array('dir' => $newDir));
        return true;
    }

    protected function act_renameDir() {
        if (!$this->config['access']['dirs']['rename'] ||
            !isset($this->post['dir']) ||
            !isset($this->post['newName'])
        )
            $this->errorMsg("Unknown error.");

        $dir = $this->postDir();
        $newName = $this->normalizeDirname(trim($this->post['newName']));
        if (!strlen($newName))
            $this->errorMsg("Please enter new folder name.");
        if (preg_match('/[\/\\\\]/s', $newName))
            $this->errorMsg("Unallowable characters in folder name.");
        if (substr($newName, 0, 1) == ".")
            $this->errorMsg("Folder name shouldn't begins with '.'");
        if (!@rename($dir, dirname($dir) . "/$newName"))
            $this->errorMsg("Cannot rename the folder.");
        $thumbDir = "$this->thumbsTypeDir/{$this->post['dir']}";
        if (is_dir($thumbDir))
            @rename($thumbDir, dirname($thumbDir) . "/$newName");
        return json_encode(array('name' => $newName));
    }

    protected function act_deleteDir() {
        if (!$this->config['access']['dirs']['delete'] ||
            !isset($this->post['dir']) ||
            !strlen(trim($this->post['dir']))
        )
            $this->errorMsg("Unknown error.");

        $dir = $this->postDir();

        if (!dir::isWritable(str_ireplace("//", "/", strtolower($dir))))
            $this->errorMsg("Cannot delete the folder.");
        $result = !dir::prune($dir, false);
        if (is_array($result) && count($result))
            $this->errorMsg("Failed to delete {count} files/folders.",
                array('count' => count($result)));
        $thumbDir = "$this->thumbsTypeDir/{$this->post['dir']}";
        if (is_dir($thumbDir)) dir::prune($thumbDir);
        return true;
    }

    protected function act_upload() {
        $this->post['dir'] = "pdf";
        if (!$this->config['access']['files']['upload'] ||
            !isset($this->post['dir'])
        )
            $this->errorMsg("Unknown error321.");

        $dir = $this->postDir();

        if (!dir::isWritable(str_ireplace("//", "/", strtolower($dir))))
            $this->errorMsg("Cannot access or write to upload folder.");
        
        if (is_array($this->file['name'])) {
            $return = array();
            foreach ($this->file['name'] as $i => $name) {
                $return[] = $this->moveUploadFile(array(
                    'name' => $name,
                    'tmp_name' => $this->file['tmp_name'][$i],
                    'error' => $this->file['error'][$i]
                ), $dir);
            }
            return implode("\n", $return);
        } else
            return $this->moveUploadFile($this->file, $dir);
    }

    protected function act_download() {
        $dir = $this->postDir();
        if (!isset($this->post['dir']) ||
            !isset($this->post['file']) ||
            (false === ($file = "$dir/{$this->post['file']}")) ||
            !file_exists($file) || !is_readable($file)
        )
            $this->errorMsg("Unknown error.");

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . str_replace('"', "_", $this->post['file']) . '"');
        header("Content-Transfer-Encoding:­ binary");
        header("Content-Length: " . filesize($file));
        readfile($file);
        die;
    }

    protected function act_rename() {
        $dir = $this->postDir();
        $this->type = $_GET['type'];
        if (!$this->config['access']['files']['rename'] ||
            !isset($this->post['dir']) ||
            !isset($this->post['file']) ||
            !isset($this->post['newName']) ||
            (false === ($file = "$dir/{$this->post['file']}")) ||
            !file_exists($file) || !is_readable($file) || !file::isWritable($file)
        )
            $this->errorMsg("Unknown error6.");

        if (isset($this->config['denyExtensionRename']) &&
            $this->config['denyExtensionRename'] &&
            (file::getExtension($this->post['file'], true) !==
                file::getExtension($this->post['newName'], true)
            )
        )
            $this->errorMsg("You cannot rename the extension of files!");

        $newName = $this->normalizeFilename(trim($this->post['newName']));
        if (!strlen($newName))
            $this->errorMsg("Please enter new file name.");
        if (preg_match('/[\/\\\\]/s', $newName))
            $this->errorMsg("Unallowable characters in file name.");
        if (substr($newName, 0, 1) == ".")
            $this->errorMsg("File name shouldn't begins with '.'");
        $newName = "$dir/$newName";
        if (file_exists($newName))
            $this->errorMsg("A file or folder with that name already exists.");
        $ext = file::getExtension($newName);
        if (!$this->validateExtension($ext, $this->type))
            $this->errorMsg("Denied file extension.");
        if (!@rename($file, $newName))
            $this->errorMsg("Unknown error987.");
        
        //Change the database based on new file name
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $old_name = "upload/gallery/" . strtolower($this->post['dir']) . "/" . strtolower($this->post['file']);
        $strSQL = "SELECT id  FROM " . $db_config['prefix'] . "materials WHERE `material_url` = '" . $old_name . "' LIMIT 1"; 
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_material_id) = mysqli_fetch_row($hRes);
        $new_name = "upload/gallery/" . strtolower($this->post['dir']) . "/" . strtolower($this->post['newName']);
        $strSQL = "UPDATE " . $db_config['prefix'] . "materials SET `material_url` = '" . $new_name . "' WHERE id = " . $i_material_id; 
        mysqli_query($this->obj_con, $strSQL);
        
        $thumbDir = strtolower("{$this->thumbsTypeDir}/{$this->post['dir']}");
        $thumbFile = "$thumbDir/{$this->post['file']}";

        if (file_exists($thumbFile))
            @rename($thumbFile, "$thumbDir/" . basename($newName));
        
        
        return true;
    }
    
    protected function act_addCaption() 
    {
        $dir = $this->postDir();
        $this->type = $_GET['type'];
        
        //Change the database based on new file name
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $name = "upload/gallery/" . strtolower($this->post['dir']) . "/" . strtolower($this->post['file']);
        $strSQL = "SELECT id  FROM " . $db_config['prefix'] . "materials WHERE `material_url` = '" . $name . "' LIMIT 1"; 
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_material_id) = mysqli_fetch_row($hRes);
        
        $strSQL = "UPDATE " . $db_config['prefix'] . "materials SET `caption` = '" . $this->post['caption'] . "', `source` = '" . $this->post['source'] . "' WHERE id = " . $i_material_id; 
        mysqli_query($this->obj_con, $strSQL);
        
        $strSQL = "SELECT id  FROM " . $db_config['prefix'] . "material_menu WHERE material_id = " . $i_material_id . " LIMIT 1"; 
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_gallery_id) = mysqli_fetch_row($hRes);
        
        if(isset($i_gallery_id) && $i_gallery_id!="")
        {
            
            $dir    = str_replace("ckeditor".DIRECTORY_SEPARATOR."kcfinder".DIRECTORY_SEPARATOR."core","",dirname(__FILE__));
            $dir = $dir."upload".DIRECTORY_SEPARATOR."cache";
            $files = scandir($dir);
            
            
            $file_name_content = "ALL_GALLERY_CACHE";
            foreach($files as $value)
            {
                
                if(strpos($value, "ALL_GALLERY_CACHE")!==FALSE)
                {
                    
                    unlink($dir.DIRECTORY_SEPARATOR.$value);
                }        
                
            }    
            
         
        }    
        
        return true;
    }
    
    protected function act_showvideo() 
    {
        $dir = $this->postDir();
        
        //Change the database based on new file name
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $name = "upload/gallery/" . strtolower($this->post['dir']) . "/" . strtolower($this->post['file']);
        $strSQL = "SELECT url  FROM " . $db_config['prefix'] . "materials_video mv INNER JOIN " . $db_config['prefix'] . "materials m 
                   ON m.video_id = mv.id WHERE m.material_url = '" . $name . "' LIMIT 1"; 
        $hRes = mysqli_query($this->obj_con, $strSQL);
        if ( mysqli_num_rows($hRes) > 0 )
        {
            return json_encode(mysqli_fetch_object($hRes));
        }
        else 
        {
            return false;
        }
    }
    
    protected function act_getCaption() {
        $dir = $this->postDir();
        $this->type = $_GET['type'];
        
        //Change the database based on new file name
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $name = "upload/gallery/" . strtolower($this->post['dir']) . "/" . strtolower($this->post['file']);
        $strSQL = "SELECT caption, source  FROM " . $db_config['prefix'] . "materials WHERE `material_url` = '" . $name . "' LIMIT 1"; 
        $hRes = mysqli_query($this->obj_con, $strSQL);
        $obj_images_caption = mysqli_fetch_object($hRes);
        
        return json_encode($obj_images_caption);
    }

    protected function act_delete() {
        $dir = $this->postDir();
        if (!$this->config['access']['files']['delete'] ||
            !isset($this->post['dir']) ||
            !isset($this->post['file']) ||
            (false === ($file = "$dir/{$this->post['file']}")) ||
            !file_exists($file) || !is_readable($file) || !file::isWritable($file) ||
            !@unlink($file)
        )
            $this->errorMsg("Unknown error12345.");

        $thumb = "{$this->thumbsTypeDir}/{$this->post['dir']}/{$this->post['file']}";
        if (file_exists($thumb)) @unlink($thumb);
        
        //remove also from database
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $name = "upload/gallery/" . strtolower($this->post['dir']) . "/" . strtolower($this->post['file']);
        $strSQL = "SELECT id  FROM " . $db_config['prefix'] . "materials WHERE `material_url` = '" . $name . "' LIMIT 1"; 
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_material_id) = mysqli_fetch_row($hRes);
        
        
        $strSQL = "DELETE FROM " . $db_config['prefix'] . "materials WHERE id = " . $i_material_id; 
        mysqli_query($this->obj_con, $strSQL);
        return true;
    }
    
    protected function act_addWatermark()
    {
        $dir = $this->postDir();
        $this->type = $_GET['type'];
        
        $source = $dir . "/" . $this->post['file'];
        $fileinfo = pathinfo($source);
        $output = $dir . "/" . $fileinfo['filename'] . "_watermark." . $fileinfo['extension'];
        
        list($source_width, $source_height, $source_type) = getimagesize($source);
        
        if ($source_type === NULL) 
        {
            return false;
        }
        $overlay_image  = $this->config['uploadDir'] . "/logo.png"; 
        $overlay_gd_image = imagecreatefrompng($overlay_image);
        
        switch ($source_type) 
        {
            case IMAGETYPE_GIF:
                    $source_gd_image = imagecreatefromgif($source);
                    break;
            case IMAGETYPE_JPEG:
                    $source_gd_image = imagecreatefromjpeg($source);
                    break;
            case IMAGETYPE_PNG:
                    $source_gd_image = imagecreatefrompng($source_file_path);
                    break;
            default:
                    return false;
        }
        
        $overlay_width = imagesx($overlay_gd_image);
        $overlay_height = imagesy($overlay_gd_image);
        imagecopymerge( $source_gd_image, $overlay_gd_image, $source_width - $overlay_width, 
                        $source_height - $overlay_height, 0, 0, $overlay_width, $overlay_height, 50);
        
        imagejpeg($source_gd_image, $output, 90);
        imagedestroy($source_gd_image);
        imagedestroy($overlay_gd_image);
        
        @unlink($source);
        
        @rename($output, $source);
    }

    protected function act_cp_cbd() {
        $dir = $this->postDir();
        if (!$this->config['access']['files']['copy'] ||
            !isset($this->post['dir']) ||
            !is_dir($dir) || !is_readable($dir) || !dir::isWritable(str_ireplace("//", "/", strtolower($dir))) ||
            !isset($this->post['files']) || !is_array($this->post['files']) ||
            !count($this->post['files'])
        )
            $this->errorMsg("Unknown error091.");

        $error = array();
        foreach($this->post['files'] as $file) {
            $file = path::normalize($file);
            if (substr($file, 0, 1) == ".") continue;
            $type = explode("/", $file);
            $type = $type[0];
            if ($type != $this->type) continue;
            $path = "{$this->config['uploadDir']}/$file";
            $base = basename($file);
            $replace = array('file' => $base);
            $ext = file::getExtension($base);
            if (!file_exists($path))
                $error[] = $this->label("The file '{file}' does not exist.", $replace);
            elseif (substr($base, 0, 1) == ".")
                $error[] = "$base: " . $this->label("File name shouldn't begins with '.'");
            elseif (!$this->validateExtension($ext, $type))
                $error[] = "$base: " . $this->label("Denied file extension.");
            elseif (file_exists("$dir/$base"))
                $error[] = "$base: " . $this->label("A file or folder with that name already exists.");
            elseif (!is_readable($path) || !is_file($path))
                $error[] = $this->label("Cannot read '{file}'.", $replace);
            elseif (!@copy($path, "$dir/$base"))
                $error[] = $this->label("Cannot copy '{file}'.", $replace);
            else {
                if (function_exists("chmod"))
                    @chmod("$dir/$base", $this->config['filePerms']);
                $fromThumb = "{$this->thumbsDir}/$file";
                if (is_file($fromThumb) && is_readable($fromThumb)) {
                    $toThumb = "{$this->thumbsTypeDir}/{$this->post['dir']}";
                    if (!is_dir($toThumb))
                        @mkdir($toThumb, $this->config['dirPerms'], true);
                    $toThumb .= "/$base";
                    @copy($fromThumb, $toThumb);
                }
            }
        }
        if (count($error))
            return json_encode(array('error' => $error));
        return true;
    }

    protected function act_mv_cbd() {
        $dir = $this->postDir();
        if (!$this->config['access']['files']['move'] ||
            !isset($this->post['dir']) ||
            !is_dir($dir) || !is_readable($dir) || !dir::isWritable(str_ireplace("//", "/", strtolower($dir))) ||
            !isset($this->post['files']) || !is_array($this->post['files']) ||
            !count($this->post['files'])
        )
            $this->errorMsg("Unknown error801.");

        $error = array();
        foreach($this->post['files'] as $file) {
            $file = path::normalize($file);
            if (substr($file, 0, 1) == ".") continue;
            $type = explode("/", $file);
            $type = $type[0];
            if ($type != $this->type) continue;
            $path = "{$this->config['uploadDir']}/$file";
            $base = basename($file);
            $replace = array('file' => $base);
            $ext = file::getExtension($base);
            if (!file_exists($path))
                $error[] = $this->label("The file '{file}' does not exist.", $replace);
            elseif (substr($base, 0, 1) == ".")
                $error[] = "$base: " . $this->label("File name shouldn't begins with '.'");
            elseif (!$this->validateExtension($ext, $type))
                $error[] = "$base: " . $this->label("Denied file extension.");
            elseif (file_exists("$dir/$base"))
                $error[] = "$base: " . $this->label("A file or folder with that name already exists.");
            elseif (!is_readable($path) || !is_file($path))
                $error[] = $this->label("Cannot read '{file}'.", $replace);
            elseif (!file::isWritable($path) || !@rename($path, "$dir/$base"))
                $error[] = $this->label("Cannot move '{file}'.", $replace);
            else {
                if (function_exists("chmod"))
                    @chmod("$dir/$base", $this->config['filePerms']);
                $fromThumb = "{$this->thumbsDir}/$file";
                if (is_file($fromThumb) && is_readable($fromThumb)) {
                    $toThumb = "{$this->thumbsTypeDir}/{$this->post['dir']}";
                    if (!is_dir($toThumb))
                        @mkdir($toThumb, $this->config['dirPerms'], true);
                    $toThumb .= "/$base";
                    @rename($fromThumb, $toThumb);
                }
            }
        }
        if (count($error))
            return json_encode(array('error' => $error));
        return true;
    }

    protected function act_rm_cbd() {
        if (!$this->config['access']['files']['delete'] ||
            !isset($this->post['files']) ||
            !is_array($this->post['files']) ||
            !count($this->post['files'])
        )
            $this->errorMsg("Unknown error1.");

        $error = array();
        foreach($this->post['files'] as $file) {
            $file = path::normalize($file);
            if (substr($file, 0, 1) == ".") continue;
            $type = explode("/", $file);
            $type = $type[0];
            if ($type != $this->type) continue;
            $path = "{$this->config['uploadDir']}/$file";
            $base = basename($file);
            $replace = array('file' => $base);
            if (!is_file($path))
                $error[] = $this->label("The file '{file}' does not exist.", $replace);
            elseif (!@unlink($path))
                $error[] = $this->label("Cannot delete '{file}'.", $replace);
            else {
                $thumb = "{$this->thumbsDir}/$file";
                if (is_file($thumb)) @unlink($thumb);
            }
        }
        if (count($error))
            return json_encode(array('error' => $error));
        return true;
    }

    protected function act_downloadDir() {
        $dir = $this->postDir();
        if (!isset($this->post['dir']) || $this->config['denyZipDownload'])
            $this->errorMsg("Unknown error2.");
        $filename = basename($dir) . ".zip";
        do {
            $file = md5(time() . session_id());
            $file = "{$this->config['uploadDir']}/$file.zip";
        } while (file_exists($file));
        new zipFolder($file, $dir);
        header("Content-Type: application/x-zip");
        header('Content-Disposition: attachment; filename="' . str_replace('"', "_", $filename) . '"');
        header("Content-Length: " . filesize($file));
        readfile($file);
        unlink($file);
        die;
    }

    protected function act_downloadSelected() {
        $dir = $this->postDir();
        if (!isset($this->post['dir']) ||
            !isset($this->post['files']) ||
            !is_array($this->post['files']) ||
            $this->config['denyZipDownload']
        )
            $this->errorMsg("Unknown error3.");

        $zipFiles = array();
        foreach ($this->post['files'] as $file) {
            $file = path::normalize($file);
            if ((substr($file, 0, 1) == ".") || (strpos($file, '/') !== false))
                continue;
            $file = "$dir/$file";
            if (!is_file($file) || !is_readable($file))
                continue;
            $zipFiles[] = $file;
        }

        do {
            $file = md5(time() . session_id());
            $file = "{$this->config['uploadDir']}/$file.zip";
        } while (file_exists($file));

        $zip = new ZipArchive();
        $res = $zip->open($file, ZipArchive::CREATE);
        if ($res === TRUE) {
            foreach ($zipFiles as $cfile)
                $zip->addFile($cfile, basename($cfile));
            $zip->close();
        }
        header("Content-Type: application/x-zip");
        header('Content-Disposition: attachment; filename="selected_files_' . basename($file) . '"');
        header("Content-Length: " . filesize($file));
        readfile($file);
        unlink($file);
        die;
    }

    protected function act_downloadClipboard() {
        if (!isset($this->post['files']) ||
            !is_array($this->post['files']) ||
            $this->config['denyZipDownload']
        )
            $this->errorMsg("Unknown error4.");

        $zipFiles = array();
        foreach ($this->post['files'] as $file) {
            $file = path::normalize($file);
            if ((substr($file, 0, 1) == "."))
                continue;
            $type = explode("/", $file);
            $type = $type[0];
            if ($type != $this->type)
                continue;
            $file = $this->config['uploadDir'] . "/$file";
            if (!is_file($file) || !is_readable($file))
                continue;
            $zipFiles[] = $file;
        }

        do {
            $file = md5(time() . session_id());
            $file = "{$this->config['uploadDir']}/$file.zip";
        } while (file_exists($file));

        $zip = new ZipArchive();
        $res = $zip->open($file, ZipArchive::CREATE);
        if ($res === TRUE) {
            foreach ($zipFiles as $cfile)
                $zip->addFile($cfile, basename($cfile));
            $zip->close();
        }
        header("Content-Type: application/x-zip");
        header('Content-Disposition: attachment; filename="clipboard_' . basename($file) . '"');
        header("Content-Length: " . filesize($file));
        readfile($file);
        unlink($file);
        die;
    }

    protected function act_check4Update() {
        if ($this->config['denyUpdateCheck'])
            return json_encode(array('version' => false));

        // Caching HTTP request for 6 hours
        if (isset($this->session['checkVersion']) &&
            isset($this->session['checkVersionTime']) &&
            ((time() - $this->session['checkVersionTime']) < 21600)
        )
            return json_encode(array('version' => $this->session['checkVersion']));

        $protocol = "http";
        $host = "kcfinder.sunhater.com";
        $port = 80;
        $path = "/checkVersion.php";

        $url = "$protocol://$host:$port$path";
        $pattern = '/^\d+\.\d+$/';
        $responsePattern = '/^[A-Z]+\/\d+\.\d+\s+\d+\s+OK\s*([a-zA-Z0-9\-]+\:\s*[^\n]*\n)*\s*(.*)\s*$/';

        // file_get_contents()
        if (ini_get("allow_url_fopen") &&
            (false !== ($ver = file_get_contents($url))) &&
            preg_match($pattern, $ver)

        // HTTP extension
        ) {} elseif (
            function_exists("http_get") &&
            (false !== ($ver = @http_get($url))) &&
            (
                (
                    preg_match($responsePattern, $ver, $match) &&
                    false !== ($ver = $match[2])
                ) || true
            ) &&
            preg_match($pattern, $ver)

        // Curl extension
        ) {} elseif (
            function_exists("curl_init") &&
            (false !== (   $curl = @curl_init($url)                                    )) &&
            (              @ob_start()                 ||  (@curl_close($curl) && false)) &&
            (              @curl_exec($curl)           ||  (@curl_close($curl) && false)) &&
            ((false !== (  $ver = @ob_get_clean()   )) ||  (@curl_close($curl) && false)) &&
            (              @curl_close($curl)          ||  true                         ) &&
            preg_match($pattern, $ver)

        // Socket extension
        ) {} elseif (function_exists('socket_create')) {
            $cmd =
                "GET $path " . strtoupper($protocol) . "/1.1\r\n" .
                "Host: $host\r\n" .
                "Connection: Close\r\n\r\n";

            if ((false !== (  $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)  )) &&
                (false !==    @socket_connect($socket, $host, $port)                    ) &&
                (false !==    @socket_write($socket, $cmd, strlen($cmd))                ) &&
                (false !== (  $ver = @socket_read($socket, 2048)                       )) &&
                preg_match($responsePattern, $ver, $match)
            )
                $ver = $match[2];

            if (isset($socket) && is_resource($socket))
                @socket_close($socket);
        }

        if (isset($ver) && preg_match($pattern, $ver)) {
            $this->session['checkVersion'] = $ver;
            $this->session['checkVersionTime'] = time();
            return json_encode(array('version' => $ver));
        } else
            return json_encode(array('version' => false));
    }
    
    protected function db_connect()
    {
        $db_config = $this->config['db_config'];
        $this->obj_con = mysqli_connect( $db_config['host'], $db_config['user'], $db_config['pass'], $db_config['db'] );
        //mysql_select_db( $db_config['db'], $this->obj_con );
    }
    
    protected function save_material( $s_target, $dir )
    {
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $strSQL = "SELECT id FROM " . $db_config['prefix'] . "gallery WHERE `gallery_name` = '" . $dir . "'"; 
        
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_gallery_id) = mysqli_fetch_row($hRes);
        
        $base_dir = dirname(dirname(dirname(dirname(__FILE__))));
        $base_dir = str_replace("\\", "/", $base_dir);
        
        $strSQL = "INSERT INTO " . $db_config['prefix'] . "materials SET `material_url` = '" . str_replace($base_dir, "", $s_target) . "', 
                   `gallery_id` = " . $i_gallery_id . ", `imagedate` = '" . date("Y-m-d") . "', caption = '', source = ''"; 
        
        mysqli_query($this->obj_con, $strSQL);
    }
    
    protected function sanitize($str, $char = '-')
    {
        // Lower case the string and remove whitespace from the beginning or end
        $str = trim(strtolower($str));

        // Remove single quotes from the string
        $str = str_replace("'", '', $str);

        // Every character other than a-z, 0-9 will be replaced with a single dash (-)
        $str = preg_replace("/[^a-z0-9.]+/", $char, $str);

        // Remove any beginning or trailing dashes
        $str = trim($str, $char);

        return $str;
     } 
    
    protected function getfilename($file)
    {
        return $this->sanitize($file['name']);
//        $strNewName = substr(md5(uniqid(rand(), true)), 0, 7);
//        $filename = $strNewName . "." . file::getExtension($file['name']);
//        if (file_exists($filename) )
//        {
//            $this->getfilename($file);
//        }
//        else
//        {
//            return $filename;
//        }
    }

    protected function moveUploadFile($file, $dir) 
    {
        $ar_has_caption = array(
            1 => 'has_caption',
            2 => 'no_caption',
            3 => 'no_caption',
            4 => 'no_caption',
            5 => 'has_caption',
            6 => 'no_caption'
        );
        $fdir = str_ireplace($this->typeDir, "", $dir);
        
        $i_pos = strrpos($fdir, "/");
        if ( $i_pos !== FALSE )
        {
            $fdir = substr($fdir, $i_pos + 1, strlen($fdir));
        }
        
        $db_config = $this->config['db_config'];
        $this->db_connect();
        $strSQL = "SELECT gallery_type  FROM " . $db_config['prefix'] . "gallery WHERE `gallery_name` = '" . $fdir . "'"; 
        
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_gallery_type) = mysqli_fetch_row($hRes);
        
        $message = $this->checkUploadedFile($file);
        
        if ($message !== true) {
            if (isset($file['tmp_name']))
                @unlink($file['tmp_name']);
            return "{$file['name']}: $message";
        }

        $filename = $this->normalizeFilename($this->getfilename($file, $dir));
        $target = "$dir/" . file::getInexistantFilename($filename, $dir);
        
        if (!@move_uploaded_file($file['tmp_name'], $target) &&
            !@rename($file['tmp_name'], $target) &&
            !@copy($file['tmp_name'], $target)
        ) {
            @unlink($file['tmp_name']);
            return "{$file['name']}: " . $this->label("Cannot move uploaded file to target folder.");
        } 
        elseif (function_exists('chmod'))
        {
            $s_target = str_ireplace("../../", "", $target);        
            $s_target = str_ireplace("./", "", $s_target);
            $this->save_material($s_target, $fdir);
            chmod($target, $this->config['filePerms']);
        }
        
        if(strpos($target,".jpg") || strpos($target,".JPG") )
        {
           $target_url = "http://jpgoptimiser.com/optimise"; 
           $image_path = str_replace("../../", "/home/champs21/public_html/website/", $target);
           $image_path = str_replace("/./", "/", $image_path);
           $dest_path  = "/".$image_path;
           $this->super_compress($target_url, $image_path, $dest_path);
       } 
        
        if(strpos($target,".png") || strpos($target,".PNG") )
        {
           $target_url = 'http://pngcrush.com/crush';
           $image_path = str_replace("../../", "/home/champs21/public_html/website/", $target);
           $image_path = str_replace("/./", "/", $image_path);
           $dest_path  = "/".$image_path;
           $this->super_compress($target_url,$image_path, $dest_path);
           
        } 
        $this->makeThumb($target);
        
      
      
        return "/" . basename($target) . "|" . $ar_has_caption[$i_gallery_type];
    }
    private function super_compress( $target_url, $image_path, $dest_image_path )
    {
        /*
        * To change this template, choose Tools | Templates
        * and open the template in the editor.
        */
       $file_name_with_full_path = $image_path;
       $post = array('input'=>'@'.$file_name_with_full_path);

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,$target_url);
       curl_setopt($ch, CURLOPT_POST,1);
       curl_setopt($ch, CURLOPT_HEADER, 0);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $result=curl_exec ($ch);
       curl_close ($ch);


       if(file_exists($dest_image_path)){
            unlink($dest_image_path);
        }
        $fp = fopen(substr($dest_image_path, 1, strlen($dest_image_path)),'w+');
        fwrite($fp, $result);
        fclose($fp);
    }
    

    protected function sendDefaultThumb($file=null) {
        if ($file !== null) {
            $ext = file::getExtension($file);
            $thumb = "themes/{$this->config['theme']}/img/files/big/$ext.png";
        }
        if (!isset($thumb) || !file_exists($thumb))
        {
            $thumb = "themes/{$this->config['theme']}/img/files/big/..png";
        }
        header("Content-Type: image/png");
        readfile($thumb);
        die;
    }

    protected function getFiles($dir) {
        $db_config = $this->config['db_config'];
        $this->db_connect();
        
        $thumbDir = strtolower("{$this->config['uploadDir']}/{$this->config['thumbsDir']}/$dir");
        $i_pos = strrpos($dir, "/");
        $gallery_name = "";
        if ( $i_pos !== FALSE )
        {
            $gallery_name = substr($dir, $i_pos + 1, strlen($dir));
        }
        $dir = "{$this->config['uploadDir']}/$dir";
        
        $strSQL = "SELECT id FROM " . $db_config['prefix'] . "gallery WHERE `gallery_name` = '" . $gallery_name . "'"; 
        
        $hRes = mysqli_query($this->obj_con, $strSQL);
        list($i_gallery_id) = mysqli_fetch_row($hRes);
        
        $return = array();
        $s_from_date = date("Y-m-d");
        $s_to_date = date("Y-m-d");
        
        if ( isset($this->get['range']) )
        {
            $d_range = $this->get['range'];
            $ar_d_range = explode(" - ", $d_range);
            $s_from_date = date("Y-m-d", strtotime($ar_d_range[0]));
            $s_to_date = date("Y-m-d", strtotime($ar_d_range[1]));
        }
        else if ( isset($this->post['range']) )
        {
            $d_range = $this->post['range'];
            $ar_d_range = explode(" - ", $d_range);
            $s_from_date = date("Y-m-d", strtotime($ar_d_range[0]));
            $s_to_date = date("Y-m-d", strtotime($ar_d_range[1]));
        }
        
        $s_where = "";
        if ( isset($this->post['search_text']) )
        {
            $s_search_text = $this->post['search_text'];
            if (strlen($s_search_text) )
            {
                $s_upload_dir = str_ireplace("../../", "", strtolower($dir));
                $s_upload_dir = str_ireplace("../", "", $s_upload_dir);
                $s_upload_dir = str_ireplace("//", "/", $s_upload_dir);
                $s_search_text = $s_upload_dir . "/%" . $s_search_text;
                $s_where = " AND material_url Like '" . $s_search_text . "%'";
            }
        }
        else if( isset($this->get['search_text']) )
        {
            $s_search_text = $this->get['search_text'];
            if (strlen($s_search_text) )
            {
                $s_upload_dir = str_ireplace("../../", "", strtolower($dir));
                $s_upload_dir = str_ireplace("../", "", $s_upload_dir);
                $s_upload_dir = str_ireplace("//", "/", $s_upload_dir);
                $s_search_text = $s_upload_dir . "/" . $s_search_text;
                $s_where = " AND material_url Like '%" . $s_search_text . "%'";
            }
        }
        
        $strSQL = "SELECT * FROM " . $db_config['prefix'] . "materials WHERE `gallery_id` = '" . $i_gallery_id . "' AND imagedate BETWEEN '" . $s_from_date . "' AND '" . $s_to_date . "' " . $s_where . " ORDER BY id DESC"; 
        //print $strSQL;
        $hRes = mysqli_query($this->obj_con, $strSQL);
        $files = array();
        while($obj_materials = mysqli_fetch_object($hRes))
        {
            $s_material_names = $this->config['uploadDir'] . '/../../' . $obj_materials->material_url;
            array_push($files, $s_material_names);
        }
        //$files = dir::content($dir, array('types' => "file"));
        if ($files === false)
            return $return;

        foreach ($files as $file) {
            $size = @getimagesize($file);
            if (is_array($size) && count($size)) {
                $thumb_file = "$thumbDir/" . basename($file);
                if (!is_file($thumb_file))
                    $this->makeThumb($file, false);
                $smallThumb =
                    ($size[0] <= $this->config['thumbWidth']) &&
                    ($size[1] <= $this->config['thumbHeight']) &&
                    in_array($size[2], array(IMAGETYPE_GIF, IMAGETYPE_PNG, IMAGETYPE_JPEG));
            } else
                $smallThumb = false;

            $stat = @stat($file);
            if ($stat === false) continue;
            $name = basename($file);
            $ext = file::getExtension($file);
            $bigIcon = file_exists("themes/{$this->config['theme']}/img/files/big1/$ext.png");
            $smallIcon = file_exists("themes/{$this->config['theme']}/img/files/small/$ext.png");
            //print "$thumbDir/$name";
            $thumb = file_exists("$thumbDir/$name");
            $return[] = array(
                'name' => stripcslashes($name),
                'size' => $stat['size'],
                'mtime' => $stat['mtime'],
                'date' => @strftime($this->dateTimeSmall, $stat['mtime']),
                'readable' => is_readable($file),
                'writable' => file::isWritable($file),
                'bigIcon' => $bigIcon,
                'smallIcon' => $smallIcon,
                'thumb' => $thumb,
                'smallThumb' => $smallThumb
            );
        }
        return $return;
    }

    protected function getTree($dir, $index=0) 
    {
        $path = explode("/", $dir);

        $pdir = "";
        for ($i = 0; ($i <= $index && $i < count($path)); $i++)
            $pdir .= "/{$path[$i]}";
        if (strlen($pdir))
            $pdir = substr($pdir, 1);

        $fdir = "{$this->config['uploadDir']}/$pdir";

        $dirs = $this->getDirs($fdir);
        
        if (is_array($dirs) && count($dirs) && ($index <= count($path) - 1)) {

            foreach ($dirs as $i => $cdir) {
                if ($cdir['hasDirs'] &&
                    (
                        ($index == count($path) - 1) ||
                        ($cdir['name'] == $path[$index + 1])
                    )
                ) {
                    if ( ! isset($dirs[$i]['dirs']) )
                    {
                        $dirs[$i]['dirs'] = $this->getTree($dir, $index + 1);
                        if (!is_array($dirs[$i]['dirs']) || !count($dirs[$i]['dirs'])) {
                            unset($dirs[$i]['dirs']);
                            continue;
                        }
                    }
                }
            }
        } else
            return false;
        
        return $dirs;
    }

    protected function postDir($existent=true) {
        $dir = $this->typeDir;
        if ( isset($this->post_dir) )
        {
            $this->post_dir = str_ireplace("gallery/", "", $this->post_dir);
            $this->post['dir'] = $this->post_dir;
        }
        
        if (isset($this->post['dir']))
            $dir .= strtolower ($this->post['dir']);
        
        if ($existent && (!is_dir($dir) || !is_readable($dir)))
            $this->errorMsg("Inexistant or inaccessible folder.");
        return $dir;
    }

    protected function getDir($existent=true) {
        $dir = $this->typeDir;
        
        $this->get['dir'] = str_ireplace("gallery/", "", $this->get['dir']);
        
        if (isset($this->get['dir']))
            $dir .= "/" . $this->get['dir'];
        $dir = strtolower($dir);
        if ($existent && (!is_dir($dir) || !is_readable($dir)))
            $this->errorMsg("Inexistant or inaccessible folder.");
        return $dir;
    }

    protected function getDirs($dir) {
        $dirs = dir::content($dir, array('types' => "dir"));
        $return = array();
        if (is_array($dirs)) {
            $writable = dir::isWritable(str_ireplace("//", "/", strtolower($dir)));
            foreach ($dirs as $cdir) {
                $info = $this->getDirInfo($cdir);
                if ($info === false || $info['name']=="Ads") continue;
                $info['removable'] = $writable && $info['writable'];
                $return[] = $info;
            }
        }
        return $return;
    }

    protected function getDirInfo($dir, $removable=false) {
        if ((substr(basename($dir), 0, 1) == ".") || !is_dir($dir) || !is_readable($dir))
            return false;
        $dirs = dir::content($dir, array('types' => "dir"));
        if ( ucfirst(stripslashes(basename($dir))) == ucfirst($this->current_dir) )
        {
            $imgDirs = array();
            $i = 0;
            foreach ($dirs as $cdir)
            {
                $writable = dir::isWritable(str_ireplace("//", "/", strtolower($cdir)));
                
                $dirs_sub = dir::content($cdir, array('types' => "dir"));
                if (is_array($dirs_sub)) 
                {
                    $hasDirs = count($dirs_sub) ? true : false;
                } 
                else
                    $hasDirs = false;
                if (substr(basename($cdir), 0, 1) != ".")
                {
                    $ar_tmp = array(
                        'name' => ucfirst(stripslashes(basename($cdir))),
                        'readable' => is_readable($cdir),
                        'writable' => $writable,
                        'removable' => $removable && $writable && dir::isWritable(str_ireplace("//", "/", strtolower(dirname($cdir)))),
                        'hasDirs' => $hasDirs 
                    );
                    $namefolder = stripslashes(basename($cdir));
                    if ( $namefolder == "post" )
                        $ar_tmp['current'] = true;
                    $i++;
                    $ar_tmp['name_before'] = $ar_tmp['name'];
                    
                    array_push($imgDirs, $ar_tmp);
                }
            }
            //print_r($dirs);
        }
        if (is_array($dirs)) 
        {
            foreach ($dirs as $key => $cdir)
                if (substr(basename($cdir), 0, 1) == ".")
                    unset($dirs[$key]);
            $hasDirs = count($dirs) ? true : false;
        } 
        else
            $hasDirs = false;

        $writable = dir::isWritable(str_ireplace("//", "/", strtolower($dir)));
        
        if ( ucfirst(stripslashes(basename($dir))) == ucfirst($this->current_dir) )
        {
            $info = array(
                'name' => ucfirst(stripslashes(basename($dir))),
                'readable' => is_readable($dir),
                'writable' => $writable,
                'removable' => $removable && $writable && dir::isWritable(str_ireplace("//", "/", strtolower(dirname($dir)))),
                'hasDirs' => $hasDirs,
                'dirs'    => $imgDirs
            );
        }
        else
        {    
            $info = array(
                'name' => ucfirst(stripslashes(basename($dir))),
                'readable' => is_readable($dir),
                'writable' => $writable,
                'removable' => $removable && $writable && dir::isWritable(str_ireplace("//", "/", strtolower(dirname($dir)))),
                'hasDirs' => $hasDirs
            );
        }
        
        return $info;
    }

    protected function output($data=null, $template=null) {
        if (!is_array($data)) $data = array();
        if ($template === null)
            $template = $this->action;

        if (file_exists("tpl/tpl_$template.php")) {
            ob_start();
            $eval = "unset(\$data);unset(\$template);unset(\$eval);";
            $_ = $data;
            foreach (array_keys($data) as $key)
                if (preg_match('/^[a-z\d_]+$/i', $key))
                    $eval .= "\$$key=\$_['$key'];";
            $eval .= "unset(\$_);require \"tpl/tpl_$template.php\";";
            eval($eval);
            return ob_get_clean();
        }

        return "";
    }

    protected function errorMsg($message, array $data=null) {
        if (in_array($this->action, array("thumb", "upload", "download", "downloadDir")))
            die($this->label($message, $data));
        if (($this->action === null) || ($this->action == "browser"))
            $this->backMsg($message, $data);
        else {
            $message = $this->label($message, $data);
            die(json_encode(array('error' => $message)));
        }
    }
}

?>