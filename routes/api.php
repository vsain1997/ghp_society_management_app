<?php

use App\Http\Controllers\API\V1\ResourceController;
use App\Http\Controllers\API\V1\ComplaintController;
use App\Http\Controllers\API\V1\ServiceProviderController;
use App\Http\Controllers\API\V1\ServiceCategoryController;
use App\Http\Controllers\API\V1\SettingsController;
use App\Http\Controllers\API\V1\SosCategoryController;
use App\Http\Controllers\API\V1\SosController;
use App\Http\Controllers\API\V1\BillController;
use App\Http\Controllers\API\V1\DocumentController;
use App\Http\Controllers\API\V1\EventController;
use App\Http\Controllers\API\V1\NoticeController;
use App\Http\Controllers\API\V1\PollController;
use App\Http\Controllers\API\V1\ReferPropertyController;
use App\Http\Controllers\API\V1\SocietyController;
use App\Http\Controllers\API\V1\StaffController;
use App\Http\Controllers\API\V1\SupportController;
use App\Http\Controllers\API\V1\TradePropertyController;
use App\Http\Controllers\API\V1\VisitorController;
use App\Http\Controllers\API\V1\ParcelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\CheckinoutController;
use Illuminate\Support\Facades\RateLimiter;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('user/v1')->group(function () {


    //test-push
    Route::post('/test-push', [VisitorController::class, 'sendPushNotification']);

    // resource
    Route::get('/societies', [SocietyController::class, 'index'])->name('society.list');
    Route::get('/sliders', [ResourceController::class, 'sliders'])->name('resource.sliders');
    Route::get('/privacy-policy', [ResourceController::class, 'privacyPolicy'])->name('resource.privacyPolicy');
    Route::get('/terms-of-use', [ResourceController::class, 'termsUse'])->name('resource.termsUse');
    // auth
    Route::post('otp', [UserController::class, 'sendOtp'])->name('otp.generate');
    Route::post('otp-verify', [UserController::class, 'otpVerifyLogin'])->name('otp.verify');
    Route::post('login', [UserController::class, 'passwordLogin'])->name('password.login');


    Route::middleware(['auth:sanctum', 'auth.apisAppCommon'])->group(function () {

        // auth
        Route::post('logout', [UserController::class, 'logout'])->name('logout');

        //user
        Route::get('profile/{user_id?}', [UserController::class, 'getProfile'])->name('profile');
        Route::post('profile-update', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::post('change-password', [UserController::class, 'changePassword'])->name('password.update');

        // settings - notification settings
        Route::prefix('settings')->name('settings.')->group(function () {

            Route::post('/notification', [SettingsController::class, 'updateNotificationSettings'])->name('notifications.update');

            Route::get('/notifications', [SettingsController::class, 'notificationSettings'])->name('notifications');
        });
        // notification
        Route::prefix('notification')->name('notification.')->group(function () {

            Route::post('/mark-as-read', [UserController::class, 'markAsReadNotification'])->name('markAsReadNotification');

            Route::get('/list', [UserController::class, 'notificationList'])->name('notificationList');
        });

    });



    Route::middleware(['auth:sanctum', 'auth.residentAndSecurityGuardCommonApis'])->group(function () {

        // notice
        Route::prefix('notice')->name('notice.')->group(function () {

            Route::get('/all', [NoticeController::class, 'index'])->name('list');
            Route::get('/details/{id}', [NoticeController::class, 'details'])->name('details');
        });

        // event
        Route::prefix('event')->name('event.')->group(function () {

            Route::get('/all', [EventController::class, 'index'])->name('list');
            Route::get('/details/{id}', [EventController::class, 'details'])->name('details');
        });

        // sos
        Route::prefix('sos')->name('sos.')->group(function () {

            Route::get('/all', [SosController::class, 'index'])->name('all');

            Route::get('/categories', [SosController::class, 'getSosCategories'])->name('categories');

            Route::get('/elements', [SosController::class, 'elements'])->name('elements');
            // Route::get('/details/{id}', [SosController::class, 'details'])->name('details');
            Route::post('/send', [SosController::class, 'store'])->name('create');
            Route::post('/cancel', [SosController::class, 'cancelSOS'])->name('cancel');

            // below-api::not-for-app---start
            Route::post('/category', [SosCategoryController::class, 'store'])->name('category.create');
            // below-api::not-for-app---end./
        });

        // support
        Route::prefix('support')->name('support.')->group(function () {

            Route::get('/contact', [SupportController::class, 'getContact'])->name('notifications.update');

        });

        // society apis which require authentication
        Route::prefix('society')->name('society.')->group(function () {

            Route::get('/elements', [SocietyController::class, 'elements'])->name('elements');
            Route::get('/members', [SocietyController::class, 'memberList'])->name('members');
            Route::get('/society-members', [SocietyController::class, 'societyAllMembers'])->name('society.members');
            Route::get('/contacts', [SocietyController::class, 'societyContacts'])->name('contacts');
            // not for api use
            Route::get('/society-properties', [SocietyController::class, 'societyProperties'])->name('society.properties');
        });

        // visitor
        Route::prefix('visitor')->name('visitor.')->group(function () {

            Route::get('/all', [VisitorController::class, 'index'])->name('list');
            Route::get('/details/{id}', [VisitorController::class, 'details'])->name('details');
            Route::get('/elements', [VisitorController::class, 'elements'])->name('elements');
            Route::post('/create', [VisitorController::class, 'store'])->name('create');
            Route::post('/status/{id}', [VisitorController::class, 'changeStatus'])->name('changeStatus');
            Route::post('/resident-not-responding', [VisitorController::class, 'residentNotResponding'])->name('residentNotResponding');
            // Route::get('/visitor-count', [VisitorController::class, 'visitorCount'])->name('visitorCount');

            Route::post('/visitor-incoming-response', [VisitorController::class, 'visitorIncomingResponse'])->name('sendIncomingResponse');
        });

        // parcel
        Route::prefix('parcel')->name('parcel.')->group(function () {

            Route::get('/elements', [ParcelController::class, 'elements'])->name('elements');
            Route::post('/create', [ParcelController::class, 'store'])->name('create');
            Route::get('/all', [ParcelController::class, 'index'])->name('list');
            Route::get('/details/{id}', [ParcelController::class, 'details'])->name('details');
            Route::post('/parcel-received', [ParcelController::class, 'receiveParcel'])->name('receiveParcel');
        });

        // resident + daily help - checkin
        Route::prefix('resident')->name('resident.')->group(function () {
            Route::post('/checkin', [CheckinoutController::class, 'residentCheckIn'])->name('checkin');
            Route::post('/checkout', [CheckinoutController::class, 'residentCheckOut'])->name('checkout');

            Route::get('/all-checkin-log', [CheckinoutController::class, 'residentCheckinoutLog'])->name('residentCheckinoutLog');
            Route::get('/checkin-details/{user_id?}', [CheckinoutController::class, 'residentCheckinoutDetails'])->name('residentCheckinoutDetails');

            Route::get('/daily-help-all-checkin-log', [CheckinoutController::class, 'dailyHelpCheckinoutLog'])->name('dailyHelpCheckinoutLog');
            Route::get('/daily-help-checkin-details/{user_id?}', [CheckinoutController::class, 'dailyHelpCheckinoutDetails'])->name('dailyHelpCheckinoutDetails');
        });

    });

    // Route::middleware(['auth:sanctum', 'auth.serviceProviderApp'])->group(function () {

    // });

    Route::middleware(['auth:sanctum', 'auth.residentApp'])->group(function () {

        // document
        Route::prefix('document')->name('document.')->group(function () {

            // Route::post('/create', [DocumentController::class, 'store'])->name('create');
            // Route::get('/all', [DocumentController::class, 'index'])->name('list');
            Route::get('/details/{id}', [DocumentController::class, 'details'])->name('details');
            Route::get('/files/{id}', [DocumentController::class, 'getFiles'])->name('files');
            Route::get('/elements', [DocumentController::class, 'elements'])->name('elements');
            Route::get('/requests-count', [DocumentController::class, 'requestsCount'])->name('requestsCount');
            Route::post('/send-request', [DocumentController::class, 'sendRequest'])->name('sendRequest');
            Route::get('/outgoing-requests', [DocumentController::class, 'outgoingRequests'])->name('outgoingRequests');
            Route::get('/incoming-requests', [DocumentController::class, 'incomingRequests'])->name('incomingRequests');
            Route::post('/upload-document', [DocumentController::class, 'uploadDocument'])->name('upload');
            Route::delete('/delete/{id}', [DocumentController::class, 'destroy'])->name('delete');

        });

        //documents
        // Route::get('get-documenttype', [DocumentController::class, 'getDocumentsType'])->name('document.type');
        // Route::get('document-index', [DocumentController::class, 'getIndex'])->name('document.index');
        // Route::get('document-incomingIndex', [DocumentController::class, 'incomingIndex'])->name('document.incomingIndex');
        // Route::get('view-incomingIndex', [DocumentController::class, 'getDetail'])->name('document.view-incomingIndex');
        // Route::post('upload-documents', [DocumentController::class, 'uploadIncomingDocuments'])->name('document.upload-documents');

        //outgoing
        // Route::get('document-outgoingIndex', [DocumentController::class, 'outgoingIndex'])->name('document.ouutgoing');
        // Route::post('outgoing-request', [DocumentController::class, 'addRequest'])->name('outgoing-request');
        Route::get('download-document', [DocumentController::class, 'downloadDocuments'])->name('download.document');
        // Route::get('delete-outgoing-request', [DocumentController::class, 'deleteOutgoingRequest'])->name('delete.outgoing.request');


        // poll
        Route::prefix('poll')->name('poll.')->group(function () {

            Route::get('/all', [PollController::class, 'index'])->name('list');
            Route::post('/create', [PollController::class, 'store'])->name('create');
            // below-api::not-for-app---start
            Route::post('/vote/{poll_id}', [PollController::class, 'vote'])->name('vote');
            // below-api::not-for-app---end./
        });

        // refer-property
        Route::prefix('refer-property')->name('refer-property.')->group(function () {

            Route::get('/elements', [ReferPropertyController::class, 'elements'])->name('elements');
            Route::post('/create', [ReferPropertyController::class, 'store'])->name('create');
            Route::get('/all', [ReferPropertyController::class, 'index'])->name('list');
            Route::get('/details/{id}', [ReferPropertyController::class, 'details'])->name('details');
            Route::post('/update/{id}', [ReferPropertyController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [ReferPropertyController::class, 'destroy'])->name('destroy');

        });

        // bill
        Route::prefix('bill')->name('bill.')->group(function () {

            Route::get('/all', [BillController::class, 'index'])->name('list');
            Route::get('/details/{id}', [BillController::class, 'details'])->name('details');

            // below-api::not-for-app---start
            Route::post('/create', [BillController::class, 'store'])->name('create');
            // below-api::not-for-app---end./

            Route::post('/payment-details', [BillController::class, 'paymentDetails'])->name('payment.details');
        });

        // buy-sell-rent-property
        Route::prefix('trade')->name('trade.')->group(function () {

            Route::get('/elements', [TradePropertyController::class, 'elements'])->name('elements');

            Route::post('rent/create', [TradePropertyController::class, 'storeRent'])->name('rent.create');
            Route::post('sell/create', [TradePropertyController::class, 'storeSell'])->name('sell.create');

            Route::get('/mylisting/all', [TradePropertyController::class, 'myRentSellList'])->name('my.list');
            Route::get('/details/{id}', [TradePropertyController::class, 'detailsRentSell'])->name('my.list.details');


            Route::get('/societylisting/all', [TradePropertyController::class, 'mySocietiesOthersRentSellList'])->name('society.list');

            Route::post('/sell/update/{id}', [TradePropertyController::class, 'updateSell'])->name('my.sell.update');
            Route::post('/rent/update/{id}', [TradePropertyController::class, 'updateRent'])->name('my.rent.update');

            Route::delete('/rent-sell/delete/{id}', [TradePropertyController::class, 'destroy'])->name('delete');

        });

        // service-providers
        Route::prefix('service-provider')->name('service-provider.')->group(function () {

            Route::get('/categories', [ServiceProviderController::class, 'getServiceCategories'])->name('categories');
            Route::get('/all', [ServiceProviderController::class, 'getServiceProviders'])->name('all');
            Route::post('/callback-request', [ServiceProviderController::class, 'createRequestCallback'])->name('callback.request');

            // below-api::not-for-app---start
            Route::post('/category', [ServiceCategoryController::class, 'store'])->name('category.create');
            Route::post('/create', [ServiceProviderController::class, 'store'])->name('create');
            // below-api::not-for-app---end./
        });

        // complaint
        Route::prefix('complaint')->name('complaint.')->group(function () {

            Route::get('/elements', [ComplaintController::class, 'getComplaintCategories'])->name('elements');
            Route::post('/create', [ComplaintController::class, 'store'])->name('create');
            Route::get('/all/{id?}', [ComplaintController::class, 'getResidentComplaints'])->name('all.byCategoryId');
            Route::post('/status/cancel', [ComplaintController::class, 'statusCancelUpdate'])->name('status.cancel.update');
        });

        // staff
        Route::prefix('staff')->name('staff.')->group(function () {

            Route::get('/categories', [StaffController::class, 'getStaffCategories'])->name('categories');
            Route::get('/all', [StaffController::class, 'getStaff'])->name('all');

            // below-api::not-for-app---start
            Route::post('/create', [StaffController::class, 'store'])->name('create');
            // below-api::not-for-app---end./
        });

        // visitor-resident-specific
        Route::prefix('visitor')->name('visitor.')->group(function () {

            Route::post('/give-feedback', [VisitorController::class, 'giveFeedback'])->name('giveFeedback');
            Route::delete('/delete-visitor/{id}', [VisitorController::class, 'destroy'])->name('delete');

            Route::get('/visitor-incoming-requests-list', [VisitorController::class, 'visitorIncomingRequestList'])->name('visitorIncomingRequestList');
        });

        // parcel
        Route::prefix('parcel')->name('parcel.')->group(function () {

            Route::delete('/delete-parcel/{id}', [ParcelController::class, 'destroy'])->name('destroy');
            Route::post('/create-complaint', [ParcelController::class, 'createComplaint'])->name('createComplaint');

        });

    });

    Route::middleware(['auth:sanctum', 'auth.staffApp'])->group(function () {
        // staff
        Route::prefix('service')->name('service.')->group(function () {

            Route::get('/requests-history', [StaffController::class, 'getServiceHistory'])->name('requests.history');
            Route::get('/requests', [StaffController::class, 'getServiceRequest'])->name('requests');
            Route::get('/details', [StaffController::class, 'getServiceRequestDetails'])->name('details');
            Route::post('/start', [StaffController::class, 'startService'])->name('start');
            Route::post('/mark-as-done', [StaffController::class, 'completeService'])->name('markAsDone');

        });



    });

    Route::middleware(['auth:sanctum', 'auth.securityGuardApp'])->group(function () {

        // visitor
        Route::prefix('visitor')->name('visitor.')->group(function () {

            Route::post('/check-in', [VisitorController::class, 'checkIn'])->name('checkin');
            Route::post('/check-out', [VisitorController::class, 'checkOut'])->name('checkout');
            Route::post('/visitor-incoming-request', [VisitorController::class, 'visitorIncomingRequest'])->name('sendIncomingRequest');

        });

        // parcel
        Route::prefix('parcel')->name('parcel.')->group(function () {

            Route::post('/parcel-delivered', [ParcelController::class, 'deliverParcel'])->name('deliverParcel');
            Route::post('/check-out', [ParcelController::class, 'checkoutDeliveryAgent'])->name('checkoutDeliveryAgent');
            Route::get('/pending-count', [ParcelController::class, 'pendingParcelCount'])->name('pendingParcelCount');

        });

        // // notice
        // Route::prefix('notice')->name('notice.')->group(function () {

        //     Route::get('/all', [NoticeController::class, 'index'])->name('list');
        //     Route::get('/details/{id}', [NoticeController::class, 'details'])->name('details');
        // });

        // // event
        // Route::prefix('event')->name('event.')->group(function () {

        //     Route::get('/all', [EventController::class, 'index'])->name('list');
        //     Route::get('/details/{id}', [EventController::class, 'details'])->name('details');
        // });


        // sos
        Route::prefix('sos')->name('sos.')->group(function () {
            Route::post('/acknowledge', [SosController::class, 'acknowledgeSOS'])->name('acknowledgeSOS');
        });

        // // resident - checkin
        // Route::prefix('resident')->name('resident.')->group(function () {
        //     Route::post('/checkin', [CheckinoutController::class, 'residentCheckIn'])->name('checkin');
        //     Route::post('/checkout', [CheckinoutController::class, 'residentCheckOut'])->name('checkout');
        // });


    });

});
