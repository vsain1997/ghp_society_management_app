<?php

use App\Http\Controllers\CronJobsController;
use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/clean', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');

    return 'cleaned';
});

Route::get('/migrate', function () {
    Artisan::call('migrate'); // Run migrations
    try {
        Artisan::call('migrate', ['--force' => false]);

        return 'migrated successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::post('/trigger-queue-process', function () {
    try {
        // Log the start of the process
        Log::info("Test Btn ========>:::");
        Log::info("Queue process triggered manually.");

        // Execute the queue:work command
        Artisan::call('queue:work', [
            '--stop-when-empty' => true, // Stops the worker after processing all jobs
        ]);

        // Log successful execution
        Log::info("Queue processed successfully.");
        Log::info("Test Btn :::<========");

        // Redirect back with success message
        return response()->json(['status' => 'success', 'message' => 'Queue processed successfully!']);
    } catch (\Exception $e) {
        // Log the error
        Log::error("Error processing queue: " . $e->getMessage());

        // Redirect back with error message
        return response()->json(['status' => 'error', 'message' => 'Failed to process queue.']);
    }
});

if (config('app.env') === 'local') {
    Route::get('/fixUsersSettings', [TestingController::class, 'fixUsersSettings']);
}

Route::get('/run-schedule', function () {
    try {
        Log::info("Manual trigger: schedule:run started.");

        // Call the schedule:run command
        Artisan::call('schedule:run');

        Log::info("Manual trigger: schedule:run completed successfully.");
        return response()->json(['success' => true, 'message' => 'Scheduler ran successfully']);
    } catch (\Exception $e) {
        Log::error("Error running schedule:run: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to run scheduler']);
    }
});
// -------DO NOT REMOVE BELOW----------
Route::get('/csrf-refresh', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.refresh');






/** WHATSAPP WEBHOOKS & CRON JOBS ROUTES - START **/

    Route::prefix('jobs')->name('cron.jobs')->group(function(){
        Route::get('/check-bills-and-send-reminders', [CronJobsController::class, 'billReminder'])->name('bill.reminder');
    });

/** WHATSAPP WEBHOOKS & CRON JOBS ROUTES - END **/
