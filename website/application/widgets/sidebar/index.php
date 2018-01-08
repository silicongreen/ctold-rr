<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Widget Plugin 
 * 
 * Install this file as application/plugins/widget_pi.php
 * 
 * @version:     0.1
 * $copyright     Copyright (c) Wiredesignz 2009-03-24
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class sidebar extends widget
{

    function run()
    {
        $data['menu'] = array(
            "User" => 
                array(
                        "User" => array("users"),
                        "Free User" => array("free_user"),
                        "User Group" => array("groups"), 
                        "Controllers" => array("controllers"), 
                        "Methods" => array("methods")
                     ),
            
            "Post Management" => 
                array(
                        "Category" => array("categories"),
                        "Sort Category" => array("categories", "sort_categories"),
                        "Author" => array("bylines"),
                        "Sort Authors" => array("bylines", "sort_bylines"),
                        "Posts" => array("news"),
                        "Add Post" => array("news","add"),
                        "Post Properties" => array("newsfeatures"),
                        "Editor Picks" => array("editorpicks"),
                        "Pin Post" => array("pinpost"),
                        "Feature Post" => array("featurepost"),
                        "Trash"    => array("news","trash"),
                     ),
             "Spelling Bee" => 
                array(
                        "Spelling Bee Word" => array("spellingbee"),
                        "Gallery" => array("galleries"),
                        "Sort Gallery" => array("galleries", "sort_galleries"),
                        "Gallery Photo" => array("gallery_photo"),
                        
                     ),
            "School Activities" => 
                array(
                    "Over All Activities"    => array("paidstatictis","overall"),
                    "Feature Activities"    => array("paidstatictis","overall_feature"),
                    "Activities"    => array("paidstatictis")
                ),
            "Post Arrangement" => 
                array(
                    "Arrange Category Posts"    => array("news","inner_news_arrangement"),
                    "Arrange Posts"    => array("news","news_arrangement")
                ),
            
            "School Management" => 
                array(
                    "School"    => array("school"),
                    "School Page"    => array("schoolpage"),
                    "Add ALL School Page"    => array("schoolpage","add_all"),
                    "School Activities"    => array("schoolactivities"),
                    "User Submitted School"    => array("userschool"),
                    "School Info"    => array("userschoolinfo"),
                    "Arrange School"    => array("school","sort_school")
                ),
            "Science Rocks" => 
                array(
                        "Question Topic" => array("sccategories"),
                        "Sort Topic" => array("sccategories", "sort_categories"),
                        "Level and Question" => array("topicandquestion"),
                        "Daily Dose" => array("dailydose"),
                        "Ask The Anchor" => array("asktheanchor"),
                        "Episode And Winner" => array("srwinner")
                        
                     ),
            "Assessment" => 
                array(
                    "Assessment"    => array("assessment"),
                ),
	    "Ads Management"=>array(
                    "Ads"=>array("ad"),
                    "Ad to Category"=>array("ad","adassignsection"),
                ),
            "Others"=>array(
                    //"Twitter"=>array("twitter"),
                    "General Knowledge"=>array("gk"),
                    "Gallery"=>array("gallery"),
                    "Layout"=>array("setting"),
                    "Doodle Upload"=>array("doodle"),
                    //"What to Watch "=>array("watch"),
                    //"Poll"=>array("polls"),
                    //"Voice Box"=>array("voice_box"),
                )
            
            );
        $this->render($data);
    }

}

