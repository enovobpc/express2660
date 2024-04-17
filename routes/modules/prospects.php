<?php
/*=================================================================================================================
 * PROSPECTION MANAGEMENT
 =================================================================================================================*/

Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {

    //MEETINGS
    Route::post('meetings/datatable', 'Prospects\MeetingsController@datatable')
        ->name('admin.meetings.datatable');

    Route::post('meetings/selected/destroy', 'Prospects\MeetingsController@massDestroy')
        ->name('admin.meetings.selected.destroy');

    Route::post('meetings/search/customer', 'Prospects\MeetingsController@searchCustomer')
        ->name('admin.meetings.search.customer');

    Route::resource('meetings', 'Prospects\MeetingsController', [
        'as' => 'admin',
        'except' => ['show']]);

    //PROSPECTS
    Route::post('prospects/datatable', 'Prospects\ProspectsController@datatable')
        ->name('admin.prospects.datatable');

    Route::post('prospects/selected/destroy', 'Prospects\ProspectsController@massDestroy')
        ->name('admin.prospects.selected.destroy');

    Route::post('prospects/{id}/convert', 'Prospects\ProspectsController@convert')
        ->name('admin.prospects.convert');

    Route::post('prospects/selected/activate', 'Prospects\ProspectsController@massActivate')
        ->name('admin.prospects.selected.activate');

    Route::post('prospects/{id}/business/history/datatable', 'Prospects\ProspectsController@datatableBusinessHistory')
        ->name('admin.prospects.business.history.datatable');

    Route::resource('prospects', 'Prospects\ProspectsController', [
        'as' => 'admin',
        'except' => ['show']]);
});