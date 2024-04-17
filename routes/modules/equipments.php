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


Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin\Equipments'), function() {

    /**
     * Warehouses
     */
    Route::post('equipments/warehouses/datatable', 'WarehousesController@datatable')
        ->name('admin.equipments.warehouses.datatable');

    Route::post('equipments/warehouses/selected/destroy', 'WarehousesController@massDestroy')
        ->name('admin.equipments.warehouses.selected.destroy');

    Route::resource('equipments/warehouses', 'WarehousesController', [
        'as' => 'admin.equipments',
        'except' => ['show']]);

    /**
     *
     * Locations
     *
     */
    Route::post('equipments/locations/datatable', 'LocationsController@datatable')
        ->name('admin.equipments.locations.datatable');

    Route::post('equipments/locations/selected/destroy', 'LocationsController@massDestroy')
        ->name('admin.equipments.locations.selected.destroy');

    Route::resource('equipments/locations', 'LocationsController', [
        'as' => 'admin.equipments'
    ]);



    /**
     *
     * Categories
     *
     */
    Route::post('equipments/categories/datatable', 'CategoriesController@datatable')
        ->name('admin.equipments.categories.datatable');

    Route::get('equipments/categories/{id}/history', 'CategoriesController@history')
        ->name('admin.equipments.categories.history');

    Route::resource('equipments/categories', 'CategoriesController', [
        'as' => 'admin.equipments',
        'except' => ['show']]);

    /**
     *
     * Equipments
     *
     */
    Route::post('equipments/datatable', 'EquipmentsController@datatable')
        ->name('admin.equipments.datatable');

    Route::post('equipments/datatable/locations', 'EquipmentsController@datatableLocations')
        ->name('admin.equipments.datatable.locations');

    Route::post('equipments/selected/destroy', 'EquipmentsController@massDestroy')
        ->name('admin.equipments.selected.destroy');

    Route::post('equipments/picking/store', 'EquipmentsController@pickingStore')
        ->name('admin.equipments.picking.store');

    Route::post('equipments/search/customer', 'EquipmentsController@searchCustomer')
        ->name('admin.equipments.search.customer');

    Route::post('equipments/conference', 'EquipmentsController@importConferenceFile')
        ->name('admin.equipments.conference.store');

    Route::get('equipments/filter/export/{group?}', 'EquipmentsController@filterExportFile')
        ->name('admin.equipments.filter.export');

    Route::resource('equipments', 'EquipmentsController', [
        'as' => 'admin'
    ]);
    
});

/**
 * Exports
 */
Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {

    Route::get('export/equipments', 'Exports\EquipmentsController@productsList')
        ->name('admin.equipments.export');

    Route::post('export/filter/{group?}', 'Exports\EquipmentsController@filterExportFile')
        ->name('admin.equipments.filter.export.file');
   
   Route::get('export/file/{group?}', 'Exports\EquipmentsController@filterExportFile')
        ->name('admin.equipments.export.file');

});

/**
 * Print
 */
Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {

    Route::get('equipments/printer/inventory/{group?}', 'Printer\EquipmentsController@inventory')
        ->name('admin.equipments.printer.inventory');

    Route::get('equipments/printer/labels', 'Printer\EquipmentsController@labels')
        ->name('admin.equipments.printer.labels');

});