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


Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin\AirWaybills'), function() {

    
    /**
     *
     * AWB > Models
     *
     */

    Route::post('air-waybills/models/datatable', 'ModelsController@datatable')
        ->name('admin.air-waybills.models.datatable');

    Route::post('air-waybills/models/selected/destroy', 'ModelsController@massDestroy')
        ->name('admin.air-waybills.models.selected.destroy');

    Route::resource('air-waybills/models', 'ModelsController', [
        'as' => 'admin.air-waybills',
        'except' => ['show']]);

    /**
     *
     * AWB > Providers
     *
     */

    Route::post('air-waybills/providers/datatable', 'ProvidersController@datatable')
        ->name('admin.air-waybills.providers.datatable');

    Route::post('air-waybills/providers/selected/destroy', 'ProvidersController@massDestroy')
        ->name('admin.air-waybills.providers.selected.destroy');

    Route::resource('air-waybills/providers', 'ProvidersController', [
        'as' => 'admin.air-waybills',
        'except' => ['show']]);

    /**
     *
     * AWB > Agents
     *
     */

    Route::post('air-waybills/agents/datatable', 'AgentsController@datatable')
        ->name('admin.air-waybills.agents.datatable');

    Route::post('air-waybills/agents/selected/destroy', 'AgentsController@massDestroy')
        ->name('admin.air-waybills.agents.selected.destroy');

    Route::resource('air-waybills/agents', 'AgentsController', [
        'as' => 'admin.air-waybills',
        'except' => ['show']]);

    /**
     *
     * AWB > Expenses
     *
     */

    Route::post('air-waybills/expenses/datatable', 'ExpensesController@datatable')
        ->name('admin.air-waybills.expenses.datatable');

    Route::post('air-waybills/expenses/selected/destroy', 'ExpensesController@massDestroy')
        ->name('admin.air-waybills.expenses.selected.destroy');

    Route::resource('air-waybills/expenses', 'ExpensesController', [
        'as' => 'admin.air-waybills',
        'except' => ['show']]);

    /**
     *
     * AWB > Goods Types
     *
     */

    Route::post('air-waybills/goods-types/datatable', 'GoodsTypesController@datatable')
        ->name('admin.air-waybills.goods-types.datatable');

    Route::post('air-waybills/goods-types/selected/destroy', 'GoodsTypesController@massDestroy')
        ->name('admin.air-waybills.goods-types.selected.destroy');

    Route::resource('air-waybills/goods-types', 'GoodsTypesController', [
        'as' => 'admin.air-waybills',
        'except' => ['show']]);

    /**
     *
     * AWB
     *
     */

    Route::post('air-waybills/datatable', 'WaybillsController@datatable')
        ->name('admin.air-waybills.datatable');

    Route::post('air-waybills/selected/destroy', 'WaybillsController@massDestroy')
        ->name('admin.air-waybills.selected.destroy');

    Route::get('air-waybills/{id}/print/pdf', 'WaybillsController@printAwb')
        ->name('admin.air-waybills.print.pdf');

    Route::get('air-waybills/{id}/print/hawbs', 'WaybillsController@printHawbs')
        ->name('admin.air-waybills.print.hawbs');

    Route::get('air-waybills/{id}/print/labels', 'WaybillsController@printLabels')
        ->name('admin.air-waybills.print.labels');

    Route::get('air-waybills/{id}/print/manifest', 'WaybillsController@printManifest')
        ->name('admin.air-waybills.print.manifest');

    Route::get('air-waybills/{id}/print/summary', 'WaybillsController@printSummary')
        ->name('admin.air-waybills.print.summary');

    Route::post('air-waybills/get/provider', 'WaybillsController@getProvider')
        ->name('admin.air-waybills.get.provider');

    Route::post('air-waybills/get/customer', 'WaybillsController@getCustomer')
        ->name('admin.air-waybills.get.customer');

    Route::post('air-waybills/get/price', 'WaybillsController@getPrice')
        ->name('admin.air-waybills.get.price');

    Route::get('air-waybills/search/customer', 'WaybillsController@searchCustomer')
        ->name('admin.air-waybills.search.customer');

    Route::post('air-waybills/search/airport', 'WaybillsController@searchAirport')
        ->name('admin.air-waybills.search.airport');

    Route::post('air-waybills/prefill', 'WaybillsController@create')
        ->name('admin.air-waybills.prefill');

    Route::post('air-waybills/{id}/replicate', 'WaybillsController@replicate')
        ->name('admin.air-waybills.replicate');

    Route::resource('air-waybills', 'WaybillsController', [
        'as' => 'admin',
        'except' => ['show']]);


    /**
     *
     * HAWB
     *
     */
    Route::resource('air-waybills/hawb', 'HouseWaybillsController', [
        'as' => 'admin.air-waybills',
        'except' => ['show']]);

    /**
     *
     * HAWB > Invoices
     *
     */
    Route::get('air-waybills/selected/billing', 'InvoicesController@massBilling')
        ->name('admin.air-waybills.selected.billing');
    
    Route::post('air-waybills/invoice/create', 'InvoicesController@createInvoice')
        ->name('admin.air-waybills.invoice.create');

    Route::post('air-waybills/{id}/invoice/destroy', 'InvoicesController@destroyInvoice')
        ->name('admin.air-waybills.invoice.destroy');

    Route::post('air-waybills/{id}/invoice/convert', 'InvoicesController@convertFromDraft')
        ->name('admin.air-waybills.invoice.convert');

    Route::get('air-waybills/{id}/invoice/download', 'InvoicesController@downloadInvoice')
        ->name('admin.air-waybills.invoice.download');

    Route::get('air-waybills/{id}/email/edit', 'InvoicesController@editBillingEmail')
        ->name('admin.air-waybills.email.edit');

    Route::post('air-waybills/{id}/email/submit', 'ExpressServicesController@submitBillingEmail')
        ->name('admin.air-waybills.email.submit');
});