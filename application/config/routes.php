<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['view/(:num)'] = 'PollController/view/$1';
$route['status/(:num)'] = 'PollController/status/$1';


$route['register'] = 'PagesController/register';
$route['login'] = 'PagesController/login';


$route['reload'] = "welcome/reloadPage";

$route['register/save'] = "UsersController/saveUser";
$route['login/auth'] = "UsersController/loginUser";

$route['vote/save'] = "VotesController/save";

$route['manage/login'] = "StaffController/login";
$route['manage/login/auth'] = "StaffController/loginUser";
$route['manage/save'] = "PollController/newPoll";
$route['manage/deletePoll'] = "PollController/deletePoll";
$route['manage/delete/all_users'] = "UsersController/deleteAllUsers";
$route['manage/reset/all_users'] = "UsersController/resetAllUserVotes";
$route['manage/edit/user'] = "UsersController/edit_user";
$route['manage/delete/user'] = "UsersController/delete_user";
$route['manage/search/user'] = "UsersController/search_user";

$route['publish'] = "PollController/publish";

$route['lagout'] = "UsersController/lagout";

$route['image/upload'] = "PollController/do_upload";

$route['manage/allusers'] = "UsersController/getAllUsers";
$route['manage/filterusers'] = "UsersController/filterUsers";
$route['manage/pagination/(:num)'] = "UsersController/pagination/$1";
$route['users/import'] = "CSV_import/upload_file";