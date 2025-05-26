<?php

use Illuminate\Support\Facades\Route;
use Modules\SuperAdmin\App\Http\Controllers\BillingController;
use Modules\SuperAdmin\App\Http\Controllers\ComplaintController;
use Modules\SuperAdmin\App\Http\Controllers\ComplaintsCategoryController;
use Modules\SuperAdmin\App\Http\Controllers\DashboardController;
use Modules\SuperAdmin\App\Http\Controllers\DocumentController;
use Modules\SuperAdmin\App\Http\Controllers\EventController;
use Modules\SuperAdmin\App\Http\Controllers\NoticeController;
use Modules\SuperAdmin\App\Http\Controllers\NotificationController;
use Modules\SuperAdmin\App\Http\Controllers\ParcelController;
use Modules\SuperAdmin\App\Http\Controllers\PollController;
use Modules\SuperAdmin\App\Http\Controllers\ReferPropertyController;
use Modules\SuperAdmin\App\Http\Controllers\ServiceProviderCategoryController;
use Modules\SuperAdmin\App\Http\Controllers\ServiceProviderController;
use Modules\SuperAdmin\App\Http\Controllers\SosCategoryController;
use Modules\SuperAdmin\App\Http\Controllers\SosController;
use Modules\SuperAdmin\App\Http\Controllers\StaffController;
use Modules\SuperAdmin\App\Http\Controllers\SuperAdminController;
use Modules\SuperAdmin\App\Http\Controllers\SocietyController;
use Modules\SuperAdmin\App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Modules\SuperAdmin\App\Http\Controllers\BillingsController;
use Modules\SuperAdmin\App\Http\Controllers\TradeController;
use Modules\SuperAdmin\App\Http\Controllers\VisitorController;
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

Route::fallback(function () {
    return redirect()->route('superadmin.login.form'); // Redirect to login page
});

Route::middleware('guest')->group(function () {

    Route::get('/superadmin/login', [SuperAdminController::class, 'showLoginForm'])
        ->name('superadmin.login.form');
    //
    Route::post('/superadmin/login', [SuperAdminController::class, 'processLogin'])
        ->name('superadmin.login.process');


    // Route::get('/superadmin/forget-password', [SuperAdminController::class, 'showForgotPasswordForm'])
    //     ->name('superadmin.forget.password');

    Route::get('/superadmin/password/forgot', [SuperAdminController::class, 'showLinkRequestForm'])->name('superadmin.password.request');
    Route::post('/superadmin/password/email', [SuperAdminController::class, 'sendResetLinkEmail'])->name('superadmin.password.email');
    Route::get('/superadmin/password/reset/{token}', [SuperAdminController::class, 'showResetForm'])->name('superadmin.password.reset');
    Route::post('/superadmin/password/reset', [SuperAdminController::class, 'reset'])->name('superadmin.password.update');
});

