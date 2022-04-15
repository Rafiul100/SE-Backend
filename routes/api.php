<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController; 
use App\Http\Controllers\API\ProductController; 
use App\Http\Controllers\API\StudentProductController; 
use App\Http\Controllers\API\PurchaseController; 
use App\Http\Controllers\API\CartController; 
use App\Http\Controllers\API\ProfileController; 
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\OrderController; 
use App\Http\Controllers\API\FeedbackController; 
use App\Http\Controllers\API\WishlistController; 
use App\Http\Controllers\API\SubscriptionController; 


//register and sign in
Route::post('register', [AuthController::class, 'register'])->name('account.register'); 
Route::post('signin ', [AuthController::class, 'signin'])->name('account.signin'); 



//protected routes - all incoming requests must be authenticated
//will ensure that incoming requests are authenticated as either stateful, cookie authenticated requests or contain a valid API token header.
//ApiAdmin and ApiStudentBuy middleware is created so that the appropriate responses are sent back to users, restricting them to specific privileges and pages.
Route::middleware(['auth:sanctum', 'ApiAdmin'])->group(function() {
    
    Route::get('/AdminAuthentication', function() { 
        return response()->json (['message' => 'you have access', 'status' =>200], 200);
    }); 

    //Products
    Route::post('add-product', [ProductController::class, 'store']); 
    Route::get('view-product', [ProductController::class, 'view']); 
    Route::get('view-allstudentproduct', [ProductController::class, 'studentview']); 
    Route::get('edit-product/{id}', [ProductController::class, 'edit']); 
    Route::post('update-product/{id}', [ProductController::class, 'update']); 
    Route::post('admin-filter', [ProductController::class, 'filter']); 
    Route::get('admin-edit-studentproduct/{id}', [ProductController::class, 'adminEdit']);
    Route::post('admin-update-studentproduct/{id}', [ProductController::class, 'adminUpdate']); 
    Route::delete('delete-admin-item/{id}', [ProductController::class, 'deleteProduct']); 

    //orders  
    Route::get('admin-orders', [OrderController::class, 'adminOrders']);
    Route::get('admin-order-items/{id}', [OrderController::class, 'orderedItems']);
    Route::post('delete-admin-order/{id}', [OrderController::class, 'deleteOrder']);
    

});



Route::middleware(['auth:sanctum', 'ApiStudent'])->group(function() {

    Route::get('/StudentAuthentication', function() { 
        return response()->json (['message' => 'you have access', 'status' =>200], 200);
    }); 


    //products to buy
    Route::get('all-admin-product', [PurchaseController::class, 'viewAllAdmin']);
    Route::get('all-student-product', [PurchaseController::class, 'viewAllStudent']); 
    Route::post('get-subcategory', [PurchaseController::class, 'getCategory']); 
    Route::post('buy-filter', [PurchaseController::class, 'changeBuyFilter']);  
    Route::get('view-product-details/{type}/{product}/{id}', [PurchaseController::class, 'getDetails']); 
    Route::post('add-to-cart/{type}', [CartController::class, 'addToCart']); 
    Route::get('view-cart', [CartController::class, 'viewCart']); 
    Route::put('update-cart-quantity/{id}/{type}/{stock}', [CartController::class, 'updateCart']);
    Route::delete('delete-cart-item/{id}', [CartController::class, 'deleteCart']);
    Route::post('place-order', [CheckoutController::class, 'placeOrder']);
    Route::post('validate-info', [CheckoutController::class, 'validateInfo']);

    Route::get('homepage-products', [PurchaseController::class, 'homepageProducts']); 

    //shopping history and feedback
    Route::get('shopping-history', [PurchaseController::class, 'getHistory']);
    Route::post('feedback', [FeedbackController::class, 'addFeedback']);
    Route::get('student-feedback/{id}', [PurchaseController::class, 'getFeedback']);

    //wishlist
    Route::post('add-wishlist/{type}', [WishlistController::class, 'addWishlist']);
    Route::get('get-wishlist', [WishlistController::class, 'getWishlist']);
    Route::delete('remove-wishlist/{id}', [WishlistController::class, 'deleteWishlist']);

    //subscription
    Route::get('view-subscription-details/{type}/{id}', [SubscriptionController::class, 'subscriptionDetails']);
    Route::post('place-subscription-order', [SubscriptionController::class, 'subscriptionOrder']);
    Route::get('get-subscriptions', [SubscriptionController::class, 'studentSubscriptions']);
    Route::delete('remove-subscription/{id}/{type}', [SubscriptionController::class, 'removeSubscriptions']);



    //Products to sell
    Route::post('student-filter', [StudentProductController::class, 'filter']); 
    Route::post('add-studentproduct', [StudentProductController::class, 'store']); 
    Route::post('view-student-product', [StudentProductController::class, 'view']); 
    Route::get('edit-studentproduct/{id}', [StudentProductController::class, 'edit']); 
    Route::post('update-studentproduct/{id}', [StudentProductController::class, 'update']); 
    Route::get('view-profile/{id}', [ProfileController::class, 'viewProfile']);
    Route::post('update-profile/{id}', [ProfileController::class, 'updateProfile']);
    Route::delete('delete-student-item/{id}', [StudentProductController::class, 'deleteProduct']);  



    //orders 
    Route::get('student-orders', [OrderController::class, 'studentOrders']);
    Route::get('student-order-items/{id}', [OrderController::class, 'orderedItems']);
    Route::post('delete-student-order/{id}', [OrderController::class, 'deleteOrder']);

});


Route::middleware(['auth:sanctum'])->group(function() {
  
    Route::post('signout', [AuthController::class, 'signout'] ); 
});



/*Route::middleware(['auth:sanctum', 'ApiStudentSell'])->group(function() {

    Route::get('/StudentSellAuthentication', function() { 
        return response()->json (['message' => 'you have access', 'status' =>200], 200);
    }); 


});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
  */ 