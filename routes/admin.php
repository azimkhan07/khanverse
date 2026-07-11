<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProjectAttachmentController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {

        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/status', [OrderController::class, 'changeStatus'])->name('status');
    });

    // PROJECTS
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
    });

    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        Route::get('/', [ProjectAttachmentController::class, 'index'])->name('attachments.index');
        Route::post('/attachments', [ProjectAttachmentController::class, 'store'])->name('attachments.store');
        Route::get('/attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])->name('attachments.download');
        Route::delete('/attachments/{attachment}', [ProjectAttachmentController::class, 'destroy'])->name('attachments.destroy');
        Route::get('/attachments/create', [ProjectAttachmentController::class, 'create'])->name('attachments.create');
    });

    // SERVICES
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/create', [ServiceController::class, 'create'])->name('create');
        Route::post('/store', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::post('/{service}/update', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}/delete', [ServiceController::class, 'destroy'])->name('destroy');

        Route::get('/{service}/gallery', [ServiceController::class, 'gallery'])->name('gallery');
        Route::post('/{service}/gallery/store', [ServiceController::class, 'galleryStore'])->name('gallery.store');
        Route::delete('/gallery/{image}', [ServiceController::class, 'galleryDelete'])->name('gallery.delete');
    });

    // CATEGORIES
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/{category}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/status', [CategoryController::class, 'toggleStatus'])->name('status');
    });

    // Banner
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::post('/{banner}/update', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
        Route::post('/status/{banner}', [BannerController::class, 'status'])->name('status');
    });

    // USERS (Buyers + Sellers)
    Route::prefix('users')->name('users.')->group(function () {

        Route::get('/buyers', fn() => "Buyers List")->name('buyers.index');

        Route::get('/sellers', fn() => "Sellers List")->name('sellers.index');
    });

    Route::prefix('reports')->name('reports.')->group(function () {

        Route::get('/invoices', fn() => "Invoices Report")->name('invoices');

        Route::get('/sales', fn() => "Sales Report")->name('sales');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::get('/admin', [SettingController::class, 'admin'])->name('admin');
        Route::get('/seller', [SettingController::class, 'seller'])->name('seller');
        Route::get('/buyer', [SettingController::class, 'buyer'])->name('buyer');
        Route::get('/frontend', [SettingController::class, 'frontend'])->name('frontend');
        Route::get('/auth', [SettingController::class, 'auth'])->name('auth');

        //CRUD
        Route::get('/create/{group}', [SettingController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [SettingController::class, 'edit'])->name('edit');

        Route::post('/store', [SettingController::class, 'store'])->name('store');
        Route::post('/update/{id}', [SettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SettingController::class, 'destroy'])->name('delete');
    });

    Route::resource('menu', MenuController::class)->names('menu')->middleware(['auth', 'role:admin']);

    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/create', [MenuController::class, 'create'])->name('create');
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MenuController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [MenuController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuController::class, 'destroy'])->name('destroy');
    });

    // Modules
    Route::prefix('modules')->name('modules.')->group(function () {
        Route::get('/', [ModuleController::class, 'index'])->name('index');
        Route::get('/create', [ModuleController::class, 'create'])->name('create');
        Route::post('/store', [ModuleController::class, 'store'])->name('store');
        Route::get('/{module}/edit', [ModuleController::class, 'edit'])->name('edit');
        Route::post('/{module}/update', [ModuleController::class, 'update'])->name('update');
        // DELETE
        Route::delete('/{module}', [ModuleController::class, 'destroy'])->name('destroy');

        // STATUS TOGGLE
        Route::post('/{module}/status', [ModuleController::class, 'toggleStatus'])->name('status');
    });

    Route::resource('roles', RoleController::class);

    Route::resource('permissions', PermissionController::class);

    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');

    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.permissions.store');

    Route::get('/test-permission', function () {

        return "Permission Working";
    })->middleware('permission:users.view');
});
