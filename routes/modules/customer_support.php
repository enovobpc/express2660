<?php
/*=================================================================================================================
 * CREATE BUDGETS - MODULE PDF
 =================================================================================================================*/

Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {
    

    //REQUESTS
    Route::post('customer-support/datatable', 'CustomerSupport\TicketsController@datatable')
        ->name('admin.customer-support.datatable');

    Route::post('customer-support/search/shipment', 'CustomerSupport\TicketsController@searchShipment')
        ->name('admin.customer-support.search.shipment');

    Route::post('customer-support/search/ticket', 'CustomerSupport\TicketsController@searchTicket')
        ->name('admin.customer-support.search.ticket');

    Route::post('customer-support/datatables/shipments', 'CustomerSupport\TicketsController@datatableShipments')
        ->name('admin.customer-support.datatable.shipments');

    Route::get('customer-support/{id}/attachment/{name}', 'CustomerSupport\TicketsController@attachment')
        ->name('admin.customer-support.attachment');

    Route::post('customer-support/{id}/adjudicate', 'CustomerSupport\TicketsController@adjudicate')
        ->name('admin.customer-support.adjudicate');

    Route::post('customer-support/{id}/conclude', 'CustomerSupport\TicketsController@conclude')
        ->name('admin.customer-support.conclude');

    Route::post('customer-support/selected/destroy', 'CustomerSupport\TicketsController@massDestroy')
        ->name('admin.customer-support.selected.destroy');

    Route::get('customer-support/sync/emails', 'CustomerSupport\TicketsController@syncEmails')
        ->name('admin.customer-support.sync.emails');

    Route::get('customer-support/{id}/merge', 'CustomerSupport\TicketsController@mergeBudget')
        ->name('admin.customer-support.merge');

    Route::post('customer-support/{id}/merge/store', 'CustomerSupport\TicketsController@mergeBudgetStore')
        ->name('admin.customer-support.merge.store');

    Route::resource('customer-support', 'CustomerSupport\TicketsController', [
        'as' => 'admin']);


    //MESSAGES
    Route::post('customer-support/{id}/messages/datatable', 'CustomerSupport\MessagesController@datatable')
        ->name('admin.customer-support.messages.datatable');

    Route::get('customer-support/{id}/messages/{name}/attachment', 'CustomerSupport\MessagesController@attachment')
        ->name('admin.customer-support.messages.attachment');

    Route::resource('customer-support.messages', 'CustomerSupport\MessagesController', [
        'as' => 'admin',
        'only' => ['create', 'store']]);

    //PROPOSES
    Route::post('customer-support/{id}/proposes/datatable', 'CustomerSupport\ProposesController@datatable')
        ->name('admin.customer-support.proposes.datatable');

    Route::resource('customer-support.proposes', 'CustomerSupport\ProposesController', [
        'as' => 'admin',
        'only' => ['create', 'store', 'destroy', 'show']]);

    Route::get('customer-support/{id}/proposes/{name}/attachment', 'CustomerSupport\ProposesController@attachment')
        ->name('admin.customer-support.proposes.attachment');

});