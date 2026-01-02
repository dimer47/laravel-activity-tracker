<?php

/*
|--------------------------------------------------------------------------
| Laravel Logger Web Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'activity', 'namespace' => 'Dimer47\LaravelActivityTracker\App\Http\Controllers', 'middleware' => (function () {
    $middleware = ['web', 'activity'];

    if (config('LaravelActivityTracker.authRequired', true)) {
        $middleware[] = 'auth';
    }

    return $middleware;
})()], function () {
    // Dashboards
    Route::get('/', 'ActivityTrackerController@showAccessLog')->name('activity');
    Route::get('/cleared', ['uses' => 'ActivityTrackerController@showClearedActivityLog'])->name('cleared');

    // Drill Downs
    Route::get('/log/{id}', 'ActivityTrackerController@showAccessLogEntry');
    Route::get('/cleared/log/{id}', 'ActivityTrackerController@showClearedAccessLogEntry');

    // Forms
    Route::delete('/clear-activity', ['uses' => 'ActivityTrackerController@clearActivityLog'])->name('clear-activity');
    Route::delete('/destroy-activity', ['uses' => 'ActivityTrackerController@destroyActivityLog'])->name('destroy-activity');
    Route::post('/restore-log', ['uses' => 'ActivityTrackerController@restoreClearedActivityLog'])->name('restore-activity');

    // LiveSearch
    Route::post('/live-search', ['uses' => 'ActivityTrackerController@liveSearch'])->name('liveSearch');

    // Export functionality
    Route::get('/export', ['uses' => 'ActivityTrackerController@exportActivityLog'])->name('export-activity');
});
