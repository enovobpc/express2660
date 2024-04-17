<?php

/**
 * Public routes
 */
Route::post('account/get/budget', 'DefaultSite\HomeController@budget')
    ->name('account.guest.budget');

Route::get('account/public/{customerId}/balance', 'DefaultSite\BalanceController@index')
    ->name('account.public.balance.index');

Route::post('account/public/{customerId}/balance/datatable', 'DefaultSite\BalanceController@datatable')
    ->name('account.public.balance.datatable');

Route::get('account/public/{customerId}/balance/{docId}/download', 'DefaultSite\BalanceController@getInvoice')
    ->name('account.public.balance.download');

Route::post('account/public/{customerId}/balance/sync', 'DefaultSite\BalanceController@syncBalanceAll')
    ->name('account.public.balance.sync');

/*
 * Customer Login
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/login', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'guest']), function () {

    if (hasModule('website') || hasModule('website_integrated_login')) {
        Route::get('/', 'Auth\LoginController@index')
            ->name('account.login');
    } else {
        Route::get('/', 'DefaultSite\HomeController@index')
            ->name('account.login');
    }

    Route::post('/', 'Auth\LoginController@login')
        ->name('account.login.submit');
});


/*
 * Customer Register
 */
if (hasModule('account_signup')) {
    Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/criar-conta', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'guest']), function () {

        if (hasModule('website') || hasModule('website_integrated_login')) {
            Route::get('/', 'Auth\RegisterController@index')
                ->name('account.register');
        } else {
            Route::get('/', 'DefaultSite\HomeController@register')
                ->name('account.register');
        }

        Route::post('/', 'Auth\RegisterController@create')
            ->name('account.register.submit');
    });
}

