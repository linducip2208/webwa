<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AutoReplyController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MessageLogController;
use App\Http\Controllers\ProgrammaticSeoController;
use App\Http\Controllers\SendController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / marketing
|--------------------------------------------------------------------------
*/
Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/harga', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/docs', [DocsController::class, 'index'])->name('docs');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

/*
|--------------------------------------------------------------------------
| Programmatic SEO
|--------------------------------------------------------------------------
*/
Route::get('/best-whatsapp-gateway', [ProgrammaticSeoController::class, 'best'])->name('pseo.best');
Route::get('/whatsapp-gateway-untuk-{industry}', [ProgrammaticSeoController::class, 'industry'])->name('pseo.industry');
Route::get('/whatsapp-gateway-{city}', [ProgrammaticSeoController::class, 'city'])->name('pseo.city');
Route::get('/alternatif-{competitor}', [ProgrammaticSeoController::class, 'alternative'])->name('pseo.alternative');
Route::get('/bandingkan/{a}-vs-{b}', [ProgrammaticSeoController::class, 'compare'])->name('pseo.compare');

/*
|--------------------------------------------------------------------------
| Guest auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated user app
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Devices
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::post('/devices/{device}/connect', [DeviceController::class, 'connect'])->name('devices.connect');
    Route::get('/devices/{device}/state', [DeviceController::class, 'state'])->name('devices.state');
    Route::post('/devices/{device}/disconnect', [DeviceController::class, 'disconnect'])->name('devices.disconnect');
    Route::post('/devices/{device}/reset', [DeviceController::class, 'reset'])->name('devices.reset');

    // API keys
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::post('/api-keys/{apiKey}/toggle', [ApiKeyController::class, 'toggle'])->name('api-keys.toggle');
    Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

    // Send + logs
    Route::get('/send', [SendController::class, 'create'])->name('send.create');
    Route::post('/send', [SendController::class, 'store'])->name('send.store');
    Route::get('/logs', [MessageLogController::class, 'index'])->name('logs.index');

    // Auto replies
    Route::get('/auto-replies', [AutoReplyController::class, 'index'])->name('auto-replies.index');
    Route::post('/auto-replies', [AutoReplyController::class, 'store'])->name('auto-replies.store');
    Route::put('/auto-replies/{autoReply}', [AutoReplyController::class, 'update'])->name('auto-replies.update');
    Route::post('/auto-replies/{autoReply}/toggle', [AutoReplyController::class, 'toggle'])->name('auto-replies.toggle');
    Route::delete('/auto-replies/{autoReply}', [AutoReplyController::class, 'destroy'])->name('auto-replies.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/devices', [AdminController::class, 'devices'])->name('devices');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
});

/*
|--------------------------------------------------------------------------
| License v3 pairing wizard (/__pair)
|--------------------------------------------------------------------------
*/
require base_path('routes/pair-routes.php');
