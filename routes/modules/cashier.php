<?php
/*=================================================================================================================
 * MODULE - CASHIER
 =================================================================================================================*/

Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {

    Route::post('cashier/datatable', 'Cashier\CashierController@datatable')
        ->name('admin.cashier.datatable');

    Route::post('cashier/search/customer', 'Cashier\CashierController@searchCustomer')
        ->name('admin.cashier.search.customer');

    Route::post('cashier/search/provider', 'Cashier\CashierController@searchProvider')
        ->name('admin.cashier.search.provider');

    Route::post('cashier/selected/destroy', 'Cashier\CashierController@massDestroy')
        ->name('admin.cashier.selected.destroy');

    Route::post('cashier/{id}/replicate', 'Cashier\CashierController@replicate')
        ->name('admin.cashier.replicate');

    Route::resource('cashier', 'Cashier\CashierController', [
        'as' => 'admin',
        'except' => ['show']]);
});