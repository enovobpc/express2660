<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('mobile/shipments/guide/{trk?}', 'Api\Mobile\ShipmentsController@printTransportGuide')
    ->name('api.mobile.shipments.guide.download');

Route::get('mobile/shipments/cmr/{trk?}', 'Api\Mobile\ShipmentsController@printCMR')
    ->name('api.mobile.shipments.cmr.download');

Route::get('mobile/shipments/labels/{trk?}', 'Api\Mobile\ShipmentsController@printLabels')
    ->name('api.mobile.shipments.labels.download');

Route::get('mobile/shipments/labels/{trk?}', 'Api\Mobile\ShipmentsController@printLabels')
    ->name('api.mobile.shipments.labels.download');

/**
 * ROUTE TO BASIC API
 */
Route::group(['domain' => config('api.domain'), 'namespace' => 'Api\Basic', 'prefix' => 'basic'], function () {

    //SHIPMENTS
    Route::get('shipments/list', 'ShipmentsController@lists')
        ->name('api.basic.shipments.lists');

    Route::get('shipments/history', 'ShipmentsController@history')
        ->name('api.basic.shipments.history');

    Route::post('shipments/attachments/store', 'ShipmentsController@storeAttachment')
        ->name('api.basic.shipments.attachments.store');

    Route::post('shipments/store', 'ShipmentsController@store')
        ->name('api.basic.shipments.store');

    Route::get('shipments/store', 'ShipmentsController@store')
        ->name('api.basic.shipments.get');
});

/**
 * ROUTE TO ANDROID APP
 */
