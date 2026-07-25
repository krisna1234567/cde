<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LegalController;
use App\Http\Controllers\Frontend\MediaController;
use App\Http\Controllers\Frontend\ProductServiceController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');

Route::prefix('product-services')->name('products.')->group(function (): void {
    Route::get('/', [ProductServiceController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductServiceController::class, 'show'])->name('show');
});

Route::prefix('projects')->name('projects.')->group(function (): void {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProjectController::class, 'show'])->name('show');
});

Route::prefix('media')->name('media.')->group(function (): void {
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::get('/{slug}', [MediaController::class, 'show'])->name('show');
});

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');
Route::get('/privacy-policy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-use', [LegalController::class, 'terms'])->name('terms');

Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