Route::prefix('superadmin')->name('superadmin.')->middleware('auth.superadmin')->group(function () {

    Route::get('/logout', [SuperAdminController::class, 'processLogout'])
        ->name('logout.process');
    //user
    Route::post('/user/check/phone', [SuperAdminController::class, 'checkPhone'])
        ->name('check.user.phone');
    Route::post('/user/check/email', [SuperAdminController::class, 'checkEmail'])
        ->name('check.user.email');
    Route::post('/user/check/apertment', [SuperAdminController::class, 'checkApertmentNo'])
        ->name('check.user.apertmentNo');
    Route::post('/user/check/isAlreadyAdminExist', [SuperAdminController::class, 'isAlreadyAdminExist'])
        ->name('check.user.isAlreadyAdminExist');
    Route::post('/user/check/checkVacancy', [SuperAdminController::class, 'checkVacancy'])
        ->name('check.user.checkVacancy');
    Route::get('/get-state', [SuperAdminController::class, 'getStateByPlaceMatch'])
        ->name('getStateByPlaceMatch');

    // dashboard
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

    Route::get('/settings', [SuperAdminController::class, 'profile'])->name('settings');

    Route::post('/profile', [SuperAdminController::class, 'updateProfile'])->name('profile.update');

    Route::post('/update-permission', [SuperAdminController::class, 'updatePermission'])->name('permission.update');

    Route::post('/update-permission-bulk', [SuperAdminController::class, 'bulkUpdatePermissions'])->name('permission.update.bulk');

    Route::get('/get-user-permissions', [SuperAdminController::class, 'getUserPermissions'])->name('user.permission.get');

    //society
    Route::prefix('society')->name('society.')->group(function () {

        Route::post('/create', [SocietyController::class, 'store'])->name('store');
        Route::get('/details/{id}', [SocietyController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [SocietyController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [SocietyController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SocietyController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [SocietyController::class, 'changeStatus'])->name('status.change');
        Route::get('/properties', [SocietyController::class, 'memberList'])->name('resident_unit.index');
        Route::get('/get-block-floor', [SocietyController::class, 'getBlockFloor'])->name('getBlockFloor');

    });
    Route::post('/get-blocks', [SocietyController::class, 'getBlocks'])->name('get.blocks');


    //member
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

    //visitor
    Route::prefix('visitor')->name('visitor.')->group(function () {

        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::get('/details/{id}', [VisitorController::class, 'show'])->name('details');
    });
    Route::prefix('visitor-other')->name('visitor-other.')->group(function () {

        Route::get('/', [VisitorController::class, 'indexOtherVisitor'])->name('index');
        Route::get('/details/{id}', [VisitorController::class, 'showOtherVisitor'])->name('details');
    });

    // notification
    Route::prefix('notification')->name('notification.')->group(function () {

        Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('getNotifications');
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::post('/notification-settings/update', [NotificationController::class, 'updateNotificationSettings'])->name('settings.update');

    });

    // parcel
    Route::prefix('parcel')->name('parcel.')->group(function () {

        Route::get('/', [ParcelController::class, 'index'])->name('index');
        Route::get('/details/{id}', [ParcelController::class, 'show'])->name('details');
    });

    //property listing
    Route::prefix('property-listing')->name('property_listing.')->group(function () {

        Route::get('/', [TradeController::class, 'index'])->name('index');
        Route::get('/details/{id}', [TradeController::class, 'show'])->name('details');
    });

    // sos_category
    Route::prefix('sos-category')->name('sos_category.')->group(function () {

        Route::get('/', [SosCategoryController::class, 'index'])->name('index');
        Route::post('/create', [SosCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SosCategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [SosCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SosCategoryController::class, 'destroy'])->name('delete');
    });

    //sos
    Route::prefix('sos')->name('sos.')->group(function () {

        Route::get('/', [SosController::class, 'index'])->name('index');
        Route::get('/details/{id}', [SosController::class, 'show'])->name('details');
    });

    //refer_property
    Route::prefix('refer-property')->name('refer_property.')->group(function () {

        Route::get('/', [ReferPropertyController::class, 'index'])->name('index');
        Route::get('/details/{id}', [ReferPropertyController::class, 'show'])->name('details');
    });

    //complaints
    Route::prefix('complaint')->name('complaint.')->group(function () {

        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::get('/details/{id}', [ComplaintController::class, 'show'])->name('details');
        Route::post('/assign/', [ComplaintController::class, 'assignServiceProvider'])->name('assign.serviceProvider');
    });

    // complaints_category
    Route::prefix('complaints-category')->name('complaints_category.')->group(function () {

        Route::get('/', [ComplaintsCategoryController::class, 'index'])->name('index');
        Route::post('/create', [ComplaintsCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ComplaintsCategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ComplaintsCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ComplaintsCategoryController::class, 'destroy'])->name('delete');
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

    //document
    Route::prefix('document')->name('document.')->group(function () {

        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/details/{id}', [DocumentController::class, 'show'])->name('details');
        Route::post('/send-request', [DocumentController::class, 'store'])->name('sendRequest');
        Route::post('/upload-document', [DocumentController::class, 'uploadDocument'])->name('uploadDocument');
        Route::delete('/delete/{id}', [DocumentController::class, 'destroy'])->name('delete');
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

    //billing
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::post('/create', [BillingController::class, 'store'])->name('store');
        Route::get('/details/{id}', [BillingController::class, 'show'])->name('details');
        Route::get('/edit/{id}', [BillingController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [BillingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BillingController::class, 'destroy'])->name('delete');
        Route::post('/status-change/{id}/{status}', [BillingController::class, 'changeStatus'])->name('status.change');

        // NEW ROUTES
        Route::match(['get', 'post'], '/create-new', [BillingController::class, 'createNewBill'])->name('add');
        Route::match(['get', 'post'], '/collect-cash-payment/{id?}', [BillingController::class, 'collectCashPayment'])->name('collect.cash.payment');
        Route::match(['get', 'post'], '/update-bill/{bill_id?}', [BillingController::class, 'updateBillingNew'])->name('update.bill');
        Route::match(['get', 'post'], '/payment-info/{bill_id?}', [BillingController::class, 'paymentInformations'])->name('payment.info');
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
        // below for daily help staff
        Route::post('/assign-member', [StaffController::class, 'assignMember'])->name('assignMember');
        Route::get('/getAssignedMembers/{staffUserId}', [StaffController::class, 'getAssignedMembers'])->name('getAssignedMembers');

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

    //service_provider_category
    Route::prefix('service-provider-category')->name('service_provider_category.')->group(function () {

        Route::get('/', [ServiceProviderCategoryController::class, 'index'])->name('index');
        Route::post('/create', [ServiceProviderCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ServiceProviderCategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ServiceProviderCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ServiceProviderCategoryController::class, 'destroy'])->name('delete');
    });

    Route::get('/societies', function (Request $request) {
        // Store the active tab in the session
        session(['active_tab' => '#tab3-tab']);

        // Redirect to the specified URL with the stored active tab
        return redirect(url('superadmin/settings'));
    })->name('settings.societies');

    Route::get('/access-permission', function (Request $request) {
        // Store the active tab in the session
        session(['active_tab' => '#tab4-tab']);

        // Redirect to the specified URL with the stored active tab
        return redirect(url('superadmin/settings'));
    })->name('settings.accessPermission');




    //     Route::get('/users', [UserController::class, 'index'])->name('admin.users');
//     Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
//     Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
});


// Route::get('/superadmin', [SuperAdminController::class, 'login'])->name('superadmin.loginPage')->middleware('CheckAuth');

// Route::group([], function () {
//     Route::resource('superadmin', SuperAdminController::class)->names('superadmin');
// });
