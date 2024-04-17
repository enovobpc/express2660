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


Route::group(array('prefix' => 'admin/fleet', 'middleware' => 'auth.admin', 'namespace' => 'Admin\FleetGest'), function () {

    /**
     * 
     * FleetGest > Brands
     * 
     */

    Route::post('brands/datatable', 'BrandsController@datatable')
        ->name('admin.fleet.brands.datatable');

    Route::post('brands/selected/destroy', 'BrandsController@massDestroy')
        ->name('admin.fleet.brands.selected.destroy');

    Route::resource('brands', 'BrandsController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Models
     *
     */

    Route::post('brand-models/datatable', 'BrandsModelsController@datatable')
        ->name('admin.fleet.brand-models.datatable');

    Route::post('brand-models/selected/destroy', 'BrandsModelsController@massDestroy')
        ->name('admin.fleet.brand-models.selected.destroy');

    Route::resource('brand-models', 'BrandsModelsController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Parts
     *
     */

    Route::post('parts/datatable', 'PartsController@datatable')
        ->name('admin.fleet.parts.datatable');

    Route::post('parts/selected/destroy', 'PartsController@massDestroy')
        ->name('admin.fleet.parts.selected.destroy');

    Route::resource('parts', 'PartsController', [
        'as' => 'admin.fleet',
        'except' => ['show', 'store', 'update', 'create', 'edit']
    ]);

    /**
     *
     * FleetGest > Services
     *
     */

    Route::post('services/datatable', 'ServicesController@datatable')
        ->name('admin.fleet.services.datatable');

    Route::post('services/selected/destroy', 'ServicesController@massDestroy')
        ->name('admin.fleet.services.selected.destroy');

    Route::resource('services', 'ServicesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Providers
     *
     */

    Route::post('providers/datatable', 'ProvidersController@datatable')
        ->name('admin.fleet.providers.datatable');

    Route::post('providers/selected/destroy', 'ProvidersController@massDestroy')
        ->name('admin.fleet.providers.selected.destroy');

    Route::resource('providers', 'ProvidersController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);


    /**
     *
     * FleetGest > Vehicles
     *
     */

    Route::post('vehicles/datatable', 'VehiclesController@datatable')
        ->name('admin.fleet.vehicles.datatable');

    Route::post('vehicles/datatable/checklists', 'VehiclesController@datatableChecklists')
        ->name('admin.fleet.vehicles.datatable.checklists');

    Route::post('vehicles/selected/destroy', 'VehiclesController@massDestroy')
        ->name('admin.fleet.vehicles.selected.destroy');

    Route::post('vehicles/{id}/history/datatable', 'VehiclesController@datatableHistory')
        ->name('admin.fleet.vehicles.history.datatable');

    Route::post('vehicles/get/brand-models', 'VehiclesController@getBrandModels')
        ->name('admin.fleet.vehicles.get.brand-models');

    Route::post('vehicles/set/auto-names', 'VehiclesController@setAutomaticName')
        ->name('admin.fleet.vehicles.set.auto-names');

    Route::resource('vehicles', 'VehiclesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);


    /**
     *
     * FleetGest > Vehicles > Attachments
     *
     */

    Route::post('vehicles/{id}/attachments/datatable', 'VehiclesAttachmentsController@datatable')
        ->name('admin.fleet.vehicles.attachments.datatable');

    Route::post('vehicles/{id}/attachments/selected/destroy', 'VehiclesAttachmentsController@massDestroy')
        ->name('admin.fleet.vehicles.attachments.selected.destroy');

    Route::get('vehicles/{id}/attachments/sort', 'VehiclesAttachmentsController@sortEdit')
        ->name('admin.fleet.vehicles.attachments.sort');

    Route::post('vehicles/{id}/attachments/sort', 'VehiclesAttachmentsController@sortUpdate')
        ->name('admin.fleet.vehicles.attachments.sort.update');

    Route::resource('vehicles.attachments', 'VehiclesAttachmentsController', [
        'as' => 'admin.fleet',
        'except' => ['show', 'index']
    ]);


    /**
     *
     * FleetGest > Fuel
     *
     */

    Route::post('fuel/datatable', 'FuelLogsController@datatable')
        ->name('admin.fleet.fuel.datatable');

    Route::post('fuel/selected/destroy', 'FuelLogsController@massDestroy')
        ->name('admin.fleet.fuel.selected.destroy');

    Route::resource('fuel', 'FuelLogsController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Tolls
     *
     */

    Route::post('tolls/datatable', 'TollsLogsController@datatable')
        ->name('admin.fleet.tolls.datatable');

    Route::post('tolls/selected/destroy', 'TollsLogsController@massDestroy')
        ->name('admin.fleet.tolls.selected.destroy');

    Route::post('tolls/import', 'TollsLogsController@import')
        ->name('admin.fleet.tolls.import');

    Route::resource('tolls', 'TollsLogsController', [
        'as' => 'admin.fleet',
        'only' => ['index', 'show', 'destroy']
    ]);


    /**
     *
     * FleetGest > Incidences > Images
     *
     */
    Route::post('incidences/{vehicleId}/images/selected/destroy', 'IncidencesImagesController@massDestroy')
        ->name('admin.fleet.incidences.images.selected.destroy');

    Route::get('incidences/{vehicleId}/images/sort', 'ProductsImagesController@sortEdit')
        ->name('admin.fleet.incidences.images.sort');

    Route::post('incidences/{vehicleId}/images/sort', 'ProductsImagesController@sortUpdate')
        ->name('admin.fleet.incidences.images.sort.update');

    Route::post('incidences/{vehicleId}/images/{id}/cover', 'ProductsImagesController@setCover')
        ->name('admin.fleet.incidences.images.cover');

    Route::resource('incidences.images', 'ProductsImagesController', [
        'as' => 'admin.fleet',
        'only' => ['index', 'store', 'destroy']
    ]);


    /**
     *
     * FleetGest > Incidences
     *
     */

    Route::post('incidences/datatable', 'IncidencesController@datatable')
        ->name('admin.fleet.incidences.datatable');

    Route::get('incidences/{id}/fix/edit', 'IncidencesController@fixEdit')
        ->name('admin.fleet.incidences.fix.edit');

    Route::post('incidences/{id}/fix/update', 'IncidencesController@fixUpdate')
        ->name('admin.fleet.incidences.fix.update');

    Route::post('incidences/selected/destroy', 'IncidencesController@massDestroy')
        ->name('admin.fleet.incidences.selected.destroy');

    Route::resource('incidences', 'IncidencesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Maintenance
     *
     */
    Route::post('maintenances/datatable', 'MaintenancesController@datatable')
        ->name('admin.fleet.maintenances.datatable');

    Route::post('maintenances/selected/destroy', 'MaintenancesController@massDestroy')
        ->name('admin.fleet.maintenances.selected.destroy');

    Route::get('maintenances/search/services', 'MaintenancesController@searchServices')
        ->name('admin.fleet.maintenances.search.services');

    Route::get('maintenances/search/parts', 'MaintenancesController@searchParts')
        ->name('admin.fleet.maintenances.search.parts');

    Route::get('maintenances/{id}/print', 'MaintenancesController@printMaintenance')
        ->name('admin.fleet.maintenances.print');

    Route::resource('maintenances', 'MaintenancesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Expenses
     *
     */
    Route::post('expenses/datatable', 'ExpensesController@datatable')
        ->name('admin.fleet.expenses.datatable');

    Route::post('expenses/selected/destroy', 'ExpensesController@massDestroy')
        ->name('admin.fleet.expenses.selected.destroy');

    Route::get('expenses/search/services', 'ExpensesController@searchExpenses')
        ->name('admin.fleet.expenses.search.expense');

    Route::resource('expenses', 'ExpensesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Reminders
     *
     */
    Route::post('reminders/datatable', 'RemindersController@datatable')
        ->name('admin.fleet.reminders.datatable');

    Route::post('reminders/selected/destroy', 'RemindersController@massDestroy')
        ->name('admin.fleet.reminders.selected.destroy');

    Route::post('reminders/{id}/conclude', 'RemindersController@conclude')
        ->name('admin.fleet.reminders.conclude');

    Route::get('reminders/reset/edit', 'RemindersController@resetEdit')
        ->name('admin.fleet.reminders.reset.edit');

    Route::post('reminders/reset/store', 'RemindersController@resetStore')
        ->name('admin.fleet.reminders.reset.store');

    Route::resource('reminders', 'RemindersController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Fixed Costs
     *
     */
    Route::post('fixed-costs/datatable', 'FixedCostsController@datatable')
        ->name('admin.fleet.fixed-costs.datatable');

    Route::post('fixed-costs/selected/destroy', 'FixedCostsController@massDestroy')
        ->name('admin.fleet.fixed-costs.selected.destroy');
        
    Route::post('fixed-costs/search/provider', 'FixedCostsController@searchProviderSelect2')
        ->name('admin.invoices.sales.search.provider');

    Route::resource('fixed-costs', 'FixedCostsController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Accessories
     *
     */
    Route::post('accessories/datatable', 'AccessoriesController@datatable')
        ->name('admin.fleet.accessories.datatable');

    Route::post('accessories/selected/destroy', 'AccessoriesController@massDestroy')
        ->name('admin.fleet.accessories.selected.destroy');

    Route::resource('accessories', 'AccessoriesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Usages
     *
     */
    Route::post('usages/datatable', 'UsagesController@datatable')
        ->name('admin.fleet.usages.datatable');

    Route::post('usages/selected/destroy', 'UsagesController@massDestroy')
        ->name('admin.fleet.usages.selected.destroy');

    Route::resource('usages', 'UsagesController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Tyres
     *
     */
    Route::post('tyres/datatable', 'TyresController@datatable')
        ->name('admin.fleet.tyres.datatable');

    Route::post('tyres/selected/destroy', 'TyresController@massDestroy')
        ->name('admin.fleet.tyres.selected.destroy');

    Route::resource('tyres', 'TyresController', [
        'as' => 'admin.fleet',
        'except' => ['show']
    ]);

    /**
     *
     * FleetGest > Checklists
     *
     */
    Route::post('checklists/datatable', 'ChecklistsController@datatable')
        ->name('admin.fleet.checklists.datatable');

    Route::post('checklists/selected/destroy', 'ChecklistsController@massDestroy')
        ->name('admin.fleet.checklists.selected.destroy');

    Route::get('checklists/{id}/answer', 'ChecklistsController@answerEdit')
        ->name('admin.fleet.checklists.answer.edit');

    Route::post('checklists/{id}/answer', 'ChecklistsController@answerStore')
        ->name('admin.fleet.checklists.answer.store');

    Route::post('checklists/answers/load', 'ChecklistsController@answersLoad')
        ->name('admin.fleet.checklists.answer.load');

    Route::get('checklists/{checkListId}/answer/{hash}', 'ChecklistsController@answerShow')
        ->name('admin.fleet.checklists.answer.details');

    Route::delete('checklists/{checkListId}/answer/{hash}', 'ChecklistsController@answerDestroy')
        ->name('admin.fleet.checklists.answer.destroy');

    Route::resource('checklists', 'ChecklistsController', [
        'as' => 'admin.fleet'
    ]);

    /**
     *
     * FleetGest > Export
     *
     */
    Route::get('export/fleet/{export}', 'ExportsController@export')
        ->name('admin.fleet.export');

    /**
     *
     * FleetGest > Stats
     *
     */

    Route::get('vehicles/stats/{id}/resume-chart', 'StatsController@getResumeChart')
        ->name('admin.fleet.stats.resume-chart');

    Route::resource('stats', 'StatsController', [
        'as' => 'admin.fleet',
        'only' => ['index']
    ]);

    /**
     *
     * FleetGest > Printer
     *
     */
    Route::get('printer/validities', 'PrinterController@validities')
        ->name('admin.fleet.printer.validities');

    Route::get('printer/reminders', 'PrinterController@summary')
        ->name('admin.fleet.printer.reminders');

    Route::get('printer/costs-balance', 'PrinterController@costsBalance')
        ->name('admin.fleet.printer.costs-balance');
});
