<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Minify config
 *
 * This code use Minify libs : http://code.google.com/p/minify/
 *
 * @author              Spir
 * @license             MIT
 * @version             0.2
 */

/*
|--------------------------------------------------------------------------
| Minify settings
|--------------------------------------------------------------------------
|
| minify_lib_path : Path to Minify folder
| use_minify : set if minify is used or not
|
*/
$config['minify_lib_path']        = APPPATH.'/libraries/minify_2.1.3/lib/';

$config['use_js_compress']        = TRUE;
$config['use_css_compress']        = TRUE;

$config['CSS_JS_V'] = 1;	

/*
|--------------------------------------------------------------------------
| CSS settings
|--------------------------------------------------------------------------
|
| css_route_segment : route segment to handle CSS files. exeample: mysite.com/css/myfile.css would be 'css'
| css_local_path : Path to your CSS folder root
| css_cache_path : path to CSS cache folder (usually application/cache/)
| css_cache_max_age: header expires
| css_groups: files name and path (relative to css_local_path)
|               do not names a group like a file!
|
*/
$config['css_route_segment']         = 'css';
$config['css_local_path']         = FCPATH.'/';
$config['css_cache_path']         = APPPATH.'/cache/'; // for minify cache only
$config['css_cache_max_age']      = 3600 * 24 * 7 * 4; // 4 week cache header
$config['css_groups']            = Array(
                        'champs21.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                                    'merapi/style/bootstrap.css',
                                    'merapi/style/plugin.css',
                                    'merapi/style/style.css',
                                    'merapi/style/font.css',
                                    'merapi/css.css'
                        ),
                        'styles.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                                    'merapi/style/styles.css',
                                    'styles/layouts/tdsfront/css/fonts.css',
                                    'styles/layouts/tdsfront/css/freefeed.css',
                        ),
                        'sidebar.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                                    'Profiler/sidebar.css',
                                    'Profiler/theme.css',
                                    'Profiler/template.css',
                                    'Profiler/megamenu.css',
                                    'Profiler/megamenu-theme.css',
                                    'Profiler/custom_theme.css',
                                    'styles/plugins/fancybox/fancybox.css',
                                    'merapi/style/header-icon-animation.css'
                        )
);

/*
|--------------------------------------------------------------------------
| JS settings
|--------------------------------------------------------------------------
|
| js_route_segment : route segment to handle javascript files. exeample: mysite.com/js/myfile.js would be 'js'
| js_local_path : Path to your javascript folder root
| js_cache_path : path to javascript cache folder (usually application/cache/)
| js_cache_max_age: header expires
| js_groups: files name and path (relative to js_local_path)
|              do not names a group like a file!
|
*/
$config['js_route_segment']         = 'js';
$config['js_local_path']         = FCPATH.'/';
$config['js_cache_path']         = APPPATH.'/cache/'; // for minify cache only
$config['js_cache_max_age']         = 3600 * 24 * 7 * 4; // 4 week cache header
$config['js_groups']            = Array(
                        
                        'top-main.js' => Array( // when loading example.com/js/example1.js you will load javascript1.js and javascript2.js into one single file
                                'scripts/jquery/jquery-min.js',
                                'scripts/jquery/jqueryui-min.js',
                                'merapi/script/jquery.imagesloaded.js',
                                'merapi/script/jquery.masonry.js',
                                'merapi/script/jquery.masonry.ordered.js',
                                'merapi/jquery-migrate.js',
                                'merapi/pluginsHead.js',
                                'scripts/layouts/tdsfront/js/bootstrap-datepicker.js',
                                'merapi/script/flexcroll.js',
                                //'merapi/script/snowstorm.js',//THIS IS FOR ONLY SNOW FALL EFFECT
                        ),
                        'main-bottom.js' => Array( // when loading example.com/js/example1.js you will load javascript1.js and javascript2.js into one single file
                                'merapi/main.js',
                                'merapi/scripts.js',                            
                                'merapi/devicepx-jetpack.js',
                                'merapi/pluginsFoot.js',
                                'Profiler/jquery.form.min.js',
                                'Profiler/bootstrap.js',
                                'Profiler/jquery.easing.min.js',
                                'Profiler/jquery.tinyscrollbar.min.js',
                                'Profiler/custom_theme.js',
                                'Profiler/menu.js',
                                'Profiler/profile_script_resize.js',
                                'scripts/jquery/jquery.carouFredSel-6.2.1-packed.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp.min.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp_custom.js',
                                'scripts/fancybox/fancybox.js',
                                'scripts/layouts/tdsfront/js/lib.js',
                                'scripts/layouts/tdsfront/js/index.js',
                                'scripts/layouts/tdsfront/js/index-req.js',
                                'scripts/jquery/jquery.tree.js',
                                'scripts/custom/customTree.js',
                                'scripts/layouts/tdsfront/js/jquery.liteuploader.js',
                                'gallery/html5gallery.js',
                        )
                        
);
