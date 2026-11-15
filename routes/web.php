<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', LocaleController::class)->name('locale');

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/process', [SiteController::class, 'process'])->name('process');
Route::get('/quality', [SiteController::class, 'quality'])->name('quality');
Route::get('/sustainability', [SiteController::class, 'sustainability'])->name('sustainability');
Route::get('/products', [SiteController::class, 'products'])->name('products');
Route::get('/products/{fabricProduct}', [SiteController::class, 'product'])->name('products.show');
Route::get('/gallery', [SiteController::class, 'gallery'])->name('gallery');
Route::get('/careers', [SiteController::class, 'careers'])->name('careers');
Route::get('/careers/{jobOpening}', [SiteController::class, 'career'])->name('careers.show');
Route::get('/blog', [SiteController::class, 'blog'])->name('blog');
Route::get('/blog/{post}', [SiteController::class, 'post'])->name('blog.show');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'inquire'])->name('contact.store');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');

Route::middleware('auth')->prefix('staff')->group(function () {
    Route::get('/trace/{code}', [StaffController::class, 'trace'])->name('staff.trace');
    Route::get('/invoices/{invoice}/pdf', [StaffController::class, 'invoice'])->name('staff.invoice');
    Route::get('/work-orders/{workOrder}/job-card', [StaffController::class, 'job-card'])->name('staff.job-card');
    Route::get('/work-orders/{workOrder}/packing-list', [StaffController::class, 'packing-list'])->name('staff.packing-list');
    Route::get('/reports/{report}', [StaffController::class, 'report'])->name('staff.report');
});
