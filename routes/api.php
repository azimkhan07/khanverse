<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\FrontendApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Admin\Api\DashboardApiController as AdminDashboardApiController;
use App\Http\Controllers\Admin\Api\OrderApiController as AdminOrderApiController;
use App\Http\Controllers\Admin\Api\ProjectApiController as AdminProjectApiController;
use App\Http\Controllers\Admin\Api\ServiceApiController as AdminServiceApiController;
use App\Http\Controllers\Admin\Api\CategoryApiController as AdminCategoryApiController;
use App\Http\Controllers\Admin\Api\UserApiController as AdminUserApiController;
use App\Http\Controllers\Admin\Api\RoleApiController as AdminRoleApiController;
use App\Http\Controllers\Admin\Api\SettingApiController as AdminSettingApiController;
use App\Http\Controllers\Admin\Api\MenuApiController as AdminMenuApiController;
use App\Http\Controllers\Admin\Api\ModuleApiController as AdminModuleApiController;
use App\Http\Controllers\Admin\Api\TutorialApiController as AdminTutorialApiController;
use App\Http\Controllers\Admin\Api\PermissionApiController as AdminPermissionApiController;
use App\Http\Controllers\Admin\Api\DeviceApiController as AdminDeviceApiController;
use App\Http\Controllers\Admin\Api\SuspiciousApiController as AdminSuspiciousApiController;
use App\Http\Controllers\Admin\Api\NotificationApiController as AdminNotificationApiController;
use App\Http\Controllers\Admin\Api\WebsiteApiController as AdminWebsiteApiController;
use App\Http\Controllers\Admin\Api\InvoiceDesignApiController as AdminInvoiceDesignApiController;
use App\Http\Controllers\Admin\Api\AdminEmailSettingApiController;
use App\Http\Controllers\Admin\Api\AdminPaymentGatewayApiController;
use App\Http\Controllers\Admin\Api\AdminInvoiceSettingApiController;
use App\Http\Controllers\Admin\Api\AdminSettlementApiController;

use App\Http\Controllers\Seller\Api\DashboardApiController as SellerDashboardApiController;
use App\Http\Controllers\Seller\Api\OrderApiController as SellerOrderApiController;
use App\Http\Controllers\Seller\Api\ServiceApiController as SellerServiceApiController;
use App\Http\Controllers\Seller\Api\ProjectApiController as SellerProjectApiController;
use App\Http\Controllers\Seller\Api\ReviewApiController as SellerReviewApiController;
use App\Http\Controllers\Seller\Api\WalletApiController as SellerWalletApiController;
use App\Http\Controllers\Seller\Api\AccountApiController as SellerAccountApiController;
use App\Http\Controllers\Api\ChatApiController;

use App\Http\Controllers\Buyer\Api\DashboardApiController as BuyerDashboardApiController;
use App\Http\Controllers\Buyer\Api\OrderApiController as BuyerOrderApiController;
use App\Http\Controllers\Buyer\Api\ProjectApiController as BuyerProjectApiController;
use App\Http\Controllers\Buyer\Api\ReviewApiController as BuyerReviewApiController;
use App\Http\Controllers\Buyer\Api\WalletApiController as BuyerWalletApiController;
use App\Http\Controllers\Buyer\Api\AccountApiController as BuyerAccountApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/auth-settings', [AuthController::class, 'authSettings'])->name('api.auth.settings');