/*
 * Customer forgot and reset password
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/password', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'guest']), function () {

    Route::get('/forgot', 'Auth\ForgotPasswordController@index')
        ->name('account.password.forgot.index');

    Route::post('/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')
        ->name('account.password.email');

    if (hasModule('website')) {
        Route::get('reset/{token}', 'Auth\ResetPasswordController@showResetForm')
            ->name('account.password.reset');

        Route::post('reset', 'Auth\ResetPasswordController@reset')
            ->name('account.password.reset.submit');
    } else {
        Route::get('reset/{token}', 'DefaultSite\ResetPasswordController@showResetForm')
            ->name('account.password.reset');

        Route::post('reset', 'DefaultSite\ResetPasswordController@reset')
            ->name('account.password.reset.submit');
    }
});

/**
 * Customer account
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account'), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('/', 'HomeController@index')
        ->name('account.index');
});

/**
 * Customer Shipments
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/envios', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'ShipmentsController@datatable')
        ->name('account.shipments.datatable');

    Route::get('imprimir', 'ShipmentsController@printByDate')
        ->name('account.shipments.print');

    Route::get('fecho', 'ShipmentsController@editCloseShipments')
        ->name('account.shipments.close.edit');

    Route::get('mapas-fecho', 'ShipmentsController@showCloseShipments')
        ->name('account.shipments.close.show');

    Route::post('fechar', 'ShipmentsController@storeCloseShipments')
        ->name('account.shipments.close.store');

    Route::get('orcamentar', 'ShipmentsController@createBudget')
        ->name('account.shipments.budget');

    Route::post('orcamentar', 'ShipmentsController@getBudget')
        ->name('account.shipments.budget.calculate');

    Route::get('{trk}/pagamento', 'ShipmentsController@payShipment')
        ->name('account.shipments.payment.store');

    Route::post('importar', 'ShipmentsController@import')
        ->name('account.shipments.import');

    Route::get('certificados/aceitacao-ctt', 'ShipmentsController@showAcceptanceCertificates')
        ->name('account.shipments.ctt-delivery-manifest');

    Route::get('', 'ShipmentsController@index')
        ->name('account.shipments.index');

    Route::get('create', 'ShipmentsController@create')
        ->name('account.shipments.create');

    Route::post('store', 'ShipmentsController@store')
        ->name('account.shipments.store');

    Route::get('{id}', 'ShipmentsController@show')
        ->name('account.shipments.show');

    Route::get('{id}/edit', 'ShipmentsController@edit')
        ->name('account.shipments.edit');

    Route::put('{id}/update', 'ShipmentsController@update')
        ->name('account.shipments.update');

    Route::get('{id}/email/edit', 'ShipmentsController@editEmail')
        ->name('account.shipments.email.edit');

    Route::post('{id}/email/send', 'ShipmentsController@sendNewEmail')
        ->name('account.shipments.email.send');

    Route::delete('{id}/destroy', 'ShipmentsController@destroy')
        ->name('account.shipments.destroy');

    Route::get('{id}/replicate', 'ShipmentsController@replicateEdit')
        ->name('account.shipments.replicate');

    Route::post('{id}/replicate', 'ShipmentsController@replicateStore')
        ->name('account.shipments.replicate');

    Route::get('search/recipients', 'ShipmentsController@searchRecipient')
        ->name('account.shipments.search.recipient');

    Route::post('get/recipient', 'ShipmentsController@getRecipient')
        ->name('account.shipments.get.recipient');

    Route::post('get/department', 'ShipmentsController@getDepartment')
        ->name('account.shipments.get.department');

    /*Route::post('get/volumetric-weight', 'ShipmentsController@getVolumetricWeight')
        ->name('account.shipments.get.volumetric-weight');*/

    Route::post('get/agency', 'ShipmentsController@getAgency')
        ->name('account.shipments.get.agency');

    Route::post('get/pudos', 'ShipmentsController@getPudos')
        ->name('account.shipments.get.pudos');

    Route::post('get/price', 'ShipmentsController@getPrice')
        ->name('account.shipments.get.price');

    Route::post('set/payment', 'ShipmentsController@setPayment')
        ->name('account.shipments.set.payment');
    
    Route::get('{id}/show/payment', 'ShipmentsController@showPayment')
        ->name('account.shipments.show.payment');

    Route::get('selected/close', 'ShipmentsController@editCloseShipments')
        ->name('account.shipments.selected.close');

    Route::get('selected/imprimir/etiquetas', 'ShipmentsController@massAdhesiveLabels')
        ->name('account.shipments.selected.print.labels');

    Route::get('selected/imprimir/guia-transporte', 'ShipmentsController@massTransportationGuide')
        ->name('account.shipments.selected.print.guide');

    Route::get('selected/imprimir/manifesto-recolha', 'ShipmentsController@massPickupManifest')
        ->name('account.shipments.selected.print.pickup-manifest');

    Route::get('selected/imprimir/manifesto-temperatura', 'ShipmentsController@massColdManifest')
        ->name('account.shipments.selected.print.cold-manifest');

    Route::get('selected/imprimir/envios/{doctype?}', 'ShipmentsController@massPrint')
        ->name('account.shipments.selected.print');

    Route::get('{id}/imprimir/guia-transporte', 'ShipmentsController@createTransportationGuide')
        ->name('account.shipments.get.guide');

    Route::get('{id}/imprimir/manifesto-recolha', 'ShipmentsController@createPickupManifest')
        ->name('account.shipments.get.pickup-manifest');

    Route::get('{id}/imprimir/cmr', 'ShipmentsController@createCmr')
        ->name('account.shipments.get.cmr');

    Route::get('{id}/imprimir/guia-reembolso', 'ShipmentsController@createReimbursementGuide')
        ->name('account.shipments.get.reimbursement-guide');

    Route::get('{id}/imprimir/etiquetas', 'ShipmentsController@createAdhesiveLabels')
        ->name('account.shipments.get.labels');

    Route::get('/imprimir/etiquetas/a4', 'ShipmentsController@editPrintA4')
        ->name('account.shipments.get.labelsA4.edit');

    Route::get('{id}/imprimir/etiquetas', 'ShipmentsController@createAdhesiveLabels')
        ->name('account.shipments.get.labels');

    Route::get('{id}/imprimir/declaracao-valores', 'ShipmentsController@createValueStatement')
        ->name('account.shipments.get.value-statement');

    Route::get('{id}/pod', 'ShipmentsController@getPod')
        ->name('account.shipments.get.pod');

    Route::get('shipments/search/sku', 'ShipmentsController@searchSku')
        ->name('account.shipments.search.sku');

    Route::post('selected/fechar-envios', 'ShipmentsController@massCloseShipments')
        ->name('account.shipments.selected.close-shipment');

    //ATTACHMENTS
    Route::get('shipments/{shipmentId}/attachments/create', 'ShipmentsAttachmentsController@create')
        ->name('account.shipments.attachments.create');

    Route::post('shipments/{shipmentId}/attachments/store', 'ShipmentsAttachmentsController@store')
        ->name('account.shipments.attachments.store');

    Route::get('shipments/{shipmentId}/attachments/{id}/edit', 'ShipmentsAttachmentsController@edit')
        ->name('account.shipments.attachments.edit');

    Route::put('shipments/{shipmentId}/attachments/{id}/update', 'ShipmentsAttachmentsController@update')
        ->name('account.shipments.attachments.update');

    Route::delete('shipments/{shipmentId}/attachments{id}/destroy', 'ShipmentsAttachmentsController@destroy')
        ->name('account.shipments.attachments.destroy');

    
    Route::get('shipments/zip-codes/search', 'ShipmentsController@searchZipCodes')
        ->name('account.shipments.zip-codes.search');
});

