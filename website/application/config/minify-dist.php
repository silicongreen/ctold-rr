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
$config['minify_lib_path']         = APPPATH.'/libraries/minify_2.1.3/lib/';
$config['use_minify']             = TRUE;
$config['use_ci_cache']             = FALSE; // set to TRUE if you use phil's cache lib
$config['use_min_cache']         = FALSE;

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
$config['css_local_path']         = FCPATH.'/styles/';
$config['css_cache_path']         = APPPATH.'/cache/'; // for minify cache only
$config['css_cache_max_age']         = 3600 * 24 * 7 * 4; // 4 week cache header
$config['css_groups']            = Array(
                        'all.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                                    'themes/layout_fixed.css',
                                    'layouts/tdsfront/css/index.css',
                                    'layouts/tdsfront/css/jquery/smoothness/jquery-ui-1.10.3.custom.min.css',
                                    'layouts/tdsfront/css/jquery.bxslider.css',
                                    'layouts/tdsfront/css/datepicker.css',
                                    'layouts/tdsfront/css/custom_datepicker.css',
                                    'layouts/tdsfront/css/jquery.scrollUp_custom.css',                           
                                    'layouts/tdsfront/css/yaml/core/base.min.css',
                                    'plugins/fancybox/fancybox.css',
                                    'layouts/tdsfront/css/style.css',
                                    'layouts/tdsfront/css/captionjs.min.css',
                                    'layouts/tdsfront/css/ticker-style.css',
                                    'layouts/tdsfront/css/ticker-style-custom.css',
                                    'layouts/tdsfront/widgets/newstricker/css/component.css',
                                    'layouts/tdsfront/widgets/newstricker/css/custom_component.css',
                                    'layouts/tdsfront/widgets/carouselblock/carrosel.css',
                                    'layouts/tdsfront/widgets/carouselblock/custom_carrosel.css',
                                    'layouts/tdsfront/widgets/polls/polls.css',
                                    'layouts/tdsfront/widgets/weekly/ImageOverlay.css',
                                    'layouts/tdsfront/widgets/innermiddlenewsblock/innermiddlenewsblock.css'
                        ),
                        'example2.css?v='.$config['CSS_JS_V'] => Array( // when loading example.com/css/example2.css you will load stylesheet2.css and stylesheet3.css into one single file
                                    'stylesheet2.css',
                                    'stylesheet3.css',
                        ),
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
                        'index.js' => Array( // when loading example.com/js/example1.js you will load javascript1.js and javascript2.js into one single file
                                'scripts/jquery/jquery.js',
                                'scripts/layouts/tdsfront/js/bootstrap-datepicker.js',
                                'scripts/jquery/jquery.carouFredSel-6.2.1-packed.js',
                                'scripts/layouts/tdsfront/js/jquery.ticker.js',
                                'scripts/layouts/tdsfront/js/newstricker.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp.min.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp_custom.js',                            
                                'gallery/html5gallery.js',
                                'scripts/jquery/jquery.lazyload.js',
                                'scripts/fancybox/fancybox.js',
                                'scripts/jquery/imgLiquid-min.js',
                                'scripts/layouts/tdsfront/widgets/weekly/weekly.js',
                                'scripts/layouts/tdsfront/widgets/polls/polls.js',
                                'scripts/layouts/tdsfront/js/lib.js',
                                'scripts/layouts/tdsfront/js/index.js',
                                'scripts/layouts/tdsfront/js/index-req.js'
                        ),
                        'inner.js' => Array( // when loading example.com/js/example1.js you will load javascript1.js and javascript2.js into one single file
                                 'scripts/jquery/jquery.js',
'in_picture/js/jquery.tn3lite.min.js',
                                'scripts/layouts/tdsfront/js/bootstrap-datepicker.js',
                                'scripts/jquery/jquery.carouFredSel-6.2.1-packed.js',
                                'scripts/layouts/tdsfront/js/jquery.ticker.js',
                                'scripts/layouts/tdsfront/js/newstricker.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp.min.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp_custom.js',                            
                                'gallery/html5gallery.js',
                                'scripts/jquery/jquery.lazyload.js',
                                'scripts/fancybox/fancybox.js',
                                'scripts/jquery/imgLiquid-min.js',
                                'scripts/layouts/tdsfront/js/jquery.bxslider.min.js',
//                                'scripts/jquery/jquery-ui.js',
                                'scripts/layouts/tdsfront/js/lib.js',
                                'scripts/layouts/tdsfront/js/index.js',
                                'scripts/layouts/tdsfront/js/index-req.js',
                                'scripts/layouts/tdsfront/widgets/innermiddlenewsblock/innermiddlenewsblock.js'
                        ),
                        'post.js' => Array( // when loading example.com/js/example1.js you will load javascript1.js and javascript2.js into one single file
                                'scripts/jquery/jquery.js',
                                'scripts/layouts/tdsfront/js/bootstrap-datepicker.js',
                                'scripts/layouts/tdsfront/js/jquery.ticker.js',
                                'scripts/layouts/tdsfront/js/newstricker.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp.min.js',
                                'scripts/layouts/tdsfront/js/jquery.scrollUp_custom.js',                            
                                'gallery/html5gallery.js',
                                'scripts/jquery/jquery.lazyload.js',
                                'scripts/fancybox/fancybox.js',
                                'scripts/jquery/imgLiquid-min.js',
                                'scripts/layouts/tdsfront/js/jquery.bxslider.min.js',
                                'scripts/layouts/tdsfront/js/lib.js',
                                'scripts/post/scripts.js'
                        ),
                        'extra.js?v='.$config['CSS_JS_V'] => Array( // when loading example.com/js/example1.js you will load javascript1.js and javascript2.js into one single file
                                'scripts/layouts/tdsfront/js/jquery.fitvids.js',
                                'scripts/layouts/tdsfront/js/jquery.bxslider.min.js',
                                'scripts/jquery/jquery-ui.js',
                                //'styles/layouts/tdsfront/css/yaml/core/js/yaml-focusfix.js',
                                
                                'styles/layouts/tdsfront/widgets/newstricker/js/modernizr.custom.js',
                                'scripts/layouts/tdsfront/widgets/innermiddlenewsblock/jquery.ImageOverlay.min.js',
                                'scripts/layouts/tdsfront/widgets/innermiddlenewsblock/innermiddlenewsblock.js',
                                'scripts/layouts/tdsfront/js/index.js',
                                //'scripts/post/jquery.social.share.2.0.js',
                                'scripts/post/scripts.js'
                        ),
                        'ie9.js?v='.$config['CSS_JS_V'] => Array( // when loading example.com/js/example2.js you will load javascript2.js and javascript3.js into one single file
                                'scripts/layouts/tdsfront/js/lib/html5shiv/html5shiv.js',
                        ),
);