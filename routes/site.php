<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']],
    function () {



        Route::group(['namespace' => 'Site', 'middleware' => ['auth','VerifiedUser']], function () {

            Route::get('profile',function (){
                return 'you are authenticated';
            });
        });

        Route::group(['namespace' => 'Site', 'middleware' => 'auth'], function () {

            Route::post('verify-user/','VerificationCodeController@verify')->name('verify-user');
            Route::get('verify','VerificationCodeController@getVerifyPage')->name('get.verification.form');
        });

        Route::group(['namespace' => 'Site'/* ,'middleware' => 'guest' */], function () {
            Route::get('/','HomeController@home')->name('home');
            Route::get('category/{slug}','CategoryController@productsBySlug')->name('category');
            Route::get('product/{slug}', 'ProductController@productsBySlug')->name('product.details');

        });


        Route::group(['namespace' => 'Site', 'middleware' => 'auth'], function () {
            Route::post('wishlist', 'WishlistController@store')->name('wishlist.store');
            Route::delete('wishlist', 'WishlistController@destroy')->name('wishlist.destroy');
            Route::get('wishlist/products', 'WishlistController@index')->name('wishlist.products.index');
        });

    });