/**
 * Customer Shipments
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/recolhas', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'PickupsController@index')
        ->name('account.pickups.index');

    Route::get('create', 'PickupsController@create')
        ->name('account.pickups.create');

    Route::post('store', 'PickupsController@store')
        ->name('account.pickups.store');

    Route::get('{id}', 'PickupsController@show')
        ->name('account.pickups.show');

    Route::get('{id}/edit', 'PickupsController@edit')
        ->name('account.pickups.edit');

    Route::put('{id}/update', 'PickupsController@update')
        ->name('account.pickups.update');
});

/**
 * Customer Recipients
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/destinatarios', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('import', 'RecipientsController@import')
        ->name('account.recipients.import');

    Route::post('selected/destroy', 'RecipientsController@massDestroy')
        ->name('account.recipients.selected.destroy');

    Route::post('datatable', 'RecipientsController@datatable')
        ->name('account.recipients.datatable');

    Route::get('', 'RecipientsController@index')
        ->name('account.recipients.index');

    Route::get('create', 'RecipientsController@create')
        ->name('account.recipients.create');

    Route::post('store', 'RecipientsController@store')
        ->name('account.recipients.store');

    Route::get('{id}/edit', 'RecipientsController@edit')
        ->name('account.recipients.edit');

    Route::put('{id}/update', 'RecipientsController@update')
        ->name('account.recipients.update');

    Route::delete('{id}/destroy', 'RecipientsController@destroy')
        ->name('account.recipients.destroy');
});

/**
 * Exports
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/exportar', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('envios', 'ExportController@shipments')
        ->name('account.export.shipments');

    Route::get('extrato-mensal', 'ExportController@monthExtract')
        ->name('account.export.billing.month');

    Route::get('incidencias', 'ExportController@incidences')
        ->name('account.export.incidences');
});

/**
 * Imports
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/importar', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'FileImporterController@index')
        ->name('account.importer.index');

    Route::post('import', 'FileImporterController@executeImportation')
        ->name('account.importer.import');
});

/**
 * Customer Contacts
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/contactos', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'ContactsController@datatable')
        ->name('account.contacts.datatable');

    Route::get('create', 'ContactsController@create')
        ->name('account.contacts.create');

    Route::post('store', 'ContactsController@store')
        ->name('account.contacts.store');

    Route::get('{id}/edit', 'ContactsController@edit')
        ->name('account.contacts.edit');

    Route::put('{id}/update', 'ContactsController@update')
        ->name('account.contacts.update');

    Route::delete('{id}/destroy', 'ContactsController@destroy')
        ->name('account.contacts.destroy');
});

/**
 * Customer Departments
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/departamentos', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'DepartmentsController@datatable')
        ->name('account.departments.datatable');

    Route::get('', 'DepartmentsController@index')
        ->name('account.departments.index');

    Route::get('create', 'DepartmentsController@create')
        ->name('account.departments.create');

    Route::post('store', 'DepartmentsController@store')
        ->name('account.departments.store');

    Route::get('{id}/edit', 'DepartmentsController@edit')
        ->name('account.departments.edit');

    Route::put('{id}/update', 'DepartmentsController@update')
        ->name('account.departments.update');

    Route::delete('{id}/destroy', 'DepartmentsController@destroy')
        ->name('account.departments.destroy');
});

/**
 * Customer Billing
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/faturacao', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'BillingController@index')
        ->name('account.billing.index');
});

/**
 * Customer Invoices
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/faturacao/conta-corrente', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('print', 'InvoicesController@printSummary')
        ->name('account.billing.invoices.print');

    Route::post('datatable', 'InvoicesController@datatable')
        ->name('account.billing.invoices.datatable');

    Route::post('sync', 'InvoicesController@syncBalanceAll')
        ->name('account.billing.invoices.sync');

    Route::get('{id}/invoice/download', 'InvoicesController@getInvoice')
        ->name('account.billing.invoices.download.invoice');
});


/**
 * Customer Month Extracts
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/faturacao/extratos', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'BillingController@datatable')
        ->name('account.billing.datatable');

    Route::get('download', 'BillingController@printBilling')
        ->name('account.billing.print');

    Route::get('destinatarios', 'BillingController@byRecipients')
        ->name('account.billing.recipients');
});


/**
 * Customer Refunds Control
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/controlo-reembolsos', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'RefundsControlController@datatable')
        ->name('account.refunds.datatable');

    Route::get('', 'RefundsControlController@index')
        ->name('account.refunds.index');

    Route::put('{id}/update/field', 'RefundsControlController@updateField')
        ->name('account.refunds.update.field');

    Route::get('selected/export', 'RefundsControlController@massExport')
        ->name('account.refunds.selected.export');

    Route::get('selected/print', 'RefundsControlController@massPrint')
        ->name('account.refunds.selected.print');

    Route::post('selected/confirm', 'RefundsControlController@massConfirm')
        ->name('account.refunds.selected.confirm');

    Route::post('requests/datatable', 'RefundsControlController@datatableRequests')
        ->name('account.refunds.requests.datatable');

    Route::get('requests/{id}/show', 'RefundsControlController@showRequests')
        ->name('account.refunds.requests.show');

    Route::delete('requests/{id}/destroy', 'RefundsControlController@destroyRequests')
        ->name('account.refunds.requests.destroy');

    Route::post('selected/request', 'RefundsControlController@massRequest')
        ->name('account.refunds.selected.request');
});

/**
 * Customer account details
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/os-meus-dados', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'DetailsController@index')
        ->name('account.details.index');

    Route::post('gravar', 'DetailsController@customerUpdate')
        ->name('account.details.update');

    Route::post('login/gravar', 'DetailsController@loginUpdate')
        ->name('account.details.login.update');

    Route::post('password/gravar', 'DetailsController@passwordUpdate')
        ->name('account.details.password.update');
});

/**
 * Customer Messages
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/caixa-mensagens', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'MessagesController@datatable')
        ->name('account.messages.datatable');

    Route::get('', 'MessagesController@index')
        ->name('account.messages.index');

    Route::get('{id}', 'MessagesController@show')
        ->name('account.messages.show');

    Route::post('{id}/read', 'MessagesController@setRead')
        ->name('account.messages.read');

    Route::delete('{id}/destroy', 'MessagesController@destroy')
        ->name('account.messages.destroy');
});

/**
 * Customer Logistic
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/gestao-logistica', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account\Logistic'), function () {

    //RECEPTION ORDERS
    Route::post('reception-orders/datatable', 'ReceptionOrdersController@datatable')
        ->name('account.logistic.reception-orders.datatable');

    Route::get('reception-orders/{receptionOrder}/show', 'ReceptionOrdersController@show')
        ->name('account.logistic.reception-orders.show');

    Route::get('reception-orders/create', 'ReceptionOrdersController@create')
        ->name('account.logistic.reception-orders.create');

    Route::get('reception-orders/{receptionOrder}/edit', 'ReceptionOrdersController@edit')
        ->name('account.logistic.reception-orders.edit');

    Route::post('reception-orders/store', 'ReceptionOrdersController@store')
        ->name('account.logistic.reception-orders.store');

    Route::post('reception-orders/{receptionOrder}/update', 'ReceptionOrdersController@update')
        ->name('account.logistic.reception-orders.update');

    Route::delete('reception-orders/{receptionOrder}/destroy', 'ReceptionOrdersController@destroy')
        ->name('account.logistic.reception-orders.destroy');

    Route::get('reception-orders', 'ReceptionOrdersController@index')
        ->name('account.logistic.reception-orders.index');

    //SHIPPING ORDERS
    Route::post('datatable/shipping-orders/datatable', 'ShippingOrdersController@datatable')
        ->name('account.logistic.shipping-orders.datatable');

    Route::get('shipping-orders/{code}/show', 'ShippingOrdersController@show')
        ->name('account.logistic.shipping-orders.show');

    Route::get('shipping-orders', 'ShippingOrdersController@index')
        ->name('account.logistic.shipping-orders.index');

    //CART
    Route::get('cart/show', 'LogisticController@showCart')
        ->name('account.logistic.cart.show');

    Route::post('cart/{id}/add', 'LogisticController@addCart')
        ->name('account.logistic.cart.add');

    Route::post('cart/destroy', 'LogisticController@destroyCart')
        ->name('account.logistic.cart.destroy');

    Route::post('cart/conclude', 'LogisticController@concludeCart')
        ->name('account.logistic.cart.conclude');

    Route::get('cart/conclude/create/shipment', 'LogisticController@createShipment')
        ->name('account.logistic.cart.create.shipment');

    Route::get('cart/set/locations', 'LogisticController@setLocations')
        ->name('account.logistic.cart.set.locations');


    Route::get('cart/index', 'CartController@index')
        ->name('account.logistic.cart.index');

    Route::post('cart/datatable', 'CartController@datatable')
        ->name('account.logistic.cart.datatable');

    Route::delete('cart/order/{id}/destroy', 'CartController@destroy')
        ->name('account.logistic.cart.order.destroy');

    Route::get('cart/order/{id}/show', 'CartController@show')
        ->name('account.logistic.cart.order.show');

    Route::post('cart/order/{id}/refuse', 'CartController@refuse')
        ->name('account.logistic.cart.refuse');

    Route::get('cart/order/{id}/accept', 'CartController@accept')
        ->name('account.logistic.cart.accept');

    Route::post('cart/product/delete', 'CartController@deleteProduct')
        ->name('account.logistic.cart.deleteProduct');


    //PRODUCTS
    Route::post('datatable', 'LogisticController@datatable')
        ->name('account.logistic.products.datatable');

    Route::get('', 'LogisticController@index')
        ->name('account.logistic.products.index');

    Route::get('export', 'LogisticController@export')
        ->name('account.logistic.products.export');

    Route::get('{id}', 'LogisticController@show')
        ->name('account.logistic.products.show');

    Route::get('{id}/history', 'LogisticController@history')
        ->name('account.logistic.products.history');

    Route::get('{id}/details', 'LogisticController@details')
        ->name('account.logistic.products.details');

    Route::get('{id}/edit', 'LogisticController@edit')
        ->name('account.logistic.products.edit');

    Route::put('{id}', 'LogisticController@update')
        ->name('account.logistic.products.update');

    Route::post('datatable/{id}/history', 'LogisticController@datatableHistory')
        ->name('account.logistic.products.datatable.history');

    Route::get('products/search', 'LogisticController@searchProducts')
        ->name('account.logistic.products.search');
});

/**
 * Customer Support Tickets
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/suporte', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('datatable', 'CustomerSupportController@datatable')
        ->name('account.customer-support.datatable');

    Route::get('', 'CustomerSupportController@index')
        ->name('account.customer-support.index');

    Route::get('create', 'CustomerSupportController@create')
        ->name('account.customer-support.create');

    Route::post('store', 'CustomerSupportController@store')
        ->name('account.customer-support.store');

    Route::get('{id}/edit', 'CustomerSupportController@edit')
        ->name('account.customer-support.edit');

    Route::put('{id}/update', 'CustomerSupportController@update')
        ->name('account.customer-support.update');

    Route::delete('{id}/destroy', 'CustomerSupportController@destroy')
        ->name('account.customer-support.destroy');

    Route::post('{id}/conclude', 'CustomerSupportController@conclude')
        ->name('account.customer-support.conclude');

    Route::get('{ticketCode}', 'CustomerSupportController@show')
        ->name('account.customer-support.show');

    Route::post('search/shipment', 'CustomerSupportController@searchShipment')
        ->name('account.customer-support.search.shipment');



    //messages
    Route::post('suporte/{ticketCode}/messages/datatable', 'CustomerSupportController@datatableMessages')
        ->name('account.customer-support.messages.datatable');

    Route::get('suporte/{ticketCode}/messages/create', 'CustomerSupportController@createMessage')
        ->name('account.customer-support.messages.create');

    Route::post('suporte/{ticketCode}/messages/store', 'CustomerSupportController@storeMessage')
        ->name('account.customer-support.messages.store');

    Route::get('suporte/{ticketCode}/messages/{name}/attachment', 'CustomerSupportController@messageAttachment')
        ->name('account.customer-support.messages.attachment');
});

/**
 * Customer Incidences
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/incidencias', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::post('incidences/datatable', 'IncidencesController@datatable')
        ->name('account.incidences.datatable');

    Route::get('', 'IncidencesController@index')
        ->name('account.incidences.index');

    Route::get('{shipmentId}/resolve', 'IncidencesController@create')
        ->name('account.incidences.resolve');

    Route::post('{shipmentId}/store', 'IncidencesController@store')
        ->name('account.incidences.store');
});

/**
 * Customer Budgeter
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/budgeter', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'BudgeterController@index')
        ->name('account.budgeter.index');

    Route::post('calc', 'BudgeterController@calc')
        ->name('account.budgeter.calc');

    Route::get('/preview-prices', 'BudgeterController@modalBudget')
        ->name('account.budgeter.preview-prices.index');

    Route::post('preview-prices/calc', 'BudgeterController@modalCalc')
        ->name('account.budgeter.preview-prices.calc');

    Route::post('get/distance', 'BudgeterController@calcDistance')
        ->name('account.budgeter.get.distance');

    Route::post('get/transit-time', 'BudgeterController@getTransitTime')
        ->name('account.budgeter.get.transit-time');
});

/**
 * Customer wallet
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/wallet', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'WalletController@index')
        ->name('account.wallet.index');

    Route::get('create', 'WalletController@create')
        ->name('account.wallet.create');

    Route::get('show/{id}', 'WalletController@show')
        ->name('account.wallet.show');

    Route::post('store', 'WalletController@store')
        ->name('account.wallet.store');

    Route::post('check/payment', 'WalletController@checkPaymentStatus')
        ->name('account.wallet.check.payment');

    Route::post('wallet/datatable', 'WalletController@datatable')
        ->name('account.wallet.datatable');
});

/**
 * Customer Event-Manager
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/event-manager', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {

    Route::get('', 'EventsManagerController@index')
        ->name('account.event-manager.index');

    Route::post('datatable', 'EventsManagerController@datatable')
        ->name('account.event-manager.datatable');

    Route::get('create', 'EventsManagerController@create')
        ->name('account.event-manager.create');

    Route::post('store', 'EventsManagerController@store')
        ->name('account.event-manager.store');

    Route::get('{id}/edit', 'EventsManagerController@edit')
        ->name('account.event-manager.edit');

    Route::put('{id}/update', 'EventsManagerController@update')
        ->name('account.event-manager.update');

    Route::delete('{id}/destroy', 'EventsManagerController@destroy')
        ->name('account.event-manager.destroy');

    Route::post('status/{id}', 'EventsManagerController@statusUpdate')
        ->name('account.event-manager.status.update');

    Route::post('event-manager/{eventId}/line/{lineId?}', 'EventsManagerController@updateEventLine')
        ->name('account.event-manager.line.update');

    Route::post('{eventId}/line/{lineId}', 'EventsManagerController@removeEventLine')
        ->name('account.event-manager.remove-event-line');
});

/**
 * Customer Products
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/products', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account\Products'), function () {

    Route::get('', 'ProductsController@index')
        ->name('account.products.index');

    Route::get('products/{id}/buy', 'ProductsController@buy')
        ->name('account.products.buy');
});

/**
 * Custome Ecommerce Gateways
 */
