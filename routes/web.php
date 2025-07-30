<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\PaymentController;

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

//Website Routes
Route::get('/', function () {
    return view('website.home');
})->name('home.page');

//Home Page
Route::get('/', [HomeController::class, 'index'])->name('home.page');
Route::get('/category/{category}', [HomeController::class, 'productsByCategory'])->name('products.byCategory');
Route::get('/products/{product}', [HomeController::class, 'show'])->name('single.page');

//Favourites
Route::middleware(['auth'])->group(function () {
    Route::post('/favourites/toggle/{product}', [FavouriteController::class, 'toggle'])->name('favourites.toggle');
    Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.page');

});

//Add to cart
Route::prefix('cart')->name('cart.')->group(function() {
    Route::get('/', [CartController::class, 'index'])->name('index'); 
    Route::post('add/{product}', [CartController::class, 'add'])->name('add'); 
    Route::post('update/{product}', [CartController::class, 'updateQnty'])->name('update'); 
    Route::delete('remove/{product}', [CartController::class, 'destroy'])->name('remove'); 

});

//CheckOut
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    // عرض صفحة الشراء
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    // حفظ الطلب
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    //رسالة نجاح اتمام الطلب
    Route::get('/order-success/{order}', function (Order $order) {
        return view('website.orders.orderSuccess',  compact('order'));
    })->name('success');

    Route::patch('/cancel/{order}', [CheckoutController::class, 'cancel'])->name('cancel');
    });


//show orders
Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/details/{order}', [OrderController::class, 'show'])->name('show');
});

//payment Integrations
Route::middleware('auth')->prefix('payment')->name('payment.')->group(function () {
    Route::get('/iframe', [PaymentController::class, 'getPaymentIframe'])->name('iframe');

    Route::get('/callback', [PaymentController::class, 'paymentCallback'])->name('callback');
});





//Admin Routes
Route::middleware(['auth', 'can:admin-control'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard page – بتعرض كل المستخدمين
    Route::get('/panel', [UserController::class, 'index'])->name('panel');

    // تفعيل / تعطيل مستخدم
    Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    //انشاء مستخدم
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    //إضافة مستخدم
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');

    //عرض المستخدم
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // تعديل مستخدم
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    // تحديث بيانات مستخدم
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    // حذف مستخدم
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::prefix('admin')->middleware(['auth', 'can:admin-control'])->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
});

Route::prefix('admin')->middleware(['auth', 'can:admin-control'])->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
});

Route::prefix('admin')->middleware(['auth', 'can:admin-control'])->name('admin.')->group(function () {
    Route::resource('discounts', DiscountController::class);
});

//Manage Orders
Route::middleware(['auth', 'can:admin-control'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
require __DIR__.'/auth.php';
