<?php

/*
|--------------------------------------------------------------------------
| Fleet Gest Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin\Core'), function() {

    //LICENCES
    Route::post('licenses/datatable', 'LicenseController@datatable')
        ->name('admin.licenses.datatable');

    Route::get('license/details', 'LicenseController@details')
        ->name('admin.licenses.details');

    Route::get('licenses/calendar', 'LicenseController@calendar')
        ->name('admin.licenses.calendar.index');

    Route::resource('licenses', 'LicenseController', [
        'as' => 'admin']);

    //LICENCES PAYMENTS
    Route::post('licenses/{id}/payments/datatable', 'LicensesPaymentsController@datatable')
        ->name('admin.licenses.payments.datatable');

    Route::post('licenses/{id}/payments/datatable/details', 'LicenseController@datatableDetails')
        ->name('admin.licenses.payments.datatable.details');

    Route::post('licenses/{id}/payments/selected/destroy', 'LicensesPaymentsController@massDestroy')
        ->name('admin.licenses.payments.selected.destroy');

    Route::resource('licenses.payments', 'LicensesPaymentsController', [
        'as' => 'admin',
        'except' => ['index','show']]);

    //MODULES
    Route::resource('modules', 'ModulesController', [
        'as' => 'admin',
        'only' => ['index','store']]);

});