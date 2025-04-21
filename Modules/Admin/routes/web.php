<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\App\Http\Controllers\AdminController;
use Modules\Admin\App\Http\Controllers\BillingController;
use Modules\Admin\App\Http\Controllers\DashboardController;
use Modules\Admin\App\Http\Controllers\NoticeController;
use Modules\Admin\App\Http\Controllers\NotificationController;
use Modules\Admin\App\Http\Controllers\ReferPropertyController;
use Modules\Admin\App\Http\Controllers\SosController;
use Modules\Admin\App\Http\Controllers\SocietyController;
use Modules\Admin\App\Http\Controllers\VisitorController;
use Modules\Admin\App\Http\Controllers\ParcelController;
use Modules\Admin\App\Http\Controllers\EventController;
use Modules\Admin\App\Http\Controllers\PollController;
use Modules\Admin\App\Http\Controllers\ComplaintsCategoryController;
use Modules\Admin\App\Http\Controllers\SosCategoryController;
use Modules\Admin\App\Http\Controllers\StaffController;
use Modules\Admin\App\Http\Controllers\ComplaintController;
use Modules\Admin\App\Http\Controllers\MemberController;
use Modules\Admin\App\Http\Controllers\ServiceProviderController;
use Modules\Admin\App\Http\Controllers\ServiceProviderCategoryController;
use Modules\Admin\App\Http\Controllers\TradeController;
use Modules\Admin\App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('admin', AdminController::class)->names('admin');
// });

Route::fallback(function () {
    return redirect()->route('admin.login.form'); // Redirect to login page
});