Route::group(['domain' => config('api.domain'), 'namespace' => 'Api\Mobile', 'middleware' => 'client_credentials', 'prefix' => 'mobile'], function () {

    //OPERATORS
    Route::post('login', 'OperatorsController@login')
        ->name('api.mobile.login');

    Route::get('operators/list', 'OperatorsController@lists')
        ->name('api.mobile.operators.list');

    Route::get('operators/get', 'OperatorsController@get')
        ->name('api.mobile.operators.get');

    Route::post('operators/update', 'OperatorsController@update')
        ->name('api.mobile.operators.update');

    Route::post('operators/locations/history', 'OperatorsController@storeLocationHistory')
        ->name('api.mobile.operators.locations.history');

    //CUSTOMERS
    Route::get('customers/list', 'CustomersController@lists')
        ->name('api.mobile.customers.list');

    Route::get('routes/list', 'SettingsController@listsRoutes')
        ->name('api.mobile.routes.list');

    //SHIPMENTS
    Route::get('shipments/list', 'ShipmentsController@lists')
        ->name('api.mobile.shipments.lists');

    Route::post('shipments/timer/store', 'ShipmentsController@timer')
        ->name('api.mobile.shipments.timer.store');

    Route::post('shipments/optimize', 'ShipmentsController@optimizeDelivery')
        ->name('api.mobile.shipments.optimize');

    Route::get('shipments/{tracking}', 'ShipmentsController@show')
        ->name('api.mobile.shipments.show');

    Route::get('shipments/{tracking}/history', 'ShipmentsController@history')
        ->name('api.mobile.shipments.history');

    Route::post('shipments/{tracking}/update', 'ShipmentsController@update')
        ->name('api.mobile.shipments.update');

    Route::post('{tracking}/update', 'ShipmentsController@update') //url antigo programado no android.
        ->name('api.mobile.shipments.update.alternative');

    //TRACEABILITY
    Route::get('traceability/check/{tracking}', 'TraceabilityController@check')
        ->name('api.mobile.traceability.check');

    Route::post('traceability/store', 'TraceabilityController@store')
        ->name('api.mobile.traceability.store');

    Route::post('traceability/weight-control/store', 'TraceabilityController@storeWeightControl')
        ->name('api.mobile.traceability.weight-control.store');

    Route::get('traceability/shipments/list', 'TraceabilityController@listShipment')
        ->name('api.mobile.traceability.list.shipments');

    //STATUS
    Route::post('status/store', 'TrackingController@store')
        ->name('api.mobile.status.store');

    Route::post('status/massive', 'TrackingController@storeMassive')
        ->name('api.mobile.status.massive');

    Route::post('status/pickuped', 'TrackingController@setPickuped')
        ->name('api.mobile.status.pickuped');

    Route::post('status/readed', 'TrackingController@setReadedByOperator')
        ->name('api.mobile.status.readed');

    Route::post('status/transfer', 'TrackingController@transferOperator')
        ->name('api.mobile.status.transfer');

    Route::get('status/list', 'TrackingController@listsStatus')
        ->name('api.mobile.status.lists');

    Route::get('incidences/list', 'TrackingController@listsIncidences')
        ->name('api.mobile.incidences.lists');

    //TASKS
    Route::get('tasks/list', 'TasksController@lists')
        ->name('api.mobile.tasks.lists');

    Route::post('tasks/store', 'TasksController@store')
        ->name('api.mobile.tasks.store');

    Route::post('tasks/{tracking}/update', 'TasksController@update')
        ->name('api.mobile.tasks.update');


    //VEHICLES
    Route::get('fleet/vehicles/list', 'FleetController@lists')
        ->name('api.mobile.fleet.vehicles.lists');

    Route::get('fleet/providers/list', 'FleetController@providers')
        ->name('api.mobile.fleet.providers.lists');

    //FUEL
    Route::post('fleet/fuel/store', 'FleetController@fuel')
        ->name('api.mobile.fleet.fuel.store');

    Route::post('fleet/drive/store', 'FleetController@storeDriveLog')
        ->name('api.mobile.fleet.drive.store');

     //USAGE LOG
     Route::get('fleet/usage-log/list', 'FleetController@listUsagesLogs')
        ->name('api.mobile.fleet.usage.log.list');
     
    Route::post('fleet/usage-log/store', 'FleetController@storeUsagesLogs')
        ->name('api.mobile.fleet.usage.log.store');

    //AUXILIAR METHODS
    Route::get('settings', 'SettingsController@getSettings')
        ->name('api.mobile.settings');

    //STATISTICS
    Route::get('stats', 'StatsController@getStatistics')
        ->name('api.mobile.stats');

    //LOGISTIC PRODUCTS
    Route::get('logistic/products/list', 'LogisticController@listsProducts')
        ->name('api.mobile.logistic.products.lists');

    Route::post('logistic/products/move', 'LogisticController@moveLocation')
        ->name('api.mobile.logistic.products.move');

    Route::get('logistic/products/{id?}', 'LogisticController@showProduct')
        ->name('api.mobile.logistic.products.show');


    //LOGISTIC SHIPPING ORDERS
    Route::get('logistic/shipping-orders/list', 'LogisticController@listsShippingOrders')
        ->name('api.mobile.logistic.shipping-orders.lists');

    Route::post('logistic/shipping-orders/picking/line/store', 'LogisticController@storePickingLine')
        ->name('api.mobile.logistic.shipping-orders.picking.line.store');

    Route::get('logistic/shipping-orders/{id?}', 'LogisticController@showShippingOrder')
        ->name('api.mobile.logistic.shipping-orders.show');

    //LOGISTIC RECEPTION ORDERS
    Route::get('logistic/reception-orders/list', 'LogisticController@listsReceptionOrders')
        ->name('api.mobile.logistic.reception-orders.lists');
    
    Route::post('logistic/reception-orders/store', 'LogisticController@storeReceptionOrder')
        ->name('api.mobile.logistic.reception-orders.store');
        
    Route::get('logistic/reception-orders/{id?}', 'LogisticController@showReceptionOrder')
        ->name('api.mobile.logistic.reception-orders.show');
        
    Route::get('logistic/reception-orders/{id}/articles', 'LogisticController@getArticlesReceptionOrder')
        ->name('api.mobile.logistic.reception-orders.articles');
        
    Route::post('logistic/reception-orders/confirmation/line/store', 'LogisticController@storeConfirmationItem')
        ->name('admin.mobile.logistic.reception-orders.confirmation.line.store');
        
    Route::post('logistic/reception-orders/confirmation/line/edit', 'LogisticController@updateConfirmationItem')
        ->name('admin.mobile.logistic.reception-orders.confirmation.line.update');

    //LOGISTIC LOCATIONS
    Route::get('logistic/locations/list', 'LogisticController@listsLocations')
        ->name('api.mobile.logistic.locations.lists');


    //EQUIPMENTS
    Route::get('equipments/list', 'EquipmentsController@listEquipments')
        ->name('api.mobile.equipments.lists');

    Route::get('equipments/locations/list', 'EquipmentsController@listLocations')
        ->name('api.mobile.equipments.locations.lists');

    Route::get('equipments/categorys/list', 'EquipmentsController@listCategories')
        ->name('api.mobile.equipments.categories.lists');

    Route::get('equipments/check/{tracking}', 'EquipmentsController@checkEquipments')
        ->name('api.mobile.equipments.check');

    Route::post('equipments/store', 'EquipmentsController@pickingEquipments')
        ->name('api.mobile.equipments.store');
});