Route::prefix('frontend')->name('frontend.')->group(function () {
    Route::get('/home', [FrontendApiController::class, 'home'])->name('home');
    Route::get('/categories', [FrontendApiController::class, 'categories'])->name('categories');
    Route::get('/faqs', [FrontendApiController::class, 'faqs'])->name('faqs');
    Route::get('/testimonials', [FrontendApiController::class, 'testimonials'])->name('testimonials');
    Route::get('/tutorials', [FrontendApiController::class, 'tutorials'])->name('tutorials');
    Route::get('/app-settings', [FrontendApiController::class, 'appSettings'])->name('app.settings');
    Route::get('/navigation', [FrontendApiController::class, 'navigation'])->name('navigation');
    Route::get('/footer', [FrontendApiController::class, 'footer'])->name('footer');
    Route::get('/categories/{slug}', [FrontendApiController::class, 'categoryServices'])->name('category.services')->where('slug', '[a-z0-9\-]+');
    Route::get('/services/{id}', [FrontendApiController::class, 'service'])->name('service.show')->where('id', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| Auth API Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'guest'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.password.reset');
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('api.otp.send');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('api.otp.verify');
});

/*
|--------------------------------------------------------------------------
| Auth API Routes (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::post('/lock/verify', [AuthController::class, 'verifyLock'])->name('api.lock.verify');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('api.verification.send');

    Route::post('/become-seller', [AuthController::class, 'becomeSeller'])->name('api.become-seller');
    Route::post('/become-buyer', [AuthController::class, 'becomeBuyer'])->name('api.become-buyer');
    Route::get('/role-record', [AuthController::class, 'currentRoleRecord'])->name('api.role-record');

    Route::prefix('chat')->name('api.chat.')->group(function () {
        Route::get('/', [ChatApiController::class, 'index'])->name('index');
        Route::get('/{conversation}', [ChatApiController::class, 'show'])->name('show');
        Route::get('/{conversation}/messages', [ChatApiController::class, 'loadMessages'])->name('messages');
        Route::post('/open', [ChatApiController::class, 'openConversation'])->name('open');
        Route::post('/send', [ChatApiController::class, 'sendMessage'])->name('send');
        Route::post('/{conversation}/seen', [ChatApiController::class, 'markAsSeen'])->name('seen');
        Route::delete('/message/{message}', [ChatApiController::class, 'deleteMessage'])->name('delete');
    });
});

/*
|--------------------------------------------------------------------------
| Contact API
|--------------------------------------------------------------------------
*/
Route::post('/contact', [ContactController::class, 'store'])->name('api.contact');

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:web', 'role:admin'])->prefix('admin')->name('admin.api.')->group(function () {
    Route::get('/dashboard/stats', [AdminDashboardApiController::class, 'index'])->name('dashboard.stats');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderApiController::class, 'show'])->name('show');
        Route::post('/{id}/status', [AdminOrderApiController::class, 'changeStatus'])->name('status');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [AdminProjectApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminProjectApiController::class, 'show'])->name('show');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [AdminServiceApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminServiceApiController::class, 'show'])->name('show');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminCategoryApiController::class, 'show'])->name('show');
        Route::post('/', [AdminCategoryApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminCategoryApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCategoryApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/status', [AdminCategoryApiController::class, 'toggleStatus'])->name('status');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserApiController::class, 'index'])->name('index');
        Route::get('/buyers', [AdminUserApiController::class, 'buyers'])->name('buyers');
        Route::get('/sellers', [AdminUserApiController::class, 'sellers'])->name('sellers');
        Route::get('/{id}', [AdminUserApiController::class, 'show'])->name('show');
        Route::post('/{id}/status', [AdminUserApiController::class, 'toggleStatus'])->name('status');
        Route::post('/{id}/ban', [AdminUserApiController::class, 'toggleBan'])->name('ban');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [AdminRoleApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminRoleApiController::class, 'show'])->name('show');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingApiController::class, 'index'])->name('index');
        Route::put('/', [AdminSettingApiController::class, 'update'])->name('update');
        Route::post('/', [AdminSettingApiController::class, 'store'])->name('store');
        Route::delete('/{id}', [AdminSettingApiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [AdminMenuApiController::class, 'index'])->name('index');
        Route::get('/parents', [AdminMenuApiController::class, 'parents'])->name('parents');
        Route::get('/{id}', [AdminMenuApiController::class, 'show'])->name('show');
        Route::post('/', [AdminMenuApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminMenuApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminMenuApiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('modules')->name('modules.')->group(function () {
        Route::get('/', [AdminModuleApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminModuleApiController::class, 'show'])->name('show');
        Route::post('/', [AdminModuleApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminModuleApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminModuleApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/status', [AdminModuleApiController::class, 'toggleStatus'])->name('status');
    });

    Route::prefix('tutorials')->name('tutorials.')->group(function () {
        Route::get('/', [AdminTutorialApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminTutorialApiController::class, 'show'])->name('show');
        Route::post('/', [AdminTutorialApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminTutorialApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminTutorialApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/status', [AdminTutorialApiController::class, 'toggleStatus'])->name('status');
    });

    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [AdminPermissionApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPermissionApiController::class, 'show'])->name('show');
        Route::post('/', [AdminPermissionApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminPermissionApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminPermissionApiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('/', [AdminDeviceApiController::class, 'index'])->name('index');
    });

    Route::prefix('suspicious')->name('suspicious.')->group(function () {
        Route::get('/', [AdminSuspiciousApiController::class, 'index'])->name('index');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminNotificationApiController::class, 'index'])->name('index');
        Route::get('/unread-count', [AdminNotificationApiController::class, 'unreadCount'])->name('unread-count');
        Route::post('/read-all', [AdminNotificationApiController::class, 'markAllRead'])->name('read-all');
        Route::post('/{id}/read', [AdminNotificationApiController::class, 'markRead'])->name('read');
        Route::delete('/{id}', [AdminNotificationApiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('website')->name('website.')->group(function () {
        Route::get('/banners', [AdminWebsiteApiController::class, 'banners'])->name('banners');
        Route::get('/banners/{banner}', [AdminWebsiteApiController::class, 'bannerShow'])->name('banners.show');
        Route::post('/banners', [AdminWebsiteApiController::class, 'bannersStore'])->name('banners.store');
        Route::put('/banners/{banner}', [AdminWebsiteApiController::class, 'bannersUpdate'])->name('banners.update');
        Route::delete('/banners/{banner}', [AdminWebsiteApiController::class, 'bannersDestroy'])->name('banners.destroy');
        Route::post('/banners/{banner}/status', [AdminWebsiteApiController::class, 'bannersToggle'])->name('banners.status');

        Route::get('/faqs', [AdminWebsiteApiController::class, 'faqs'])->name('faqs');
        Route::get('/faqs/{faq}', [AdminWebsiteApiController::class, 'faqShow'])->name('faqs.show');
        Route::post('/faqs', [AdminWebsiteApiController::class, 'faqsStore'])->name('faqs.store');
        Route::put('/faqs/{faq}', [AdminWebsiteApiController::class, 'faqsUpdate'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [AdminWebsiteApiController::class, 'faqsDestroy'])->name('faqs.destroy');
        Route::post('/faqs/{faq}/status', [AdminWebsiteApiController::class, 'faqsToggle'])->name('faqs.status');

        Route::get('/testimonials', [AdminWebsiteApiController::class, 'testimonials'])->name('testimonials');
        Route::get('/testimonials/{testimonial}', [AdminWebsiteApiController::class, 'testimonialShow'])->name('testimonials.show');
        Route::post('/testimonials', [AdminWebsiteApiController::class, 'testimonialsStore'])->name('testimonials.store');
        Route::put('/testimonials/{testimonial}', [AdminWebsiteApiController::class, 'testimonialsUpdate'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [AdminWebsiteApiController::class, 'testimonialsDestroy'])->name('testimonials.destroy');

        Route::get('/pages', [AdminWebsiteApiController::class, 'pages'])->name('pages');
        Route::get('/pages/{page}', [AdminWebsiteApiController::class, 'pageShow'])->name('pages.show');
        Route::post('/pages', [AdminWebsiteApiController::class, 'pagesStore'])->name('pages.store');
        Route::put('/pages/{page}', [AdminWebsiteApiController::class, 'pagesUpdate'])->name('pages.update');
        Route::delete('/pages/{page}', [AdminWebsiteApiController::class, 'pagesDestroy'])->name('pages.destroy');
        Route::post('/pages/{page}/status', [AdminWebsiteApiController::class, 'pagesToggle'])->name('pages.status');

        Route::get('/homepage-sections', [AdminWebsiteApiController::class, 'homepageSections'])->name('homepage-sections');
        Route::get('/homepage-sections/{section}', [AdminWebsiteApiController::class, 'homepageSectionShow'])->name('homepage-sections.show');
        Route::post('/homepage-sections', [AdminWebsiteApiController::class, 'homepageSectionsStore'])->name('homepage-sections.store');
        Route::put('/homepage-sections/{section}', [AdminWebsiteApiController::class, 'homepageSectionsUpdate'])->name('homepage-sections.update');
        Route::delete('/homepage-sections/{section}', [AdminWebsiteApiController::class, 'homepageSectionsDestroy'])->name('homepage-sections.destroy');
        Route::post('/homepage-sections/{section}/status', [AdminWebsiteApiController::class, 'homepageSectionsToggle'])->name('homepage-sections.status');

        Route::get('/seo', [AdminWebsiteApiController::class, 'seoSettings'])->name('seo');
        Route::get('/seo/{seoSetting}', [AdminWebsiteApiController::class, 'seoSettingShow'])->name('seo.show');
        Route::post('/seo', [AdminWebsiteApiController::class, 'seoSettingsStore'])->name('seo.store');
        Route::put('/seo/{seoSetting}', [AdminWebsiteApiController::class, 'seoSettingsUpdate'])->name('seo.update');
        Route::delete('/seo/{seoSetting}', [AdminWebsiteApiController::class, 'seoSettingsDestroy'])->name('seo.destroy');
        Route::post('/seo/{seoSetting}/status', [AdminWebsiteApiController::class, 'seoSettingsToggle'])->name('seo.status');

        Route::get('/maintenance', [AdminWebsiteApiController::class, 'maintenance'])->name('maintenance');
        Route::put('/maintenance', [AdminWebsiteApiController::class, 'maintenanceUpdate'])->name('maintenance.update');
    });

    Route::prefix('invoice-designs')->name('invoice-designs.')->group(function () {
        Route::get('/', [AdminInvoiceDesignApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminInvoiceDesignApiController::class, 'show'])->name('show');
        Route::get('/{id}/preview', [AdminInvoiceDesignApiController::class, 'preview'])->name('preview');
        Route::post('/', [AdminInvoiceDesignApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminInvoiceDesignApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminInvoiceDesignApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/status', [AdminInvoiceDesignApiController::class, 'toggleStatus'])->name('status');
    });

    Route::prefix('invoice-settings')->name('invoice-settings.')->group(function () {
        Route::get('/orders', [AdminInvoiceSettingApiController::class, 'orderSettings'])->name('orders');
        Route::put('/orders', [AdminInvoiceSettingApiController::class, 'updateOrderSettings'])->name('orders.update');
        Route::get('/invoices', [AdminInvoiceSettingApiController::class, 'invoiceSettings'])->name('invoices');
        Route::put('/invoices', [AdminInvoiceSettingApiController::class, 'updateInvoiceSettings'])->name('invoices.update');
    });

    Route::prefix('email-templates')->name('email-templates.')->group(function () {
        Route::get('/', [AdminEmailSettingApiController::class, 'templates'])->name('index');
        Route::get('/{id}', [AdminEmailSettingApiController::class, 'templateShow'])->name('show');
        Route::get('/{id}/preview', [AdminEmailSettingApiController::class, 'templatePreview'])->name('preview');
        Route::post('/', [AdminEmailSettingApiController::class, 'storeTemplate'])->name('store');
        Route::put('/{id}', [AdminEmailSettingApiController::class, 'updateTemplate'])->name('update');
        Route::delete('/{id}', [AdminEmailSettingApiController::class, 'deleteTemplate'])->name('delete');
        Route::post('/{id}/status', [AdminEmailSettingApiController::class, 'templateToggle'])->name('status');
    });

    Route::prefix('email-smtp')->name('email-smtp.')->group(function () {
        Route::get('/settings', [AdminEmailSettingApiController::class, 'smtpSettings'])->name('settings');
        Route::put('/settings', [AdminEmailSettingApiController::class, 'updateSmtp'])->name('settings.update');
        Route::post('/settings/test', [AdminEmailSettingApiController::class, 'testSmtp'])->name('settings.test');
    });

    Route::prefix('payment-gateways')->name('payment-gateways.')->group(function () {
        Route::get('/', [AdminPaymentGatewayApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPaymentGatewayApiController::class, 'show'])->name('show');
        Route::post('/', [AdminPaymentGatewayApiController::class, 'store'])->name('store');
        Route::put('/{id}', [AdminPaymentGatewayApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminPaymentGatewayApiController::class, 'destroy'])->name('delete');
        Route::post('/{id}/status', [AdminPaymentGatewayApiController::class, 'toggleStatus'])->name('status');
        Route::post('/{id}/default', [AdminPaymentGatewayApiController::class, 'setDefault'])->name('default');
    });

    Route::prefix('settlements')->name('settlements.')->group(function () {
        Route::get('/', [AdminSettlementApiController::class, 'index'])->name('index');
        Route::get('/stats', [AdminSettlementApiController::class, 'stats'])->name('stats');
        Route::get('/{id}', [AdminSettlementApiController::class, 'show'])->name('show');
        Route::post('/{id}/pay', [AdminSettlementApiController::class, 'markPaid'])->name('pay');
    });
});

/*
|--------------------------------------------------------------------------
| Seller API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:web', 'role:seller'])->prefix('seller')->name('seller.api.')->group(function () {
    Route::get('/dashboard', [SellerDashboardApiController::class, 'index'])->name('dashboard');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [SellerOrderApiController::class, 'index'])->name('index');
        Route::get('/{id}', [SellerOrderApiController::class, 'show'])->name('show');
        Route::post('/{id}/status', [SellerOrderApiController::class, 'changeStatus'])->name('status');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [SellerServiceApiController::class, 'index'])->name('index');
        Route::get('/{id}', [SellerServiceApiController::class, 'show'])->name('show');
        Route::post('/', [SellerServiceApiController::class, 'store'])->name('store');
        Route::put('/{id}', [SellerServiceApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [SellerServiceApiController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/gallery', [SellerServiceApiController::class, 'gallery'])->name('gallery');
        Route::post('/{id}/gallery', [SellerServiceApiController::class, 'uploadGallery'])->name('gallery.upload');
        Route::delete('/{id}/gallery/{imageId}', [SellerServiceApiController::class, 'deleteGalleryImage'])->name('gallery.delete');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [SellerProjectApiController::class, 'index'])->name('index');
        Route::get('/{id}', [SellerProjectApiController::class, 'show'])->name('show');
        Route::post('/{id}/status', [SellerProjectApiController::class, 'changeStatus'])->name('status');
        Route::get('/{id}/attachments', [SellerProjectApiController::class, 'attachments'])->name('attachments');
        Route::post('/{id}/attachments/upload', [SellerProjectApiController::class, 'uploadAttachment'])->name('attachments.upload');
        Route::get('/attachment/{id}/download', [SellerProjectApiController::class, 'downloadAttachment'])->name('attachments.download');
        Route::delete('/attachment/{id}', [SellerProjectApiController::class, 'deleteAttachment'])->name('attachments.delete');
        Route::get('/{id}/delivery-key', [SellerProjectApiController::class, 'showDeliveryKey'])->name('delivery-key');
        Route::post('/{id}/verify-key', [SellerProjectApiController::class, 'verifyKey'])->name('verify-key');
        Route::post('/{id}/forgot-key', [SellerProjectApiController::class, 'forgotKey'])->name('forgot-key');
        Route::get('/{id}/hostings', [SellerProjectApiController::class, 'viewHostings'])->name('hostings');
        Route::post('/{id}/deliver', [SellerProjectApiController::class, 'submitDelivery'])->name('deliver');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [SellerReviewApiController::class, 'index'])->name('index');
        Route::get('/{id}', [SellerReviewApiController::class, 'show'])->name('show');
    });

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [SellerWalletApiController::class, 'index'])->name('index');
        Route::get('/transactions', [SellerWalletApiController::class, 'transactions'])->name('transactions');
        Route::get('/transaction/{id}', [SellerWalletApiController::class, 'showTransaction'])->name('transaction.show');
        Route::post('/withdraw', [SellerWalletApiController::class, 'withdrawRequest'])->name('withdraw');
        Route::get('/withdraw-history', [SellerWalletApiController::class, 'withdrawHistory'])->name('withdraw.history');
    });

    Route::get('/profile', [SellerAccountApiController::class, 'profile'])->name('profile');
    Route::post('/profile', [SellerAccountApiController::class, 'updateProfile'])->name('profile.update');

    Route::get('/settings', [SellerAccountApiController::class, 'settings'])->name('settings');
    Route::post('/settings', [SellerAccountApiController::class, 'updateSettings'])->name('settings.update');

    Route::get('/notifications', [SellerAccountApiController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [SellerAccountApiController::class, 'markAllRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Buyer API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:web', 'role:buyer'])->prefix('buyer')->name('buyer.api.')->group(function () {
    Route::get('/dashboard', [BuyerDashboardApiController::class, 'index'])->name('dashboard');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [BuyerOrderApiController::class, 'index'])->name('index');
        Route::post('/', [BuyerOrderApiController::class, 'store'])->name('store');
        Route::get('/{id}', [BuyerOrderApiController::class, 'show'])->name('show');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [BuyerProjectApiController::class, 'index'])->name('index');
        Route::get('/{id}', [BuyerProjectApiController::class, 'show'])->name('show');
        Route::post('/{id}/hosting', [BuyerProjectApiController::class, 'submitHosting'])->name('hosting');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [BuyerReviewApiController::class, 'index'])->name('index');
        Route::get('/{id}', [BuyerReviewApiController::class, 'show'])->name('show');
        Route::post('/', [BuyerReviewApiController::class, 'store'])->name('store');
    });

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [BuyerWalletApiController::class, 'index'])->name('index');
        Route::get('/transactions', [BuyerWalletApiController::class, 'transactions'])->name('transactions');
        Route::post('/deposit', [BuyerWalletApiController::class, 'deposit'])->name('deposit');
    });

    Route::get('/profile', [BuyerAccountApiController::class, 'profile'])->name('profile');
    Route::post('/profile', [BuyerAccountApiController::class, 'updateProfile'])->name('profile.update');

    Route::get('/settings', [BuyerAccountApiController::class, 'settings'])->name('settings');
    Route::post('/settings', [BuyerAccountApiController::class, 'updateSettings'])->name('settings.update');

    Route::get('/notifications', [BuyerAccountApiController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [BuyerAccountApiController::class, 'markAllRead'])->name('notifications.read-all');
});
