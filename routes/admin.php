<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

// note that there is prefix named admin before that routes

Route::group(['prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']],
    function () {

        Route::group(['namespace' => 'Dashboard', 'middleware' => 'auth:admin' , 'prefix'=>'admin'], function () {

            Route::get('/', 'DashboardController@index')->name('admin.dashboard');
            Route::get('logout', 'LoginController@logout')->name('admin.logout');

            //////  shipping methods

            Route::group(['prefix' => 'settings'], function () {
                Route::get('shipping-method/{type}', 'SettingsController@editShippingMethods')->name('edit.shipping.methods');
                Route::put('shipping-method/{id}', 'SettingsController@updateShippingMethods')->name('update.shipping.methods');
            });

            ///// edit admin profile /////

            Route::group(['prefix' => 'profile'], function () {
                Route::get('edit', 'ProfileController@editProfile')->name('edit.profile');
                Route::put('update', 'ProfileController@updateProfile')->name('update.profile');
//                Route::put('update/password', 'ProfileController@updatePassword')->name('update.profile.password');
            });
        });

        Route::group(['namespace' => 'Dashboard', 'middleware' => 'guest:admin' , 'prefix'=>'admin'], function () {

            Route::get('login', 'LoginController@login')->name('admin.login');
            Route::post('login', 'LoginController@postLogin')->name('admin.post.login');


        });


    });
