<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ShopOwnerController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/shops', [CustomerController::class, 'getShops']); // Public shop listing
Route::get('/categories', [AdminController::class, 'getCategories']); // Public categories listing
Route::get('/services/{shopId}', [CustomerController::class, 'getServices']); // Public services listing

// Social Authentication Routes
Route::prefix('auth')->group(function () {
    // Google OAuth
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    
    // Facebook OAuth
    Route::get('/facebook', [SocialAuthController::class, 'redirectToFacebook']);
    Route::get('/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Customer routes
    Route::prefix('customer')->middleware('role:customer')->group(function () {
        Route::get('/shops', [CustomerController::class, 'getShops']);
        Route::get('/shops/{id}', [CustomerController::class, 'getShopDetails']);
        Route::get('/services/{shopId}', [CustomerController::class, 'getServices']);
        Route::post('/book', [CustomerController::class, 'bookService']);
        Route::get('/orders', [CustomerController::class, 'getOrders']);
        Route::delete('/orders/{id}', [CustomerController::class, 'cancelOrder']);
        Route::get('/track/{trackingNumber}', [CustomerController::class, 'trackOrder']);
        Route::post('/payment/process', [CustomerController::class, 'processPayment']);
        Route::put('/profile/update', [CustomerController::class, 'updateProfile']);
        Route::get('/dashboard', [CustomerController::class, 'getDashboard']);
        Route::get('/notifications', [CustomerController::class, 'getNotifications']);
        Route::put('/notifications/{id}/read', [CustomerController::class, 'markNotificationRead']);
        Route::delete('/notifications/{id}', [CustomerController::class, 'deleteNotification']);
        Route::put('/update-password', [CustomerController::class, 'updatePassword']);
    });

    // Shop Owner routes
    Route::prefix('shop')->middleware('role:owner')->group(function () {
        Route::get('/dashboard', [ShopOwnerController::class, 'getDashboard']);
        Route::get('/download-report', [ShopOwnerController::class, 'downloadReport']);
        Route::get('/shop', [ShopOwnerController::class, 'getShop']);
        Route::put('/shop/update', [ShopOwnerController::class, 'updateShop']);
        Route::get('/categories', [ShopOwnerController::class, 'getCategories']);
        Route::get('/services', [ShopOwnerController::class, 'getServices']);
        Route::post('/services', [ShopOwnerController::class, 'createService']);
        Route::put('/services/{id}', [ShopOwnerController::class, 'updateService']);
        Route::delete('/services/{id}', [ShopOwnerController::class, 'deleteService']);
        Route::get('/orders', [ShopOwnerController::class, 'getOrders']);
        Route::put('/orders/{id}/status', [ShopOwnerController::class, 'updateOrderStatus']);
        Route::delete('/orders/{id}', [ShopOwnerController::class, 'cancelOrder']);
        Route::get('/profile', [ShopOwnerController::class, 'getProfile']);
        Route::put('/profile/update', [ShopOwnerController::class, 'updateProfile']);
        Route::get('/notifications', [ShopOwnerController::class, 'getNotifications']);
        Route::put('/notifications/{id}/read', [ShopOwnerController::class, 'markNotificationRead']);
        Route::delete('/notifications/{id}', [ShopOwnerController::class, 'deleteNotification']);
        Route::put('/update-password', [ShopOwnerController::class, 'updatePassword']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'getDashboard']);
        Route::get('/download-report', [AdminController::class, 'downloadReport']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::put('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus']);
        Route::get('/categories', [AdminController::class, 'getCategories']);
        Route::post('/categories', [AdminController::class, 'createCategory']);
        Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);
        Route::get('/orders', [AdminController::class, 'getOrders']);
        Route::put('/update-email', [AdminController::class, 'updateEmail']);
        Route::put('/update-password', [AdminController::class, 'updatePassword']);
        
        // Shop verification routes
        Route::get('/shops/pending', [AdminController::class, 'getPendingShops']);
        Route::get('/shops', [AdminController::class, 'getShops']);
        Route::put('/shops/{id}/approve', [AdminController::class, 'approveShop']);
        Route::put('/shops/{id}/reject', [AdminController::class, 'rejectShop']);
    });
});
