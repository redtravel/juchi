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

$route['default_controller'] = "index";
$route['404_override'] = 'index/err404';

$route['activity'] = 'index/start_page';
$route['index-(.+)?'] = 'index/index/$1';
$route['help-(\d+)'] = 'article/help/$1';
$route['article-(\d+)'] = 'article/info/$1';

//现抢
$route['rushlist'] = 'index/index/$1';
$route['rush-(.+)'] = 'rush/index/$1.html';
//分类
$route['category-(.+)'] = 'category/index/$1.html';
$route['brand-(.+)'] = 'category/brand/$1.html';
$route['provider-(.+)'] = 'category/provider/$1.html';
//商品详情
$route['product-(.+)'] = 'product/info/$1.html';

$route['tuan-(.+)'] = 'tuan/index/$1.html';
$route['tuanDetail-(.+)'] = 'tuanDetail/info/$1.html';

//$route['brand-(.+)'] = 'product/brand/$1';
$route['search'] = 'product/search';
$route['searchResult'] = 'product/searchResult';
$route['pdetail-(\d+)'] = 'product/pdetail/$1.html';
$route['brands'] = 'product/brands';
$route['shops'] = 'product/shops';
$route['brandstory-(\d+)'] = 'product/brand_story/$1';
$route['user/register'] = 'user/login/register';
//导航
$route['boy'] = '/index/index/22';
$route['girl'] = '/index/index/23';
$route['baby'] = '/index/index/24';
//专题
$route['zhuanti/(:any)'] = 'zhuanti/index/$1';
$route['temaiqu'] = '/product/temaiqu';
/* End of file routes.php */
/* Location: ./application/config/routes.php */