Route::group(array('prefix' => LaravelLocalization::setLocale() . '/' . LaravelLocalization::transRoute('account/global.routes.account') . '/ecommerce-gateways', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth'], 'namespace' => 'Account'), function () {
    Route::get('/', 'EcommerceGatewayController@index')
        ->name('account.ecommerce-gateway.index');

    Route::get('/create', 'EcommerceGatewayController@create')
        ->name('account.ecommerce-gateway.create');

    Route::get('{id}/edit', 'EcommerceGatewayController@edit')
        ->name('account.ecommerce-gateway.edit');

    Route::post('/store', 'EcommerceGatewayController@store')
        ->name('account.ecommerce-gateway.store');

    Route::put('{id}/update', 'EcommerceGatewayController@update')
        ->name('account.ecommerce-gateway.update');

    Route::delete('{id}/destroy', 'EcommerceGatewayController@destroy')
        ->name('account.ecommerce-gateway.destroy');

    Route::get('{id}/mapping', 'EcommerceGatewayController@mapping')
        ->name('account.ecommerce-gateway.mapping');

    Route::post('{id}/mapping', 'EcommerceGatewayController@mappingStore')
        ->name('account.ecommerce-gateway.mapping.store');

    Route::get('/orders/{id?}', 'EcommerceGatewayController@orders')
        ->name('account.ecommerce-gateway.orders');

    Route::post('/datatable', 'EcommerceGatewayController@datatable')
        ->name('account.ecommerce-gateway.datatable');
});



/**
 * 
 * Logout
 * 
 */
Route::get('account/logout', 'Auth\LoginController@logout')
    ->name('account.logout')
    ->middleware('auth');