Route::middleware('guest')->group(function () {

    Route::get('/admin/login', [AdminController::class, 'showLoginForm'])
        ->name('admin.login.form');
    //
    Route::post('/admin/login', [AdminController::class, 'processLogin'])
        ->name('admin.login.process');

    Route::get('/admin/password/forgot', [AdminController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('/admin/password/email', [AdminController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/admin/password/reset/{token}', [AdminController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/admin/password/reset', [AdminController::class, 'reset'])->name('admin.password.update');
});


Route::prefix('admin')->name('admin.')->middleware('auth.admin')->group(function () {

    Route::get('/logout', [AdminController::class, 'processLogout'])
        ->name('logout.process');
    //user
    Route::post('/user/check/phone', [AdminController::class, 'checkPhone'])
        ->name('check.user.phone');
    Route::post('/user/check/email', [AdminController::class, 'checkEmail'])
        ->name('check.user.email');
    Route::post('/user/check/apertment', [AdminController::class, 'checkApertmentNo'])
        ->name('check.user.apertmentNo');
    Route::post('/user/check/checkVacancy', [AdminController::class, 'checkVacancy'])
        ->name('check.user.checkVacancy');

    //dashboard
    Route::post('/set-society-master', function (Request $request) {
        session(['__selected_society__' => $request->society_id]);
        return response()->json(['success' => true]);
    })->name('set.society.master');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // settings
    Route::post('/set-active-tab', function (Request $request) {
        // Store the active tab in the session
        session(['active_tab' => $request->input('activeTab')]);

        return response()->json(['status' => 'success']);
    })->name('set.active.tab');

    Route::get('/settings', [AdminController::class, 'profile'])->name('settings');

    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    //notice
    Route::prefix('notice')->name('notice.')->group(function () {

        Route::get('/', [NoticeController::class, 'index'])->name('index');
        Route::post('/create', [NoticeController::class, 'store'])->name('store');
        Route::get('/details/{id}', [NoticeController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [NoticeController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [NoticeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [NoticeController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [NoticeController::class, 'changeStatus'])->name('status.change');
    });

    //sos
    Route::prefix('sos')->name('sos.')->group(function () {

        Route::get('/', [SosController::class, 'index'])->name('index');
        Route::get('/details/{id}', [SosController::class, 'show'])->name('details');
    });

    //society
    Route::prefix('society')->name('society.')->group(function () {

        Route::get('/properties', [SocietyController::class, 'memberList'])->name('resident_unit.index');
        Route::get('/details/{id}', [SocietyController::class, 'show'])->name('resident_unit.details');
        Route::get('/get-block-floor', [SocietyController::class, 'getBlockFloor'])->name('getBlockFloor');

    });

    //visitor
    Route::prefix('visitor')->name('visitor.')->group(function () {

        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::get('/details/{id}', [VisitorController::class, 'show'])->name('details');
    });
    Route::prefix('visitor-other')->name('visitor-other.')->group(function () {

        Route::get('/', [VisitorController::class, 'indexOtherVisitor'])->name('index');
        Route::get('/details/{id}', [VisitorController::class, 'showOtherVisitor'])->name('details');
    });

    //event
    Route::prefix('event')->name('event.')->group(function () {

        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::post('/create', [EventController::class, 'store'])->name('store');
        Route::get('/details/{id}', [EventController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [EventController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [EventController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [EventController::class, 'destroy'])->name('delete');
    });

    //poll
    Route::prefix('poll')->name('poll.')->group(function () {

        Route::get('/', [PollController::class, 'index'])->name('index');
        Route::post('/create', [PollController::class, 'store'])->name('store');
        Route::get('/details/{id}', [PollController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [PollController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [PollController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PollController::class, 'destroy'])->name('delete');
    });

    // complaints_category
    Route::prefix('complaints-category')->name('complaints_category.')->group(function () {

        Route::get('/', [ComplaintsCategoryController::class, 'index'])->name('index');
        Route::post('/create', [ComplaintsCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ComplaintsCategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ComplaintsCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ComplaintsCategoryController::class, 'destroy'])->name('delete');
    });

    // sos_category
    Route::prefix('sos-category')->name('sos_category.')->group(function () {

        Route::get('/', [SosCategoryController::class, 'index'])->name('index');
        Route::post('/create', [SosCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SosCategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [SosCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SosCategoryController::class, 'destroy'])->name('delete');
    });

    //staff
    Route::prefix('staff')->name('staff.')->group(function () {

        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::post('/create', [StaffController::class, 'store'])->name('store');
        Route::get('/details/{id}', [StaffController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [StaffController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [StaffController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [StaffController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [StaffController::class, 'changeStatus'])->name('status.change');

        Route::post('/assign-member', [StaffController::class, 'assignMember'])->name('assignMember');
        Route::get('/getAssignedMembers/{staffUserId}', [StaffController::class, 'getAssignedMembers'])->name('getAssignedMembers');

    });

    //complaints
    Route::prefix('complaint')->name('complaint.')->group(function () {

        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::get('/details/{id}', [ComplaintController::class, 'show'])->name('details');
        Route::post('/assign/', [ComplaintController::class, 'assignServiceProvider'])->name('assign.serviceProvider');
    });

    //member
    Route::post('/get-blocks', [SocietyController::class, 'getBlocks'])->name('get.blocks');

    Route::prefix('member')->name('member.')->group(function () {

        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::post('/create', [MemberController::class, 'store'])->name('store');
        // Route::get('/show/{id}', [MemberController::class, 'show'])->name('show');
        Route::get('/details/{id}', [MemberController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [MemberController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [MemberController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MemberController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [MemberController::class, 'changeStatus'])->name('status.change');
    });

    //service_provider
    Route::prefix('service-provider')->name('service_provider.')->group(function () {

        Route::get('/', [ServiceProviderController::class, 'index'])->name('index');
        Route::post('/create', [ServiceProviderController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ServiceProviderController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ServiceProviderController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ServiceProviderController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [ServiceProviderController::class, 'changeStatus'])->name('status.change');
    });

    //service_provider
    Route::prefix('service-provider-category')->name('service_provider_category.')->group(function () {

        Route::get('/', [ServiceProviderCategoryController::class, 'index'])->name('index');
        Route::post('/create', [ServiceProviderCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ServiceProviderCategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ServiceProviderCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ServiceProviderCategoryController::class, 'destroy'])->name('delete');
    });

    //property listing
    Route::prefix('property-listing')->name('property_listing.')->group(function () {

        Route::get('/', [TradeController::class, 'index'])->name('index');
        Route::get('/details/{id}', [TradeController::class, 'show'])->name('details');
    });

    //BILLING - OLD
    Route::prefix('billing')->name('billing.')->group(function () {

        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::post('/create', [BillingController::class, 'store'])->name('store');
        Route::get('/details/{id}', [BillingController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [BillingController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [BillingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BillingController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [BillingController::class, 'changeStatus'])->name('status.change');


        //NEW ROUTES
        Route::match(['get', 'post'], '/create-new', [BillingController::class, 'createNewBill'])->name('add');
        Route::match(['get', 'post'], '/update-bill/{bill_id?}', [BillingController::class, 'updateBillingNew'])->name('update.bill');
        Route::match(['get', 'post'], '/collect-cash-payment/{id?}', [BillingController::class, 'collectCashPayment'])->name('collect.cash.payment');
        Route::match(['get', 'post'], '/payment-info/{bill_id?}', [BillingController::class, 'paymentInformations'])->name('payment.info');


    });



    //BILLING - NEW
    // Route::prefix('billing')->name('billing.')->group(function () {
    //     Route::get('/', [BillingController::class, 'index'])->name('index');
    //     Route::post('/create', [BillingController::class, 'store'])->name('store');
    //     Route::get('/details/{id}', [BillingController::class, 'show'])->name('details');
    //     Route::get('/edit/{id}', [BillingController::class, 'edit'])->name('edit');
    //     Route::post('/edit/{id}', [BillingController::class, 'update'])->name('update');
    //     Route::delete('/delete/{id}', [BillingController::class, 'destroy'])->name('delete');
    //     Route::post('/status-change/{id}/{status}', [BillingController::class, 'changeStatus'])->name('status.change');

    //     // NEW ROUTES
    //     Route::match(['get', 'post'], '/create-new', [BillingController::class, 'createNewBill'])->name('add');
    //     Route::match(['get', 'post'], '/collect-cash-payment/{id?}', [BillingController::class, 'collectCashPayment'])->name('collect.cash.payment');
    //     Route::match(['get', 'post'], '/update-bill/{bill_id?}', [BillingController::class, 'updateBillingNew'])->name('update.bill');
    //     Route::match(['get', 'post'], '/payment-info/{bill_id?}', [BillingController::class, 'paymentInformations'])->name('payment.info');
    // });



    //refer_property
    Route::prefix('refer-property')->name('refer_property.')->group(function () {

        Route::get('/', [ReferPropertyController::class, 'index'])->name('index');
        Route::get('/details/{id}', [ReferPropertyController::class, 'show'])->name('details');
    });

    //document
    Route::prefix('document')->name('document.')->group(function () {

        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/details/{id}', [DocumentController::class, 'show'])->name('details');
        Route::post('/send-request', [DocumentController::class, 'store'])->name('sendRequest');
        Route::post('/upload-document', [DocumentController::class, 'uploadDocument'])->name('uploadDocument');
        Route::delete('/delete/{id}', [DocumentController::class, 'destroy'])->name('delete');
    });

    // notification
    Route::prefix('notification')->name('notification.')->group(function () {

        Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('getNotifications');
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::post('/notification-settings/update', [NotificationController::class, 'updateNotificationSettings'])->name('settings.update');

    });

    //visitor
    Route::prefix('parcel')->name('parcel.')->group(function () {

        Route::get('/', [ParcelController::class, 'index'])->name('index');
        Route::get('/details/{id}', [ParcelController::class, 'show'])->name('details');
    });

});