/**
 * ROUTE TO PARTNERS API
 */
Route::group(['domain' => config('api.domain'), 'namespace' => 'Api\Partners', 'middleware' => 'client_credentials', 'prefix' => 'partners'], function () {

    //CUSTOMERS
    Route::get('customers/list', 'CustomersController@lists')
        ->name('api.partners.entities.customers.lists');

    Route::post('customers/store', 'CustomersController@store')
        ->name('api.partners.entities.customers.store');

    Route::delete('customers/{code}/destroy', 'CustomersController@destroy')
        ->name('api.partners.entities.customers.delete');

    Route::post('customers/login', 'CustomersController@storeLogin')
        ->name('api.partners.entities.customers.login');

    //PROVIDERS
    Route::get('providers/list', 'ProvidersController@lists')
        ->name('api.partners.entities.providers.lists');

    Route::post('providers/store', 'ProvidersController@store')
        ->name('api.partners.entities.providers.store');

    Route::delete('providers/{code}/destroy', 'ProvidersController@destroy')
        ->name('api.partners.entities.providers.delete');

    //USERS
    Route::get('users/list', 'UsersController@lists')
        ->name('api.partners.entities.users.lists');

    Route::post('users/store', 'UsersController@store')
        ->name('api.partners.entities.users.store');

    Route::delete('users/{code}/destroy', 'UsersController@destroy')
        ->name('api.partners.entities.users.delete');

    //LOGISTIC
    Route::get('logistic/shipping-orders', 'LogisticController@listsShippingOrders')
        ->name('api.partners.logistic.shipping-orders.lists');



    //INCIDENCES
    Route::post('shipments/incidences/resolve', 'ShipmentsController@resolveIncidence')
        ->name('api.shipments.incidences.resolve');

    //SHIPMENTS
    Route::get('shipments/list', 'ShipmentsController@lists')
        ->name('api.shipments.lists');

    Route::get('shipments/history/massive', 'ShipmentsController@massHistory')
        ->name('api.partners.shipments.history.massive');

    Route::post('shipments/history/store', 'ShipmentsController@storeHistory')
        ->name('api.partners.shipments.history.store');

    Route::get('shipments/close-ctt', 'ShipmentsController@closeCtt')
        ->name('api.shipments.close-ctt');

    Route::get('shipments/{tracking}', 'ShipmentsController@show')
        ->name('api.shipments.show');

    Route::post('shipments/store', 'ShipmentsController@store')
        ->name('api.shipments.store');

    Route::delete('shipments/{tracking}', 'ShipmentsController@destroy')
        ->name('api.shipments.destroy');

    Route::get('shipments/print/cargo-manifest', 'ShipmentsController@getCargoManifest')
        ->name('api.shipments.print.cargo-manifest');

    Route::get('shipments/print/labels', 'ShipmentsController@getLabels')
        ->name('api.shipments.labels');

    Route::get('shipments/print/guide', 'ShipmentsController@getTransportationGuide')
        ->name('api.shipments.guide');

    Route::get('shipments/print/cmr', 'ShipmentsController@getCMR')
        ->name('api.shipments.cmr');

    Route::get('shipments/print/pod', 'ShipmentsController@getPOD')
        ->name('api.shipments.pod');

    Route::get('shipments/{tracking}/history', 'ShipmentsController@history')
        ->name('api.shipments.history');

    //SMS
    Route::post('shipments/delivery/sms', 'ShipmentsController@sendSmsPin')
        ->name('api.shipments.send.sms');

    //PRICES
    Route::post('prices/calc', 'ShipmentsController@getPrice')
        ->name('api.prices.calc');

    Route::get('cod/details', 'ShipmentsController@getCOD')
        ->name('api.cod.details');

    //TRACEABILITY
    Route::get('traceability/{tracking}/history', 'ShipmentsController@traceabilityHistory')
        ->name('api.traceability.history');

    //AUXILIAR TABLES
    Route::get('shipments/services/list', 'ShipmentsController@listsServices')
        ->name('api.shipments.services.list');

    Route::get('shipments/pudo/list', 'ShipmentsController@listsPudo')
        ->name('api.shipments.pudo.list');

    Route::get('shipments/status/list', 'ShipmentsController@listsStatus')
        ->name('api.shipments.status.list');
});



