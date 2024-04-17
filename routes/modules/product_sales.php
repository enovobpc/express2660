<?php


Route::group(array('prefix' => 'admin/products', 'middleware' => 'auth.admin', 'namespace' => 'Admin\Products'), function() {
    /**
     *
     * Logistic > Auxiliar Tables > Products
     *
     */
    Route::post('items/datatable', 'ProductsController@datatable')
        ->name('admin.products.items.datatable');

    Route::post('items/selected/destroy', 'ProductsController@massDestroy')
        ->name('admin.products.items.selected.destroy');

    Route::resource('items', 'ProductsController', [
        'as' => 'admin.products',
        'except' => ['show']]);

    /**
     *
     * Logistic > Auxiliar Tables > Products Sales
     *
     */
    Route::post('sales/datatable', 'ItemsController@datatable')
        ->name('admin.products.sales.datatable');

    Route::post('sales/selected/destroy', 'ItemsController@massDestroy')
        ->name('admin.products.sales.selected.destroy');

    Route::post('sales/search/customer', 'ItemsController@searchCustomer')
        ->name('admin.products.sales.search.customer');

    Route::post('sales/search/products', 'ItemsController@searchProduct')
        ->name('admin.products.sales.search.products');

    Route::post('sales/get/product', 'ItemsController@getProduct')
        ->name('admin.products.sales.get.product');

    Route::resource('sales', 'ItemsController', [
        'as' => 'admin.products',
        'except' => ['show']]);
});