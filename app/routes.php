<?php
defined('ORION') || exit('Acesso negado.');

$router->get('', 'LandingController@index');

$router->get('login',     'AuthController@showLogin');
$router->post('login',    'AuthController@login');
$router->post('logout',   'AuthController@logout');
$router->get('register',  'RegisterController@show');
$router->post('register', 'RegisterController@store');

$router->get('admin', 'DashboardController@index');

$router->get('admin/users',             'UserController@index');
$router->get('admin/users/create',      'UserController@create');
$router->post('admin/users',            'UserController@store');
$router->get('admin/users/{id}/edit',   'UserController@edit');
$router->post('admin/users/{id}',       'UserController@update');
$router->post('admin/users/{id}/delete','UserController@destroy');

$router->get('admin/movies',              'MovieController@index');
$router->get('admin/movies/create',       'MovieController@create');
$router->post('admin/movies',             'MovieController@store');
$router->get('admin/movies/{id}/edit',    'MovieController@edit');
$router->post('admin/movies/{id}',        'MovieController@update');
$router->post('admin/movies/{id}/delete', 'MovieController@destroy');

$router->get('browse',              'CatalogController@index');
$router->get('search',              'CatalogController@search');
$router->get('title/{id}',          'TitleController@show');
$router->get('title/{id}/watch',    'TitleController@watch');
$router->post('title/{id}/rent',    'RentalController@store');
$router->post('title/{id}/favorite','FavoriteController@toggle');

$router->get('rentals',             'RentalController@index');
$router->post('rentals/{id}/return','RentalController@returnIt');

$router->get('list',                'FavoriteController@index');

$router->get('account',  'AccountController@profile');
$router->post('account', 'AccountController@update');
$router->get('pricing',  'AccountController@pricing');