/**
 * ROUTE TO USER APP V1
 */

Route::group(['domain' => config('api.domain'), 'namespace' => 'Api\Customers', 'middleware' => 'auth:api'], function () {

    Route::get('{level}/account/details', 'AccountController@details')
        ->name('api.account.details');

    Route::get('{level}/account/rgpd/status', 'AccountController@rgpdStatus')
        ->name('api.account.rgpd.status');

    Route::post('{level}/account/rgpd/request/{action}', 'AccountController@rgpdRequest')
        ->name('api.account.rgpd.request');

    Route::post('{level}/shipments/incidences/resolve', 'ShipmentsController@resolveIncidence')
        ->name('api.shipments.incidences.resolve');

    Route::get('{level}/shipments/services/list', 'ShipmentsController@listsServices')
        ->name('api.shipments.services.list');

    Route::get('{level}/shipments/pudo/list', 'ShipmentsController@listsPudo')
        ->name('api.shipments.pudo.list');

    Route::get('{level}/shipments/status/list', 'ShipmentsController@listsStatus')
        ->name('api.shipments.status.list');

    Route::get('{level}/shipments/providers/list', 'ShipmentsController@listsProviders')
        ->name('api.shipments.providers.list');

    Route::get('{level}/shipments/list', 'ShipmentsController@lists')
        ->name('api.shipments.lists');

    Route::get('{level}/shipments/history/massive', 'ShipmentsController@massHistory')
        ->name('api.mobile.shipments.massive.history');

    Route::get('{level}/shipments/close-ctt', 'ShipmentsController@closeCtt')
        ->name('api.shipments.close-ctt');

    Route::get('{level}/shipments/{tracking}', 'ShipmentsController@show')
        ->name('api.shipments.show');

    Route::post('{level}/shipments/store', 'ShipmentsController@store')
        ->name('api.shipments.store');

    Route::post('{level}/shipments/createDecathlon', 'ShipmentsController@createDecathlon')
        ->name('api.shipments.create.decathlon');

    Route::delete('{level}/shipments/{tracking}', 'ShipmentsController@destroy')
        ->name('api.shipments.destroy');

    Route::get('{level}/shipments/print/cargo-manifest', 'ShipmentsController@getCargoManifest')
        ->name('api.shipments.print.cargo-manifest');

    Route::get('{level}/shipments/{tracking}/labels', 'ShipmentsController@getLabels')
        ->name('api.shipments.labels');

    Route::get('{level}/shipments/{tracking}/guide', 'ShipmentsController@getTransportationGuide')
        ->name('api.shipments.guide');

    Route::get('{level}/shipments/{tracking}/cmr', 'ShipmentsController@getCMR')
        ->name('api.shipments.cmr');

    Route::get('{level}/shipments/{tracking}/pod', 'ShipmentsController@getPOD')
        ->name('api.shipments.pod');

    Route::get('{level}/shipments/{tracking}/history', 'ShipmentsController@history')
        ->name('api.shipments.history');

    Route::post('{level}/prices/calc', 'ShipmentsController@getPrice')
        ->name('api.prices.calc');

    Route::get('{level}/cod/details', 'ShipmentsController@getCOD')
        ->name('api.cod.details');

    Route::get('{level}/traceability/{tracking}/history', 'ShipmentsController@traceabilityHistory')
        ->name('api.traceability.history');

    Route::get('{level}/incidences-types/list', 'ShipmentsController@listsIncidencesStatus')
        ->name('api.incidences-types.list');


    /**
     * LOGISTIC
     */
    Route::get('{level}/logistic/products/list', 'LogisticController@listsProducts')
        ->name('api.logistic.products.lists');

    Route::get('{level}/logistic/shipping-orders', 'LogisticController@listsShippingOrders')
        ->name('api.logistic.shipping-orders.lists');

    Route::get('{level}/logistic/products/list', 'LogisticController@listsProducts')
        ->name('api.logistic.products.lists');

    Route::get('{level}/logistic/shipping-orders', 'LogisticController@listsShippingOrders')
        ->name('api.logistic.shipping-orders.lists');
});

Route::group(['domain' => config('api.domain'), 'prefix' => 'docs', 'namespace' => 'Api'], function () {

    Route::get('{level?}/{version?}/{category?}/{section?}', 'DocsController@index')
        ->name('api.docs.index');
});
