<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Manager\AuthController;
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

// Route::post('/test', 'Api\TestController@test');

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
            Route::post('/parse-referral-link', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'parseReferralLink']);
            //Password  Reset
            Route::post('/password/email', [\App\Http\Controllers\Api\v1\User\ForgotPasswordController::class, 'sendResetLinkEmail']);
            //Print Receipt
            Route::get('/orders/{id}/receipt','User\OrderReceiptController@show');

            Route::get('/app_data', [\App\Http\Controllers\Api\v1\User\AppDataController::class,'getAppData']);

            Route::get('/categories', [\App\Http\Controllers\Api\v1\User\CategoryController::class,'index']);

            Route::get('/categories/{id}/shops',  [\App\Http\Controllers\Api\v1\User\CategoryController::class,'getShops']);

                //----------------- Banners ------------------------------//
            Route::get('/banners', 'Api\v1\User\BannerController@index');

                //------------------ Category-----------------------//
            //Category product
            Route::get('/categories/{id}/products',  [\App\Http\Controllers\Api\v1\User\CategoryController::class,'getProducts']);
            Route::get('/categories/{id}/subcategories', [\App\Http\Controllers\Api\v1\User\CategoryController::class,'getSubcategories']);
            Route::get('/shops',  [\App\Http\Controllers\Api\v1\User\ShopController::class,'index']);
            Route::get('/shops/location',  [\App\Http\Controllers\Api\v1\User\ShopController::class,'shopLocation']);
            Route::get('/shops/{id}/subcategory',  [\App\Http\Controllers\Api\v1\User\ShopController::class,'getSubCategoryByShop']);

            // Route::get('/delivery-boy/{category_id}/?{shop_id}/location',  [\App\Http\Controllers\Api\v1\User\DeliveryBoyReviewController::class,'deliveryBoyLocation']);
            Route::get('/delivery-boy/{category_id}/location',  [\App\Http\Controllers\Api\v1\User\DeliveryBoyReviewController::class,'deliveryBoyLocation']);
            Route::get('/delivery-boy',  [\App\Http\Controllers\Api\v1\User\DeliveryBoyReviewController::class,'deliveryBoySearch']);
            
            Route::get('/get-delivery-or-shop-by-location/{category_id}/location',  [\App\Http\Controllers\Api\v1\User\CategoryController::class,'getByShopOrDeliveryBoyLocation']);

            //////////////////////////// Auth /////////////////////////////////////////////////////////////
            Route::group(['middleware' => ['auth:user-api']], function () {

                Route::post('/verify_mobile_number', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'verifyMobileNumber']);
                Route::post('/mobile_verified', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'mobileVerified']);
                Route::post('/delete', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'delete']);

                //---------------------------- App Data -------------------------//
                //App User Data
                Route::get('/app_data/user', [\App\Http\Controllers\Api\v1\User\AppDataController::class,'getAppDataWithUser']);



                //---------------------- Setting ----------------------------//
                Route::put('/update_profile', [\App\Http\Controllers\Api\v1\User\AuthController::class, 'updateProfile']);

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
                Route::get('/addresses', [\App\Http\Controllers\Api\v1\User\UserAddressController::class, 'index']);
                Route::post('/addresses',[\App\Http\Controllers\Api\v1\User\UserAddressController::class, 'store']);
                Route::post('/addresses/{address_id}', [\App\Http\Controllers\Api\v1\User\UserAddressController::class, 'update']);
                Route::delete('/addresses/{id}', [\App\Http\Controllers\Api\v1\User\UserAddressController::class, 'destroy']);
                Route::put('/addresses/{id}/set-default', [\App\Http\Controllers\Api\v1\User\UserAddressController::class, 'setDefaultAddress']);
                Route::get('/addresses/default', [\App\Http\Controllers\Api\v1\User\UserAddressController::class, 'getDefaultAddress']);


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


                Route::post('/verify_mobile_number', [\App\Http\Controllers\Api\v1\DeliveryBoy\AuthController::class, 'verifyMobileNumber']);
                Route::post('/mobile_verified', [\App\Http\Controllers\Api\v1\DeliveryBoy\AuthController::class, 'mobileVerified']);
                //App User Data
                Route::get('/app_data/delivery_boy', 'Api\v1\DeliveryBoy\AppDataController@getAppDataWithDeliveryBoy');


                //---------------------- Setting ----------------------------//
               // Route::post('/update_profile', 'Api\v1\DeliveryBoy\AuthController@updateProfile');
                Route::put('/update_profile', [\App\Http\Controllers\Api\v1\DeliveryBoy\AuthController::class, 'updateProfile']);


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

                Route::get('/shop', [\App\Http\Controllers\Api\v1\DeliveryBoy\ShopController::class, 'index']);


                //---------------------- Transactions -----------------------------//
                Route::get('/transactions', 'Api\v1\DeliveryBoy\TransactionController@index');



                //----------------------- Settings --------------------//
                //Route::post('/change_status','Api\v1\DeliveryBoy\AuthController@changeStatus');
                Route::put('/change_status', [\App\Http\Controllers\Api\v1\DeliveryBoy\AuthController::class, 'changeStatus']);

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
            Route::post('/register', [\App\Http\Controllers\Api\v1\Manager\AuthController::class, 'register']);
            Route::post('/login', [\App\Http\Controllers\Api\v1\Manager\AuthController::class, 'login']);

            //Password  Reset
            Route::post('/password/email',[\App\Http\Controllers\Api\v1\Manager\ForgotPasswordController::class,'sendResetLinkEmail']);

            //Print Receipt
            Route::get('/orders/{id}/receipt','Manager\OrderReceiptController@show');


            //App Data
            Route::get('/app_data', 'Api\v1\Manager\AppDataController@getAppData');


            Route::group(['middleware' => ['auth:manager-api']], function () {

                Route::post('/verify_mobile_number', [\App\Http\Controllers\Api\v1\Manager\AuthController::class, 'verifyMobileNumber']);
                Route::post('/mobile_verified', [\App\Http\Controllers\Api\v1\Manager\AuthController::class, 'mobileVerified']);
                Route::post('/status_shop', [\App\Http\Controllers\Api\v1\Manager\AuthController::class, 'statusShopOpenOrClose']);
                Route::get('/app_data/manager', [\App\Http\Controllers\Api\v1\Manager\AppDataController::class,'getAppDataWithManager']);
                Route::post('/update_profile',  [\App\Http\Controllers\Api\v1\Manager\AuthController::class, 'updateProfile']);

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

                //Delete
                Route::delete('/productImages/{id}','Api\v1\Manager\ProductImageController@destroy');

                //-------------------------------- Shop --------------------------------//
                Route::get('/shops',[\App\Http\Controllers\Api\v1\Manager\ShopController::class,'index']);
                // Route::patch('/shops/{id}',[\App\Http\Controllers\Api\v1\Manager\ShopController::class,'update']);
                Route::get('/shops/reviews',[\App\Http\Controllers\Api\v1\Manager\ShopController::class,'showReviews']);
                //----------------------------- Delivery Boy ---------------------------------//

                //Manage
                Route::get('/delivery_boys/get_all',[\App\Http\Controllers\Api\v1\Manager\DeliveryBoyController::class,'getAll']);
                Route::post('/delivery_boys/create',[\App\Http\Controllers\Api\v1\Manager\DeliveryBoyController::class,'create']);
                Route::put('/delivery_boys/update/{id}',[\App\Http\Controllers\Api\v1\Manager\DeliveryBoyController::class,'update']);
                Route::delete('/delivery_boys/delete/{id}',[\App\Http\Controllers\Api\v1\Manager\DeliveryBoyController::class,'destroy']);
                Route::get('/delivery_boys_requests/accept/{id}',[\App\Http\Controllers\Api\v1\Manager\DeliveryBoyController::class, 'accept']);
                Route::get('/delivery_boys_requests/decline/{id}',[\App\Http\Controllers\Api\v1\Manager\DeliveryBoyController::class, 'decline']);


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
                Route::get('/categories',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'index']);
                Route::get('/mainCategories',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'mainCategories']);
                // Route::get('/subCategories',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'subCategories']);
                Route::get('/mySubCategories',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'mySubCategories']);
                Route::post('/mySubCategories',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'store']);
                Route::put('/mySubCategories/update/{id}',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'update']);
                Route::post('/subcategories/select',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'selectSubCategories']);
                Route::post('/subcategories/remove/{id}',[\App\Http\Controllers\Api\v1\Manager\CategoryController::class,'remove']);
                Route::delete('/subcategories/{id}', [\App\Http\Controllers\Api\v1\Manager\CategoryController::class, 'destroy']);

            });

            Route::get('/maintenance', function () {
                return response(['message' => [env('APP_NAME').' is now online']], 200);
            });

        });
    });

