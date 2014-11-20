<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['admin'] = "admin/login";
$route['m'] = "m/mobile";

$route['default_controller'] = "front/home";
$route['contact-us'] = "front/home/contact_us";
$route['about-us'] = "front/home/about_us";
$route['privacy-policy'] = "front/home/privacy_policy";
$route['ano/(:any)'] = "ano/home";
$route['404_override'] = '';



$route['js/(:any)'] = "minify/$1"; 
$route['css/(:any)'] = "minify/$1";
$route['css1/(:any)'] = "front/ajax/minify_css/$1";
#$route['tds-adm-master/ad/datatable'] = "ad/datatable";
#$route['tds-adm-master/ad/add'] = "ad/add";
#$route['tds-adm-master/ad/delete'] = "ad/delete";
#$route['tds-adm-master/ad/edit/(:num)'] = "ad/edit/$1";




/* End of file routes.php */
/* Location: ./application/config/routes.php */