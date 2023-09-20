<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/test', 'Api\TestController@test');

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['api', 'ChangeLanguage', 'localize', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::group(['prefix' => 'v1/user'], function () {

            //---------------- Auth --------------------//
            Route::post('/register', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'register']);
            Route::post('/login', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'login']);

            //Password  Reset
            Route::post('/password/email','Api\v1\User\ForgotPasswordController@sendResetLinkEmail');


            //Print Receipt
                Route::get('/orders/{id}/receipt','User\OrderReceiptController@show');

                Route::get('/app_data', 'Api\v1\User\AppDataController@getAppData');

                Route::get('/categories', 'Api\v1\User\CategoryController@index');

                Route::get('/categories/{id}/shops', 'Api\v1\User\CategoryController@getShops');

                    //----------------- Banners ------------------------------//
                Route::get('/banners', 'Api\v1\User\BannerController@index');

                    //------------------ Category-----------------------//

                //Category product
                Route::get('/categories/{id}/products', 'Api\v1\User\CategoryController@getProducts');
                Route::get('/categories/{id}/subcategories', 'Api\v1\User\CategoryController@getSubcategories');
                Route::get('/shops', 'Api\v1\User\ShopController@index');



            //////////////////////////// Auth /////////////////////////////////////////////////////////////
            Route::group(['middleware' => ['auth:user-api']], function () {

                Route::post('/verify_mobile_number', 'Api\v1\User\AuthController@verifyMobileNumber');
                Route::post('/mobile_verified', 'Api\v1\User\AuthController@mobileVerified');

                Route::post('/delete', 'Api\v1\User\AuthController@delete');

                //---------------------------- App Data -------------------------//
                //App User Data
                Route::get('/app_data/user', 'Api\v1\User\AppDataController@getAppDataWithUser');



                //---------------------- Setting ----------------------------//
                Route::post('/update_profile', 'Api\v1\User\AuthController@updateProfile');



                //----------------- Home ------------------------------//
                Route::get('/home/{user_address_id}', 'Api\v1\User\HomeController@index');


                //----------------- Products ------------------------------//
                Route::get('/products', 'Api\v1\User\ProductController@index');
                Route::get('/products/{id}', 'Api\v1\User\ProductController@show');
                Route::get('/products/{id}/reviews', 'Api\v1\User\ProductController@showReviews');


                //----------------- Cart -------------------------------//
                Route::get('/carts', 'Api\v1\User\CartController@index');
                Route::post('/carts', 'Api\v1\User\CartController@store');
                Route::patch('/carts/{id}', 'Api\v1\User\CartController@update');
                Route::delete('/carts/{id}', 'Api\v1\User\CartController@destroy');


                //-------------------- Address ------------------------//
                Route::get('/addresses', 'Api\v1\User\UserAddressController@index');
                Route::post('/addresses', 'Api\v1\User\UserAddressController@store');
                Route::post('/addresses/{address_id}', 'Api\v1\User\UserAddressController@update');
                Route::delete('/addresses/{id}', 'Api\v1\User\UserAddressController@destroy');


                //--------------- Favourite ------------------------//
                Route::get('/favorites', 'Api\v1\User\FavoriteController@index');
                Route::post('/favorites', 'Api\v1\User\FavoriteController@store');
                Route::delete('/favorites', 'Api\v1\User\FavoriteController@destroy');



                //---------------------- Order -----------------------//
                Route::get('/orders',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'index']);
                Route::get('/orders/{id}',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'show']);
                Route::get('/orders/{id}/reviews',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'showReviews']);
                Route::patch('/orders/{id}',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'update']);
                Route::post('/orders',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'store']);
                Route::post('/orders/driver',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'storeDriverOrder']);
                Route::patch('/orders/{id}',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'update']);//index
                Route::post('/deliveryAssign',[\App\Http\Controllers\Api\v1\User\OrderController::class, 'deliveryAssign']);

                /*         Route::get('/orders', 'Api\v1\User\OrderController@index');
                Route::get('/orders/{id}', 'Api\v1\User\OrderController@show');
                Route::get('/orders/{id}/reviews', 'Api\v1\User\OrderController@showReviews');
                Route::patch('/orders/{id}', 'Api\v1\User\OrderController@update');
                Route::post('/orders', 'Api\v1\User\OrderController@store');
                Route::post('/orders/driver', 'Api\v1\User\OrderController@storeDriverOrder');
                Route::patch('/orders/{id}', 'Api\v1\User\OrderController@update');
                Route::post('/deliveryAssign', 'Api\v1\User\OrderController@deliveryAssign'); */


                //-------------------- Product Review ----------------------//
                Route::post('/product-reviews', 'Api\v1\User\ProductReviewController@store');

                //-------------------- Shop Review ----------------------//
                Route::post('/shop-reviews', 'Api\v1\User\ShopReviewController@store');

                //-------------------- Delivery Boy Review ----------------------//
                Route::post('/delivery-boy-reviews', 'Api\v1\User\DeliveryBoyReviewController@store');





                //---------------- Shop --------------------------//
                Route::get('/shops/addresses/{user_address_id}', 'Api\v1\User\ShopController@getShopsFromUserAddress');
                Route::get('/shops/{id}', 'Api\v1\User\ShopController@show');
                Route::get('/shops/{id}/coupons', 'Api\v1\User\ShopController@getCoupons');


                // ---------------------- Coupon ------------------------- //
                Route::get('/coupons', 'Api\v1\User\CouponController@index');


                //coupon for shop
                Route::get('/shop_coupon/{id}', 'Api\v1\User\UserCouponController@getForShop');



                //wallet coupon
                Route::get('/wallet-coupons','Api\v1\User\WalletCouponsController@index');
                Route::get('/userCoupons','Api\v1\User\WalletCouponsController@userCoupons');


                //------------- For Testing Purpose -----------------------//
                Route::get('/test', 'Api\v1\User\TestController@test');


            });

            Route::get('/maintenance', function () {
                return response(['message' => ['EMall is now online']], 200);
            });


        });

        Route::group(['prefix' => '/v1/delivery-boy'], function () {


            //App Data
            Route::get('/app_data', 'Api\v1\DeliveryBoy\AppDataController@getAppData');

            //---------------- Auth --------------------//
            Route::post('/register', [\App\Http\Controllers\Api\v1\DeliveryBoy\AuthController::class, 'register']);
            Route::post('/login', [\App\Http\Controllers\Api\v1\DeliveryBoy\AuthController::class, 'login']);

            //Password  Reset
            Route::post('/password/email','Api\v1\DeliveryBoy\ForgotPasswordController@sendResetLinkEmail');

            //Print Receipt
            Route::get('/orders/{id}/receipt','DeliveryBoy\OrderReceiptController@show');


            Route::group(['middleware' => ['auth:delivery-boy-api']], function () {

                Route::post('/verify_mobile_number', 'Api\v1\DeliveryBoy\AuthController@verifyMobileNumber');
                Route::post('/mobile_verified', 'Api\v1\DeliveryBoy\AuthController@mobileVerified');

                //App User Data
                Route::get('/app_data/delivery_boy', 'Api\v1\DeliveryBoy\AppDataController@getAppDataWithDeliveryBoy');


                //---------------------- Setting ----------------------------//
                Route::post('/update_profile', 'Api\v1\DeliveryBoy\AuthController@updateProfile');


                //------------------- Orders ---------------------------------//
                Route::get('/orders', 'Api\v1\DeliveryBoy\OrderController@index');

                // acceptDeclineOrder
                Route::post('/accept-decline-order', 'Api\v1\DeliveryBoy\OrderController@acceptOrder');

                Route::get('/orders/{id}', 'Api\v1\DeliveryBoy\OrderController@show');
                Route::post('/orders/{id}', 'Api\v1\DeliveryBoy\OrderController@update');

                //Reviews
                Route::get('/orders/{id}/reviews', 'Api\v1\DeliveryBoy\OrderController@showReviews');

                //---------------------- Revenue -----------------------------//
                Route::get('/revenues', 'Api\v1\DeliveryBoy\RevenueController@index');

                //Shop
                Route::get('/shop', 'Api\v1\DeliveryBoy\ShopController@index');


                //---------------------- Transactions -----------------------------//
                Route::get('/transactions', 'Api\v1\DeliveryBoy\TransactionController@index');



                //----------------------- Settings --------------------//
                Route::post('/change_status','Api\v1\DeliveryBoy\AuthController@changeStatus');

                //Reviews
                Route::get('/reviews', 'Api\v1\DeliveryBoy\ReviewController@index');

                //------------- For Testing Purpose -----------------------//
                Route::get('/test', 'Api\v1\DeliveryBoy\TestController@test');
            });

            Route::get('/maintenance', function () {
                return response(['message' => [env('APP_NAME').' is now online']], 200);
            });

        });

        Route::group(['prefix' => '/v1/manager'], function () {


            Route::get('/app_data', 'Api\v1\Manager\AppDataController@getAppData');


            //---------------- Auth --------------------//
            Route::post('/register', 'Api\v1\Manager\AuthController@register');
            Route::post('/login', 'Api\v1\Manager\AuthController@login');

            //Password  Reset
            Route::post('/password/email','Api\v1\Manager\ForgotPasswordController@sendResetLinkEmail');

            //Print Receipt
            Route::get('/orders/{id}/receipt','Manager\OrderReceiptController@show');


            //App Data
            Route::get('/app_data', 'Api\v1\Manager\AppDataController@getAppData');


            Route::group(['middleware' => ['auth:manager-api']], function () {


                Route::post('/verify_mobile_number', 'Api\v1\Manager\AuthController@verifyMobileNumber');
                Route::post('/mobile_verified', 'Api\v1\Manager\AuthController@mobileVerified');


                //---------------------------- App Data -------------------------//
                //App User Data
                Route::get('/app_data/manager', 'Api\v1\Manager\AppDataController@getAppDataWithManager');


                //---------------------- Setting ----------------------------//
                Route::post('/update_profile', 'Api\v1\DeliveryBoy\AuthController@updateProfile');


                //------------------- Orders ---------------------------------//
                Route::get('/orders', 'Api\v1\Manager\OrderController@index');
                Route::get('/orders/{id}', 'Api\v1\Manager\OrderController@show');
                Route::patch('/orders/{id}', 'Api\v1\Manager\OrderController@update');


                //Assign
                Route::get('/delivery_boys/assign/{order_id}','Api\v1\Manager\DeliveryBoyController@showForAssign');
                Route::post('/delivery_boys/assign/{order_id}/{delivery_boy_id}','Api\v1\Manager\DeliveryBoyController@assign');


                //------------------- Products ---------------------------------//
                Route::get('/products', 'Api\v1\Manager\ProductController@index');
                Route::post('/products', 'Api\v1\Manager\ProductController@store');

                //Edit
                Route::patch('/products/{id}','Api\v1\Manager\ProductController@update');


                //Product show
                Route::get('/products/{id}','Api\v1\Manager\ProductController@show');

                //Upload product image
                Route::post('/products/{id}/images','Api\v1\Manager\ProductImageController@store');



                //-------------------------------- Product Images --------------------------------//


                //Delete
                Route::delete('/productImages/{id}','Api\v1\Manager\ProductImageController@destroy');

                //-------------------------------- Shop --------------------------------//

                //Index
                Route::get('/shops','Api\v1\Manager\ShopController@index');
                Route::patch('/shops/{id}','Api\v1\Manager\ShopController@update');

                //Shop Reviews
                Route::get('/shops/reviews','Api\v1\Manager\ShopController@showReviews');



                //----------------------------- Delivery Boy ---------------------------------//

                //Manage
                Route::get('/delivery_boys/get_all','Api\v1\Manager\DeliveryBoyController@getAll');


                //--------------------------- Coupons -------------------------------------------//
                Route::get('/coupons','Api\v1\Manager\ShopCouponController@index');
                Route::patch('/coupons/{id}','Api\v1\Manager\ShopCouponController@update');



                //--------------------------------- Reviews -----------------------------------//

                //Index
                Route::get('/reviews','Api\v1\Manager\ProductReviewController@index');



                //-------------------------------- Dashboard -----------------------------------//
                Route::get('/dashboard','Api\v1\Manager\ManagerController@index');




                //------------------------------ Transactions --------------------------//
                //index
                Route::get('/transactions','Api\v1\Manager\TransactionController@index');



                //-------------------------------- Shop Request --------------------------------//

                //Index
                Route::get('/shop_requests','Api\v1\Manager\ShopRequestController@index');

                //Create
                Route::post('/shop_requests','Api\v1\Manager\ShopRequestController@store');

                //Delete
                Route::delete('/shop_requests','Api\v1\Manager\ShopRequestController@destroy');



                //------------------ Category-----------------------//
                Route::get('/categories', 'Api\v1\Manager\CategoryController@index');




            });

            Route::get('/maintenance', function () {
                return response(['message' => [env('APP_NAME').' is now online']], 200);
            });

        });
    });

