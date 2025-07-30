<?php

use App\Http\Controllers\Api\Auth\LogoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'Register']);
Route::post('/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);


//Home page
Route::get('/home', [HomeController::class, 'index']);
Route::get('/categories/{category}', [HomeController::class, 'productsByCategory']);
Route::get('/products/{product}', [HomeController::class, 'show']);

//Cart
Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('add/{product}', [CartController::class, 'add']);
    Route::post('update/{product}', [CartController::class, 'updateQnty']);
    Route::delete('remove/{product}', [CartController::class, 'destroy']);
});

//checkout
Route::prefix('checkout')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CheckoutController::class, 'index']);
    Route::post('/', [CheckoutController::class, 'store']);

    Route::get('/order-success/{order}', function (Order $order) {
        return response()->json([
            'message' => 'تم استلام الطلب بنجاح',
            'order' =>new OrderResource($order->load(['orderItems.product', 'address', 'payment'])),
        ]);
    })->name('api.success');
    Route::post('/cancel-order/{order}', [CheckoutController::class, 'cancel']);

});

//payment Integrations
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/payment/iframe', [PaymentController::class, 'getPaymentIframe'])->name('api.payment.iframe');;
    Route::get('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('api.payment.callback');
});




