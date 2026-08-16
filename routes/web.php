<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\PageSectionItemController;
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
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');

    Route::get('/pages/{page}/sections/create', [PageSectionController::class, 'create'])->name('pages.sections.create');
    Route::post('/pages/{page}/sections', [PageSectionController::class, 'store'])->name('pages.sections.store');
    Route::get('/pages/{page}/sections/{section}/edit', [PageSectionController::class, 'edit'])->name('pages.sections.edit');
    Route::put('/pages/{page}/sections/{section}', [PageSectionController::class, 'update'])->name('pages.sections.update');
    Route::delete('/pages/{page}/sections/{section}', [PageSectionController::class, 'destroy'])->name('pages.sections.destroy');
    Route::patch('/pages/{page}/sections/{section}/move/{direction}', [PageSectionController::class, 'move'])
        ->whereIn('direction', ['up', 'down'])->name('pages.sections.move');

    Route::get('/pages/{page}/sections/{section}/items/create', [PageSectionItemController::class, 'create'])->name('pages.sections.items.create');
    Route::post('/pages/{page}/sections/{section}/items', [PageSectionItemController::class, 'store'])->name('pages.sections.items.store');
    Route::get('/pages/{page}/sections/{section}/items/{item}/edit', [PageSectionItemController::class, 'edit'])->name('pages.sections.items.edit');
    Route::put('/pages/{page}/sections/{section}/items/{item}', [PageSectionItemController::class, 'update'])->name('pages.sections.items.update');
    Route::delete('/pages/{page}/sections/{section}/items/{item}', [PageSectionItemController::class, 'destroy'])->name('pages.sections.items.destroy');
    Route::patch('/pages/{page}/sections/{section}/items/{item}/move/{direction}', [PageSectionItemController::class, 'move'])
        ->whereIn('direction', ['up', 'down'])->name('pages.sections.items.move');
});

require __DIR__.'/auth.php';
