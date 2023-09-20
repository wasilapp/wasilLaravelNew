<?php

use Carbon\Carbon;
use App\Models\Order;
use App\Helpers\TextUtil;
use App\Models\DeliveryBoy;
use App\Models\ShopRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ShopRequestController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\DeliveryBoyController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\WalletCouponsController;

Route::get('/testView',[App\Http\Controllers\Controller::class,'testView']);

Route::get('privacy-policy',[App\Http\Controllers\Controller::class,'privacy']);
Route::get('admin/privacy-policy',[AdminController::class,'create_privacy'])->name('user.privacy');
Route::put('admin/privacy-policy/update',[AdminController::class,'updatePrivacy']);


Route::prefix('/user')->group(function (){

    Route::get('/mobile/orders/{order_id}/payment/stripe/pay/', 'User\OrderPaymentController@stripePaymentViaMobile');
    Route::post('/mobile/orders/payment/stripe/callback/', 'User\OrderPaymentController@stripeCallbackViaMobile')->name('user.mobile.orders_payment.stripe.callback');
});


/*  Route::get('/',function(){
    return redirect('/ar');
});
if(str_contains(request()->url(),'/ar')){
    $locale = 'ar';
}
else{
    $locale = 'en';
}  */
Route::get('/',[App\Http\Controllers\Admin\Auth\LoginController::class,'showLoginForm'])->name('home');

    Route::prefix('admin')->group(function (){



        Route::get('/login',[App\Http\Controllers\Admin\Auth\LoginController::class,'showLoginForm']);
        Route::get('/register',[App\Http\Controllers\Admin\Auth\LoginController::class,'showRegisterForm'])->name('manager.register');
        Route::post('/login',[App\Http\Controllers\Admin\Auth\LoginController::class,'login'])->name('admin.login');
        Route::post('/register',[App\Http\Controllers\Admin\Auth\LoginController::class,'create'])->name('admin.register');


        //Password  Reset
        Route::post('/password/email',[App\Http\Controllers\Admin\Auth\ForgotPasswordController::class,'sendResetLinkEmail'])->name('admin.password.email');
        Route::get('/password/reset',[App\Http\Controllers\Admin\Auth\ForgotPasswordController::class,'showLinkRequestForm'])->name('admin.password.request');
        Route::post('/password/reset',[App\Http\Controllers\Admin\Auth\ResetPasswordController::class,'reset']);
        Route::get('/password/reset/{token}',[App\Http\Controllers\Admin\Auth\ResetPasswordController::class,'showResetForm'])->name('admin.password.reset');

    });

    // Route::prefix('manager')->group(function (){

    //     Route::get('/login','Manager\Auth\LoginController@showLoginForm');
    //     Route::get('/register','Manager\Auth\RegisterController@showRegisterForm');
    //     Route::post('/login','Manager\Auth\LoginController@login')->name('manager.login');
    //     Route::post('/register','Manager\Auth\RegisterController@create')->name('manager.register');


    //     //Password  Reset
    //     Route::post('/password/email','Manager\Auth\ForgotPasswordController@sendResetLinkEmail')->name('manager.password.email');
    //     Route::get('/password/reset','Manager\Auth\ForgotPasswordController@showLinkRequestForm')->name('manager.password.request');
    //     Route::post('/password/reset','Manager\Auth\ResetPasswordController@reset');
    //     Route::get('/password/reset/{token}','Manager\Auth\ResetPasswordController@showResetForm')->name('manager.password.reset');

    //     //Print Receipt
    //    // Route::get('/orders/{id}/receipt','Manager\OrderReceiptController@show')->name('user.orders.receipt');



    // });

    // Route::prefix('user')->group(function (){

    //     Route::get('/login','User\Auth\LoginController@showLoginForm');
    //     Route::get('/register','User\Auth\RegisterController@showRegisterForm');
    //     Route::post('/login','User\Auth\LoginController@login')->name('user.login');
    //     Route::post('/register','User\Auth\RegisterController@create')->name('user.register');


    //     //Password  Reset
    //     Route::post('/password/email','User\Auth\ForgotPasswordController@sendResetLinkEmail')->name('user.password.email');
    //     Route::get('/password/reset','User\Auth\ForgotPasswordController@showLinkRequestForm')->name('user.password.request');
    //     Route::post('/password/reset','User\Auth\ResetPasswordController@reset');
    //     Route::get('/password/reset/{token}','User\Auth\ResetPasswordController@showResetForm')->name('user.password.reset');

    // });

    Route::group(['middleware'=>'auth:admin','prefix'=>'/admin'],function (){
        /* Route::get('/status/{id}/orders' , function($id){
            $orders =  Order::with('shop', 'orderPayment')->where('status',$id)->orderBy('updated_at','DESC')->paginate(10);
                return view('admin.orders.orders')->with([
                    'orders'=>$orders
                ]);
        })->name('status'); */

        //----------------------------- Auth -----------------------------------//
        Route::get('/logout',[\App\Http\Controllers\Admin\Auth\LoginController::class,'logout'])->name('admin.logout');

       //  Route::get('/create/privacy',[AdminController::class,'create_privacy'])->name('user.create.privacy');
       //  Route::post('/create/privacy',[AdminController::class,'updatePrivacy'])->name('user.store.privacy');


        // ------------------------ Admin ---------------------------//
        Route::get('/',[AdminController::class,'index'])->name('admin.dashboard');
        Route::get('/setting',[AdminController::class,'edit'])->name('admin.setting.edit');
        Route::patch('/setting',[AdminController::class,'update'])->name('admin.setting.update');
        Route::patch('/setting/updateLocale/{langCode}',[AdminController::class,'updateLocale'])->name('admin.setting.updateLocale');

        //----------------------- Add Data -----------------------------//
        Route::get('/app_data','Admin\AppDataController@index')->name('admin.appdata.index');
        Route::post('/app_data','Admin\AppDataController@create')->name('admin.appdata.create');


        //----------------------- Banner -----------------------------//
        Route::prefix('/banners')->group(function () {
            Route::get('/',[BannerController::class,'index'])->name('admin.banners.index');
            Route::post('/store',[BannerController::class,'store'])->name('admin.banners.store');
            Route::delete('/delete',[BannerController::class,'destroy'])->name('admin.banners.destroy');
        });


        //-------------------------------- User --------------------------------//
        Route::prefix('/users')->group(function () {
            Route::get('',[UserController::class,'index'])->name('admin.users.index');
            Route::get('/{id}',[UserController::class,'edit'])->name('admin.users.edit');
            Route::patch('/{id}',[UserController::class,'update'])->name('admin.users.update');
            Route::DELETE('/{id}',[UserController::class,'destroy'])->name('admin.users.destroy');
        });
        //-------------------------------- Category --------------------------------//
        Route::prefix('/categories')->group(function () {
            Route::get('/',[CategoryController::class,'index'])->name('admin.categories.index');//Index
            Route::get('/create',[CategoryController::class,'create'])->name('admin.categories.create');//Create
            Route::post('/',[CategoryController::class,'store'])->name('admin.categories.store');
            Route::get('/{id}',[CategoryController::class,'show'])->name('admin.categories.show'); //Read
            Route::get('/{id}/edit',[CategoryController::class,'edit'])->name('admin.categories.edit');
            Route::patch('/{id}',[CategoryController::class,'update'])->name('admin.categories.update');//Update
            Route::get('/{id}/delete',[CategoryController::class,'destroy'])->name('admin.categories.delete');//Delete
        });
        //-------------------------------- Sub - Category --------------------------------//
        Route::prefix('/sub_categories')->group(function () {
            Route::get('/', [SubCategoryController::class, 'index'])->name('admin.sub-categories.index');
            Route::get('/create', [SubCategoryController::class, 'create'])->name('admin.sub-categories.create');
            Route::post('/store', [SubCategoryController::class, 'store'])->name('admin.sub-categories.store');
            Route::get('/show/{id}', [SubCategoryController::class, 'show'])->name('admin.sub-categories.show');
            Route::get('/edit/{id}', [SubCategoryController::class, 'edit'])->name('admin.sub-categories.edit');
            Route::patch('/update/{id}', [SubCategoryController::class, 'update'])->name('admin.sub-categories.update');
            Route::get('/delete/{id}', [SubCategoryController::class, 'destroy'])->name('admin.sub-categories.delete');
        });
        //-------------------------------- Coupon --------------------------------//
        Route::prefix('/coupons')->group(function () {
            Route::get('/',[CouponController::class,'index'])->name('admin.coupons.index');
            Route::get('/create',[CouponController::class,'create'])->name('admin.coupons.create');
            Route::post('/store',[CouponController::class,'store'])->name('admin.coupons.store');
            Route::get('/{id}',[CouponController::class,'show'])->name('admin.coupons.show');
            Route::get('/{id}/edit',[CouponController::class,'edit'])->name('admin.coupons.edit');
            Route::patch('/{id}',[CouponController::class,'update'])->name('admin.coupons.update');
            Route::delete('/{id}',[CouponController::class,'destroy'])->name('admin.coupons.destroy');
        });
        //--------------------------------Wallet  Coupon --------------------------------//
        Route::prefix('/wallet-coupons')->group(function () {
            Route::get('/',[WalletCouponsController::class,'index'])->name('admin.wallet-coupons.index');
            Route::get('/create',[WalletCouponsController::class,'create'])->name('admin.wallet-coupons.create');
            Route::post('/store',[WalletCouponsController::class,'store'])->name('admin.wallet-coupons.store');
            Route::get('/{id}',[WalletCouponsController::class,'show'])->name('admin.wallet-coupons.show');
            Route::get('/{id}/edit',[WalletCouponsController::class,'edit'])->name('admin.coupons.edit');
            Route::patch('/{id}',[WalletCouponsController::class,'update'])->name('admin.wallet-coupons.update');
            Route::delete('/{id}',[WalletCouponsController::class,'destroy'])->name('admin.coupons.destroy');
        });
        //-------------------------------- Product --------------------------------//
        Route::prefix('/products')->group(function () {
            Route::get('/',[ProductController::class,'index'])->name('admin.products.index');
            Route::get('/{id}',[ProductController::class,'show'])->name('admin.products.show');
            Route::get('/{id}/edit',[ProductController::class,'edit'])->name('admin.products.edit');
            Route::get('/{id}/images',[ProductImageController::class,'edit'])->name('admin.product-images.edit');
            Route::post('/{id}/images',[ProductImageController::class,'store'])->name('admin.product-images.store');
            Route::patch('/{id}',[ProductController::class,'update'])->name('admin.products.update');
            Route::delete('/{id}',[ProductController::class,'destroy'])->name('admin.products.destroy');
            Route::delete('/productImages',[ProductImageController::class,'destroy'])->name('admin.product-images.delete');
        });
        //------------------------------ Order ----------------------------------------//
        Route::get('/orders',[OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/show/{id}',[OrderController::class, 'show'])->name('admin.orders.show');
        Route::get('/orders/{id}',[OrderController::class, 'update'])->name('admin.orders.update');
        Route::get('/status/{id}/orders',[OrderController::class, 'getOrderByStatus'])->name('admin.orders.status');

/*         Route::get('/status/{id}/orders' , function($id){
            $orders =  Order::with('shop', 'orderPayment')->where('status',$id)->orderBy('updated_at','DESC')->paginate(10);
                return view('admin.orders.orders')->with([
                    'orders'=>$orders
                ]);
        })->name('status'); */


        //----------------------------- Shop ------------------------------------//
        Route::get('/shops',[ShopController::class, 'index'])->name('admin.shops.index');//index
        Route::get('/shops/create',[ShopController::class, 'create'])->name('admin.shops.create'); //create
        Route::post('/shops',[ShopController::class, 'store'])->name('admin.shops.store'); //store
        Route::get('/shops/{id}',[ShopController::class, 'show'])->name('admin.shops.show');//show
        Route::get('/shops/{id}/delete',[ShopController::class, 'destroy'])->name('admin.shops.delete');
        Route::get('/shops/{id}/edit',[ShopController::class, 'edit'])->name('admin.shops.edit');//update
        Route::patch('/shops/{id}/edit',[ShopController::class, 'update'])->name('admin.shops.update');//update
        Route::get('/shops/{id}/reviews',[ShopController::class, 'showReviews'])->name('admin.shops.reviews.show');//Shop Reviews




        //---------------------------- Shop Review -----------------------------//

        //Delete
        Route::delete('/shops-reviews/{id}','Admin\ShopReviewController@destroy')->name('admin.shops.reviews.delete');



        //------------------------------ Shop Request --------------------------//
        Route::get('/shop_requests',[ShopRequestController::class, 'index'])->name('admin.shop_requests.index');//index
        Route::patch('/shop_requests/{id}',[ShopRequestController::class, 'update'])->name('admin.shop_requests.update');


        //------------------------------ Delivery Boy --------------------------//
        //index
        Route::get('/delivery-boys',[DeliveryBoyController::class,'index'])->name('admin.delivery-boys.index');
        Route::get('/delivery-boys/{id}/reviews',[DeliveryBoyController::class,'showReviews'])->name('admin.delivery-boy.reviews.show');
        Route::get('/delivery-boys/{id}',[DeliveryBoyController::class,'show'])->name('admin.delivery-boy.show');
        Route::get('/delivery-boy/create',[DeliveryBoyController::class,'create'])->name('admin.delivery-boy.create');
        Route::post('/delivery-boys',[DeliveryBoyController::class,'store'])->name('admin.delivery-boy.store');
        Route::post('/delivery-boys/{id}',[DeliveryBoyController::class,'update'])->name('admin.delivery-boy.update');
        Route::DELETE('/delivery-boys/{id}',[DeliveryBoyController::class,'destroy'])->name('admin.delivery-boy.destroy');
        //Delete review
        Route::delete('/delivery-boy-reviews/{id}',[DeliveryBoyController::class,'destroy'])->name('admin.delivery-boy.review.delete');

        Route::prefix('/transactions')->group(function () {
            Route::get('/',[TransactionController::class,'index'])->name('admin.transactions.index');
            Route::get('/{id}',[TransactionController::class,'show'])->name('admin.transactions.show');
            Route::get('/{id}/add',[TransactionController::class,'add'])->name('admin.transactions.create');
            Route::post('/{id}',[TransactionController::class,'store_add'])->name('admin.transactions.store');
            Route::get('/{id}/add_delivery_transaction',[TransactionController::class,'add_delivery_transaction'])->name('admin.transactions.add_delivery_transaction');
            Route::get('/get_total/{id}',[TransactionController::class,'get_total'])->name('ordertotal');
            Route::post('/capture_transaction/{id}',[TransactionController::class,'capturePayment'])->name('admin.transaction.capture');
            Route::post('/refund_transaction/{id}',[TransactionController::class,'refundPayment'])->name('admin.transaction.refund');
        });

        //-----------------  FCM Notifications ------------------------//
        Route::get('/notifications','Admin\NotificationController@create')->name('admin.notifications.create');
        Route::post('/notifications','Admin\NotificationController@send')->name('admin.notifications.send');


    });



    Route::group(['middleware'=>['auth:manager','numberVerification:manager'],'prefix'=>'/manager'],function (){
          Route::get('/status/{id}/orders' , function($id){
            $orders =  Order::with('shop', 'orderPayment')->where('status',$id)->orderBy('updated_at','DESC')->paginate(10);
                return view('manager.orders.orders')->with([
                    'orders'=>$orders
                ]);

        })->name('manager.status');


        Route::get('/getorder' , function(){
           $todayorders = Order::with('user')->where('shop_id', auth()->user()->shop->id)->whereDate('created_at', Carbon::today())->where('is_notification',1)->count();
           Order::with('user')->where('shop_id', auth()->user()->shop->id)->where('created_at', Carbon::today())->where('is_notification',1)->update([
               'is_notification' => 0
               ]);
            $data = ['order_count' => $todayorders];
            return json_encode($data);
        });


        //--------------------------- Auth -------------------------------------//
        Route::get('/logout','Manager\Auth\LoginController@logout')->name('manager.logout');



        //-------------------------- Manager -----------------------------------//
        Route::get('/','Manager\ManagerController@index')->name('manager.dashboard');
        Route::get('/setting','Manager\ManagerController@edit')->name('manager.setting.edit');
        Route::patch('/setting','Manager\ManagerController@update')->name('manager.setting.update');
        Route::patch('/setting/updateLocale/{langCode}','Manager\ManagerController@updateLocale')->name('manager.setting.updateLocale');






        //-------------------------------- Shop --------------------------------//

        //Index
        Route::get('/shops','Manager\ShopController@index')->name('manager.shops.index');

        //Create is not available

        //Read
        Route::get('/shops/{id}','Manager\ShopController@show')->name('manager.shops.show');

        //Update
        Route::get('/shops/{id}/edit','Manager\ShopController@edit')->name('manager.shops.edit');
        Route::patch('/shops/{id}','Manager\ShopController@update')->name('manager.shops.update');

        //Delete
        Route::delete('/shops/{id}','Manager\ShopController@destroy')->name('manager.shops.destroy');

        //Shop Reviews
        Route::get('/shops/{id}/reviews','Manager\ShopController@showReviews')->name('manager.shops.show_reviews');

       //-------------------------------- Code --------------------------------//

        //Index
        Route::get('/codes','Manager\CodeController@index')->name('manager.codes.index');

        //Create is not available
        Route::get('/codes/create','Manager\CodeController@create')->name('manager.codes.create');

        Route::post('/codes/store','Manager\CodeController@store')->name('manager.codes.store');

        //Update
        Route::get('/codes/{id}/edit','Manager\CodeController@edit')->name('manager.codes.edit');
        Route::patch('/codes/{id}','Manager\CodeController@update')->name('manager.codes.update');

        //Delete
        // Route::delete('/codes/{id}','Manager\CodeController@destroy')->name('manager.shops.destroy');


        //-------------------------------- Shop Request --------------------------------//

        Route::post('/shop_requests','Manager\ShopRequestController@store')->name('manager.shop_requests.store');

        //Delete
        Route::delete('/shop_requests/{id}','Manager\ShopRequestController@destroy')->name('manager.shop_requests.destroy');





        //-------------------------------- Product --------------------------------//

        //Index
        Route::get('/products','Manager\ProductController@index')->name('manager.products.index');

        //Create
        Route::get('/products/create','Manager\ProductController@create')->name('manager.products.create');
        Route::post('/products','Manager\ProductController@store')->name('manager.products.store');
        Route::post('/products/{id}/images','Manager\ProductImageController@store')->name('manager.product-images.store');

        //Read

        Route::get('/products/{id}','Manager\ProductController@show')->name('manager.products.show');

        //Update
        Route::get('/products/{id}/edit','Manager\ProductController@edit')->name('manager.products.edit');
        Route::get('/products/{id}/images','Manager\ProductImageController@edit')->name('manager.product-images.edit');
        Route::patch('/products/{id}','Manager\ProductController@update')->name('manager.products.update');

        //Delete
        Route::delete('/products/{id}','Manager\ProductController@destroy')->name('manager.products.destroy');




        //-------------------------------- Product Images --------------------------------//


        //Delete
        Route::delete('/productImages','Manager\ProductImageController@destroy')->name('manager.product-images.delete');


        //--------------------------------- Reviews -----------------------------------//

        //Index
        Route::get('/reviews','Manager\ProductReviewController@index')->name('manager.reviews.index');




        //---------------------------------- Order -----------------------------------//

        //Index
        Route::get('/orders','Manager\OrderController@index')->name('manager.orders.index');

        //Update
        Route::get('/orders/{id}/edit','Manager\OrderController@edit')->name('manager.orders.edit');
        Route::patch('/orders/{id}','Manager\OrderController@update')->name('manager.orders.update');

     //---------------------------------- Scheduled Order  -----------------------------------//

        //Index
        Route::get('/scheduledorders','Manager\ScheduledOrderController@index')->name('manager.schedule-orders.index');


        //----------------------------- Shop Revenues ------------------------------------//
        //index
        Route::get('/shop-revenues','Manager\ShopRevenueController@index')->name('manager.shop-revenues.index');


        //------------------------------ Transactions --------------------------//
        //index
        Route::get('/transaction','Manager\TransactionController@index')->name('manager.transaction.index');




        //---------------------------------------- Coupon -------------------------//
        Route::get('/coupons','Manager\ShopCouponController@index')->name('manager.coupons.index');
        Route::patch('/coupons','Manager\ShopCouponController@update')->name('manager.coupons.update');




        //----------------------------- Delivery Boy ---------------------------------//

        //Index
        Route::get('/delivery_boys','Manager\DeliveryBoyController@index')->name('manager.delivery-boys.index');



        //Show reviews
        Route::get('/delivery-boys/{id}/reviews','Manager\DeliveryBoyController@showReviews')->name('manager.delivery-boy.reviews.show');


        //Assign
        Route::get('/delivery_boys/assign/{order_id}','Manager\DeliveryBoyController@showForAssign')->name('manager.delivery-boys.showForAssign');
        Route::post('/delivery_boys/assign/{order_id}/{delivery_boy_id}','Manager\DeliveryBoyController@assign')->name('manager.delivery-boys.assign');

        Route::get('/delivery-boy/create','Manager\DeliveryBoyController@create')->name('manager.delivery-boy.create');
        Route::post('/delivery-boys','Manager\DeliveryBoyController@store')->name('manager.delivery-boy.store');

        Route::get('/transactions','Manager\TransactionController@index')->name('manager.transactions.index');
        Route::get('/transactions/{id}/show','Manager\TransactionController@show')->name('manager.transactions.show');




    });


    Route::group(['middleware'=>['auth:user'],'prefix'=>'/user'],function () {
        Route::get('/mobile_verification','User\Auth\NumberVerificationController@showNumberVerificationForm')->name('user.auth.numberVerificationForm');
        Route::post('/verify_mobile_number','User\Auth\NumberVerificationController@verifyMobileNumber')->name('user.auth.verify_mobile_number');
        Route::post('/mobile_verified','User\Auth\NumberVerificationController@mobileVerified')->name('user.auth.mobile_verified');

        //--------------------------- Blocked -------------------------------------//
        Route::get('/block','User\Auth\BlockController@show')->name('user.block.show');

        //--------------------------- Auth -------------------------------------//
        Route::get('/logout','User\Auth\LoginController@logout')->name('user.logout');

    });


    Route::group(['middleware'=>['auth:user','numberVerification:user','blocked:user'],'prefix'=>'/user'],function (){


        //-------------------------- User -----------------------------------//
        Route::get('/','User\UserController@index')->name('user.dashboard');
        Route::get('/setting','User\UserController@edit')->name('user.setting.edit');
        Route::patch('/setting','User\UserController@update')->name('user.setting.update');
        Route::patch('/setting/updateLocale/{langCode}','User\UserController@updateLocale')->name('user.setting.updateLocale');


        //-------------------------------- Product --------------------------------//

        //Index
        Route::get('/products','User\ProductController@index')->name('user.products.index');


        //Show
        Route::get('/products/{id}','User\ProductController@show')->name('user.products.show');

        //Show Reviews
        Route::get('/products/{id}/reviews', 'User\ProductController@showReviews')->name('user.product.reviews.show');


        //----------------- Category -------------------------------//
        Route::get('/categories/{id}', 'User\CategoryController@show')->name('user.categories.show');

        //----------------- Sub Category -------------------------------//
        Route::get('/sub_categories/{id}', 'User\SubCategoryController@show')->name('user.sub-categories.show');




        //--------------- Favourite ------------------------//
        Route::get('/favorites', 'User\FavoriteController@index')->name('user.favorites.index');
        Route::post('/favorites', 'User\FavoriteController@store')->name('user.favorites.store');



        //----------------- Cart -------------------------------//
        Route::get('/carts', 'User\CartController@index')->name('user.carts.index');
        Route::post('/carts', 'User\CartController@store')->name('user.carts.store');
        Route::delete('/carts', 'User\CartController@destroy')->name('user.carts.delete');
        Route::patch('/carts/{id}', 'User\CartController@update')->name('user.carts.update');


        //----------------------------------- Order ----------------------------------------//
        Route::get('/orders', 'User\OrderController@index')->name('user.orders.index');
        Route::patch('/orders/{id}', 'User\OrderController@update')->name('user.orders.update');
        Route::get('/orders/{id}', 'User\OrderController@show')->name('user.orders.show');
        Route::post('/orders', 'User\OrderController@store')->name('user.orders.store');
        Route::get('/orders/{id}/reviews', 'User\OrderController@showReviews')->name('user.order.review.show');



        //---------------- Shop --------------------------//
        Route::get('/shops/{id}', 'User\ShopController@show')->name('user.shops.show');
        Route::get('/shops', 'User\ShopController@index')->name('user.shops.index');
        Route::get('/shops/{id}/reviews', 'User\ShopController@showReviews')->name('user.shop.reviews.show');



        //--------------------- Order Payment -----------------------------------------------//
        Route::get('/orders/{id}/payment', 'User\OrderPaymentController@index')->name('user.orders_payment.index');

        //Paystack Gateway
        Route::post('orders/payment/paystack/pay', 'User\OrderPaymentController@paystackPayment')->name('user.orders_payment.paystack.pay');
        Route::get('orders/payment/paystack/callback', 'User\OrderPaymentController@handleGatewayCallback');

        //Stripe Gateway
        Route::get('orders/payment/stripe/pay', 'User\OrderPaymentController@stripePayment')->name('user.orders_payment.stripe.pay');
        Route::post('orders/payment/stripe/callback', 'User\OrderPaymentController@handleStripePaymentCallback')->name('user.orders_payment.stripe.callback');


        //----------------- Order Checkout -------------------------------//
        Route::get('/checkout', 'User\CheckoutController@index')->name('user.checkout.index');



        //-------------------- Address ------------------------//
        Route::get('/addresses', 'User\UserAddressController@index')->name('user.addresses.index');
        Route::get('/addresses/create', 'User\UserAddressController@create')->name('user.addresses.create');
        Route::post('/addresses', 'User\UserAddressController@store')->name('user.addresses.store');
        Route::delete('/addresses/{id}', 'User\UserAddressController@destroy')->name('user.addresses.delete');


        //-------------------- Shop Review ----------------------//
        Route::post('/shop-reviews', 'User\ShopReviewController@store')->name('user.shop_reviews.store');

        //-------------------- Product Review ----------------------//
        Route::post('/product-reviews', 'User\ProductReviewController@store')->name('user.product_reviews.store');


        //-------------------- Delivery Boy Review ----------------------//
        Route::post('/delivery-boy-reviews', 'User\DeliveryBoyReviewController@store')->name('user.delivery_boy_reviews.store');





    });


    // Route::prefix('user')->group(function (){

    //     //Password  Reset
    //     Route::post('/password/reset','User\Auth\ResetPasswordController@reset')->name('user.password.reset');
    //     Route::get('/password/reset/{token}','User\Auth\ResetPasswordController@showResetForm')->name('user.password.resetForm');


    //     //Print Receipt
    //    // Route::get('/orders/{id}/receipt','User\OrderReceiptController@show')->name('user.orders.receipt');



    // });

    // Route::prefix('delivery-boy')->group(function (){

    //     //Password  Reset
    //     Route::post('/password/reset','DeliveryBoy\Auth\ResetPasswordController@reset')->name('delivery-boy.password.reset');
    //     Route::get('/password/reset/{token}','DeliveryBoy\Auth\ResetPasswordController@showResetForm')->name('delivery-boy.password.resetForm');


    //     //Print Receipt
    //     Route::get('/orders/{id}/receipt','DeliveryBoy\OrderReceiptController@show')->name('delivery-boy.orders.receipt');

    // });



    Route::group(['middleware' => 'auth', 'prefix' => '/'], function () {

    });

    /* Route::get('/', function () {
        return view('home');
    })->name('home'); */
  Route::get('admin/{id}/verify', function ($id) {
        $del = DeliveryBoy::findOrFail($id);
        $del->is_verified = 1;
        $del->is_offline = 0;
        $del->save();
        return redirect()->back()->with(['success' => 'Updated']);
    })->name('admin.verify');



//Auth::routes();




   Route::get('admin/getorder' , function(){
           $todayorders = Order::whereDate('created_at', Carbon::today())->where('is_notification',1)->count();

            $shopRequests = ShopRequest::count();
            $deliveryCount = DeliveryBoy::where('is_verified',0)->count();

            $data = ['order_count' => $todayorders,
                    'shop_count' => $shopRequests,
                    'delivery_count' =>$deliveryCount];
            return json_encode($data);

   });
//Applications
Route::get('/downloads/apk',function (){
    return redirect(TextUtil::$DOCS_APK);
})->name('downloads.apk');

Route::get('/downloads/apk/emall',function (){
    return redirect(TextUtil::$EMALL_APK_DOWNLOAD);
})->name('downloads.apk.emall');

Route::get('/downloads/apk/manager',function (){
    return redirect(TextUtil::$MANAGER_APK_DOWNLOAD);
})->name('downloads.apk.manager');


Route::get('/downloads/apk/delivery-boy',function (){
    return redirect(TextUtil::$DELIVERY_BOY_APK_DOWNLOAD);
})->name('downloads.apk.delivery-boy');

