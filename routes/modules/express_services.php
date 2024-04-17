<?php
/*=================================================================================================================
 * MODULE - EXPRESS SERVICES
 =================================================================================================================*/
Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {
    Route::post('express-services/datatable', 'ExpressServicesController@datatable')
        ->name('admin.express-services.datatable');

    Route::post('express-services/selected/destroy', 'ExpressServicesController@massDestroy')
        ->name('admin.express-services.selected.destroy');

    Route::get('express-services/selected/billing', 'ExpressServicesController@massBilling')
        ->name('admin.express-services.selected.billing');

    Route::post('express-services/search/customer', 'ExpressServicesController@searchCustomer')
        ->name('admin.express-services.search.customer');

    Route::post('express-services/invoice/create', 'ExpressServicesController@createInvoice')
        ->name('admin.express-services.invoice.create');

    Route::post('express-services/{id}/invoice/destroy', 'ExpressServicesController@destroyInvoice')
        ->name('admin.express-services.invoice.destroy');

    Route::post('express-services/{id}/invoice/convert', 'ExpressServicesController@convertFromDraft')
        ->name('admin.express-services.invoice.convert');

    Route::get('express-services/{id}/invoice/download', 'ExpressServicesController@downloadInvoice')
        ->name('admin.express-services.invoice.download');

    Route::get('express-services/{id}/email/edit', 'ExpressServicesController@editBillingEmail')
        ->name('admin.express-services.email.edit');

    Route::post('express-services/{id}/email/submit', 'ExpressServicesController@submitBillingEmail')
        ->name('admin.express-services.email.submit');

    Route::resource('express-services', 'ExpressServicesController', [
        'as' => 'admin',
        'except' => ['show']]);
});