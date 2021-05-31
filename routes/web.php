<?php

use ArielMejiaDev\LarapexCharts\LarapexChart;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

/* start routes for Charts*/
Route::post('/chart/{sku}/{filter}', 'ChartsController@Chart');

Route::get('/search', 'FiltersAndSearchController@index');

Route::any('/searching', 'FiltersAndSearchController@search');

Route::get('/chart_filter', 'ChartsController@ChartWithFilter');

Route::get('/get_chart_excel', 'FiltersAndSearchController@downloadAsExcel');

Route::get('/get_concentration', 'FiltersAndSearchController@GetConcentration');

Route::get('/get_size', 'FiltersAndSearchController@GetSize');

Route::get('/get_color', 'FiltersAndSearchController@GetColor');

Route::get('/get_texture', 'FiltersAndSearchController@GetTexture');

Route::get('/get_skin_type', 'FiltersAndSearchController@GetSkinType');

Route::get('/get_area_of_apply', 'FiltersAndSearchController@GetAreaOfApply');

Route::any('/data', 'ChartsController@anyData');

/* end routes for Charts*/

/* start routes for User Management*/
Route::get('/user_management', 'UserMangementController@index');

Route::get('/add_user', 'UserMangementController@AddUser');

Route::post('/store_user', 'UserMangementController@StoreUser');

Route::get('/edit_user', 'UserMangementController@EditUser');

Route::post('/update_user', 'UserMangementController@UpdateUser');

Route::post('/delete_user/{user_id}', 'UserMangementController@destroy');

/* end routes for User Management*/



/* start routes for Profile Management*/
Route::get('/profile', 'UserMangementController@profile');

Route::post('/update_user_profile', 'UserMangementController@UpdateUserProfile');

/* end routes for Profile Management*/
