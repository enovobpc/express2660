<?php
/*=================================================================================================================
 * CREATE BUDGETS - MODULE PDF
 =================================================================================================================*/

Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {
//BUDGETS MODELS
    Route::post('budgets/courier/models/datatable', 'Budgets\CourierModelsController@datatable')
        ->name('admin.budgets.courier.models.datatable');

    Route::post('budgets/courier/models/selected/destroy', 'Budgets\CourierModelsController@massDestroy')
        ->name('admin.budgets.courier.models.selected.destroy');

    Route::resource('budgets/courier/models', 'Budgets\CourierModelsController', [
        'as' => 'admin.budgets.courier',
        'except' => ['show']]);


//BUDGETS SERVICES
    Route::post('budgets/courier/services/datatable', 'Budgets\CourierServicesController@datatable')
        ->name('admin.budgets.courier.services.datatable');

    Route::post('budgets/courier/services/selected/destroy', 'Budgets\CourierServicesController@massDestroy')
        ->name('admin.budgets.courier.services.selected.destroy');

    Route::resource('budgets/courier/services', 'Budgets\CourierServicesController', [
        'as' => 'admin.budgets.courier',
        'except' => ['show', 'create', 'edit']]);


//COURIER BUDGETS
    Route::post('budgets/animals/datatable', 'Budgets\CourierController@datatable')
        ->name('admin.budgets.courier.datatable');

    Route::post('budgets/animals/selected/destroy', 'Budgets\CourierController@massDestroy')
        ->name('admin.budgets.courier.selected.destroy');

    Route::get('budgets/animals/{id}/email/edit', 'Budgets\CourierController@editEmail')
        ->name('admin.budgets.courier.email.edit');

    Route::post('budgets/courier/model/get', 'Budgets\CourierController@getModel')
        ->name('admin.budgets.courier.model.get');

    Route::post('budgets/courier/{id}/email/send', 'Budgets\CourierController@sendEmail')
        ->name('admin.budgets.courier.email.send');

    Route::get('budgets/courier/{id}/print', 'Budgets\CourierController@printBudget')
        ->name('admin.budgets.courier.print');

    Route::post('budgets/courier/search/customer', 'Budgets\CourierController@searchCustomer')
        ->name('admin.budgets.courier.search.customer');

    Route::get('budgets/courier/stats', 'Budgets\CourierController@stats')
        ->name('admin.budgets.courier.stats');

    Route::resource('budgets/courier', 'Budgets\CourierController', [
        'as' => 'admin.budgets',
        'except' => ['show']]);

//ANIMAL BUDGETS
    Route::resource('budgets/animals', 'Budgets\AnimalsController', [
        'as' => 'admin.budgets',
        'only' => ['index']]);


    /*=================================================================================================================
     * CREATE BUDGETS - MODULE EMAIL
     =================================================================================================================*/
//REQUESTS
    Route::post('budgets/datatable', 'Budgets\BudgetsController@datatable')
        ->name('admin.budgets.datatable');

    Route::post('budgets/datatables/shipments', 'Budgets\BudgetsController@datatableShipments')
        ->name('admin.budgets.datatable.shipments');

    Route::get('budgets/{id}/attachment/{name}', 'Budgets\BudgetsController@attachment')
        ->name('admin.budgets.attachment');

    Route::post('budgets/{id}/adjudicate', 'Budgets\BudgetsController@adjudicate')
        ->name('admin.budgets.adjudicate');

    Route::post('budgets/selected/destroy', 'Budgets\BudgetsController@massDestroy')
        ->name('admin.budgets.selected.destroy');

    Route::get('budgets/sync/emails', 'Budgets\BudgetsController@syncEmails')
        ->name('admin.budgets.sync.emails');

    Route::get('budgets/contacts/list', 'Budgets\BudgetsController@showContactsList')
        ->name('admin.budgets.contacts.list');

    Route::post('budgets/contacts/list/update', 'Budgets\BudgetsController@updateContactsList')
        ->name('admin.budgets.contacts.list.update');

    Route::get('budgets/{id}/merge', 'Budgets\BudgetsController@mergeBudget')
        ->name('admin.budgets.merge');

    Route::post('budgets/{id}/merge/store', 'Budgets\BudgetsController@mergeBudgetStore')
        ->name('admin.budgets.merge.store');

    Route::post('budgets/search/budget', 'Budgets\BudgetsController@searchBudget')
        ->name('admin.budgets.search.budget');

    Route::resource('budgets', 'Budgets\BudgetsController', [
        'as' => 'admin']);


//MESSAGES
    Route::post('budgets/{id}/messages/datatable', 'Budgets\MessagesController@datatable')
        ->name('admin.budgets.messages.datatable');

    Route::get('budgets/{id}/messages/{name}/attachment', 'Budgets\MessagesController@attachment')
        ->name('admin.budgets.messages.attachment');

    Route::resource('budgets.messages', 'Budgets\MessagesController', [
        'as' => 'admin',
        'only' => ['create', 'store']]);

//PROPOSES
    Route::post('budgets/{id}/proposes/datatable', 'Budgets\ProposesController@datatable')
        ->name('admin.budgets.proposes.datatable');

    Route::resource('budgets.proposes', 'Budgets\ProposesController', [
        'as' => 'admin',
        'only' => ['create', 'store', 'destroy', 'show']]);

    Route::get('budgets/{id}/proposes/{name}/attachment', 'Budgets\ProposesController@attachment')
        ->name('admin.budgets.proposes.attachment');

});