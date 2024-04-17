<?php

Route::group(array('prefix' => 'app', 'middleware' => 'auth.admin', 'namespace' => 'Mobile'), function() {

    Route::get('logout', 'BaseController@logout')
        ->name('mobile.logout');

    Route::get('', 'BaseController@index')
        ->name('mobile.index');

    Route::get('entregas', 'BaseController@shipmentsList')
        ->name('mobile.shipments.index');

    Route::get('entregas/sort', 'BaseController@shipmentsSort')
        ->name('mobile.shipments.sort');

    Route::post('entregas/{trk}/update', 'BaseController@shipmentUpdate')
        ->name('mobile.shipments.update');

    Route::get('entregas/read/{id}', 'BaseController@shipmentRead')
        ->name('mobile.shipments.set.read');

    Route::get('entregas/{trk}', 'BaseController@findShipment')
        ->name('mobile.shipments.find');

    Route::get('entregas/{id}/transferir', 'BaseController@shipmentTransfer')
        ->name('mobile.shipments.transfer');

    Route::post('entregas/{id}/transferir', 'BaseController@storeShipmentTransfer')
        ->name('mobile.shipments.transfer.store');

    Route::get('pendente', 'BaseController@pendingsList')
        ->name('mobile.pendings.index');

    Route::get('pendente/sort', 'BaseController@pendingSort')
        ->name('mobile.pendings.sort');

    Route::post('pendente/store', 'BaseController@pendingsStore')
        ->name('mobile.pendings.store');

    Route::post('pendente/{id}/update', 'BaseController@pendingUpdate')
        ->name('mobile.pendings.update');

    Route::get('scanner/barcode', 'BaseController@scanner')
        ->name('mobile.scanner.barcode');

    Route::get('scanner/barcode/refs', 'BaseController@scannerRefs')
        ->name('mobile.scanner.refs');

    Route::post('scanner/barcode/refs/store', 'BaseController@storeScannerRefs')
        ->name('mobile.scanner.refs.store');

    Route::get('scanner', 'BaseController@scannerQr')
        ->name('mobile.scanner');

    Route::post('estado/store', 'BaseController@store')
        ->name('mobile.status.store');

    Route::get('definicoes', 'BaseController@settings')
        ->name('mobile.settings');

    Route::post('definicoes/update', 'BaseController@settingsUpdate')
        ->name('mobile.settings.update');

    Route::get('abastecimentos', 'BaseController@fuel')
        ->name('mobile.fuel');

    Route::post('abastecimentos/store', 'BaseController@fuelStore')
        ->name('mobile.fuel.store');

    Route::get('customers/map', 'BaseController@customersMap')
        ->name('mobile.customers.map');

    Route::get('operators/map', 'BaseController@operatorsMap')
        ->name('mobile.operators.map');

    Route::get('customers/map/location/disable', 'BaseController@disableLocation')
        ->name('mobile.customers.map.location.disable');

    Route::get('customers/map/location/enable', 'BaseController@enableLocation')
        ->name('mobile.customers.map.location.enable');

    Route::get('teste', 'BaseController@teste')
        ->name('mobile.teste');

    Route::get('conducao', 'BaseController@drive')
        ->name('mobile.drive');

    Route::post('conducao/store', 'BaseController@driveStore')
        ->name('mobile.drive.store');

    Route::get('checklists', 'BaseController@checklists')
        ->name('mobile.checklists');

    Route::post('checklists/store', 'BaseController@checklistsStore')
        ->name('mobile.checklists.store');

});