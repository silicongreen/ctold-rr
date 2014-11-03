-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 07, 2014 at 02:53 PM
-- Server version: 5.1.50
-- PHP Version: 5.3.9-ZS5.6.0

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `champs21_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `optimized_images`
--

DROP TABLE IF EXISTS `optimized_images`;
CREATE TABLE IF NOT EXISTS `optimized_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=76115 ;

--
-- Dumping data for table `optimized_images`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_model_hit`
--

DROP TABLE IF EXISTS `post_model_hit`;
CREATE TABLE IF NOT EXISTS `post_model_hit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hit` int(11) NOT NULL,
  `hit_date` date NOT NULL,
  `from_data` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15927 ;

--
-- Dumping data for table `post_model_hit`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_ad`
--

DROP TABLE IF EXISTS `tds_ad`;
CREATE TABLE IF NOT EXISTS `tds_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `url_link` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Ad Types:\n1 - Image\n2 - HTML\n3 - Video',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `total_view` bigint(20) NOT NULL DEFAULT '0',
  `total_click` bigint(20) NOT NULL DEFAULT '0',
  `is_active` tinyint(4) NOT NULL DEFAULT '0',
  `location` varchar(50) DEFAULT NULL,
  `country_iso` char(2) DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `age_from` tinyint(2) DEFAULT NULL,
  `age_to` tinyint(2) DEFAULT NULL,
  `user_group` varchar(255) DEFAULT NULL,
  `html_code` longtext,
  `image_path` varchar(255) DEFAULT NULL,
  `imagethumb_path` varchar(255) DEFAULT NULL,
  `gmt_offset` varchar(15) DEFAULT NULL,
  `disallow_controller` varchar(255) DEFAULT NULL,
  `for_all` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '200',
  `plan_id` int(11) NOT NULL,
  `sponser_id` int(11) NOT NULL DEFAULT '1',
  `menu_ci_key` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `foreign_users_idx` (`user_id`),
  KEY `foreign_plan_idx` (`plan_id`),
  KEY `foreign_sponser_idx` (`sponser_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=80 ;

