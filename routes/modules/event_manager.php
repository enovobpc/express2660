<?php
/*
|--------------------------------------------------------------------------
| Event Manager Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should responde
| to using a Closure or controller method. Build something great!
|
*/
Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function () {

    Route::post('event-manager/status/{id}', 'EventsManagerController@statusUpdate')
        ->name('admin.event-manager.status.update');

    Route::post('event-manager/{eventId}/line/{lineId?}', 'EventsManagerController@updateEventLine')
        ->name('admin.event-manager.line.update');

    Route::delete('event-manager/{eventId}/line/{lineId}', 'EventsManagerController@removeEventLine')
        ->name('admin.event-manager.line.destroy');

    Route::post('event-manager/datatable', 'EventsManagerController@datatable')
        ->name('admin.event-manager.datatable');

    Route::post('event-manager/selected/destroy', 'EventsManagerController@massDestroy')
        ->name('admin.event-manager.selected.destroy');

    Route::resource('event-manager', 'EventsManagerController', [
        'as' => 'admin',
        'except' => ['show']
    ]);
});
