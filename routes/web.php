<?php

use App\Http\Controllers\BiblicalTriviaController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\OrderPrintfulController;
use App\Http\Controllers\Admin\PrintfulController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileOrderController;
use App\Http\Controllers\PrintfulProductController;




// web routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/artifacts', [StoreController::class, 'index'])->name('artifacts.index');
Route::post('/artifacts/filter', [StoreController::class, 'filterArtifacts'])->name('artifacts.filter');
Route::get('/artifacts/{product:slug}', [StoreController::class, 'show'])->name('artifacts.show');

Route::get('/products', [PrintfulProductController::class, 'index'])->name('printful-products.index');
Route::get('/products/{printfulProduct}', [PrintfulProductController::class, 'show'])->name('printful-products.show');

Route::get('/journal/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/biblical-trivia', [BiblicalTriviaController::class, 'index'])->name('biblical-trivia.index');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{variant}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{variantId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{variantId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/payment-intent', [CheckoutController::class, 'paymentIntent'])->name('checkout.payment-intent');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order/thank-you/{order:order_number}', [CheckoutController::class, 'thankYou'])->name('order.thank-you');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/addresses', [UserAddressController::class, 'index'])->name('profile.addresses.index');
    Route::get('/profile/addresses/create', [UserAddressController::class, 'create'])->name('profile.addresses.create');
    Route::post('/profile/addresses', [UserAddressController::class, 'store'])->name('profile.addresses.store');
    Route::get('/profile/addresses/{userAddress}/edit', [UserAddressController::class, 'edit'])->name('profile.addresses.edit');
    Route::patch('/profile/addresses/{userAddress}', [UserAddressController::class, 'update'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{userAddress}', [UserAddressController::class, 'destroy'])->name('profile.addresses.destroy');
    Route::patch('/profile/addresses/{userAddress}/default', [UserAddressController::class, 'setDefault'])->name('profile.addresses.default');

    Route::get('/profile/orders', [ProfileOrderController::class, 'index'])->name('profile.orders.index');
    Route::get('/profile/orders/{order}', [ProfileOrderController::class, 'show'])->name('profile.orders.show');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/printful/create-draft', [OrderPrintfulController::class, 'createDraft'])->name('orders.printful.create-draft');
    Route::post('/orders/{order}/printful/confirm', [OrderPrintfulController::class, 'confirm'])->name('orders.printful.confirm');
    Route::get('/orders/{order}/printful/status', [OrderPrintfulController::class, 'status'])->name('orders.printful.status');

    Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');

    Route::get('/categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
    Route::post('/categories', [ProductCategoryController::class, 'store'])->name('product-categories.store');
    Route::put('/categories/{category}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
    Route::delete('/categories/{category}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product:slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::delete('/products/{product:slug}/gallery-image/{productImage}', [ProductController::class, 'destroyGalleryImage'])
        ->name('products.gallery-image.destroy');
    Route::put('/products/{product:slug}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product:slug}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/printful/sync-products', [PrintfulController::class, 'syncProducts'])->name('admin.printful.sync-products');
    Route::get('/printful/products', [PrintfulController::class, 'index'])->name('admin.printful.products.index');
    Route::get('/printful/products/{printfulProduct}', [PrintfulController::class, 'show'])->name('admin.printful.products.show');

    Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('blog-categories.index');
    Route::post('/blog-categories', [BlogCategoryController::class, 'store'])->name('blog-categories.store');
    Route::put('/blog-categories/{blogCategory}', [BlogCategoryController::class, 'update'])->name('blog-categories.update');
    Route::delete('/blog-categories/{blogCategory}', [BlogCategoryController::class, 'destroy'])->name('blog-categories.destroy');

    Route::post('/blog-editor/image', [AdminBlogController::class, 'uploadBlogBodyImage'])->name('blogs.editor-image');

    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [AdminBlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit', [AdminBlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}', [AdminBlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('blogs.destroy');
});

Route::prefix('admin')->middleware(['auth', 'role:user'])->group(function () {});

Route::prefix('admin')->middleware(['auth', 'role:admin|user'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
});

require __DIR__.'/auth.php';