--
-- Dumping data for table `tds_ad`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_ad_menu`
--

DROP TABLE IF EXISTS `tds_ad_menu`;
CREATE TABLE IF NOT EXISTS `tds_ad_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_menu_idx` (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tds_ad_menu`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_ad_plan`
--

DROP TABLE IF EXISTS `tds_ad_plan`;
CREATE TABLE IF NOT EXISTS `tds_ad_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `d_width` varchar(4) DEFAULT NULL,
  `d_height` varchar(4) DEFAULT NULL,
  `block` tinyint(3) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `cost` mediumtext NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `qty` int(11) NOT NULL DEFAULT '1',
  `link_location` varchar(20) NOT NULL DEFAULT 'index',
  `priority` int(11) NOT NULL DEFAULT '200',
  `is_ok` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `block_id` (`block`,`is_active`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `tds_ad_plan`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_ad_section`
--

DROP TABLE IF EXISTS `tds_ad_section`;
CREATE TABLE IF NOT EXISTS `tds_ad_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_menu_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `ad_plan_id` int(11) NOT NULL,
  `menu_ci_key` varchar(50) NOT NULL DEFAULT '0',
  `location` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `tds_ad_section`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_ad_sponsers`
--

DROP TABLE IF EXISTS `tds_ad_sponsers`;
CREATE TABLE IF NOT EXISTS `tds_ad_sponsers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sponser_name` varchar(100) DEFAULT NULL,
  `sponser_url` varchar(255) DEFAULT NULL,
  `sponser_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tds_ad_sponsers`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_ad_track`
--

DROP TABLE IF EXISTS `tds_ad_track`;
CREATE TABLE IF NOT EXISTS `tds_ad_track` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT '0',
  `ip_address` varchar(15) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`,`user_id`),
  KEY `ad_id_2` (`ad_id`,`ip_address`),
  KEY `foreign_user_idx` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_ad_track`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_bylines`
--

DROP TABLE IF EXISTS `tds_bylines`;
CREATE TABLE IF NOT EXISTS `tds_bylines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `is_columnist` tinyint(4) DEFAULT '0' COMMENT 'Whether the Byliner is a columnist',
  `priority` int(11) DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `is_feature` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_bylines`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_captcha`
--

DROP TABLE IF EXISTS `tds_captcha`;
CREATE TABLE IF NOT EXISTS `tds_captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `word` varchar(20) NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_captcha`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_categories`
--

DROP TABLE IF EXISTS `tds_categories`;
CREATE TABLE IF NOT EXISTS `tds_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `embedded` text NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `menu_icon` varchar(255) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0- disable 1= display',
  `parent_id` int(11) DEFAULT NULL,
  `category_type_id` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL COMMENT 'For Category sorting',
  `background_color` varchar(7) NOT NULL DEFAULT '003773',
  `enable_sort` tinyint(1) NOT NULL DEFAULT '1',
  `weekly_priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`parent_id`),
  KEY `foreign_category_type_idx` (`category_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tds_categories`
--

INSERT INTO `tds_categories` (`id`, `name`, `description`, `embedded`, `cover`, `icon`, `menu_icon`, `status`, `parent_id`, `category_type_id`, `priority`, `background_color`, `enable_sort`, `weekly_priority`) VALUES
(1, 'Game Zone', 'Game and game reviews', '', 'upload/gallery/image/category/hydrangeas.jpg', 'upload/gallery/image/category/chrysanthemum.jpg', 'upload/gallery/image/category/lighthouse.jpg', 1, NULL, 1, NULL, '', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tds_category_cover`
--

DROP TABLE IF EXISTS `tds_category_cover`;
CREATE TABLE IF NOT EXISTS `tds_category_cover` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_category_cover`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_category_pdf`
--

DROP TABLE IF EXISTS `tds_category_pdf`;
CREATE TABLE IF NOT EXISTS `tds_category_pdf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_category_pdf`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_category_type`
--

DROP TABLE IF EXISTS `tds_category_type`;
CREATE TABLE IF NOT EXISTS `tds_category_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(45) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1' COMMENT '0 - inactive\n1 - active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tds_category_type`
--

INSERT INTO `tds_category_type` (`id`, `type_name`, `is_active`) VALUES
(1, 'Daily', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tds_channels`
--

DROP TABLE IF EXISTS `tds_channels`;
CREATE TABLE IF NOT EXISTS `tds_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `logo` varchar(150) DEFAULT NULL,
  `is_active` tinyint(2) DEFAULT '1' COMMENT '1 - Active, 0 - Inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_channels`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_controllers`
--

DROP TABLE IF EXISTS `tds_controllers`;
CREATE TABLE IF NOT EXISTS `tds_controllers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `controller` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `allow_from_all` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Must Have an Criteria, 1 - Allow from all',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_name` (`name`),
  UNIQUE KEY `unq_controller` (`controller`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_controllers`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_functions`
--

DROP TABLE IF EXISTS `tds_functions`;
CREATE TABLE IF NOT EXISTS `tds_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `function` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `controller_id` int(11) DEFAULT NULL,
  `allow_from_all` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Must Have an Criteria, 1 - Allow from all',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_functions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_gallery`
--

DROP TABLE IF EXISTS `tds_gallery`;
CREATE TABLE IF NOT EXISTS `tds_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_name` varchar(100) NOT NULL,
  `gallery_type` tinyint(4) NOT NULL COMMENT 'Come from Codeigniter Config file',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gallery_name_UNIQUE` (`gallery_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tds_gallery`
--

INSERT INTO `tds_gallery` (`id`, `gallery_name`, `gallery_type`) VALUES
(1, 'Category', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tds_groups`
--

DROP TABLE IF EXISTS `tds_groups`;
CREATE TABLE IF NOT EXISTS `tds_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `tds_groups`
--

INSERT INTO `tds_groups` (`id`, `name`, `description`, `created`, `updated`) VALUES
(6, 'System', 'Main Admin Of champs21', '2014-07-06 12:46:17', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tds_groups_controllers`
--

DROP TABLE IF EXISTS `tds_groups_controllers`;
CREATE TABLE IF NOT EXISTS `tds_groups_controllers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `controller_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `controller_id` (`controller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_groups_controllers`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_groups_functions`
--

DROP TABLE IF EXISTS `tds_groups_functions`;
CREATE TABLE IF NOT EXISTS `tds_groups_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `function_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `function_id` (`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_groups_functions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_groups_users`
--

DROP TABLE IF EXISTS `tds_groups_users`;
CREATE TABLE IF NOT EXISTS `tds_groups_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `tds_groups_users`
--

INSERT INTO `tds_groups_users` (`id`, `group_id`, `user_id`, `created`, `updated`) VALUES
(32, 6, 1, '2014-07-06 12:46:37', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tds_homepage_data`
--

DROP TABLE IF EXISTS `tds_homepage_data`;
CREATE TABLE IF NOT EXISTS `tds_homepage_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_homepage_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_inpictures_author`
--

DROP TABLE IF EXISTS `tds_inpictures_author`;
CREATE TABLE IF NOT EXISTS `tds_inpictures_author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profession` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tds_inpictures_author`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_inpictures_photos`
--

DROP TABLE IF EXISTS `tds_inpictures_photos`;
CREATE TABLE IF NOT EXISTS `tds_inpictures_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `theme_id` int(255) NOT NULL,
  `photo_caption` text NOT NULL,
  `date_taken` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tds_inpictures_photos`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_inpictures_theme`
--

DROP TABLE IF EXISTS `tds_inpictures_theme`;
CREATE TABLE IF NOT EXISTS `tds_inpictures_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `publish_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_current` tinyint(4) NOT NULL DEFAULT '0',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tds_inpictures_theme`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_issue_date`
--

DROP TABLE IF EXISTS `tds_issue_date`;
CREATE TABLE IF NOT EXISTS `tds_issue_date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_issue_date`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_keywords`
--

DROP TABLE IF EXISTS `tds_keywords`;
CREATE TABLE IF NOT EXISTS `tds_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_keywords`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_materials`
--

DROP TABLE IF EXISTS `tds_materials`;
CREATE TABLE IF NOT EXISTS `tds_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_url` varchar(200) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `imagedate` date NOT NULL,
  `caption` text NOT NULL,
  `source` varchar(100) NOT NULL,
  `video_id` int(11) NOT NULL DEFAULT '0',
  `menu_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `foreign_gallery_id_idx` (`gallery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tds_materials`
--

INSERT INTO `tds_materials` (`id`, `material_url`, `gallery_id`, `imagedate`, `caption`, `source`, `video_id`, `menu_id`) VALUES
(1, 'upload/gallery/image/category/chrysanthemum.jpg', 1, '2014-07-07', '', '', 0, 0),
(2, 'upload/gallery/image/category/hydrangeas.jpg', 1, '2014-07-07', '', '', 0, 0),
(3, 'upload/gallery/image/category/lighthouse.jpg', 1, '2014-07-07', '', '', 0, 0),
(4, 'upload/gallery/image/category/koala.jpg', 1, '2014-07-07', '', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tds_materials_video`
--

DROP TABLE IF EXISTS `tds_materials_video`;
CREATE TABLE IF NOT EXISTS `tds_materials_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` varchar(255) NOT NULL,
  `video_type` char(8) NOT NULL,
  `thumb_small` varchar(255) NOT NULL DEFAULT '',
  `thumb_medium` varchar(255) NOT NULL DEFAULT '',
  `thumb_large` varchar(255) NOT NULL DEFAULT '',
  `additional_thumb` varchar(255) NOT NULL DEFAULT '',
  `video_title` varchar(500) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `duration` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `video_cat` varchar(300) NOT NULL,
  `tags` text NOT NULL,
  `material_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `video_type` (`video_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_materials_video`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_material_menu`
--

DROP TABLE IF EXISTS `tds_material_menu`;
CREATE TABLE IF NOT EXISTS `tds_material_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `sname` varchar(255) DEFAULT NULL,
  `issue_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_material_menu`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_menu`
--

DROP TABLE IF EXISTS `tds_menu`;
CREATE TABLE IF NOT EXISTS `tds_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL COMMENT '1 - Category\n, 2 - Text\n, 3 - Icon\n, 4 - News\n',
  `title` varchar(80) DEFAULT NULL COMMENT 'If category then Category Name otherwise Menu title, if Icon then Menu title will be blank',
  `icon_name` varchar(20) DEFAULT NULL COMMENT 'If Menu type is Icon otherwise NULL',
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '0' COMMENT '0 - Inactive\n, 1 - Active',
  `has_right` tinyint(4) NOT NULL DEFAULT '1',
  `full_custom` tinyint(4) NOT NULL DEFAULT '0',
  `all_ad` tinyint(4) NOT NULL DEFAULT '1',
  `position` tinyint(4) DEFAULT NULL COMMENT '1 - Header, \n2 - Footer',
  `footer_group` varchar(20) DEFAULT NULL COMMENT 'Footer Group will come from our CI config',
  `ci_key` varchar(255) DEFAULT NULL COMMENT 'CI action name == <Controller_name>/<Action_name>/<Param 1>/<Param 2>/...<Param N>',
  `link_type` enum('_blank','_self') DEFAULT NULL,
  `link_text` longtext,
  `permalink` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `news_id` int(11) DEFAULT NULL,
  `news_num` int(11) DEFAULT '2' COMMENT 'Number of latest news to shown',
  `priority` tinyint(4) NOT NULL DEFAULT '1',
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `expired` date DEFAULT NULL,
  `parent_menu_id` int(11) DEFAULT NULL,
  `gallery_name` varchar(255) DEFAULT NULL,
  `has_gallery` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - Gallery exists for the Menu for 0 No Gallery for the menu',
  `show_gallery` tinyint(4) NOT NULL DEFAULT '1',
  `ad_plan_id_for_gallery` int(11) NOT NULL DEFAULT '0',
  `twitter_name` varchar(255) DEFAULT NULL,
  `widget_id` bigint(20) NOT NULL DEFAULT '0',
  `has_ad` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ci_key` (`type`,`position`,`ci_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150 ;

--
-- Dumping data for table `tds_menu`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_options`
--

DROP TABLE IF EXISTS `tds_options`;
CREATE TABLE IF NOT EXISTS `tds_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ques_id` int(11) DEFAULT NULL,
  `value` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_options`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_personality`
--

DROP TABLE IF EXISTS `tds_personality`;
CREATE TABLE IF NOT EXISTS `tds_personality` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - Active, 0 - 1 - Inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `tds_personality`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post`
--

DROP TABLE IF EXISTS `tds_post`;
CREATE TABLE IF NOT EXISTS `tds_post` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shoulder` varchar(100) DEFAULT NULL,
  `headline` varchar(150) NOT NULL,
  `headline_color` char(6) NOT NULL DEFAULT '0',
  `sub_head` varchar(255) DEFAULT NULL,
  `byline_id` int(11) DEFAULT NULL,
  `summary` text,
  `embedded` text,
  `short_title` varchar(80) DEFAULT NULL,
  `google_short_url` varchar(255) DEFAULT NULL COMMENT 'Short URL generated by google',
  `content` longtext NOT NULL,
  `can_comment` tinyint(4) DEFAULT '1' COMMENT 'Whether User can comment or not',
  `lead_material` varchar(255) DEFAULT NULL COMMENT 'For carrosel News, Lead Material image is required',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 - Draft\n, 2 - Created, \n3 - Updated\n, 4 - Reviewed\n, 5 - Published\n, 6 - Delete',
  `for_all` tinyint(4) NOT NULL DEFAULT '1',
  `for` int(11) NOT NULL,
  `type` enum('Print','Online') NOT NULL DEFAULT 'Print',
  `is_featured` tinyint(4) DEFAULT NULL,
  `is_breaking` tinyint(4) DEFAULT NULL,
  `breaking_expire` timestamp NULL DEFAULT NULL,
  `is_developing` tinyint(4) DEFAULT NULL,
  `is_exclusive` tinyint(4) DEFAULT NULL,
  `exclusive_expired` timestamp NULL DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `view_count` bigint(20) DEFAULT '0',
  `published_date` timestamp NULL DEFAULT NULL,
  `publish_date_only` date DEFAULT NULL,
  `priority_type` tinyint(4) DEFAULT '5' COMMENT 'Priorirty Types are:\n1 - Carrosel News\n2 - Main News\n3 - Other Homepage News\n4 - More News\n5 - All other news',
  `priority` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `has_image` tinyint(1) NOT NULL DEFAULT '0',
  `has_video` tinyint(1) NOT NULL DEFAULT '0',
  `has_pdf` tinyint(1) NOT NULL DEFAULT '0',
  `has_related_news` tinyint(1) NOT NULL DEFAULT '0',
  `news_expire_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lead_caption` text,
  `lead_source` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `ip_address` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_category`
--

DROP TABLE IF EXISTS `tds_post_category`;
CREATE TABLE IF NOT EXISTS `tds_post_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  `inner_priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `foreign_category_id_idx` (`category_id`),
  KEY `foreign_post_id_idx` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_category`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_class`
--

DROP TABLE IF EXISTS `tds_post_class`;
CREATE TABLE IF NOT EXISTS `tds_post_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_class`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_gallery`
--

DROP TABLE IF EXISTS `tds_post_gallery`;
CREATE TABLE IF NOT EXISTS `tds_post_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `caption` text,
  `source` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_post_id_idx` (`post_id`),
  KEY `foreign_material_id_idx` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_gallery`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_keyword`
--

DROP TABLE IF EXISTS `tds_post_keyword`;
CREATE TABLE IF NOT EXISTS `tds_post_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_keyword`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_statistic`
--

DROP TABLE IF EXISTS `tds_post_statistic`;
CREATE TABLE IF NOT EXISTS `tds_post_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `home_or_abroad` tinyint(4) NOT NULL DEFAULT '1',
  `country` varchar(255) NOT NULL DEFAULT 'Bangladesh',
  `city` varchar(255) NOT NULL DEFAULT 'Dhaka',
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_statistic`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_tags`
--

DROP TABLE IF EXISTS `tds_post_tags`;
CREATE TABLE IF NOT EXISTS `tds_post_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_tags_idx` (`tag_id`),
  KEY `foreign_post_idx` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_post_user_activity`
--

DROP TABLE IF EXISTS `tds_post_user_activity`;
CREATE TABLE IF NOT EXISTS `tds_post_user_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `operation_type` tinyint(4) DEFAULT NULL COMMENT 'Come from Codeigniter Config.\n1 - Creation\n2 - Modification\n3 - Moderation\n4 - Publication\n5 - Deletion',
  `operation_date` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `user_agent` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `session_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user_idx` (`user_id`),
  KEY `foreign_post_idx` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_post_user_activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_priority_log`
--

DROP TABLE IF EXISTS `tds_priority_log`;
CREATE TABLE IF NOT EXISTS `tds_priority_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` text NOT NULL,
  `current_post_id` int(11) NOT NULL,
  `operation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1118 ;

--
-- Dumping data for table `tds_priority_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_questions`
--

DROP TABLE IF EXISTS `tds_questions`;
CREATE TABLE IF NOT EXISTS `tds_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ques` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_questions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_quotes`
--

DROP TABLE IF EXISTS `tds_quotes`;
CREATE TABLE IF NOT EXISTS `tds_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote` text NOT NULL,
  `personality_id` int(11) NOT NULL,
  `published_date` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0 - Inactive,1 - Active',
  `create_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `personality_id` (`personality_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

--
-- Dumping data for table `tds_quotes`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_related_news`
--

DROP TABLE IF EXISTS `tds_related_news`;
CREATE TABLE IF NOT EXISTS `tds_related_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) DEFAULT NULL,
  `new_link` varchar(255) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `published_date` varchar(20) NOT NULL,
  `related_type` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `foreign_post_idx` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_related_news`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_sessions`
--

DROP TABLE IF EXISTS `tds_sessions`;
CREATE TABLE IF NOT EXISTS `tds_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tds_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_settings`
--

DROP TABLE IF EXISTS `tds_settings`;
CREATE TABLE IF NOT EXISTS `tds_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` varchar(45) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `description` varchar(275) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_UNIQUE` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_tags`
--

DROP TABLE IF EXISTS `tds_tags`;
CREATE TABLE IF NOT EXISTS `tds_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tags_name` varchar(200) NOT NULL,
  `hit_count` bigint(20) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_name_UNIQUE` (`tags_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_twitter_settings`
--

DROP TABLE IF EXISTS `tds_twitter_settings`;
CREATE TABLE IF NOT EXISTS `tds_twitter_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `consumer_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `consumer_secret` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_twitter_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_users`
--

DROP TABLE IF EXISTS `tds_users`;
CREATE TABLE IF NOT EXISTS `tds_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `salt` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tds_users`
--

INSERT INTO `tds_users` (`id`, `username`, `password`, `email`, `name`, `salt`, `created`, `updated`) VALUES
(1, 'admin', '7343d13791e879cb1f93a321b80b5ef8cd973be9850833ab3598a05d0ef41951c30d2214505a1d8343548466d51ff29d4c22fa11c90b3b50f1c97c130b2b9527', 'fahim@gmail.com', 'Fahim Md. Chowdhury', 'a9ec974187cdcc52f5588e08433fd4af', '2014-07-06 12:46:37', '2014-07-06 12:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `tds_voice_box`
--

DROP TABLE IF EXISTS `tds_voice_box`;
CREATE TABLE IF NOT EXISTS `tds_voice_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personality_id` int(11) NOT NULL,
  `voice` varchar(255) DEFAULT NULL,
  `published_date` datetime DEFAULT NULL,
  `is_active` varchar(255) NOT NULL DEFAULT '1' COMMENT '1 - Active, 0 - Inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_voice_box`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_votes`
--

DROP TABLE IF EXISTS `tds_votes`;
CREATE TABLE IF NOT EXISTS `tds_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `voted_on` datetime NOT NULL,
  `ip` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_votes`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_whats_on`
--

DROP TABLE IF EXISTS `tds_whats_on`;
CREATE TABLE IF NOT EXISTS `tds_whats_on` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) NOT NULL,
  `program_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - TV Program, 2 - Other Program',
  `program_details` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - Active, 0 - Inactive',
  PRIMARY KEY (`id`),
  KEY `fk_category_id` (`category_id`),
  KEY `fk_channel_id` (`channel_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_whats_on`
--


-- --------------------------------------------------------

--
-- Table structure for table `tds_widget`
--

DROP TABLE IF EXISTS `tds_widget`;
CREATE TABLE IF NOT EXISTS `tds_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `widget_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `widget_type` enum('Tab','Category','Twitter','Cartoon') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Tab',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `has_ad_before` tinyint(1) NOT NULL DEFAULT '1',
  `has_ad_after` tinyint(1) NOT NULL DEFAULT '1',
  `ad_plan_id_top` int(11) NOT NULL DEFAULT '0',
  `ad_plan_id_bottom` int(11) NOT NULL DEFAULT '0',
  `news_qty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tds_widget`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `tds_categories`
--
ALTER TABLE `tds_categories`
  ADD CONSTRAINT `tds_categories_ibfk_1` FOREIGN KEY (`category_type_id`) REFERENCES `tds_category_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tds_post_category`
--
ALTER TABLE `tds_post_category`
  ADD CONSTRAINT `tds_post_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tds_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tds_post_category_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `tds_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tds_post_gallery`
--
ALTER TABLE `tds_post_gallery`
  ADD CONSTRAINT `tds_post_gallery_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `tds_materials` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tds_post_gallery_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `tds_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tds_post_tags`
--
ALTER TABLE `tds_post_tags`
  ADD CONSTRAINT `tds_post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `tds_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tds_post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tds_tags` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tds_post_user_activity`
--
ALTER TABLE `tds_post_user_activity`
  ADD CONSTRAINT `tds_post_user_activity_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `tds_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tds_post_user_activity_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tds_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tds_related_news`
--
ALTER TABLE `tds_related_news`
  ADD CONSTRAINT `tds_related_news_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `tds_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `tds_whats_on`
--
ALTER TABLE `tds_whats_on`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `tds_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_channel_id` FOREIGN KEY (`channel_id`) REFERENCES `tds_channels` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
SET FOREIGN_KEY_CHECKS=1;
