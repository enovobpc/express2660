<?php

/*
|--------------------------------------------------------------------------
| Logistic Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Route::group(array('prefix' => 'admin/logistic', 'middleware' => 'auth.admin', 'namespace' => 'Admin\Logistic'), function () {

    /**
     * 
     * Warehouses
     * 
     */
    Route::post('warehouses/datatable', 'WarehousesController@datatable')
        ->name('admin.logistic.warehouses.datatable');

    Route::post('warehouses/selected/destroy', 'WarehousesController@massDestroy')
        ->name('admin.logistic.warehouses.selected.destroy');

    Route::resource('warehouses', 'WarehousesController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Locations
     *
     */

    Route::post('locations/datatable', 'LocationsController@datatable')
        ->name('admin.logistic.locations.datatable');

    Route::post('locations/selected/destroy', 'LocationsController@massDestroy')
        ->name('admin.logistic.locations.selected.destroy');

    Route::get('locations/{id}/labels/print', 'LocationsController@printLabels')
        ->name('admin.logistic.locations.labels.print');

    Route::get('locations/print', 'LocationsController@print')
        ->name('admin.logistic.locations.print');

    Route::get('locations/labels/massPrint', 'LocationsController@massPrintLabels')
        ->name('admin.logistic.locations.selected.labels.print');

    Route::resource('locations', 'LocationsController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Products
     *
     */
    Route::post('products/datatable', 'ProductsController@datatable')
        ->name('admin.logistic.products.datatable');

    Route::post('products/datatable/locations', 'ProductsController@datatableLocations')
        ->name('admin.logistic.products.datatable.locations');

    Route::post('products/selected/destroy', 'ProductsController@massDestroy')
        ->name('admin.logistic.products.selected.destroy');

    Route::get('products/search/product', 'ProductsController@searchProduct')
        ->name('admin.logistic.products.search.product');

    Route::post('products/search/product/select2', 'ProductsController@searchProductSelect2')
        ->name('admin.logistic.products.search.product.select2');

    Route::post('products/search/customer', 'ProductsController@searchCustomer')
        ->name('admin.logistic.products.search.customer');

    Route::post('products/{id}/history/datatable', 'ProductsController@datatableHistory')
        ->name('admin.logistic.products.history.datatable');

    Route::post('products/{id}/serials/datatable', 'ProductsController@datatableSerials')
        ->name('admin.logistic.products.serials.datatable');

    Route::get('products/{id}/labels', 'ProductsController@editLabels')
        ->name('admin.logistic.products.labels');

    Route::get('products/{id}/labels/print', 'ProductsController@printLabels')
        ->name('admin.logistic.products.labels.print');

    Route::post('products/sync', 'ProductsController@sync')
        ->name('admin.logistic.products.sync');

    Route::post('products/getList/{type}', 'ProductsController@getSelect2List')
        ->name('admin.logistic.products.getList');

    Route::get('products/adjustment', 'ProductsController@editAdjustment')
        ->name('admin.logistic.products.adjustment.edit');

    Route::post('products/adjustment', 'ProductsController@storeAdjustment')
        ->name('admin.logistic.products.adjustment.store');

    Route::get('products/stock/add', 'ProductsLocationController@addNewStock')
        ->name('admin.logistic.products.stock.add');

    Route::post('products/stock/store', 'ProductsLocationController@storeNewStock')
        ->name('admin.logistic.products.stock.store');

    Route::get('products/move', 'ProductsController@editMoveLocation')
        ->name('admin.logistic.products.move.edit');

    Route::post('products/move', 'ProductsController@storeMoveLocation')
        ->name('admin.logistic.products.move.store');

    Route::post('products/get/location', 'ProductsController@getLocation')
        ->name('admin.logistic.products.get.location');


    Route::resource('products', 'ProductsController', [
        'as' => 'admin.logistic'
    ]);

    /**
     *
     * Products > Locations
     *
     */

    Route::get('products/{productId}/stock/{locationId}/transfer', 'ProductsLocationController@editTransfer')
        ->name('admin.logistic.products.stock.transfer');

    Route::post('products/{productId}/stock/{locationId}/transfer', 'ProductsLocationController@saveTransfer')
        ->name('admin.logistic.products.stock.transfer.save');

    /**
     *
     * Products > Images
     *
     */
    Route::post('products/{productId}/images/selected/destroy', 'ProductsImagesController@massDestroy')
        ->name('admin.logistic.products.images.selected.destroy');

    Route::get('products/{productId}/images/sort', 'ProductsImagesController@sortEdit')
        ->name('admin.logistic.products.images.sort');

    Route::post('products/{productId}/images/sort', 'ProductsImagesController@sortUpdate')
        ->name('admin.logistic.products.images.sort.update');

    Route::post('products/{productId}/images/{id}/cover', 'ProductsImagesController@setCover')
        ->name('admin.logistic.products.images.cover');

    Route::resource('logistic/products.images', 'ProductsImagesController', [
        'as' => 'admin.logistic',
        'only' => ['index', 'store', 'destroy']
    ]);

    /**
     *
     * Products > Reception Orders
     *
     */
    Route::post('reception-orders/datatable', 'ReceptionOrdersController@datatable')
        ->name('admin.logistic.reception-orders.datatable');

    Route::post('reception-orders/selected/destroy', 'ReceptionOrdersController@massDestroy')
        ->name('admin.logistic.reception-orders.selected.destroy');


    Route::post('reception-orders/search/product', 'ReceptionOrdersController@searchProduct')
        ->name('admin.logistic.reception-orders.search.product');

    Route::post('reception-orders/search/customer', 'ReceptionOrdersController@searchCustomer')
        ->name('admin.logistic.reception-orders.search.customer');

    Route::post('reception-orders/product/add', 'ReceptionOrdersController@storeProduct')
        ->name('admin.logistic.reception-orders.product.add');

    Route::post('reception-orders/{receptionOrder}/product/{id}/update', 'ReceptionOrdersController@updateProduct')
        ->name('admin.logistic.reception-orders.product.update');

    Route::post('reception-orders/{receptionOrder}/product/{id}/remove', 'ReceptionOrdersController@deleteProduct')
        ->name('admin.logistic.reception-orders.product.remove');


    /*
    Route::post('reception-orders/search/product', 'ReceptionOrdersController@searchProduct')
        ->name('admin.logistic.reception-orders.search.product');*/

    /*    Route::post('reception-orders/get/product', 'ReceptionOrdersController@getProductByBarcode')
        ->name('admin.logistic.reception-orders.get.product');*/

    /*    Route::post('reception-orders/get/locations', 'ReceptionOrdersController@getProductLocations')
        ->name('admin.logistic.reception-orders.get.locations');*/




    Route::get('reception-orders/confirmation/create', 'ReceptionOrdersController@createConfirmation')
        ->name('admin.logistic.reception-orders.confirmation.create');

    Route::get('reception-orders/confirmation/{id}', 'ReceptionOrdersController@editConfirmation')
        ->name('admin.logistic.reception-orders.confirmation.edit');

    Route::post('reception-orders/confirmation/{id}/store', 'ReceptionOrdersController@storeConfirmation')
        ->name('admin.logistic.reception-orders.confirmation.store');


    Route::post('reception-orders/confirmation/{receptionOrder}/line/store', 'ReceptionOrdersController@storeConfirmationItem')
        ->name('admin.logistic.reception-orders.confirmation.line.store');

    Route::post('reception-orders/confirmation/{receptionOrder}/line/{lineId}', 'ReceptionOrdersController@updateConfirmationItem')
        ->name('admin.logistic.reception-orders.confirmation.line.update');

    Route::delete('reception-orders/confirmation/{receptionOrder}/line/{lineId}', 'ReceptionOrdersController@destroyConfirmationItem')
        ->name('admin.logistic.reception-orders.confirmation.line.destroy');

    /*    Route::post('reception-orders/confirmation/{id}/search/barcode', 'ReceptionOrdersController@confirmationSearchBarcode')
        ->name('admin.logistic.reception-orders.confirmation.search.barcode');*/

    Route::get('reception-orders/{id}/print', 'ReceptionOrdersController@printDocument')
        ->name('admin.logistic.reception-orders.print');

    Route::resource('reception-orders', 'ReceptionOrdersController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Products > Shipping Orders
     *
     */
    Route::post('shipping-orders/datatable', 'ShippingOrdersController@datatable')
        ->name('admin.logistic.shipping-orders.datatable');

    Route::post('shipping-orders/selected/destroy', 'ShippingOrdersController@massDestroy')
        ->name('admin.logistic.shipping-orders.selected.destroy');

    Route::post('shipping-orders/search/product', 'ShippingOrdersController@searchProduct')
        ->name('admin.logistic.shipping-orders.search.product');

    Route::post('shipping-orders/search/customer', 'ShippingOrdersController@searchCustomer')
        ->name('admin.logistic.shipping-orders.search.customer');

    Route::post('shipping-orders/product/add', 'ShippingOrdersController@storeProduct')
        ->name('admin.logistic.shipping-orders.product.add');

    Route::post('shipping-orders/{shippingOrder}/product/{id}/update', 'ShippingOrdersController@updateProduct')
        ->name('admin.logistic.shipping-orders.product.update');

    Route::post('shipping-orders/{shippingOrder}/product/{id}/remove', 'ShippingOrdersController@deleteProduct')
        ->name('admin.logistic.shipping-orders.product.remove');

    /*Route::post('shipping-orders/get/product', 'ShippingOrdersController@getProductByBarcode')
        ->name('admin.logistic.shipping-orders.get.product');*/

    /*Route::post('shipping-orders/get/locations', 'ShippingOrdersController@getProductLocations')
        ->name('admin.logistic.shipping-orders.get.locations');*/

    Route::get('shipping-orders/confirmation/create', 'ShippingOrdersController@createConfirmation')
        ->name('admin.logistic.shipping-orders.confirmation.create');

    Route::get('shipping-orders/confirmation/{id}', 'ShippingOrdersController@editConfirmation')
        ->name('admin.logistic.shipping-orders.confirmation.edit');

    Route::post('shipping-orders/confirmation/{id}', 'ShippingOrdersController@storeConfirmation')
        ->name('admin.logistic.shipping-orders.confirmation.store');

    Route::post('shipping-orders/confirmation/{id}/search/barcode', 'ShippingOrdersController@confirmationSearchBarcode')
        ->name('admin.logistic.shipping-orders.confirmation.search.barcode');

    Route::get('shipping-orders/print/{id?}', 'ShippingOrdersController@printSummary')
        ->name('admin.logistic.shipping-orders.print.summary');

    Route::get('shipping-orders/label/{id?}', 'ShippingOrdersController@printLabel')
        ->name('admin.logistic.shipping-orders.print.label');

    Route::get('shipping-orders/selected/print/wave-picking', 'ShippingOrdersController@printWavePicking')
        ->name('admin.logistic.selected.print.wave-picking');

    Route::resource('shipping-orders', 'ShippingOrdersController', [
        'as' => 'admin.logistic'
    ]);

    /**
     *
     * Products > Devolutions
     *
     */

    Route::post('devolutions/datatable', 'DevolutionsController@datatable')
        ->name('admin.logistic.devolutions.datatable');

    Route::post('devolutions/selected/destroy', 'DevolutionsController@massDestroy')
        ->name('admin.logistic.devolutions.selected.destroy');

    Route::post('devolutions/get/shipping-order', 'DevolutionsController@getShippingOrder')
        ->name('admin.logistic.devolutions.get.shipping-order');

    Route::post('devolutions/items/store', 'DevolutionsController@storeItem')
        ->name('admin.logistic.devolutions.items.store');

    Route::post('devolutions/{devolutionId}/items/{id}/update', 'DevolutionsController@updateItem')
        ->name('admin.logistic.devolutions.items.update');

    Route::delete('devolutions/{devolutionId}/items/{id}/destroy', 'DevolutionsController@destroyItem')
        ->name('admin.logistic.devolutions.items.destroy');

    Route::resource('devolutions', 'DevolutionsController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Products > Inventários
     *
     */
    Route::post('inventories/datatable', 'InventoriesController@datatable')
        ->name('admin.logistic.inventories.datatable');

    Route::post('inventories/selected/destroy', 'InventoriesController@massDestroy')
        ->name('admin.logistic.inventories.selected.destroy');

    Route::post('inventories/items/store', 'InventoriesController@storeProduct')
        ->name('admin.logistic.inventories.items.store');

    Route::post('inventories/items/import', 'InventoriesController@importProducts')
        ->name('admin.logistic.inventories.items.import');

    Route::get('inventories/print/maps/{mapType}', 'InventoriesController@printMap')
        ->name('admin.logistic.inventories.print.maps');

    Route::post('inventories/items/import/preview', 'InventoriesController@previewImportProducts')
        ->name('admin.logistic.inventories.items.import.preview');

    Route::post('inventories/{inventoryId}/items/{id}/update', 'InventoriesController@updateProduct')
        ->name('admin.logistic.inventories.items.update');

    Route::post('inventories/{inventoryId}/items/{id}/destroy', 'InventoriesController@destroyProduct')
        ->name('admin.logistic.inventories.items.destroy');

    Route::get('inventories/{id}/print/summary', 'InventoriesController@printSummary')
        ->name('admin.logistic.inventories.print.summary');

    Route::resource('inventories', 'InventoriesController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Products > Map
     *
     */
    Route::get('map', 'MapController@index')
        ->name('admin.logistic.map.index');

    /**
     *
     * Configs > Brands
     *
     */
    Route::post('brands/datatable', 'BrandsController@datatable')
        ->name('admin.logistic.brands.datatable');

    Route::post('brands/selected/destroy', 'BrandsController@massDestroy')
        ->name('admin.logistic.brands.selected.destroy');

    Route::get('brands/sort', 'BrandsController@sortEdit')
        ->name('admin.logistic.brands.sort');

    Route::post('brands/sort', 'BrandsController@sortUpdate')
        ->name('admin.logistic.brands.sort.update');

    Route::post('brands/get/list/{type}', 'BrandsController@getSelect2List')
        ->name('admin.logistic.brands.getList');

    Route::resource('brands', 'BrandsController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Configs > Models
     *
     */
    Route::post('models/datatable', 'ModelsController@datatable')
        ->name('admin.logistic.models.datatable');

    Route::post('models/selected/destroy', 'ModelsController@massDestroy')
        ->name('admin.logistic.models.selected.destroy');

    Route::get('models/sort', 'ModelsController@sortEdit')
        ->name('admin.logistic.models.sort');

    Route::post('models/sort', 'ModelsController@sortUpdate')
        ->name('admin.logistic.models.sort.update');

    Route::resource('models', 'ModelsController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Configs > Families
     *
     */
    Route::post('families/datatable', 'FamiliesController@datatable')
        ->name('admin.logistic.families.datatable');

    Route::post('families/selected/destroy', 'FamiliesController@massDestroy')
        ->name('admin.logistic.families.selected.destroy');

    Route::get('families/sort', 'FamiliesController@sortEdit')
        ->name('admin.logistic.families.sort');

    Route::post('families/sort', 'FamiliesController@sortUpdate')
        ->name('admin.logistic.families.sort.update');

    Route::resource('families', 'FamiliesController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Configs > Categories
     *
     */
    Route::post('categories/datatable', 'CategoriesController@datatable')
        ->name('admin.logistic.categories.datatable');

    Route::post('categories/selected/destroy', 'CategoriesController@massDestroy')
        ->name('admin.logistic.categories.selected.destroy');

    Route::get('categories/sort', 'CategoriesController@sortEdit')
        ->name('admin.logistic.categories.sort');

    Route::post('categories/sort', 'CategoriesController@sortUpdate')
        ->name('admin.logistic.categories.sort.update');

    Route::resource('categories', 'CategoriesController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);

    /**
     *
     * Configs > Subcategories
     *
     */
    Route::post('subcategories/datatable', 'SubcategoriesController@datatable')
        ->name('admin.logistic.subcategories.datatable');

    Route::post('subcategories/selected/destroy', 'SubcategoriesController@massDestroy')
        ->name('admin.logistic.subcategories.selected.destroy');

    Route::get('subcategories/sort', 'SubcategoriesController@sortEdit')
        ->name('admin.logistic.subcategories.sort');

    Route::post('subcategories/sort', 'SubcategoriesController@sortUpdate')
        ->name('admin.logistic.subcategories.sort.update');

    Route::resource('subcategories', 'SubcategoriesController', [
        'as' => 'admin.logistic',
        'except' => ['show']
    ]);
});


Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function () {
    /**
     * Exports
     */
    Route::get('export/logistic/products', 'Exports\LogisticController@productsList')
        ->name('admin.logistic.products.export');

    Route::get('export/logistic/shipping-orders/export', 'Exports\LogisticController@shippingOrders')
        ->name('admin.logistic.shipping-orders.export');

    Route::get('export/logistic/reception-orders/export', 'Exports\LogisticController@receptionOrders')
        ->name('admin.logistic.reception-orders.export');

    Route::get('export/logistic/inventories/export/{map}', 'Exports\LogisticController@inventories')
        ->name('admin.logistic.inventories.export');

    Route::get('export/logistic/selected/locations/{key}', 'Exports\LogisticController@location')
        ->name('admin.export.logistic.locations');
});
