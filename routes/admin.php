<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProjectAttachmentController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\Website\BannerController;
use App\Http\Controllers\Admin\Website\MaintenanceController;
use App\Http\Controllers\Admin\Website\AboutController;
use App\Http\Controllers\Admin\Website\ContactController;
use App\Http\Controllers\Admin\Website\FaqController;
use App\Http\Controllers\Admin\Website\HomepageController;
use App\Http\Controllers\Admin\Website\PageController;
use App\Http\Controllers\Admin\Website\SeoController;
use App\Http\Controllers\Admin\Website\TestimonialController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Web Routes (React SPA)
|--------------------------------------------------------------------------
| All GET page routes render the React SPA (admin.dashboard.react).
| Functional POST/store/update/delete/download routes remain for backend actions.
*/

    // Dashboard (path "/" so route('admin.dashboard') = /admin, the React app root)
    Route::get('/', fn () => view('admin.dashboard.react'))->name('dashboard');

    // Legacy deep-link: /admin/dashboard -> the catch-all below also renders the
    // SPA shell; React redirects "/dashboard" to "/" so it always shows the dashboard.

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/{order}', fn () => view('admin.dashboard.react'))->name('show');
        Route::post('/{order}/status', [OrderController::class, 'changeStatus'])->name('status');
    });

    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/{project}', fn () => view('admin.dashboard.react'))->name('show');
        Route::get('/{project}/attachments', fn () => view('admin.dashboard.react'))->name('attachments.index');
        Route::post('/{project}/attachments', [ProjectAttachmentController::class, 'store'])->name('attachments.store');
        Route::get('/{project}/attachments/create', fn () => view('admin.dashboard.react'))->name('attachments.create');
        Route::get('/{project}/attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])->name('attachments.download');
        Route::delete('/{project}/attachments/{attachment}', [ProjectAttachmentController::class, 'destroy'])->name('attachments.destroy');
    });

    // Invoices (designs + numbering/settings)
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/designs', fn () => view('admin.dashboard.react'))->name('designs');
        Route::get('/settings', fn () => view('admin.dashboard.react'))->name('settings');
    });

    // Services
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/create', fn () => view('admin.dashboard.react'))->name('create');
        Route::get('/{service}/edit', fn () => view('admin.dashboard.react'))->name('edit');
        Route::get('/{service}/gallery', fn () => view('admin.dashboard.react'))->name('gallery');
        Route::post('/store', [ServiceController::class, 'store'])->name('store');
        Route::post('/{service}/update', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}/delete', [ServiceController::class, 'destroy'])->name('delete');
        Route::post('/{service}/gallery/store', [ServiceController::class, 'galleryStore'])->name('gallery.store');
        Route::delete('/gallery/{image}', [ServiceController::class, 'galleryDelete'])->name('gallery.delete');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/create', fn () => view('admin.dashboard.react'))->name('create');
        Route::get('/{category}/edit', fn () => view('admin.dashboard.react'))->name('edit');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::post('/{category}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/status', [CategoryController::class, 'toggleStatus'])->name('status');
    });

    // Users (Buyers + Sellers) - GET pages -> React
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/buyers', fn () => view('admin.dashboard.react'))->name('buyers.index');
        Route::get('/buyers/{buyer}', fn () => view('admin.dashboard.react'))->name('buyers.show');
        Route::post('/buyers/{buyer}/status', fn () => response()->json(['success' => true]))->name('buyers.status');
        Route::post('/buyers/{buyer}/ban', fn () => response()->json(['success' => true]))->name('buyers.ban');
        Route::get('/sellers', fn () => view('admin.dashboard.react'))->name('sellers.index');
        Route::get('/sellers/{seller}', fn () => view('admin.dashboard.react'))->name('sellers.show');
        Route::post('/sellers/{seller}/status', fn () => response()->json(['success' => true]))->name('sellers.status');
        Route::post('/sellers/{seller}/ban', fn () => response()->json(['success' => true]))->name('sellers.ban');
        Route::post('/sellers/{seller}/available', fn () => response()->json(['success' => true]))->name('sellers.available');
        Route::get('/suspicious-users', fn () => view('admin.dashboard.react'))->name('suspicious-users');
        Route::get('/login-devices', fn () => view('admin.dashboard.react'))->name('login-devices');
    });

    // Notifications - GET -> React
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/latest', fn () => view('admin.dashboard.react'))->name('latest');
        Route::get('/unread-count', fn () => view('admin.dashboard.react'))->name('count');
        Route::post('/{id}/read', fn () => response()->json(['success' => true]))->name('read');
        Route::post('/read-all', fn () => response()->json(['success' => true]))->name('read.all');
        Route::delete('/{id}', fn () => response()->json(['success' => true]))->name('destroy');
    });

    // Reports - GET -> React
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/invoices', fn () => view('admin.dashboard.react'))->name('invoices');
        Route::get('/sales', fn () => view('admin.dashboard.react'))->name('sales');
    });

    // Settings - GET pages -> React, functional POST/update -> controllers
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/{group?}', fn ($group = null) => view('admin.dashboard.react'))->where('group', 'admin|seller|buyer|frontend|auth')->name('admin');
        Route::get('/create/{group}', fn () => view('admin.dashboard.react'))->name('create');
        Route::get('/edit/{id}', fn () => view('admin.dashboard.react'))->name('edit');
        Route::post('/store', [SettingController::class, 'store'])->name('store');
        Route::post('/update/{id}', [SettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SettingController::class, 'destroy'])->name('delete');
    });

    // Menu - GET -> React, POST/update/delete functional
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/create', fn () => view('admin.dashboard.react'))->name('create');
        Route::get('/edit/{id}', fn () => view('admin.dashboard.react'))->name('edit');
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::post('/update/{id}', [MenuController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuController::class, 'destroy'])->name('destroy');
    });

    // Website management - GET -> React, functional store/update/delete
    Route::prefix('website')->name('website.')->group(function () {
        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
            Route::get('/create', fn () => view('admin.dashboard.react'))->name('create');
            Route::get('/{homepage}', fn () => view('admin.dashboard.react'))->name('show');
            Route::get('/{homepage}/edit', fn () => view('admin.dashboard.react'))->name('edit');
            Route::post('/', [HomepageController::class, 'store'])->name('store');
            Route::put('/{homepage}', [HomepageController::class, 'update'])->name('update');
            Route::delete('/{homepage}', [HomepageController::class, 'destroy'])->name('destroy');
        });
        Route::resource('about', AboutController::class, ['only' => []]);
        Route::resource('contact', ContactController::class, ['only' => []]);
        Route::resource('faq', FaqController::class, ['except' => ['index', 'show', 'create', 'edit']]);
        Route::resource('testimonials', TestimonialController::class, ['except' => ['index', 'show', 'create', 'edit']]);
        Route::resource('seo', SeoController::class, ['except' => ['index', 'show', 'create', 'edit']]);
        Route::resource('pages', PageController::class, ['except' => ['index', 'show', 'create', 'edit']]);
        Route::prefix('banners')->name('banners.')->group(function () {
            Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
            Route::get('/create', fn () => view('admin.dashboard.react'))->name('create');
            Route::get('/{banner}/edit', fn () => view('admin.dashboard.react'))->name('edit');
            Route::post('/store', [BannerController::class, 'store'])->name('store');
            Route::post('/{banner}/update', [BannerController::class, 'update'])->name('update');
            Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
        });
        Route::get('maintenance', fn () => view('admin.dashboard.react'))->name('maintenance.index');
        Route::put('maintenance', [MaintenanceController::class, 'update'])->name('maintenance.update');
    });

    // Modules - GET -> React
    Route::prefix('modules')->name('modules.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard.react'))->name('index');
        Route::get('/create', fn () => view('admin.dashboard.react'))->name('create');
        Route::get('/{module}/edit', fn () => view('admin.dashboard.react'))->name('edit');
        Route::post('/create', fn () => response()->json(['success' => true]))->name('store');
        Route::post('/{module}/update', fn () => response()->json(['success' => true]))->name('update');
        Route::delete('/{module}', fn () => response()->json(['success' => true]))->name('destroy');
        Route::post('/{module}/status', fn () => response()->json(['success' => true]))->name('status');
    });

    // Roles & Permissions - GET -> React
    Route::get('roles', fn () => view('admin.dashboard.react'))->name('roles.index');
    Route::get('roles/create', fn () => view('admin.dashboard.react'))->name('roles.create');
    Route::get('roles/{role}', fn () => view('admin.dashboard.react'))->name('roles.show');
    Route::get('roles/{role}/edit', fn () => view('admin.dashboard.react'))->name('roles.edit');
    Route::get('roles/{role}/permissions', fn () => view('admin.dashboard.react'))->name('roles.permissions');
    Route::post('roles', fn () => response()->json(['success' => true]))->name('roles.store');
    Route::put('roles/{role}', fn () => response()->json(['success' => true]))->name('roles.update');
    Route::delete('roles/{role}', fn () => response()->json(['success' => true]))->name('roles.destroy');
    Route::post('roles/{role}/permissions', fn () => response()->json(['success' => true]))->name('roles.permissions.store');

    Route::get('permissions', fn () => view('admin.dashboard.react'))->name('permissions.index');
    Route::get('permissions/create', fn () => view('admin.dashboard.react'))->name('permissions.create');
    Route::get('permissions/{permission}', fn () => view('admin.dashboard.react'))->name('permissions.show');
    Route::get('permissions/{permission}/edit', fn () => view('admin.dashboard.react'))->name('permissions.edit');
    Route::post('permissions', fn () => response()->json(['success' => true]))->name('permissions.store');
    Route::put('permissions/{permission}', fn () => response()->json(['success' => true]))->name('permissions.update');
    Route::delete('permissions/{permission}', fn () => response()->json(['success' => true]))->name('permissions.destroy');

    // Admin profile
    Route::get('profile', fn () => view('admin.dashboard.react'))->name('profile');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

// All other admin pages -> React SPA
Route::get('/{any}', fn () => view('admin.dashboard.react'))->where('any', '.*');
