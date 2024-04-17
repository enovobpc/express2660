<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::post('/endpoint/gls', 'Controller@glsApi')
    ->name('endpoint.api');

Route::post('/endpoint/keyinvoice', 'Controller@keyinvoiceApi')
    ->name('endpoint.keyinvoice');

Route::get('/endpoint/keyinvoice', 'Controller@keyinvoiceApi')
    ->name('endpoint.keyinvoice.download');

Route::get('/core/sync/platform', 'Controller@syncShipments')
    ->name('sync.platforms');

Route::get('sending/shipments/list/{token}', 'Admin\HomeController@sendingList')
    ->name('sending.shipments.list');

Route::get('apk/{env?}', 'Admin\HomeController@ApkDownload')
    ->name('mobile.apk.download');

Route::get('entregaki/shipments/{token}', 'Api\v1\ShipmentsController@entregakiShipmentsList')
    ->name('entregaki.shipments.list');

/*=================================================================================================================
 * LOGIN & RESET PASSWORD
 =================================================================================================================*/
//LOGIN
Route::group(array('prefix' => 'admin/login', 'middleware' => 'guest.admin', 'namespace' => 'Admin\Auth'), function () {

    Route::get('/', 'LoginController@index')
        ->name('admin.login');

    Route::post('/', 'LoginController@login')
        ->name('admin.login.submit');
});

//RESET PASSWORD
Route::group(array('prefix' => 'admin/password', 'middleware' => 'guest.admin', 'namespace' => 'Admin\Auth'), function () {

    Route::get('forgot', 'ForgotPasswordController@index')
        ->name('admin.password.forgot');

    Route::post('email', 'ForgotPasswordController@sendResetLinkEmail')
        ->name('admin.password.forgot.email');

    Route::get('reset/{token}', 'ResetPasswordController@showResetForm')
        ->name('admin.password.reset');

    Route::post('reset', 'ResetPasswordController@reset')
        ->name('admin.password.reset.submit');
});

//LOGOUT
Route::get('admin/logout', 'Admin\Auth\LoginController@logout')
    ->name('admin.logout')
    ->middleware('auth.admin');


/*=================================================================================================================
 * GLOBAL APP ROUTES
 =================================================================================================================*/
Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function () {


    /*=================================================================================================================
     * DASHBOARD & GLOBAL ROUTES
     =================================================================================================================*/
    Route::get('/', 'HomeController@index')
        ->name('admin.dashboard');

    Route::get('/version', 'HomeController@versionInfo')
        ->name('admin.version.about');

    Route::get('weather', 'HomeController@getWeatherWidget')
        ->name('admin.weather.show');

    Route::post('weather/cities', 'HomeController@searchWeatherCities')
        ->name('admin.weather.search.cities');

    Route::post('weather/store', 'HomeController@storeWeatherSettings')
        ->name('admin.weather.store');

    Route::get('denied', 'HomeController@denied')
        ->name('admin.denied');

    Route::post('fast-search', 'HomeController@fastSearch')
        ->name('admin.fast-search');

    Route::get('address-book', 'HomeController@addressBook')
        ->name('admin.address-book.search');

    Route::get('mobile/install', 'HomeController@ApkInstall')
        ->name('admin.mobile.install');

    Route::get('mobile/install/print', 'HomeController@ApkInstallPrint')
        ->name('admin.mobile.install.print');


    /*=================================================================================================================
     * MANAGE MY ACCOUNT
     =================================================================================================================*/
    Route::get('account/edit', 'AccountController@edit')
        ->name('admin.account.edit');

    Route::post('account/update', 'AccountController@update')
        ->name('admin.account.update');

    Route::post('account/payment/confirm', 'AccountController@paymentConfirm')
        ->name('admin.account.payment.confirm');


    /*=================================================================================================================
     * COLABORATORS & OPERATORS
     =================================================================================================================*/
    //COLABORATORS
    Route::post('users/datatable', 'Users\UsersController@datatable')
        ->name('admin.users.datatable');

    Route::post('users/selected/destroy', 'Users\UsersController@massDestroy')
        ->name('admin.users.selected.destroy');

    Route::post('users/{id}/remote-login', 'Users\UsersController@remoteLogin')
        ->name('admin.users.remote-login');

    Route::get('users/{id}/remote-logout', 'HomeController@remoteLogout')
        ->name('admin.users.remote-logout');

    Route::put('users/store/login', 'Users\UsersController@storeLogin')
        ->name('admin.users.store.login');

    Route::put('users/{id}/update/login', 'Users\UsersController@updateLogin')
        ->name('admin.users.update.login');

    Route::resource('users', 'Users\UsersController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //EXPENSES
    Route::post('users/{id}/expenses/datatable', 'Users\ExpensesController@datatable')
        ->name('admin.users.expenses.datatable');

    Route::post('users/{id}/expenses/selected/destroy', 'Users\ExpensesController@massDestroy')
        ->name('admin.users.expenses.selected.destroy');

    Route::resource('users.expenses', 'Users\ExpensesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //WORKGROUPS
    Route::post('users/workgroups/datatable', 'Users\WorkgroupsController@datatable')
        ->name('admin.users.workgroups.datatable');

    Route::post('users/workgroups/selected/destroy', 'Users\WorkgroupsController@massDestroy')
        ->name('admin.users.workgroups.selected.destroy');

    Route::resource('users/workgroups', 'Users\WorkgroupsController', [
        'as' => 'admin.users',
        'except' => ['show', 'create']
    ]);

    //ABSENCES TYPES
    Route::post('users/absences-types/datatable', 'Users\AbsencesTypesController@datatable')
        ->name('admin.users.absences-types.datatable');

    Route::post('users/absences-types/selected/destroy', 'Users\AbsencesTypesController@massDestroy')
        ->name('admin.users.absences-types.selected.destroy');

    Route::resource('users/absences-types', 'Users\AbsencesTypesController', [
        'as' => 'admin.users',
        'except' => ['show', 'create', 'edit']
    ]);

    //ABSENCES
    Route::post('users/{id}/absences/datatable', 'Users\AbsencesController@datatable')
        ->name('admin.users.absences.datatable');

    Route::post('users/{id}/absences/selected/destroy', 'Users\AbsencesController@massDestroy')
        ->name('admin.users.absences.selected.destroy');

    Route::get('users/absences/create/global', 'Users\AbsencesController@create')
        ->name('admin.users.absences.create.global');

    Route::post('users/absences/store/global', 'Users\AbsencesController@store')
        ->name('admin.users.absences.store.global');

    Route::post('users/absences/store/adjust/{userId?}/{id?}', 'Users\AbsencesController@storeAdjust')
        ->name('admin.users.absences.store.adjust');

    Route::get('users/{id}/abscences/adjust', 'Users\AbsencesController@adjustAbsence')
        ->name('admin.users.absences.adjust');

    Route::resource('users.absences', 'Users\AbsencesController', [
        'as' => 'admin',
        'except' => ['show', 'index']
    ]);

    //ATTENDANCE
    Route::post('users/{id}/attendance/datatable', 'Users\AttendanceController@datatable')
        ->name('admin.users.attendance.datatable');

    Route::resource('users.absences', 'Users\AbsencesController', [
        'as' => 'admin',
        'except' => ['show', 'index']
    ]);

    //CONTRACTS
    Route::resource('users.contracts', 'Users\ContractsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);

    //CARDS
    Route::resource('users.cards', 'Users\CardsController', [
        'as' => 'admin',
        'only' => ['store']
    ]);

    //ATTACHMENTS
    Route::post('users/{id}/attachments/datatable', 'Users\AttachmentsController@datatable')
        ->name('admin.users.attachments.datatable');

    Route::post('users/{id}/attachments/selected/destroy', 'Users\AttachmentsController@massDestroy')
        ->name('admin.users.attachments.selected.destroy');

    Route::get('users/{id}/attachments/sort', 'Users\AttachmentsController@sortEdit')
        ->name('admin.users.attachments.sort');

    Route::post('users/{id}/attachments/sort', 'Users\AttachmentsController@sortUpdate')
        ->name('admin.users.attachments.sort.update');

    Route::resource('users.attachments', 'Users\AttachmentsController', [
        'as' => 'admin',
        'except' => ['show', 'index']
    ]);

    //OPERATORS
    /*Route::resource('operators', 'Users\OperatorsController', [
        'as' => 'admin',
        'only' => ['index']]);*/

    //ROLES & PERMISSIONS
    Route::resource('roles', 'RolesController', [
        'as' => 'admin',
        'except' => ['create', 'edit']
    ]);

    /*=================================================================================================================
     * SETTINGS
     =================================================================================================================*/
    Route::resource('settings', 'SettingsController', [
        'as' => 'admin',
        'only' => ['index', 'store']
    ]);

    /*=================================================================================================================
     * CUSTOMERS
     =================================================================================================================*/
    //TYPES
    Route::post('customers-types/datatable', 'Customers\TypesController@datatable')
        ->name('admin.customers-types.datatable');

    Route::post('customers-types/selected/destroy', 'Customers\TypesController@massDestroy')
        ->name('admin.customers-types.selected.destroy');

    Route::resource('customers-types', 'Customers\TypesController', [
        'as' => 'admin',
        'except' => ['show', 'create', 'edit']
    ]);

    //CUSTOMERS
    Route::post('customers/datatable', 'Customers\CustomersController@datatable')
        ->name('admin.customers.datatable');

    Route::post('customers/get/mandate', 'Customers\CustomersController@createMandate')
        ->name('admin.customers.get.mandate');

    Route::post('customers/modal/create', 'Customers\CustomersController@createCustomerFromModal')
        ->name('admin.customers.modal.create');

    Route::post('customers/{customerId}/meetings/datatable', 'Customers\CustomersController@datatableMeetings')
        ->name('admin.customers.meetings.datatable');

    Route::post('customers/selected/destroy', 'Customers\CustomersController@massDestroy')
        ->name('admin.customers.selected.destroy');

    Route::post('customers/selected/inactivate', 'Customers\CustomersController@massInactivate')
        ->name('admin.customers.selected.inactivate');

    Route::post('customers/selected/update', 'Customers\CustomersController@massUpdate')
        ->name('admin.customers.selected.update');

    Route::post('customers/{customerId}/balance/datatable', 'Customers\CustomersController@datatableBalance')
        ->name('admin.customers.balance.datatable');

    Route::post('customers/{customerId}/balance/reset', 'Customers\CustomersController@resetBalance')
        ->name('admin.customers.balance.reset');

    Route::post('customers/{id}/update/login', 'Customers\CustomersController@updateLogin')
        ->name('admin.customers.login');

    Route::post('customers/{id}/services/store', 'Customers\CustomersController@storeServices')
        ->name('admin.customers.services.store');

    Route::post('customers/{id}/services/import', 'Customers\CustomersController@importServices')
        ->name('admin.customers.services.import');

    Route::post('customers/{id}/future-services/store', 'Customers\CustomersController@storeFutureServices')
        ->name('admin.customers.future-services.store');

    Route::post('customers/{id}/future-services/import', 'Customers\CustomersController@importFutureServices')
        ->name('admin.customers.future-services.import');

    Route::post('customers/search', 'Customers\CustomersController@searchCustomer')
        ->name('admin.customers.search');

    Route::post('customers/search/banks', 'Customers\CustomersController@searchBanksInstitutions')
        ->name('admin.customers.search.banks');

    Route::post('customers/{id}/remote-login', 'Customers\CustomersController@remoteLogin')
        ->name('admin.customers.remote-login');

    Route::post('customers/{id}/inactivate', 'Customers\CustomersController@inactivate')
        ->name('admin.customers.inactivate');

    Route::post('customers/{id}/convert/prospect', 'Customers\CustomersController@convertProspect')
        ->name('admin.customers.convert.prospect');

    Route::post('customers/list-emails', 'Customers\CustomersController@listEmails')
        ->name('admin.customers.list-emails');

    Route::get('customers/validate', 'Customers\CustomersController@validateCustomers')
        ->name('admin.customers.validate');

    Route::post('customers/{customerId}/validate', 'Customers\CustomersController@storeValidation')
        ->name('admin.customers.validate.store');

    Route::get('customers/{customerId}/price-table/{groupId}', 'Customers\CustomersController@priceTable')
        ->name('admin.customers.price-table');

    Route::resource('customers', 'Customers\CustomersController', [
        'as' => 'admin',
        'except' => ['show']
    ]);


    //RECIPIENTS
    Route::post('customers/{customerId}/recipients/datatable', 'Customers\RecipientsController@customerDatatable')
        ->name('admin.customers.recipients.datatable');

    Route::post('customers/{customerId}/recipients/selected/destroy', 'Customers\RecipientsController@customerMassDestroy')
        ->name('admin.customers.recipients.selected.destroy');

    Route::post('customers/{customerId}/recipients/import', 'Customers\RecipientsController@import')
        ->name('admin.customers.recipients.import');

    Route::post('customers/{customerId}/destroy/duplicates', 'Customers\RecipientsController@destroyDuplicates')
        ->name('admin.customers.destroy.duplicates');

    Route::resource('customers.recipients', 'Customers\RecipientsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);


    //DEPARTMENTS
    Route::post('customers/{customerId}/departments/datatable', 'Customers\DepartmentsController@datatable')
        ->name('admin.customers.departments.datatable');

    Route::post('customers/{customerId}/departments/selected/destroy', 'Customers\DepartmentsController@massDestroy')
        ->name('admin.customers.departments.selected.destroy');

    Route::resource('customers.departments', 'Customers\DepartmentsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);


    //CONTACTS
    Route::post('customers/{customerId}/contacts/datatable', 'Customers\ContactsController@datatable')
        ->name('admin.customers.contacts.datatable');

    Route::post('customers/{customerId}/contacts/selected/destroy', 'Customers\ContactsController@massDestroy')
        ->name('admin.customers.contacts.selected.destroy');

    Route::resource('customers.contacts', 'Customers\ContactsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);

    //COVENANTS
    Route::post('customers/{customerId}/covenants/datatable', 'Customers\CovenantsController@datatable')
        ->name('admin.customers.covenants.datatable');

    Route::post('customers/{customerId}/covenants/selected/destroy', 'Customers\CovenantsController@massDestroy')
        ->name('admin.customers.covenants.selected.destroy');

    Route::resource('customers.covenants', 'Customers\CovenantsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);

    //CUSTOMER WEBSERVICES
    Route::post('customers/{customerId}/webservices/datatable', 'Customers\WebservicesController@datatable')
        ->name('admin.customers.webservices.datatable');

    Route::get('customers/{customerId}/webservices/import', 'Customers\WebservicesController@showImport')
        ->name('admin.customers.webservices.import');

    Route::post('customers/{customerId}/webservices/import', 'Customers\WebservicesController@storeImport')
        ->name('admin.customers.webservices.import.store');

    Route::resource('customers.webservices', 'Customers\WebservicesController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);


    //MESSAGES
    Route::post('customers/messages/datatable', 'Customers\MessagesController@datatable')
        ->name('admin.customers.messages.datatable');

    Route::post('customers/messages/{id}/datatable/recipients', 'Customers\MessagesController@datatableRecipients')
        ->name('admin.customers.messages.datatable.recipients');

    Route::post('customers/messages/selected/destroy', 'Customers\MessagesController@massDestroy')
        ->name('admin.customers.messages.selected.destroy');

    Route::resource('customers/messages', 'Customers\MessagesController', [
        'as' => 'admin.customers'
    ]);


    //ATTACHMENTS
    Route::post('customers/{id}/attachments/datatable', 'Customers\AttachmentsController@datatable')
        ->name('admin.customers.attachments.datatable');

    Route::post('customers/{id}/attachments/selected/destroy', 'Customers\AttachmentsController@massDestroy')
        ->name('admin.customers.attachments.selected.destroy');

    Route::get('customers/{id}/attachments/sort', 'Customers\AttachmentsController@sortEdit')
        ->name('admin.customers.attachments.sort');

    Route::post('customers/{id}/attachments/sort', 'Customers\AttachmentsController@sortUpdate')
        ->name('admin.customers.attachments.sort.update');

    Route::resource('customers.attachments', 'Customers\AttachmentsController', [
        'as' => 'admin',
        'except' => ['show', 'index']
    ]);

    /*=================================================================================================================
     * RECIPIENTS
     =================================================================================================================*/
    Route::post('recipients/datatable', 'Customers\RecipientsController@datatable')
        ->name('admin.recipients.datatable');

    Route::post('recipients/selected/update', 'Customers\RecipientsController@massUpdate')
        ->name('admin.recipients.selected.update');

    Route::post('recipients/selected/destroy', 'Customers\RecipientsController@massDestroy')
        ->name('admin.recipients.selected.destroy');

    Route::resource('recipients', 'Customers\RecipientsController', [
        'as' => 'admin',
        'only' => ['index']
    ]);

    /*=================================================================================================================
     * Pickup Points
     =================================================================================================================*/
    Route::post('pickup-points/datatable', 'PickupPointsController@datatable')
        ->name('admin.pickup-points.datatable');

    Route::post('pickup-points/selected/destroy', 'PickupPointsController@massDestroy')
        ->name('admin.pickup-points.selected.destroy');

    Route::resource('pickup-points', 'PickupPointsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * COMPANIES
     =================================================================================================================*/
    Route::post('companies/datatable', 'Agencies\CompaniesController@datatable')
        ->name('admin.companies.datatable');

    Route::post('companies/selected/destroy', 'Agencies\CompaniesController@massDestroy')
        ->name('admin.companies.selected.destroy');

    Route::post('companies/{id}/replicate', 'Agencies\CompaniesController@replicate')
        ->name('admin.companies.replicate');

    Route::resource('companies', 'Agencies\CompaniesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * AGENCIES
     =================================================================================================================*/
    Route::post('agencies/datatable', 'Agencies\AgenciesController@datatable')
        ->name('admin.agencies.datatable');

    Route::post('agencies/selected/destroy', 'Agencies\AgenciesController@massDestroy')
        ->name('admin.agencies.selected.destroy');

    Route::post('agencies/{id}/replicate', 'Agencies\AgenciesController@replicate')
        ->name('admin.agencies.replicate');

    Route::resource('agencies', 'Agencies\AgenciesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * INCIDENCES RESOLUTIONS
     =================================================================================================================*/
    //TYPE OF RESOLUTIONS
    Route::post('incidences/resolutions/datatable', 'Shipments\IncidencesResolutionsTypesController@datatable')
        ->name('admin.incidences-resolutions.datatable');

    Route::post('incidences/resolutions/selected/destroy', 'Shipments\IncidencesResolutionsTypesController@massDestroy')
        ->name('admin.incidences-resolutions.selected.destroy');

    Route::resource('incidences/resolutions', 'Shipments\IncidencesResolutionsTypesController', [
        'as' => 'admin.incidences',
        'except' => ['show']
    ]);


    //INCIDENCES CONTROL
    Route::post('shipments/{shipmentId}/incidences/resolve', 'Shipments\IncidencesController@resolveIncidence')
        ->name('admin.shipments.incidences.resolve');

    Route::post('incidences/datatable', 'Shipments\IncidencesController@datatable')
        ->name('admin.incidences.datatable');

    Route::post('incidences/selected/resolve', 'Shipments\IncidencesController@massResolve')
        ->name('admin.incidences.selected.resolve');

    Route::resource('shipments.incidences', 'Shipments\IncidencesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    Route::resource('incidences', 'Shipments\IncidencesController', [
        'as' => 'admin',
        'only' => ['index']
    ]);

    /*=================================================================================================================
     * ATTRIBUTES DECLARATIONS
     =================================================================================================================*/

    Route::get('attributes-declarations/user/{id}/equipment', 'Users\AttributesDeclarationsController@setInfoEquipment')
        ->name('admin.attributes-declarations.equipment.get');

    Route::put('attributes-declarations/equipment/store', 'Users\AttributesDeclarationsController@storeEquipment')
        ->name('admin.attributes-declarations.equipment.store');
    /*=================================================================================================================
     * SERVICES
     =================================================================================================================*/
    Route::post('services/datatable', 'Services\ServicesController@datatable')
        ->name('admin.services.datatable');

    Route::post('services/selected/destroy', 'Services\ServicesController@massDestroy')
        ->name('admin.services.selected.destroy');

    Route::get('services/sort', 'Services\ServicesController@sortEdit')
        ->name('admin.services.sort');

    Route::post('services/sort', 'Services\ServicesController@sortUpdate')
        ->name('admin.services.sort.update');

    Route::post('services/selected/update', 'Services\ServicesController@massUpdate')
        ->name('admin.services.selected.update');

    Route::post('services/selected/replicate', 'Services\ServicesController@massReplicate')
        ->name('admin.services.selected.replicate');

    Route::post('services/{id}/provider-details', 'Services\ServicesController@providerDetails')
        ->name('admin.services.provider-details');

    Route::put('services/{id}/provider-details', 'Services\ServicesController@providerDetails')
        ->name('admin.services.provider-details.update');

    Route::get('services/{service}/duplicate', 'Services\ServicesController@duplicate')
        ->name('admin.services.duplicate');

    Route::resource('services', 'Services\ServicesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //GROUPS
    Route::post('services/groups/datatable', 'Services\GroupsController@datatable')
        ->name('admin.services.groups.datatable');

    Route::post('services/groups/selected/destroy', 'Services\GroupsController@massDestroy')
        ->name('admin.services.groups.selected.destroy');

    Route::get('services/groups/sort', 'Services\GroupsController@sortEdit')
        ->name('admin.services.groups.sort');

    Route::post('services/groups/sort', 'Services\GroupsController@sortUpdate')
        ->name('admin.services.groups.sort.update');

    Route::resource('services/groups', 'Services\GroupsController', [
        'as' => 'admin.services',
        'except' => ['show', 'create', 'edit']
    ]);

    /*=================================================================================================================
     * PROVIDERS
     =================================================================================================================*/
    Route::post('providers/datatable', 'Providers\ProvidersController@datatable')
        ->name('admin.providers.datatable');

    Route::post('providers/search/customer', 'Providers\ProvidersController@searchCustomer')
        ->name('admin.providers.search.customer');

    Route::post('providers/selected/destroy', 'Providers\ProvidersController@massDestroy')
        ->name('admin.providers.selected.destroy');

    Route::post('providers/{id}/services/store', 'Providers\ProvidersController@storeServices')
        ->name('admin.providers.services.store');

    Route::post('providers/{id}/services/import', 'Providers\ProvidersController@importServices')
        ->name('admin.providers.services.import');

    Route::post('providers/{id}/expenses/datatable', 'Providers\ProvidersController@datatableExpenses')
        ->name('admin.providers.expenses.datatable');

    Route::post('providers/{providerId}/expenses/{id}/store', 'Providers\ProvidersController@storeExpense')
        ->name('admin.providers.expenses.store');

    Route::post('providers/{id}/services/datatable', 'Providers\ProvidersController@datatableServices')
        ->name('admin.providers.services.datatable');

    Route::post('providers/{id}/zip-codes/datatable', 'Providers\ProvidersController@datatableZipCodes')
        ->name('admin.providers.zip-codes.datatable');

    Route::post('providers/{providerId}/services/{id}/store', 'Providers\ProvidersController@storeVolumetricFactor')
        ->name('admin.providers.volumetric-factor.store');

    Route::get('providers/sort', 'Providers\ProvidersController@sortEdit')
        ->name('admin.providers.sort');

    Route::post('providers/sort', 'Providers\ProvidersController@sortUpdate')
        ->name('admin.providers.sort.update');

    Route::post('providers/selected/inactivate', 'Providers\ProvidersController@massInactivate')
        ->name('admin.providers.selected.inactivate');

    Route::post('providers/{id}/inactivate', 'Providers\ProvidersController@inactivate')
        ->name('admin.providers.inactivate');

    Route::put('providers/{id}/expenses', 'Providers\ProvidersController@updateCustomExpenses')
        ->name('admin.providers.expenses.update');

    Route::get('providers/{providerId}/price-table/{groupId}', 'Providers\ProvidersController@priceTable')
        ->name('admin.providers.price-table');

    Route::resource('providers', 'Providers\ProvidersController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //TYPES
    Route::post('providers-types/datatable', 'Providers\TypesController@datatable')
        ->name('admin.providers-types.datatable');

    Route::post('providers-types/selected/destroy', 'Providers\TypesController@massDestroy')
        ->name('admin.providers-types.selected.destroy');

    Route::resource('providers-types', 'Providers\TypesController', [
        'as' => 'admin',
        'except' => ['show', 'create', 'edit']
    ]);

    //ATTACHMENTS
    Route::post('providers/{id}/attachments/datatable', 'Providers\AttachmentsController@datatable')
        ->name('admin.providers.attachments.datatable');

    Route::post('providers/{id}/attachments/selected/destroy', 'Providers\AttachmentsController@massDestroy')
        ->name('admin.providers.attachments.selected.destroy');

    Route::get('providers/{id}/attachments/sort', 'Providers\AttachmentsController@sortEdit')
        ->name('admin.providers.attachments.sort');

    Route::post('providers/{id}/attachments/sort', 'Providers\AttachmentsController@sortUpdate')
        ->name('admin.providers.attachments.sort.update');

    Route::resource('providers.attachments', 'Providers\AttachmentsController', [
        'as' => 'admin',
        'except' => ['show', 'index']
    ]);

    /*=================================================================================================================
     * SHIPMENTS
     =================================================================================================================*/

    //SCHEDULED
    Route::post('shipments/scheduled/datatable', 'Shipments\ShipmentsController@datatableShipmentsScheduled')
        ->name('admin.shipments.scheduled.datatable');

    //HISTORY & STATUS CHANGE
    Route::post('shipments/history/selected', 'Shipments\HistoryController@massUpdate')
        ->name('admin.shipments.history.selected.update');

    Route::post('shipments/{shipmentId}/history/sync', 'Shipments\HistoryController@syncHistory')
        ->name('admin.shipments.history.sync');

    Route::get('shipments/{shipmentId}/history/pendings', 'Shipments\HistoryController@changePendingStatus')
        ->name('admin.shipments.history.pendings.edit');

    Route::post('shipments/{shipmentId}/history/pendings', 'Shipments\HistoryController@massChangePendingStatus')
        ->name('admin.shipments.history.pendings.update');

    //EXPENSES
    Route::get('shipments/expenses/import/modal', 'ShipmentsImportExpensesController@importModal')
        ->name('admin.shipments.expenses.import.modal');

    Route::post('shipments/expenses/import/store', 'ShipmentsImportExpensesController@import')
        ->name('admin.shipments.expenses.import.store');

    Route::post('shipments/expenses/{shipmentId?}/get/price', 'ShipmentsExpensesController@getPrice')
        ->name('admin.shipments.expenses.get.price');

    Route::resource('shipments.expenses', 'ShipmentsExpensesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //INTERVENTIONS
    Route::resource('shipments.interventions', 'Shipments\InterventionsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);

    //ATTACHMENTS
    Route::resource('shipments.attachments', 'Shipments\AttachmentsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);

    //TRANSHIPMENTS
    Route::resource('shipments.transhipments', 'Shipments\TranshipmentsController', [
        'as' => 'admin',
        'except' => ['index', 'show']
    ]);

    //SHIPMENTS
    Route::post('shipments/datatable', 'Shipments\ShipmentsController@datatable')
        ->name('admin.shipments.datatable');

    Route::post('shipments/confirm', 'Shipments\ShipmentsController@confirmShipment')
        ->name('admin.shipments.confirm');

    Route::post('shipments/selected/destroy', 'Shipments\ShipmentsController@massDestroy')
        ->name('admin.shipments.selected.destroy');

    Route::post('shipments/selected/update/{field}', 'Shipments\ShipmentsController@massUpdate')
        ->name('admin.shipments.selected.update');

    Route::post('shipments/selected/force-sync', 'Shipments\ShipmentsController@massForceSync')
        ->name('admin.shipments.selected.force-sync');

    Route::post('shipments/selected/close-shipment', 'Shipments\ShipmentsController@massCloseShipments')
        ->name('admin.shipments.selected.close-shipment');

    Route::get('shipments/generic-transportation-guide/edit', 'Shipments\ShipmentsController@createGlobalTransportGuide')
        ->name('admin.shipments.generic-transportation-guide.edit');

    Route::post('shipments/selected/block', 'Shipments\ShipmentsController@massBlockShipments')
        ->name('admin.shipments.selected.block');

    /* Route::get('shipments/selected/assign-expenses', 'ShipmentsExpensesController@create')
        ->name('admin.shipments.selected.assign-expenses');

    Route::post('shipments/selected/assign-expenses/store', 'ShipmentsExpensesController@update')
        ->name('admin.shipments.selected.assign-expenses.store'); */

    Route::get('shipments/selected/notify/edit', 'Shipments\ShipmentsController@editNotification')
        ->name('admin.shipments.selected.notify.edit');

    Route::post('shipments/selected/notify/send', 'Shipments\ShipmentsController@sendNotification')
        ->name('admin.shipments.selected.notify.send');

    Route::get('shipments/selected/grouped', 'Shipments\ShipmentsController@massEditGrouped')
        ->name('admin.shipments.selected.grouped.edit');

    Route::post('shipments/selected/grouped', 'Shipments\ShipmentsController@massStoreGrouped')
        ->name('admin.shipments.selected.grouped.store');

    Route::get('shipments/selected/create-manifest', 'Shipments\ShipmentsController@massCreateManifest')
        ->name('admin.shipments.selected.create-manifest');

    Route::post('shipments/search/customer', 'Shipments\ShipmentsController@searchCustomer')
        ->name('admin.shipments.search.customer');

    Route::post('shipments/search/provider', 'Shipments\ShipmentsController@searchProvider')
        ->name('admin.shipments.search.provider');

    Route::get('shipments/search/sender', 'Shipments\ShipmentsController@searchSender')
        ->name('admin.shipments.search.sender');

    Route::get('shipments/search/recipient', 'Shipments\ShipmentsController@searchRecipient')
        ->name('admin.shipments.search.recipient');

    Route::get('shipments/search/sku', 'Shipments\ShipmentsController@searchSku')
        ->name('admin.shipments.search.sku');

    Route::post('shipments/get/delivery-route', 'Shipments\ShipmentsController@getDeliveryRoute')
        ->name('admin.shipments.get.delivery-route');

    Route::post('shipments/update/fields', 'Shipments\ShipmentsController@updateFields')
        ->name('admin.shipments.update.fields');

    Route::post('shipments/get/counties', 'Shipments\ShipmentsController@getCounties')
        ->name('admin.shipments.get.counties');

    Route::post('shipments/get/agency', 'Shipments\ShipmentsController@getAgency')
        ->name('admin.shipments.get.agency');

    Route::post('shipments/get/pudos', 'Shipments\ShipmentsController@getPudos')
        ->name('admin.shipments.get.pudos');

    Route::post('shipments/get/customer', 'Shipments\ShipmentsController@getCustomer')
        ->name('admin.shipments.get.customer');

    Route::post('shipments/get/price', 'Shipments\ShipmentsController@getPrice')
        ->name('admin.shipments.get.price');

    Route::post('shipments/compare/prices', 'Shipments\ShipmentsController@getPricesCompare')
        ->name('admin.shipments.compare.prices');

    Route::post('shipments/get/recipient', 'Shipments\ShipmentsController@getRecipient')
        ->name('admin.shipments.get.recipient');

    Route::get('shipments/{id}/pod/{historyId?}', 'Shipments\ShipmentsController@getPod')
        ->name('admin.shipments.get.pod');

    Route::get('shipments/{id}/return', 'Shipments\ShipmentsController@createReturn')
        ->name('admin.shipments.create.return');

    Route::post('shipments/{id}/return/direct', 'Shipments\ShipmentsController@createDirectReturn')
        ->name('admin.shipments.create.return.direct');

    Route::post('shipments/{id}/devolution', 'Shipments\ShipmentsController@createDevolution')
        ->name('admin.shipments.create.devolution');

    Route::get('shipments/{id}/replicate/create', 'Shipments\ShipmentsController@createReplicate')
        ->name('admin.shipments.replicate.create');

    Route::post('shipments/{id}/replicate', 'Shipments\ShipmentsController@replicate')
        ->name('admin.shipments.replicate');

    Route::get('shipments/print/labelsA4/edit', 'Shipments\ShipmentsController@editPrintA4')
        ->name('admin.shipments.print.labelsA4.edit');

    Route::get('shipments/{id}/email/{target}/edit', 'Shipments\ShipmentsController@editEmailDispacher')
        ->name('admin.shipments.email.edit');

    Route::post('shipments/{id}/email/{target}/submit', 'Shipments\ShipmentsController@sendEmailDispacher')
        ->name('admin.shipments.email.submit');

    //Change delivery date
    Route::get('shipments/{id}/delivery-date/edit',  'Shipments\ShipmentsController@editDeliveryDate')
        ->name('admin.shipments.delivery-date.edit');

    Route::post('shipments/{id}/delivery-date/store',  'Shipments\ShipmentsController@storeDeliveryDate')
        ->name('admin.shipments.delivery-date.store');

    //WEBSERVICE
    Route::post('shipments/{id}/sync/force', 'Shipments\ShipmentsController@forceSync')
        ->name('admin.shipments.sync.force');

    Route::get('shipments/{id}/sync/reset', 'Shipments\ShipmentsController@editResetSync')
        ->name('admin.shipments.sync.reset');

    Route::post('shipments/{id}/sync/reset', 'Shipments\ShipmentsController@storeResetSync')
        ->name('admin.shipments.sync.reset.store');

    Route::get('shipments/{id}/sync/manual', 'Shipments\ShipmentsController@editManualSync')
        ->name('admin.shipments.sync.manual');

    Route::post('shipments/{id}/sync/manual', 'Shipments\ShipmentsController@storeManualSync')
        ->name('admin.shipments.sync.manual.store');

    Route::get('shipments/budget/create', 'Shipments\ShipmentsController@createBudget')
        ->name('admin.shipments.budget.create');

    Route::post('shipments/budget/calculate', 'Shipments\ShipmentsController@calculateBudget')
        ->name('admin.shipments.budget.calculate');

    Route::get('shipments/acceptance-certificates', 'Shipments\ShipmentsController@showAcceptanceCertificates')
        ->name('admin.shipments.acceptance-certificates');

    Route::post('shipments/{id}/restore', 'Shipments\ShipmentsController@restore')
        ->name('admin.shipments.restore');

    Route::get('shipments/{id}/destroy/confirm', 'Shipments\ShipmentsController@confirmDestroy')
        ->name('admin.shipments.destroy.confirm');

    Route::post('shipments/generate-shipments-from-pickups', 'Shipments\ShipmentsController@autoGenerateShipmentsFromPickups')
        ->name('admin.shipments.generate-shipments-from-pickups');

    /**
     * Shipments Details
     */

    Route::get('shipments/route-details', 'Shipments\ShipmentsController@routeDetails')
        ->name('admin.shipments.route-details');
    
    /***/

    Route::resource('shipments', 'Shipments\ShipmentsController', ['as' => 'admin']);

    Route::resource('shipments.history', 'Shipments\HistoryController', [
        'as' => 'admin',
        'only' => ['create', 'store', 'destroy']
    ]);

    Route::post('shipments/{shipmentId}/history/{id}/restore', 'Shipments\HistoryController@restore')
        ->name('admin.shipments.history.restore');


    /*=================================================================================================================
     * SHIPMENT STATUS
     =================================================================================================================*/
    Route::post('tracking/status/datatable', 'Shipments\StatusController@datatable')
        ->name('admin.tracking.status.datatable');

    Route::post('tracking/status/selected/update', 'Shipments\StatusController@massUpdate')
        ->name('admin.tracking.status.selected.update');

    Route::post('tracking/status/selected/destroy', 'Shipments\StatusController@massDestroy')
        ->name('admin.tracking.status.selected.destroy');

    Route::get('tracking/status/sort', 'Shipments\StatusController@sortEdit')
        ->name('admin.tracking.status.sort');

    Route::post('tracking/status/sort', 'Shipments\StatusController@sortUpdate')
        ->name('admin.tracking.status.sort.update');

    Route::resource('tracking/status', 'Shipments\StatusController', [
        'as' => 'admin.tracking',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * SHIPMENT INCIDENCES LIST
     =================================================================================================================*/
    Route::post('tracking/incidences/datatable', 'Shipments\IncidencesTypesController@datatable')
        ->name('admin.tracking.incidences.datatable');

    Route::post('tracking/incidences/selected/destroy', 'Shipments\IncidencesTypesController@massDestroy')
        ->name('admin.tracking.incidences.selected.destroy');

    Route::post('tracking/incidences/selected/update', 'Shipments\IncidencesTypesController@massUpdate')
        ->name('admin.tracking.incidences.selected.update');

    Route::get('tracking/incidences/sort', 'Shipments\IncidencesTypesController@sortEdit')
        ->name('admin.tracking.incidences.sort');

    Route::post('tracking/incidences/sort', 'Shipments\IncidencesTypesController@sortUpdate')
        ->name('admin.tracking.incidences.sort.update');

    Route::resource('tracking/incidences', 'Shipments\IncidencesTypesController', [
        'as' => 'admin.tracking',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * SHIPMENT TRANSPORT TYPES
     =================================================================================================================*/
    Route::post('transport-types/datatable', 'Shipments\TransportTypesController@datatable')
        ->name('admin.transport-types.datatable');

    Route::post('transport-types/selected/destroy', 'Shipments\TransportTypesController@massDestroy')
        ->name('admin.transport-types.selected.destroy');

    Route::get('transport-types/sort', 'Shipments\TransportTypesController@sortEdit')
        ->name('admin.transport-types.sort');

    Route::post('transport-types/sort', 'Shipments\TransportTypesController@sortUpdate')
        ->name('admin.transport-types.sort.update');

    Route::resource('transport-types', 'Shipments\TransportTypesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * SHIPMENT PACK TYPES
     =================================================================================================================*/
    Route::post('pack-types/datatable', 'Shipments\PackTypesController@datatable')
        ->name('admin.pack-types.datatable');

    Route::post('pack-types/selected/destroy', 'Shipments\PackTypesController@massDestroy')
        ->name('admin.pack-types.selected.destroy');

    Route::get('pack-types/sort', 'Shipments\PackTypesController@sortEdit')
        ->name('admin.pack-types.sort');

    Route::post('pack-types/sort', 'Shipments\PackTypesController@sortUpdate')
        ->name('admin.pack-types.sort.update');

    Route::resource('pack-types', 'Shipments\PackTypesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * SHIPMENT PACK TYPES GROUPS
     =================================================================================================================*/

    // Route::post('pack-types/groups/datatable', 'Shipments\PackTypesGroupsController@datatable')
    //     ->name('admin.pack-types.groups.datatable');

    // Route::resource('pack-types/groups', 'Shipments\PackTypesGroupsController', [
    //     'as' => 'admin.pack-types',
    //     'except' => 'show'
    // ]);

    /*=================================================================================================================
     * COLLECTIONS
     =================================================================================================================*/
    Route::post('pickups/datatable', 'Shipments\PickupsController@datatable')
        ->name('admin.pickups.datatable');

    Route::get('pickups/{id}/shipment', 'Shipments\PickupsController@createShipment')
        ->name('admin.pickups.create.shipment');

    Route::post('pickup/{id}/convert', 'Shipments\PickupsController@convertToShipment')
        ->name('admin.pickup.convert');

    Route::resource(
        'pickups',
        'Shipments\PickupsController',
        [
            'as' => 'admin',
            'only' => ['index', 'create', 'store', 'edit', 'update']
        ]
    );

    /*=================================================================================================================
     * SHIPMENT INVOICES
     =================================================================================================================*/

    Route::resource('shipments.invoices', 'Shipments\InvoicesController', [
        'as' => 'admin',
        'only' => ['create']
    ]);

    /*=================================================================================================================
     * CARGO PLANNING
     =================================================================================================================*/

    Route::get('timline/events', 'Timeline\TimelineController@getCalendarEvents')
        ->name('admin.timeline.events');

    Route::resource('timeline', 'Timeline\TimelineController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //Types
    Route::resource('timeline/types', 'Timeline\EventsTypesController', [
        'as' => 'admin.timeline',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * BILLING
     =================================================================================================================*/
    //BILLING API KEYS
    Route::post('billing/api-keys/datatable', 'Billing\ApiKeysController@datatable')
        ->name('admin.billing.api-keys.datatable');

    Route::post('billing/api-keys/selected/destroy', 'Billing\ApiKeysController@massDestroy')
        ->name('admin.billing.api-keys.selected.destroy');

    Route::resource('billing/api-keys', 'Billing\ApiKeysController', [
        'as' => 'admin.billing',
        'except' => ['show']
    ]);

    //VAT RATES
    Route::post('billing/vat-rates/datatable', 'Billing\VatRatesController@datatable')
        ->name('admin.billing.vat-rates.datatable');

    Route::post('billing/vat-rates/selected/destroy', 'Billing\VatRatesController@massDestroy')
        ->name('admin.billing.vat-rates.selected.destroy');

    Route::get('billing/vat-rates/sort', 'Billing\VatRatesController@sortEdit')
        ->name('admin.billing.vat-rates.sort');

    Route::post('billing/vat-rates/sort', 'Billing\VatRatesController@sortUpdate')
        ->name('admin.billing.vat-rates.sort.update');

    Route::resource('billing/vat-rates', 'Billing\VatRatesController', [
        'as' => 'admin.billing',
        'except' => ['show']
    ]);

    //BILLING ITEMS
    Route::post('billing/items/datatable', 'Billing\ItemsController@datatable')
        ->name('admin.billing.items.datatable');

    Route::post('billing/items/selected/destroy', 'Billing\ItemsController@massDestroy')
        ->name('admin.billing.items.selected.destroy');

    Route::get('billing/items/sync', 'Billing\ItemsController@sync')
        ->name('admin.billing.items.sync');

    Route::get('billing/items/sort', 'Billing\ItemsController@sortEdit')
        ->name('admin.billing.items.sort');

    Route::post('billing/items/sort', 'Billing\ItemsController@sortUpdate')
        ->name('admin.billing.items.sort.update');

    Route::resource('billing/items', 'Billing\ItemsController', [
        'as' => 'admin.billing',
        'except' => ['show']
    ]);

    // Brands
    Route::post('brands/datatable', 'Brand\BrandController@datatable')
        ->name('admin.brands.datatable');

    Route::post('brands/selected/destroy', 'Brand\BrandController@massDestroy')
        ->name('admin.brands.selected.destroy');

    Route::get('brands/sort', 'Brand\BrandController@sortEdit')
        ->name('admin.brands.sort');

    Route::post('brands/sort', 'Brand\BrandController@sortUpdate')
        ->name('admin.brands.sort.update');

    Route::resource('brands', 'Brand\BrandController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    // Brands Models
    Route::post('brand/{brandId}/datatable', 'Brand\BrandModelController@datatable')
        ->name('admin.brands.models.datatable');

    Route::get('brand/{brandId}/models', 'Brand\BrandModelController@index')
        ->name('admin.brands.models.index');

    Route::post('brand/{brandId}/models', 'Brand\BrandModelController@store')
        ->name('admin.brands.models.store');

    Route::put('brand/{brandId}/models/{id}', 'Brand\BrandModelController@update')
        ->name('admin.brands.models.update');

    Route::delete('brand/{brandId}/models/{id}', 'Brand\BrandModelController@destroy')
        ->name('admin.brands.models.destroy');

    Route::get('brands/models/list', 'Brand\BrandModelController@list')
        ->name('admin.brands.models.list');

    //BILLING ZONES
    Route::post('billing/zones/datatable', 'Billing\ZonesController@datatable')
        ->name('admin.billing.zones.datatable');

    Route::post('billing/zones/selected/destroy', 'Billing\ZonesController@massDestroy')
        ->name('admin.billing.zones.selected.destroy');

    Route::post('billing/zones/zip-codes/search', 'Billing\ZonesController@searchZipCodes')
        ->name('admin.billing.zones.search.zip-codes');

    Route::post('billing/zones/{id}/replicate', 'Billing\ZonesController@replicate')
        ->name('admin.billing.zones.replicate');

    Route::resource('billing/zones', 'Billing\ZonesController', [
        'as' => 'admin.billing',
        'except' => ['show']
    ]);

    //BILLING PROVIDERS
    Route::post('billing/providers/providers/datatable', 'Billing\ProvidersController@datatable')
        ->name('admin.billing.providers.datatable');

    Route::post('billing/providers/{providerId}/shipments/datatable', 'Billing\ProvidersController@datatableShipments')
        ->name('admin.billing.providers.shipments.datatable');

    Route::post('billing/providers/shipments/confirm', 'Billing\ProvidersController@massConfirmShipments')
        ->name('admin.billing.providers.shipments.confirm');

    Route::resource('billing/providers', 'Billing\ProvidersController', [
        'as'    => 'admin.billing',
        'except'  => ['destroy', 'create', 'store']
    ]);


    //BILLING AGENCIES
    /*Route::post('billing/agencies/datatable', 'Billing\AgenciesController@datatable')
        ->name('admin.billing.agencies.datatable');

    Route::post('billing/agencies/{senderAgency}/{recipientAgency}/shipments/datatable', 'Billing\AgenciesController@datatableShipments')
        ->name('admin.billing.agencies.shipments.datatable');

    Route::get('billing/agencies/{senderAgency}/{recipientAgency}/show', 'Billing\AgenciesController@show')
        ->name('admin.billing.agencies.show');

    Route::post('billing/agencies/shipments/confirm', 'Billing\AgenciesController@massConfirmShipments')
        ->name('admin.billing.agencies.shipments.confirm');

    Route::get('billing/agencies/{senderAgency}/{recipientAgency}/invoice/pdf', 'Billing\AgenciesController@downloadInvoice')
        ->name('admin.billing.agencies.invoice.pdf');

    Route::post('billing/agencies/{senderAgency}/{recipientAgency}/invoice/convert', 'Billing\AgenciesController@convertFromDraft')
        ->name('admin.billing.agencies.invoice.convert');

    Route::post('billing/agencies/{senderAgency}/{recipientAgency}/invoice/destroy', 'Billing\AgenciesController@destroyInvoice')
        ->name('admin.billing.agencies.invoice.destroy');

    Route::get('billing/agencies/{senderAgency}/{recipientAgency}/email', 'Billing\AgenciesController@editBillingEmail')
        ->name('admin.billing.agencies.email.edit');

    Route::post('billing/agencies/{senderAgency}/{recipientAgency}/email/send', 'Billing\AgenciesController@submitBillingEmail')
        ->name('admin.billing.agencies.email.submit');

    Route::resource('billing/agencies', 'Billing\AgenciesController', [
        'as'    => 'admin.billing',
        'except'  => ['destroy', 'create', 'store', 'show']]);*/


    //BILLING CUSTOMERS
    Route::post('billing/customers/datatable', 'Billing\CustomersController@datatable')
        ->name('admin.billing.customers.datatable');

    Route::get('billing/customers/mass/billing/edit', 'Billing\CustomersController@massBillingEdit')
        ->name('admin.billing.customers.mass.billing.edit');

    Route::post('billing/customers/mass/billing/store', 'Billing\CustomersController@massBillingStore')
        ->name('admin.billing.customers.mass.billing.store');

    Route::post('billing/customers/mass/update-prices', 'Billing\CustomersController@massUpdatePrices')
        ->name('admin.billing.customers.mass.prices');

    Route::post('billing/customers/mass/validate', 'Billing\CustomersController@massValidatePrices')
        ->name('admin.billing.customers.mass.validate');

    Route::post('billing/customers/update/filters', 'Billing\CustomersController@updateFilters')
        ->name('admin.billing.customers.update.filters');


    Route::post('billing/customers/{id}/shipments/datatable', 'Billing\CustomersController@datatableShipments')
        ->name('admin.billing.customers.shipments.datatable');

    Route::post('billing/customers/{id}/expenses/datatable', 'Billing\CustomersController@datatableExpenses')
        ->name('admin.billing.customers.expenses.datatable');

    Route::post('billing/customers/expenses/selected/destroy', 'ShipmentsExpensesController@massDestroy')
        ->name('admin.billing.customers.expenses.selected.destroy');

    Route::post('billing/customers/{id}/products/datatable', 'Billing\CustomersController@datatableProducts')
        ->name('admin.billing.customers.products.datatable');

    Route::post('billing/customers/{id}/covenants/datatable', 'Billing\CustomersController@datatableCovenants')
        ->name('admin.billing.customers.covenants.datatable');

    Route::post('billing/customers/shipments/{customerId}/{month}/{year}/update-prices', 'Billing\CustomersController@updatePrices')
        ->name('admin.billing.customers.shipments.update-prices');

    Route::post('billing/customers/shipments/confirm', 'Billing\CustomersController@massConfirmShipments')
        ->name('admin.billing.customers.shipments.confirm');

    Route::post('billing/customers/shipments/selected/update', 'Billing\CustomersController@massUpdate')
        ->name('admin.billing.customers.shipments.selected.update');

    Route::post('billing/customers/{id}/shipments/selected/update-prices', 'Billing\CustomersController@massUpdatePrices')
        ->name('admin.billing.customers.shipments.selected.update-prices');

    Route::get('billing/customers/{id}/shipments/selected/billing', 'Billing\CustomersController@editBillingSelected')
        ->name('admin.billing.customers.shipments.selected.billing.edit');

    Route::put('billing/customers/{id}/shipments/selected/billing', 'Billing\CustomersController@billingSelected')
        ->name('admin.billing.customers.shipments.selected.billing');

    Route::post('billing/customers/{id}/shipments/selected/update-billing-date', 'Billing\CustomersController@updateBillingDate')
        ->name('admin.billing.customers.shipments.selected.update-billing-date');

    Route::get('billing/customers/{id}/email', 'Billing\CustomersController@editEmail')
        ->name('admin.billing.customers.email.edit');

    Route::post('billing/customers/{id}/email', 'Billing\CustomersController@sendEmail')
        ->name('admin.billing.customers.email.submit');

    Route::resource('billing/customers', 'Billing\CustomersController', [
        'as'    => 'admin.billing',
        'except'  => ['destroy', 'create', 'store']
    ]);

    /*=================================================================================================================
     * PURCHASE INVOICES
     =================================================================================================================*/

    //TYPES
    Route::post('invoices/purchase/types/datatable', 'Invoices\PurchaseTypesController@datatable')
        ->name('admin.invoices.purchase.types.datatable');

    Route::post('invoices-types/selected/destroy', 'Invoices\PurchaseTypesController@massDestroy')
        ->name('admin.invoices.purchase.types.selected.destroy');

    Route::resource('invoices/purchase/types', 'Invoices\PurchaseTypesController', [
        'as' => 'admin.invoices.purchase',
        'except' => ['show', 'create', 'edit']
    ]);

    //PURCHASE
    Route::post('invoices/purchase/datatable', 'Invoices\PurchasesController@datatable')
        ->name('admin.invoices.purchase.datatable');

    Route::post('invoices/selected/destroy', 'Invoices\PurchasesController@massDestroy')
        ->name('admin.invoices.purchase.selected.destroy');

    Route::post('invoices/purchase/search/invoice', 'Invoices\PurchasesController@searchInvoice')
        ->name('admin.invoices.purchase.search.invoice');

    Route::post('invoices/purchase/assign/source', 'Invoices\PurchasesController@getAssignedSources')
        ->name('admin.invoices.purchase.assign.source');

    Route::get('invoices/purchase/{invoiceId}/download/{docType?}', 'Invoices\PurchasesController@download')
        ->name('admin.invoices.purchase.download');

    Route::post('invoices/purchase/{id}/destroy', 'Invoices\PurchasesController@destroy')
        ->name('admin.invoices.purchase.destroy');

    Route::get('invoices/purchase/{providerId}/{invoiceId}/email', 'Invoices\PurchasesController@editEmail')
        ->name('admin.invoices.purchase.email.edit');

    Route::post('invoices/purchase/{providerId}/{invoiceId}/email', 'Invoices\PurchasesController@submitEmail')
        ->name('admin.invoices.purchase.email.submit');

    Route::get('invoices/purchase/search/providers', 'Invoices\PurchasesController@searchProvider')
        ->name('admin.invoices.purchase.search.providers');

    Route::post('invoices/purchase/search/providers/list', 'Invoices\PurchasesController@searchProviderSelect2')
        ->name('admin.invoices.purchase.search.providers.select2');

    Route::post('invoices/purchase/{id}/replicate', 'Invoices\PurchasesController@replicate')
        ->name('admin.invoices.purchase.replicate');

    Route::resource('invoices/purchase', 'Invoices\PurchasesController', [
        'as' => 'admin.invoices',
        'except' => ['show']
    ]);

    //ATTACHMENTS
    Route::resource('invoices/purchase.attachments', 'Invoices\PurchasesAttachmentsController', [
        'as' => 'admin.invoices',
        'except' => ['index', 'show']
    ]);

    //PAYMENT NOTES
    Route::post('invoices/purchase/payment-notes/datatable', 'Invoices\PurchasesPaymentNotesController@datatable')
        ->name('admin.invoices.purchase.payment-notes.datatable');

    Route::get('invoices/purchase/payment-notes/{paymentNoteId}/download', 'Invoices\PurchasesPaymentNotesController@download')
        ->name('admin.invoices.purchase.payment-notes.download');

    Route::get('invoices/purchase/payment-notes/{paymentNoteId}/email/edit', 'Invoices\PurchasesPaymentNotesController@editEmail')
        ->name('admin.invoices.purchase.payment-notes.email.edit');

    Route::post('invoices/purchase/payment-notes/{paymentNoteId}/email/submit', 'Invoices\PurchasesPaymentNotesController@submitEmail')
        ->name('admin.invoices.purchase.payment-notes.email.submit');

    Route::resource('invoices/purchase/payment-notes', 'Invoices\PurchasesPaymentNotesController', [
        'as' => 'admin.invoices.purchase',
        'except' => ['index']
    ]);

    /*=================================================================================================================
     * INVOICES SALES
     =================================================================================================================*/
    Route::post('invoices/sales/datatable', 'Invoices\SalesController@datatable')
        ->name('admin.invoices.datatable');

    Route::post('invoices/sales/divergences', 'Invoices\SalesController@checkDivergences')
        ->name('admin.invoices.sales.divergences');

    Route::post('invoices/sales/search/customer', 'Invoices\SalesController@searchCustomerSelect2')
        ->name('admin.invoices.sales.search.customer');

    Route::get('invoices/sales/search/item', 'Invoices\SalesController@searchItems')
        ->name('admin.invoices.sales.search.item');

    Route::post('invoices/sales/get/customer/invoices', 'Invoices\SalesController@getCustomerInvoices')
        ->name('admin.invoices.sales.get.customer.invoices');

    Route::get('invoices/sales/initial-balance', 'Invoices\SalesController@editInitialBalance')
        ->name('admin.invoices.initial-balance.edit');

    Route::post('invoices/sales/initial-balance', 'Invoices\SalesController@storeInitialBalance')
        ->name('admin.invoices.initial-balance.store');

    Route::get('invoices/sales/mass/edit', 'Invoices\SalesController@nodocSettleEdit')
        ->name('admin.invoices.mass.edit');

    Route::get('invoices/sales/receipt/create', 'Invoices\SalesController@createReceipt')
        ->name('admin.invoices.receipt.create');

    Route::post('invoices/sales/receipt/store', 'Invoices\SalesController@storeReceipt')
        ->name('admin.invoices.receipt.store');

    Route::put('invoices/sales/receipt/{id}/update', 'Invoices\SalesController@updateReceipt')
        ->name('admin.invoices.receipt.update');

    Route::get('invoices/sales/regularization/create', 'Invoices\SalesController@createRegularization')
        ->name('admin.invoices.regularization.create');

    Route::post('invoices/sales/regularization/store', 'Invoices\SalesController@storeRegularization')
        ->name('admin.invoices.regularization.store');

    Route::put('invoices/sales/regularization/{id}/update', 'Invoices\SalesController@updateRegularization')
        ->name('admin.invoices.regularization.update');
        
   /*  Route::get('invoices/sales/opening-balance/{id}/edit', 'Invoices\SalesController@editOpeningBalance')
        ->name('admin.invoices.opening-balance.edit');

    Route::put('invoices/sales/opening-balance/{id}', 'Invoices\SalesController@updateOpeningBalance')
        ->name('admin.invoices.opening-balance.update'); */

    Route::get('invoices/sales/{invoiceId}/download', 'Invoices\SalesController@documentPdf')
        ->name('admin.invoices.download.pdf');

    Route::get('invoices/sales/{customerId}/{invoiceId}/download', 'Invoices\SalesController@download')
        ->name('admin.invoices.download');

    Route::get('invoices/sales/{customerId}/{invoiceId}/summary', 'Invoices\SalesController@invoiceSummary')
        ->name('admin.invoices.summary');

    Route::post('invoices/sales/{invoiceId}/convert', 'Invoices\SalesController@convertFromDraft')
        ->name('admin.invoices.convert');

    Route::post('invoices/sales/{id}/destroy', 'Invoices\SalesController@destroy')
        ->name('admin.invoices.destroy');

    Route::get('invoices/sales/{customerId}/{invoiceId}/destroy/edit', 'Invoices\SalesController@destroyEdit')
        ->name('admin.invoices.destroy.edit');

    Route::get('invoices/sales/{customerId}/{invoiceId}/email', 'Invoices\SalesController@editEmail')
        ->name('admin.invoices.email.edit');

    /*Route::post('invoices/sales/{customerId}/{invoiceId}/email', 'Invoices\SalesController@submitEmail')
        ->name('admin.invoices.email.submit');*/

    Route::post('invoices/sales/{id}/email', 'Invoices\SalesController@submitEmail')
        ->name('admin.invoices.email.submit');

    Route::get('invoices/sales/search/customers', 'Invoices\SalesController@searchCustomer')
        ->name('admin.invoices.search.customers');

    Route::post('invoices/sales/search/customers/vat', 'Invoices\SalesController@searchCustomerByVat')
        ->name('admin.invoices.search.customers.vat');

    Route::get('invoices/sales/saft', 'Invoices\SalesController@showSaft')
        ->name('admin.invoices.saft');

    Route::get('invoices/sales/saft/{year}/{month}/download', 'Invoices\SalesController@downloadSaft')
        ->name('admin.invoices.saft.download');

    Route::post('invoices/sales/invoices/pdf/massive', 'Invoices\SalesController@downloadZipDoc')
        ->name('admin.invoices.pdf.massive');

    Route::get('invoices/sales/saft/{year}/{month}/email', 'Invoices\SalesController@editSaftEmail')
        ->name('admin.invoices.saft.email');

    Route::post('invoices/sales/saft/{year}/{month}/email/send', 'Invoices\SalesController@sendSaftEmail')
        ->name('admin.invoices.saft.email.send');

    Route::post('invoices/sales/{invoiceId}/replicate', 'Invoices\SalesController@replicate')
        ->name('admin.invoices.replicate');

    Route::get('invoices/sales/{invoiceId}/settle', 'Invoices\SalesController@nodocSettleEdit')
        ->name('admin.invoices.nodoc.settle.edit');

    Route::post('invoices/sales/{invoiceId}/settle', 'Invoices\SalesController@nodocSettleStore')
        ->name('admin.invoices.nodoc.settle.store');

    Route::get('invoices/sales/{id}/autocreate', 'Invoices\SalesController@editAutocreate')
        ->name('admin.invoices.autocreate.edit');

    Route::post('invoices/sales/{id}/autocreate/store', 'Invoices\SalesController@storeAutocreate')
        ->name('admin.invoices.autocreate.store');

    Route::resource('invoices', 'Invoices\SalesController', [
        'as' => 'admin',
        'except' => ['show', 'destroy']
    ]);

    /*=================================================================================================================
     * CURRENT ACCOUNT
     =================================================================================================================*/
    Route::post('billing/balance/datatable', 'Billing\BalanceController@datatable')
        ->name('admin.billing.balance.datatable');

    Route::post('billing/balance/divergences/upload', 'Billing\BalanceController@uploadDivergences')
        ->name('admin.billing.balance.divergences.upload');

    /* Route::get('billing/balance/email/invoice/{customerBalanceId}', 'Billing\BalanceController@editEmailInvoice')
        ->name('admin.billing.balance.email.invoice.edit'); */

    /* Route::post('billing/balance/email/invoice/{customerBalanceId?}', 'Billing\BalanceController@sendEmailInvoice')
        ->name('admin.billing.balance.email.invoice'); */

    Route::post('billing/balance/selected/email/balance', 'Billing\BalanceController@massSendEmailBalance')
        ->name('admin.billing.balance.selected.email.balance');

    Route::get('billing/balance/email/balance/{customerId}', 'Billing\BalanceController@editEmailBalance')
        ->name('admin.billing.balance.email.balance.edit');

    Route::post('billing/balance/email/balance/{customerId}', 'Billing\BalanceController@sendEmailBalance')
        ->name('admin.billing.balance.email.balance');

    Route::post('billing/balance/{id}/datatable/balance', 'Billing\BalanceController@datatableBalance')
        ->name('admin.billing.balance.datatable.balance');

    Route::post('billing/balance/update/payment-status/{customerId?}/{balanceId?}', 'Billing\BalanceController@updatePaymentStatus')
        ->name('admin.billing.balance.update.payment-status');

    Route::get('billing/balance/sync/massive', 'Billing\BalanceController@massSyncBalance')
        ->name('admin.billing.balance.mass.sync');

    Route::post('billing/balance/sync/all/{customerId?}', 'Billing\BalanceController@syncBalanceAll')
        ->name('admin.billing.balance.sync.all');

    Route::post('billing/balance/sync/{customerId?}', 'Billing\BalanceController@syncBalance')
        ->name('admin.billing.balance.sync');

    Route::get('billing/balance/{balanceId}/invoice', 'Billing\BalanceController@getInvoice')
        ->name('admin.billing.balance.invoice');

    /* Route::post('billing/balance/hide-document/{customerId}/{id}', 'Billing\BalanceController@hideDocument')
        ->name('admin.billing.balance.hide'); */

    Route::resource('billing/balance', 'Billing\BalanceController', [
        'as' => 'admin.billing',
        'only' => ['index', 'show']
    ]);


    /*=================================================================================================================
     * GLOBAL PRICES TABLES
     =================================================================================================================*/
    Route::post('prices-tables/datatable', 'PricesTablesController@datatable')
        ->name('admin.prices-tables.datatable');

    Route::post('prices-tables/selected/destroy', 'PricesTablesController@massDestroy')
        ->name('admin.prices-tables.selected.destroy');

    Route::post('prices-tables/{id}/services/store', 'PricesTablesController@storeServices')
        ->name('admin.prices-tables.services.store');

    Route::post('prices-tables/{id}/services/import', 'PricesTablesController@importServices')
        ->name('admin.prices-tables.services.import');

    Route::get('prices-tables/mass/edit', 'PricesTablesController@massEditPrices')
        ->name('admin.prices-tables.mass.edit');

    Route::post('prices-tables/mass/update-prices', 'PricesTablesController@massUpdatePrices')
        ->name('admin.prices-tables.mass.update');

    Route::resource('prices-tables', 'PricesTablesController', [
        'as'    => 'admin',
        'except'  => ['show']
    ]);


    /*=================================================================================================================
     * TRACEABILITY
     =================================================================================================================*/

    //EVENTS
    Route::post('traceability/events/datatable', 'Traceability\EventsController@datatable')
        ->name('admin.traceability.events.datatable');

    /* Route::post('traceability/events/selected/destroy', 'Traceability\EventsController@massDestroy')
        ->name('admin.traceability.events.selected.destroy');

    Route::get('traceability/events/sort', 'Traceability\EventsController@sortEdit')
        ->name('admin.traceability.events.sort'); */

    Route::post('traceability/events/sort', 'Traceability\EventsController@sortUpdate')
        ->name('admin.traceability.events.sort.update');

    Route::resource('traceability/events', 'Traceability\EventsController', [
        'as' => 'admin.traceability',
        'except' => ['show', 'create', 'edit']
    ]);

    //LOCATIONS
    Route::post('traceability/locations/datatable', 'Traceability\LocationsController@datatable')
        ->name('admin.traceability.locations.datatable');

    /* Route::post('traceability/locations/selected/destroy', 'Traceability\LocationsController@massDestroy')
        ->name('admin.traceability.locations.selected.destroy');

    Route::get('traceability/locations/sort', 'Traceability\LocationsController@sortEdit')
        ->name('admin.traceability.locations.sort'); */

    Route::post('traceability/locations/sort', 'Traceability\LocationsController@sortUpdate')
        ->name('admin.traceability.locations.sort.update');

    Route::resource('traceability/locations', 'Traceability\LocationsController', [
        'as' => 'admin.traceability',
        'except' => ['show', 'create', 'edit']
    ]);


    //TRACEABILITY
    Route::post('traceability/list/shipments', 'Traceability\TraceabilityController@listShipments')
        ->name('admin.traceability.list.shipments');

    Route::post('traceability/get/locations', 'Traceability\TraceabilityController@getAgencyLocations')
        ->name('admin.traceability.get.locations');

    Route::post('traceability/get/shipment', 'Traceability\TraceabilityController@getShipment')
        ->name('admin.traceability.get.shipment');

    Route::get('traceability/delivery-map', 'Traceability\TraceabilityController@deliveryMap')
        ->name('admin.traceability.get.manifest');

    Route::post('traceability/search-shipments', 'Traceability\TraceabilityController@searchShipments')
        ->name('admin.traceability.search');

    Route::post('traceability/shipments/datatable', 'Traceability\TraceabilityController@datatableShipments')
        ->name('admin.traceability.shipments.datatable');

    Route::post('traceability/selected/update-status', 'Traceability\TraceabilityController@updateStatus')
        ->name('admin.traceability.selected.update.status');

    Route::get('traceability/assign/ctt-correios', 'Traceability\TraceabilityController@modalVinculateProviderTrk')
        ->name('admin.traceability.assign.ctt-correios');

    Route::post('traceability/assign/ctt-correios', 'Traceability\TraceabilityController@storeVinculateProviderTrk')
        ->name('admin.traceability.assign.ctt-correios.store');

    Route::get('traceability/printer/shipments/devolutions-labels', 'Traceability\TraceabilityController@printLavelsDevolutions')
        ->name('admin.traceability.printer.shipments.devolutions-labels');

    Route::resource('traceability', 'Traceability\TraceabilityController', [
        'as' => 'admin',
        'only' => ['index', 'store']
    ]);

    /*=================================================================================================================
     * DELIVERY MANAGEMENT
     =================================================================================================================*/
    Route::post('trips/datatable', 'Trips\TripsController@datatable')
        ->name('admin.trips.datatable');

    Route::post('trips/selected/destroy', 'Trips\TripsController@massDestroy')
        ->name('admin.trips.selected.destroy');

    Route::get('trips/shipments/map/{tripId?}', 'Trips\TripsController@deliveryShipmentsMap')
        ->name('admin.trips.shipments.map.show');

    Route::get('trips/shipments/selected/add', 'Trips\TripsController@addSelectedShipments')
        ->name('admin.trips.shipments.add-selected');

    Route::post('trips/shipments/selected/add', 'Trips\TripsController@storeSelectedShipments')
        ->name('admin.trips.shipments.store-selected');

    Route::post('trips/check/operator', 'Trips\TripsController@checkOperator')
        ->name('admin.trips.check.operator');

    Route::post('trips/{id}/confirm-docs-reception', 'Trips\TripsController@confirmDocsReception')
        ->name('admin.trips.shipments.confirm-docs-reception');

    Route::post('trips/{id}/sort', 'Trips\TripsController@sortShipments')
        ->name('admin.trips.shipments.sort');

    Route::get('trips/{id}/optimize/edit', 'Trips\TripsController@optimizeRouteEdit')
        ->name('admin.trips.shipments.optimize.edit');

    Route::post('trips/{id}/optimize', 'Trips\TripsController@optimizeRouteStore')
        ->name('admin.trips.shipments.optimize.store');

    Route::get('trips/{id}/shipments/add/map', 'Trips\TripsController@addShipmentMap')
        ->name('admin.trips.shipments.add-map');

    Route::post('trips/{id}/shipments/add/single', 'Trips\TripsController@addSingleShipment')
        ->name('admin.trips.shipments.add-single');

    Route::post('trips/{id}/shipments/{shipmentId}/remove', 'Trips\TripsController@deleteShipment')
        ->name('admin.trips.shipments.remove');

    Route::get('trips/print/{id}/{docType}', 'Trips\TripsController@printDocument')
        ->name('admin.trips.print');

    Route::post('trips/{id}/shipments/datatable', 'Trips\TripsController@datatableShipments')
        ->name('admin.trips.shipments.datatable');

    Route::post('trips/{id}/shipments/datatable/notify', 'Trips\TripsController@notifyCustomers')
        ->name('admin.trips.shipments.notify');

    Route::get('trips/{id}/return', 'Trips\TripsController@createReturn')
        ->name('admin.trips.return.edit');

    Route::post('trips/{id}/direct-return', 'Trips\TripsController@createDirectReturn')
        ->name('admin.trips.return.auto');

    Route::get('trips/{id}/activity-declatation', 'Trips\TripsController@editActivityDeclaration')
        ->name('admin.trips.activity-declatation.edit');

    Route::get('trips/{id}/change/status', 'Trips\TripsController@editChangeStatus')
        ->name('admin.trips.change-status.edit');

    Route::post('trips/{id}/change-status', 'Trips\TripsController@storeChangeStatus')
        ->name('admin.trips.change-status.store');

    Route::get('trips/{id}/change/trailer', 'Trips\TripsController@editChangeTrailer')
        ->name('admin.trips.change-trailer.edit');

    Route::post('trips/{id}/change-trailer', 'Trips\TripsController@storeChangeTrailer')
        ->name('admin.trips.change-trailer.store');

    Route::resource('trips', 'Trips\TripsController', [
        'as' => 'admin'
    ]);

    Route::post('trips/calc-allowances/{id?}', 'Trips\TripsController@calculateAllowances')
        ->name('admin.trips.calculate.allowances');


    //ATTACHMENTS
    Route::post('trips/{id}/attachments/datatable', 'Trips\AttachmentsController@datatable')
        ->name('admin.trips.attachments.datatable');

    Route::post('trips/{id}/attachments/selected/destroy', 'Trips\AttachmentsController@massDestroy')
        ->name('admin.trips.attachments.selected.destroy');

    Route::get('trips/{id}/attachments/sort', 'Trips\AttachmentsController@sortEdit')
        ->name('admin.trips.attachments.sort');

    Route::post('trips/{id}/attachments/sort', 'Trips\AttachmentsController@sortUpdate')
        ->name('admin.trips.attachments.sort.update');

    Route::resource('trips.attachments', 'Trips\AttachmentsController', [
        'as' => 'admin',
        'except' => ['show', 'index']
    ]);

    //EXPENSES
    Route::get('trips/{tripId}/expenses/create', 'Trips\ExpensesController@create')
        ->name('admin.trips.expenses.create');

    Route::get('trips/{tripId}/expenses/{id}/edit', 'Trips\ExpensesController@edit')
        ->name('admin.trips.expenses.edit');

    Route::post('trips/{tripId}/expenses/store', 'Trips\ExpensesController@store')
        ->name('admin.trips.expenses.store');

    Route::put('trips/{tripId}/expenses/{id}/update', 'Trips\ExpensesController@update')
        ->name('admin.trips.expenses.update');

    Route::delete('trips/{tripId}/expenses/{id}/destroy', 'Trips\ExpensesController@destroy')
        ->name('admin.trips.expenses.destroy');


    /*=================================================================================================================
     * PICKING MANAGEMENT
     =================================================================================================================*/
    Route::post('picking/management/get/shipment', 'PickingManagementController@getShipment')
        ->name('admin.picking.management.get.shipment');

    Route::resource('picking/management', 'PickingManagementController', [
        'as' => 'admin.picking',
        'only' => ['index', 'store']
    ]);

    /*=================================================================================================================
     * ALLOWANCES
     =================================================================================================================*/
    Route::post('allowances/datatable', 'AllowancesController@datatable')
        ->name('admin.allowances.datatable');

    Route::resource('allowances', 'AllowancesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * REFUNDS
     =================================================================================================================*/
    //AGENCIES
    /* Route::post('refunds/agencies/datatable', 'Refunds\AgenciesController@datatable')
        ->name('admin.refunds.agencies.datatable');

    Route::post('refunds/agencies/selected/confirm', 'Refunds\AgenciesController@massConfirm')
        ->name('admin.refunds.agencies.selected.confirm');

    Route::post('refunds/agencies/selected/update', 'Refunds\AgenciesController@massUpdate')
        ->name('admin.refunds.agencies.selected.update');

    Route::post('refunds/agencies/selected/destroy', 'Refunds\AgenciesController@massDestroy')
        ->name('admin.refunds.agencies.selected.destroy');

    Route::resource('refunds/agencies', 'Refunds\AgenciesController', [
        'as'    => 'admin.refunds',
        'only'  => ['index', 'edit', 'update', 'destroy']
    ]); */

    //CUSTOMER
    Route::post('refunds/requests/datatable', 'Refunds\CustomersRequestsController@datatable')
        ->name('admin.refunds.requests.datatable');

    Route::resource('refunds/requests', 'Refunds\CustomersRequestsController', [
        'as'    => 'admin.refunds',
        'only'  => ['show', 'edit', 'update', 'destroy']
    ]);


    Route::post('refunds/import', 'Refunds\CustomersController@import')
        ->name('admin.refunds.customers.import');

    Route::post('refunds/import/confirm', 'Refunds\CustomersController@confirmImport')
        ->name('admin.refunds.customers.import.confirm');

    Route::post('refunds/datatable', 'Refunds\CustomersController@datatable')
        ->name('admin.refunds.customers.datatable');

    Route::post('refunds/selected/update', 'Refunds\CustomersController@massUpdate')
        ->name('admin.refunds.customers.selected.update');

    Route::post('refunds/selected/destroy', 'Refunds\CustomersController@massDestroy')
        ->name('admin.refunds.customers.selected.destroy');

    Route::resource('refunds/customers', 'Refunds\CustomersController', [
        'as'    => 'admin.refunds',
        'only'  => ['index', 'edit', 'show', 'update', 'destroy']
    ]);



    //OPERATOR CONTROL
    Route::post('refunds/operator/datatable', 'Refunds\OperatorsControlController@datatable')
        ->name('admin.operator-refunds.datatable');

    Route::post('operator-refunds/operator/{id}/datatable', 'Refunds\OperatorsControlController@datatableShipments')
        ->name('admin.operator-refunds.operator.datatable');

    Route::resource('operator-refunds', 'Refunds\OperatorsControlController', [
        'as'    => 'admin',
        'only'  => ['index', 'edit', 'update']
    ]);

    //CASH ON DELIVERY
    Route::post('refunds/cod/datatable', 'Refunds\CodController@datatable')
        ->name('admin.refunds.cod.datatable');

    Route::post('refunds/cod/selected/update', 'Refunds\CodController@massUpdate')
        ->name('admin.refunds.cod.selected.update');

    Route::post('refunds/cod/selected/assign-customers', 'Refunds\CodController@massAssignCustomers')
        ->name('admin.refunds.cod.selected.assign-customers');

    Route::resource('refunds/cod', 'Refunds\CodController', [
        'as'    => 'admin.refunds',
        'only'  => ['index', 'edit', 'update']
    ]);

    /*=================================================================================================================
     * DEVOLUTIONS
     =================================================================================================================*/
    Route::post('devolutions/datatable', 'Refunds\DevolutionsController@datatable')
        ->name('admin.devolutions.datatable');

    Route::post('devolutions/selected/destroy', 'Refunds\DevolutionsController@massDestroy')
        ->name('admin.devolutions.selected.destroy');

    Route::post('devolutions/selected/confirm', 'Refunds\DevolutionsController@massConfirmShipments')
        ->name('admin.devolutions.selected.confirm');

    Route::resource('devolutions', 'Refunds\DevolutionsController', [
        'as'    => 'admin',
        'only'  => ['index']
    ]);

    /*=================================================================================================================
     * ZIP CODES
     =================================================================================================================*/
    //ALL CORE ZIP CODES
    Route::post('zip-codes/datatable', 'ZipCodes\ZipCodesController@datatable')
        ->name('admin.zip-codes.datatable');

    Route::post('zip-codes/selected/destroy', 'ZipCodes\ZipCodesController@massDestroy')
        ->name('admin.zip-codes.selected.destroy');

    Route::post('zip-codes/search', 'ZipCodes\AgencyZipCodesController@search')
        ->name('admin.zip-codes.search');

    Route::post('zip-codes/filters/country', 'ZipCodes\AgencyZipCodesController@getFilters')
        ->name('admin.zip-codes.filters.country');

    Route::resource('zip-codes', 'ZipCodes\ZipCodesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //MAIN ZIP CODES
    Route::post('zip-codes/agencies/datatable', 'ZipCodes\AgencyZipCodesController@datatable')
        ->name('admin.zip-codes.agencies.datatable');

    Route::post('zip-codes/agencies/selected/destroy', 'ZipCodes\AgencyZipCodesController@massDestroy')
        ->name('admin.zip-codes.agencies.selected.destroy');

    Route::post('zip-codes/agencies/import', 'ZipCodes\AgencyZipCodesController@importZipCodes')
        ->name('admin.zip-codes.agencies.import');

    Route::post('zip-codes/agencies/import/agency', 'ZipCodes\AgencyZipCodesController@importZipCodesFromAgency')
        ->name('admin.zip-codes.agencies.import.agency');

    Route::post('zip-codes/agencies/selected/update', 'ZipCodes\AgencyZipCodesController@massUpdate')
        ->name('admin.zip-codes.agencies.selected.update');

    Route::resource('zip-codes/agencies', 'ZipCodes\AgencyZipCodesController', [
        'as' => 'admin.zip-codes',
        'except' => ['show']
    ]);

    //ZIP CODES ZONES
    Route::post('zip-codes/zones/datatable', 'ZipCodes\ZonesController@datatable')
        ->name('admin.zip-codes.zones.datatable');

    Route::post('zip-codes/zones/selected/destroy', 'ZipCodes\ZonesController@massDestroy')
        ->name('admin.zip-codes.zones.selected.destroy');

    Route::get('zip-codes/zones/sort', 'ZipCodes\ZonesController@sortEdit')
        ->name('admin.zip-codes.zones.sort');

    Route::post('zip-codes/zones/sort', 'ZipCodes\ZonesController@sortUpdate')
        ->name('admin.zip-codes.zones.sort.update');

    Route::resource('zip-codes/zones', 'ZipCodes\ZonesController', [
        'as' => 'admin.zip-codes',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * CALENDAR EVENTS
     =================================================================================================================*/
    Route::post('calendar/events/datatable', 'CalendarEventsController@datatable')
        ->name('admin.calendar.events.datatable');

    Route::post('shipping-expenses/selected/destroy', 'CalendarEventsController@massDestroy')
        ->name('admin.expenses.selected.destroy');

    Route::get('calendar/events/load', 'CalendarEventsController@loadEvents')
        ->name('admin.calendar.events.load');

    Route::post('calendar/events/{id}/conclude', 'CalendarEventsController@conclude')
        ->name('admin.calendar.events.conclude');

    Route::resource('calendar/events', 'CalendarEventsController', [
        'as' => 'admin.calendar',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * NOTIFICATIONS
     =================================================================================================================*/
    Route::post('notifications/datatable', 'NotificationsController@datatable')
        ->name('admin.notifications.datatable');

    Route::post('notifications/load', 'NotificationsController@load')
        ->name('admin.notifications.load');

    Route::post('notifications/all-read', 'NotificationsController@readAll')
        ->name('admin.notifications.all-read');

    Route::post('notifications/{id}/read', 'NotificationsController@read')
        ->name('admin.notifications.read');

    Route::post('notifications/selected/destroy', 'NotificationsController@massDestroy')
        ->name('admin.notifications.selected.destroy');

    Route::resource('notifications', 'NotificationsController', [
        'as' => 'admin',
        'only' => ['index', 'show', 'destroy']
    ]);



    /*=================================================================================================================
     * FILE IMPORTER
     =================================================================================================================*/
    //IMPORT FILES
    Route::get('file-importer', 'FilesImporter\ImporterController@index')
        ->name('admin.importer.index');

    Route::post('file-importer/import', 'FilesImporter\ImporterController@executeImportation')
        ->name('admin.importer.import');

    //MODELS
    Route::post('importer/models/datatable', 'FilesImporter\ModelsController@datatable')
        ->name('admin.importer.models.datatable');

    Route::post('importer/models/selected/destroy', 'FilesImporter\ModelsController@massDestroy')
        ->name('admin.importer.models.selected.destroy');

    Route::resource('importer/models', 'FilesImporter\ModelsController', [
        'as' => 'admin.importer',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * MAP & LOCATION
     =================================================================================================================*/
    Route::post('maps/sync/{type}/location', 'MapsController@syncLocation')
        ->name('admin.maps.sync.location');

    Route::post('maps/load/operator/deliveries', 'MapsController@getOperatorDeliveries')
        ->name('admin.maps.load.operator.deliveries');

    Route::post('maps/load/operator/history', 'MapsController@getOperatorHistory')
        ->name('admin.maps.load.operator.history');

    Route::post('maps/search-shipments', 'MapsController@searchShipments')
        ->name('admin.maps.search');

    Route::post('maps/shipments/datatable', 'MapsController@datatableShipments')
        ->name('admin.maps.shipments.datatable');

    Route::get('maps/operators', 'MapsController@operatorsLocation')
        ->name('admin.maps.operators');

    Route::resource('maps', 'MapsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * ADICIONAL SERVICES & SHIPPING EXPENSES
     =================================================================================================================*/
    Route::post('expenses/datatable', 'ExpensesController@datatable')
        ->name('admin.expenses.datatable');

    Route::post('expenses/selected/destroy', 'ExpensesController@massDestroy')
        ->name('admin.expenses.selected.destroy');

    Route::get('expenses/sort', 'ExpensesController@sortEdit')
        ->name('admin.expenses.sort');

    Route::post('expenses/sort', 'ExpensesController@sortUpdate')
        ->name('admin.expenses.sort.update');

    Route::resource('expenses', 'ExpensesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    Route::get('expenses/duplicate/{shippingExpense}', 'ExpensesController@duplicate')
        ->name('admin.expenses.duplicate');



    /*=================================================================================================================
     * GATEWAY PAYMENTS
     =================================================================================================================*/
    Route::post('gateway/datatable', 'GatewayPaymentsController@datatable')
        ->name('admin.gateway.payments.datatable');

    Route::post('gateway/search/customer', 'GatewayPaymentsController@searchCustomer')
        ->name('admin.gateway.payments.search.customer');

    Route::post('gateway/selected/destroy', 'GatewayPaymentsController@massDestroy')
        ->name('admin.gateway.payments.selected.destroy');

    Route::get('gateway/wallet/edit', 'GatewayPaymentsController@editWallet')
        ->name('admin.gateway.payments.wallet.edit');

    Route::post('gateway/wallet/store', 'GatewayPaymentsController@updateWallet')
        ->name('admin.gateway.payments.wallet.update');

    Route::resource('gateway/payments', 'GatewayPaymentsController', [
        'as' => 'admin.gateway',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * BANKS
     =================================================================================================================*/
    Route::post('banks/datatable', 'Banks\BanksController@datatable')
        ->name('admin.banks.datatable');

    Route::post('banks/selected/destroy', 'Banks\BanksController@massDestroy')
        ->name('admin.banks.selected.destroy');

    Route::get('banks/sort', 'Banks\BanksController@sortEdit')
        ->name('admin.banks.sort');

    Route::post('banks/sort', 'Banks\BanksController@sortUpdate')
        ->name('admin.banks.sort.update');

    Route::resource('banks', 'Banks\BanksController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //WORLD INSTITUTIONS
    Route::post('banks-institutions/datatable', 'Banks\BanksInstitutionsController@datatable')
        ->name('admin.banks-institutions.datatable');

    Route::post('banks-institutions/selected/destroy', 'Banks\BanksInstitutionsController@massDestroy')
        ->name('admin.banks-institutions.selected.destroy');

    Route::resource('banks-institutions', 'Banks\BanksInstitutionsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * PAYMENT CONDITIONS
     =================================================================================================================*/
    Route::post('payment-conditions/datatable', 'Banks\PaymentConditionsController@datatable')
        ->name('admin.payment-conditions.datatable');

    Route::post('payment-conditions/selected/destroy', 'Banks\PaymentConditionsController@massDestroy')
        ->name('admin.payment-conditions.selected.destroy');

    Route::get('payment-conditions/sort', 'Banks\PaymentConditionsController@sortEdit')
        ->name('admin.payment-conditions.sort');

    Route::post('payment-conditions/sort', 'Banks\PaymentConditionsController@sortUpdate')
        ->name('admin.payment-conditions.sort.update');

    Route::resource('payment-conditions', 'Banks\PaymentConditionsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * PAYMENT METHODS
     =================================================================================================================*/
    Route::post('payment-methods/datatable', 'Banks\PaymentMethodsController@datatable')
        ->name('admin.payment-methods.datatable');

    Route::post('payment-methods/selected/destroy', 'Banks\PaymentMethodsController@massDestroy')
        ->name('admin.payment-methods.selected.destroy');

    Route::get('payment-methods/sort', 'Banks\PaymentMethodsController@sortEdit')
        ->name('admin.payment-methods.sort');

    Route::post('payment-methods/sort', 'Banks\PaymentMethodsController@sortUpdate')
        ->name('admin.payment-methods.sort.update');

    Route::resource('payment-methods', 'Banks\PaymentMethodsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * ROUTES GROUPS
     =================================================================================================================*/

    Route::post('routes/groups/datatable', 'Routes\GroupsController@datatable')
        ->name('admin.routes.groups.datatable');

    Route::resource('routes/groups', 'Routes\GroupsController', [
        'as' => 'admin.routes',
        'except' => 'show'
    ]);

    /*=================================================================================================================
     * DELIVERY ROUTES
     =================================================================================================================*/
    Route::post('routes/datatable', 'RoutesController@datatable')
        ->name('admin.routes.datatable');

    Route::post('routes/selected/destroy', 'RoutesController@massDestroy')
        ->name('admin.routes.selected.destroy');

    Route::get('routes/sort', 'RoutesController@sortEdit')
        ->name('admin.routes.sort');

    Route::post('routes/sort', 'RoutesController@sortUpdate')
        ->name('admin.routes.sort.update');

    Route::resource('routes', 'RoutesController', [
        'as' => 'admin'
    ]);

    /*=================================================================================================================
     * VEHICLES
     =================================================================================================================*/
    Route::post('vehicles/datatable', 'VehiclesController@datatable')
        ->name('admin.vehicles.datatable');

    Route::post('vehicles/selected/destroy', 'VehiclesController@massDestroy')
        ->name('admin.vehicles.selected.destroy');

    Route::get('vehicles/sort', 'VehiclesController@sortEdit')
        ->name('admin.vehicles.sort');

    Route::post('vehicles/sort', 'VehiclesController@sortUpdate')
        ->name('admin.vehicles.sort.update');

    Route::resource('vehicles', 'VehiclesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * WEBSERVICES
     =================================================================================================================*/
    //GLOBAL CONFIG
    Route::post('webservices/datatable', 'Webservices\WebservicesController@datatable')
        ->name('admin.webservices.datatable');

    Route::post('webservices/selected/destroy', 'Webservices\WebservicesController@massDestroy')
        ->name('admin.webservices.selected.destroy');

    Route::get('webservices/mass-config', 'Webservices\WebservicesController@editWebservices')
        ->name('admin.webservices.mass-config');

    Route::post('webservices/mass-config', 'Webservices\WebservicesController@storeWebservices')
        ->name('admin.webservices.mass-config.store');

    Route::resource('webservices', 'Webservices\WebservicesController', [
        'as'    => 'admin',
        'except'  => ['show']
    ]);

    //METHODS
    Route::post('webservice-methods/datatable', 'Webservices\MethodsController@datatable')
        ->name('admin.webservice-methods.datatable');

    Route::post('webservice-methods/selected/destroy', 'Webservices\MethodsController@massDestroy')
        ->name('admin.webservice-methods.selected.destroy');

    Route::get('webservice-methods/sort', 'Webservices\MethodsController@sortEdit')
        ->name('admin.webservice-methods.sort');

    Route::post('webservice-methods/sort', 'Webservices\MethodsController@sortUpdate')
        ->name('admin.webservice-methods.sort.update');

    Route::resource('webservice-methods', 'Webservices\MethodsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //SYNC WEBSERVICE
    Route::get('webservices/sync/shipments', 'Webservices\WebservicesController@editSyncShipment')
        ->name('admin.webservices.sync.shipments');

    Route::post('webservices/sync/shipments', 'Webservices\WebservicesController@syncShipments')
        ->name('admin.webservices.sync.shipments');

    Route::post('webservices/sync/history', 'Webservices\WebservicesController@syncHistory')
        ->name('admin.webservices.sync.history');

    Route::post('webservices/sync/pickup-points', 'Webservices\WebservicesController@syncPudos')
        ->name('admin.webservices.sync.pickup-points');

    /*=================================================================================================================
     * MANAGE OPERATOR TASKS
     =================================================================================================================*/
    Route::post('operator/tasks/datatable', 'OperatorsTasksController@datatable')
        ->name('admin.operator.tasks.datatable');

    Route::post('operator/tasks/tabs', 'OperatorsTasksController@filterTabs')
        ->name('admin.operator.tasks.tabs');

    Route::get('operator/tasks/{taskId}/change-operator', 'OperatorsTasksController@changeOperator')
        ->name('admin.operator.tasks.change-operator');

    Route::post('operator/tasks/{taskId}/change-operator', 'OperatorsTasksController@changeOperatorStore')
        ->name('admin.operator.tasks.change-operator.store');

    Route::resource('operator/tasks', 'OperatorsTasksController', [
        'as' => 'admin.operator'
    ]);


    /*=================================================================================================================
     * SEPA TRANSFERS
     =================================================================================================================*/
    //PAYMENTS
    Route::post('sepa-transfers/datatable', 'SepaTransfers\PaymentsController@datatable')
        ->name('admin.sepa-transfers.datatable');

    Route::post('sepa-transfers/selected/destroy', 'SepaTransfers\PaymentsController@massDestroy')
        ->name('admin.sepa-transfers.selected.destroy');

    Route::get('sepa-transfers/import/invoices/edit', 'SepaTransfers\PaymentsController@editImportInvoices')
        ->name('admin.sepa-transfers.import.invoices.edit');

    Route::post('sepa-transfers/import/invoices/store', 'SepaTransfers\PaymentsController@storeImportInvoices')
        ->name('admin.sepa-transfers.import.invoices.store');

    Route::get('sepa-transfers/{id}/xml', 'SepaTransfers\PaymentsController@createXML')
        ->name('admin.sepa-transfers.xml');

    Route::get('sepa-transfers/{id}/return/edit', 'SepaTransfers\PaymentsController@editReturnFile')
        ->name('admin.sepa-transfers.return.edit');

    Route::post('sepa-transfers/{id}/return/store', 'SepaTransfers\PaymentsController@storeReturnFile')
        ->name('admin.sepa-transfers.return.store');

    Route::post('sepa-transfers/{id}/notify/errors', 'SepaTransfers\PaymentsController@notifyTransactionsErrors')
        ->name('admin.sepa-transfers.notify.errors');

    Route::post('sepa-transfers/select/search/{type}', 'SepaTransfers\PaymentsController@searchSelectBox')
        ->name('admin.sepa-transfers.select.search');

    Route::get('sepa-transfers/{id}/invoices/edit', 'SepaTransfers\PaymentsController@editInvoices')
        ->name('admin.sepa-transfers.invoices.edit');

    Route::post('sepa-transfers/{id}/invoices/store', 'SepaTransfers\PaymentsController@storeInvoices')
        ->name('admin.sepa-transfers.invoices.store');

    Route::resource('sepa-transfers', 'SepaTransfers\PaymentsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //PAYMENTS GROUPS
    Route::resource('sepa-transfers.groups', 'SepaTransfers\GroupsController', [
        'as' => 'admin'
    ]);


    //PAYMENTS TRANSFERS
    Route::resource('sepa-transfers.transactions', 'SepaTransfers\TransactionsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * PRINT DOCUMENTS
     =================================================================================================================*/

    //CUSTOMERS
    Route::get('printer/customers/{id}/prices-table', 'Printer\CustomersController@pricesTable')
        ->name('admin.printer.customers.prices-table');

    Route::get('printer/customers/{id}/sepa', 'Printer\CustomersController@sepaAuthorization')
        ->name('admin.printer.customers.sepa');

    //USERS
    Route::get('printer/users/validities', 'Printer\UsersController@validities')
        ->name('admin.printer.users.validities');

    Route::get('printer/users/simcommunications', 'Printer\UsersController@SIMCommunications')
        ->name('admin.printer.users.simcommunications');

    Route::get('printer/users/uniform', 'Printer\UsersController@uniform')
        ->name('admin.printer.users.uniform');

    Route::get('printer/users/activity', 'Printer\UsersController@activity')
        ->name('admin.printer.users.activity');


    //SHIPMENTS
    Route::get('printer/shipments/selected/{groupBy?}', 'Printer\ShipmentsController@summary')
        ->name('admin.printer.shipments.selected');

    Route::get('printer/shipments/cargo-manigest/{grouped?}', 'Printer\ShipmentsController@cargoManifest')
        ->name('admin.printer.shipments.cargo-manifest');

    Route::get('printer/shipments/cold-manigest/{grouped?}', 'Printer\ShipmentsController@coldManifest')
        ->name('admin.printer.shipments.cold-manifest');

    Route::get('printer/shipments/goods-manigest', 'Printer\ShipmentsController@goodsManifest')
        ->name('admin.printer.shipments.goods-manifest');

    Route::get('printer/shipments/delivery-map', 'Printer\ShipmentsController@deliveryMap')
        ->name('admin.printer.shipments.delivery-map');

    Route::get('printer/shipments/generic-transport-guide', 'Printer\ShipmentsController@globalTransportGuide')
        ->name('admin.printer.shipments.generic-transport-guide');

    Route::get('printer/shipments/labels/{shipmentId?}', 'Printer\ShipmentsController@labels')
        ->name('admin.printer.shipments.labels');

    Route::get('printer/shipments/transport-guide/{shipmentId?}', 'Printer\ShipmentsController@transportGuide')
        ->name('admin.printer.shipments.transport-guide');

    Route::get('printer/shipments/{shipmentId}/cmr', 'Printer\ShipmentsController@cmr')
        ->name('admin.printer.shipments.cmr');

    Route::get('printer/shipments/itenerary/{shipmentId?}', 'Printer\ShipmentsController@itenerary')
        ->name('admin.printer.shipments.itenerary');

    Route::get('printer/shipments/contract/{shipmentId?}', 'Printer\ShipmentsController@contract')
        ->name('admin.printer.shipments.contract');

    Route::get('printer/shipments/{shipmentId}/value-statement', 'Printer\ShipmentsController@valueStatement')
        ->name('admin.printer.shipments.value-statement');

    Route::get('printer/shipments/{shipmentId}/shipping-instructions', 'Printer\ShipmentsController@shippingInstructions')
        ->name('admin.printer.shipments.shipping-instructions');

    Route::get('printer/shipments/{shipmentId}/reimbursement-guide', 'Printer\ShipmentsController@reimbursementGuide')
        ->name('admin.printer.shipments.reimbursement-guide');

    Route::get('printer/shipments/{shipmentId}/property-declaration', 'Printer\ShipmentsController@propertyDeclaration')
        ->name('admin.printer.shipments.property-declaration');

    Route::get('printer/shipments/{shipmentId}/proof', 'Printer\ShipmentsController@shipmentProof')
        ->name('admin.printer.shipments.proof');

    //PICKUPS
    Route::get('printer/pickups/selected/manifest', 'Printer\ShipmentsController@pickupManifest')
        ->name('admin.printer.pickups.selected.manifest');

    Route::get('printer/pickups/{pickupId}/pickup-manifest', 'Printer\ShipmentsController@pickupManifest')
        ->name('admin.printer.pickups.pickup-manifest');

    //TRIPS
    Route::get('printer/trips/activity-declaration', 'Printer\TripsController@activityDeclaration')
        ->name('admin.printer.trips.activity-declaration');

    //REFUNDS
    Route::get('printer/refunds/customers/proof/{shipmentId?}', 'Printer\RefundsController@customersProof')
        ->name('admin.printer.refunds.customers.proof');

    Route::get('printer/refunds/customers/selected/summary', 'Printer\RefundsController@customersSummary')
        ->name('admin.printer.refunds.customers.summary');

    Route::get('printer/refunds/agencies/summary', 'Printer\RefundsController@agenciesSummary')
        ->name('admin.printer.refunds.agencies.summary');

    Route::get('printer/refunds/cod/summary', 'Printer\RefundsController@codSummary')
        ->name('admin.printer.refunds.cod.summary');

    Route::get('printer/refunds/devolutions/summary', 'Printer\RefundsController@devolutionsSummary')
        ->name('admin.printer.refunds.devolutions.summary');

    //CUSTOMER BILLING
    Route::get('printer/billing/customers/{id}/shipments/summary', 'Printer\BillingCustomersController@printCustomerSummary')
        ->name('admin.printer.billing.customers.shipments.summary');

    Route::get('printer/billing/customers/shipments/summary/all', 'Printer\BillingCustomersController@massPrintCustomerSummary')
        ->name('admin.printer.billing.customers.shipments.summary.all');

    Route::get('printer/billing/customers/shipments/month-values', 'Printer\BillingCustomersController@printCustomersMonthValues')
        ->name('admin.printer.billing.customers.shipments.month-values');

    //AGÊNCIES
    /*Route::get('printer/billing/agencies/{senderAgency}/{recipientAgency}/summary', 'Printer\BillingAgenciesController@summary')
        ->name('admin.printer.billing.agencies.summary');*/

    //PROVIDERS
    Route::get('printer/billing/providers/{providerId}/summary', 'Printer\BillingProvidersController@printProviderSummary')
        ->name('admin.printer.billing.providers.summary');

    //CASHIER
    Route::get('printer/cashier/movements/{grouped?}', 'Printer\CashierController@printMovements')
        ->name('admin.printer.cashier.movements');

    //INVOICES
    Route::get('printer/invoices/summary', 'Printer\InvoicesController@summary')
        ->name('admin.printer.invoices.summary');

    Route::get('printer/invoices/customers/balance', 'Printer\InvoicesController@customersBalance')
        ->name('admin.printer.invoices.customers.balance');

    Route::get('printer/invoices/customers/maps/{mapType}', 'Printer\InvoicesController@printBalanceMap')
        ->name('admin.printer.invoices.customers.maps');

    Route::get('printer/invoices/operator-accountability', 'Printer\InvoicesController@operatorAccountability')
        ->name('admin.printer.invoices.operator-accountability');

    //CURRENT ACCOUNT
    Route::get('printer/invoices/balance/{customerId?}', 'Printer\BalanceController@balanceCustomer')
        ->name('admin.printer.invoices.balance');


    //PURCHASE INVOICES
    Route::get('printer/invoices/purchase/{providerId}/balance', 'Printer\BalanceController@balanceProvider')
        ->name('admin.printer.invoices.purchase.balance');

    Route::get('printer/invoices/purchase/listing/{grouped?}', 'Printer\PurchaseInvoicesController@listing')
        ->name('admin.printer.invoices.purchase.listing');

    Route::get('printer/invoices/purchase/map/{mapType}', 'Printer\PurchaseInvoicesController@printMap')
        ->name('admin.printer.invoices.purchase.map');

    //PURCHASE INVOICES PAYMENT NOTE

    Route::get('printer/invoices/purchase/payment-notes/listing/{grouped?}', 'Printer\PurchaseInvoicesController@listingPaymentNotes')
        ->name('admin.printer.invoices.purchase.payment.note.listing');


    //SEPA TRANSFERS
    Route::get('printer/sepa-transfers/{payment_id}/summary', 'SepaTransfers\PaymentsController@printSummary')
        ->name('admin.printer.sepa-transfers.payment.summary');

    //BILLING ITEMS
    Route::get('printer/billing/items/list', 'Printer\BillingItemsController@list')
        ->name('admin.printer.billing.items.list');

    /*=================================================================================================================
     * EXPORT EXCEL
     =================================================================================================================*/
    //CUSTOMERS
    Route::get('export/customers', 'Exports\CustomersController@export')
        ->name('admin.export.customers');

    Route::get('export/customers/{customerId}/recipients', 'Exports\CustomersController@recipients')
        ->name('admin.export.customers.recipients');

    //PROVIDERS
    Route::get('export/providers', 'Exports\ProvidersController@export')
        ->name('admin.export.providers');

    //OPERATORS
    Route::get('export/operators', 'Exports\OperatorsController@export')
        ->name('admin.export.operators');

    Route::get('export/operators/absences', 'Exports\OperatorsController@absences')
        ->name('admin.export.operators.absences');

    Route::get('export/operators/holidays-balance', 'Exports\OperatorsController@holidaysBalance')
        ->name('admin.export.operators.holidays-balance');

    //SHIPMENTS
    Route::get('export/shipments', 'Exports\ShipmentsController@export')
        ->name('admin.export.shipments');

    Route::get('export/shipments/alternative', 'Exports\ShipmentsController@exportAlternative')
        ->name('admin.export.shipments.alternative');

    Route::get('export/shipments/dimensions', 'Exports\ShipmentsController@exportDimensions')
        ->name('admin.export.shipments.dimensions');

    //INCIDENCES
    Route::get('export/incidences', 'Exports\IncidencesController@export')
        ->name('admin.export.incidences');

    //BILLING
    Route::get('export/billing/customers/{id}/shipments', 'Exports\BillingController@customerShipments')
        ->name('admin.export.billing.customers.shipments');

    Route::get('export/billing/customers/{id}/shipments/mass', 'Exports\BillingController@customerMassShipments')
        ->name('admin.export.billing.customers.shipments.mass');

    Route::get('export/billing/providers/{id?}/shipments', 'Exports\BillingController@providerShipments')
        ->name('admin.export.billing.providers.shipments');

    Route::get('export/billing/operators/{id}/shipments', 'Exports\BillingController@operatorShipments')
        ->name('admin.export.billing.operators.shipments');

    Route::get('export/billing', 'Exports\BillingController@periodSummary')
        ->name('admin.export.billing');

    Route::get('export/billing/software/{invoiceSoftware}', 'Exports\BillingController@exportFileToSoftware')
        ->name('admin.export.billing.customers.software');

    //REFUNDS
    Route::get('export/refunds/agencies', 'Exports\RefundsController@agenciesExport')
        ->name('admin.export.refunds.agencies');

    Route::get('export/refunds/customer', 'Exports\RefundsController@customersExport')
        ->name('admin.export.refunds.customers');

    Route::get('export/refunds/cod', 'Exports\RefundsController@codExport')
        ->name('admin.export.refunds.cod');

    //INVOICES
    Route::get('export/invoices', 'Exports\InvoicesController@export')
        ->name('admin.export.invoices');

    //PURCHASE INVOICES
    Route::get('export/invoices/purchase', 'Exports\PurchaseInvoicesController@export')
        ->name('admin.export.invoices.purchase');

    Route::get('export/invoices/purchase/anual/grouped/type', 'Exports\PurchaseInvoicesController@exportGroupedByType')
        ->name('admin.export.invoices.purchase.anual.grouped.type');

    //PURCHASE INVOICES PAYMENT NOTE
    Route::get('export/invoices/purchase-payment-notes', 'Exports\PurchaseInvoicesController@exportPaymentNotes')
        ->name('admin.export.invoices.purchase.payment.note');

    //DELIVERY MANIFESTS
    Route::get('export/trips', 'Exports\TripsController@export')
        ->name('admin.export.trips');

    /*=================================================================================================================
     * GLOBAL STATISTICS
     =================================================================================================================*/
    Route::get('statistics/details', 'StatisticsController@details')
        ->name('admin.statistics.details');

    Route::get('statistics/incidences/details', 'StatisticsController@incidencesDetails')
        ->name('admin.statistics.incidences.details');

    Route::resource('statistics', 'StatisticsController', [
        'as' => 'admin',
        'only' => ['index']
    ]);


    /*=================================================================================================================
     * SUPPORT ALERTS
     =================================================================================================================*/
    Route::post('notices/datatable', 'NoticesController@datatable')
        ->name('admin.notices.datatable');

    Route::post('notices/selected/destroy', 'NoticesController@massDestroy')
        ->name('admin.notices.selected.destroy');

    Route::post('notices/read/{id?}', 'AccountController@setNoticeReaded')
        ->name('admin.notices.read');

    Route::get('notices/show/{id}', 'AccountController@showNotice')
        ->name('admin.notices.show');

    Route::get('notices/{id}/views', 'NoticesController@views')
        ->name('admin.notices.views');

    Route::resource('notices', 'NoticesController', [
        'as' => 'admin',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * MANAGE LOGS
     =================================================================================================================*/
    Route::get('logs/errors/{filename}/download', 'LogViewerController@download', ['as' => 'admin'])
        ->name('admin.logs.errors.download');

    Route::post('logs/errors/destroy/all', 'LogViewerController@destroyAll', ['as' => 'admin'])
        ->name('admin.logs.errors.destroy.all');

    Route::resource('logs/errors', 'LogViewerController', [
        'as'    => 'admin.logs',
        'only'  => ['index', 'destroy']
    ]);


    /*=================================================================================================================
    * CHANGE LOG
    =================================================================================================================*/
    Route::get('change-log/{source}/{sourceId}', 'ChangesLogController@show')
        ->name('admin.change-log.show');

    Route::get('login-log/{target}/{sourceId}', 'ChangesLogController@showLogin')
        ->name('admin.login-log.show');

    /*=================================================================================================================
     * API
     =================================================================================================================*/
    Route::post('api/datatable', 'Api\OauthClientsController@datatable')
        ->name('admin.api.datatable');

    Route::post('api/selected/destroy', 'Api\OauthClientsController@massDestroy')
        ->name('admin.api.selected.destroy');

    Route::post('api/search/customer', 'Api\OauthClientsController@searchCustomer')
        ->name('admin.api.search.customer');

    Route::resource('api', 'Api\OauthClientsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * EMAILS
     =================================================================================================================*/
    Route::post('emails/datatable', 'Emails\EmailsController@datatable')
        ->name('admin.emails.datatable');

    Route::post('emails/selected/destroy', 'Emails\EmailsController@massDestroy')
        ->name('admin.emails.selected.destroy');

    Route::resource('emails', 'Emails\EmailsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //MAILING LISTS
    Route::post('emails/lists/datatable', 'Emails\ListsController@datatable')
        ->name('admin.emails.lists.datatable');

    Route::post('emails/lists/selected/destroy', 'Emails\ListsController@massDestroy')
        ->name('admin.emails.lists.selected.destroy');

    Route::get('emails/lists/sort', 'Emails\ListsController@sortEdit')
        ->name('admin.emails.lists.sort');

    Route::post('emails/lists/sort', 'Emails\ListsController@sortUpdate')
        ->name('admin.emails.lists.sort.update');

    Route::resource('emails/lists', 'Emails\ListsController', [
        'as' => 'admin.emails'
    ]);

    /*=================================================================================================================
     * SMS
     =================================================================================================================*/
    Route::post('sms/datatable', 'Sms\SmsController@datatable')
        ->name('admin.sms.datatable');

    Route::post('sms/selected/destroy', 'Sms\SmsController@massDestroy')
        ->name('admin.sms.selected.destroy');

    Route::resource('sms', 'Sms\SmsController', [
        'as' => 'admin',
        'except' => ['show']
    ]);

    //PACKS
    Route::post('sms/packs/datatable', 'Sms\PacksController@datatable')
        ->name('admin.sms.packs.datatable');

    Route::post('sms/packs/selected/destroy', 'Sms\PacksController@massDestroy')
        ->name('admin.sms.packs.selected.destroy');

    Route::resource('sms/packs', 'Sms\PacksController', [
        'as' => 'admin.sms',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * FILES REPOSITORY
     =================================================================================================================*/
    Route::post('repository/datatable', 'FileRepositoryController@datatable')
        ->name('admin.repository.datatable');

    Route::post('repository/selected/destroy', 'FileRepositoryController@massDestroy')
        ->name('admin.repository.selected.destroy');

    Route::get('repository/sort', 'FileRepositoryController@sortEdit')
        ->name('admin.repository.sort');

    Route::post('repository/sort', 'FileRepositoryController@sortUpdate')
        ->name('admin.repository.sort.update');

    Route::post('repository/search/source', 'FileRepositoryController@searchSource')
        ->name('admin.repository.search.source');

    Route::get('repository/{id}/download', 'FileRepositoryController@download')
        ->name('admin.repository.download');

    Route::resource('repository', 'FileRepositoryController', [
        'as' => 'admin',
        'except' => ['show']
    ]);



    /*=================================================================================================================
     * API DOCUMENTATION
     =================================================================================================================*/
    Route::post('api/docs/methods/datatable', 'Api\Docs\MethodsController@datatable')
        ->name('admin.api.docs.methods.datatable');

    Route::post('api/docs/methods/selected/destroy', 'Api\Docs\MethodsController@massDestroy')
        ->name('admin.api.docs.methods.selected.destroy');

    Route::get('api/docs/methods/sort', 'Api\Docs\MethodsController@sortEdit')
        ->name('admin.api.docs.methods.sort');

    Route::post('api/docs/methods/sort', 'Api\Docs\MethodsController@sortUpdate')
        ->name('admin.api.docs.methods.sort.update');

    Route::resource('api/docs/methods', 'Api\Docs\MethodsController', [
        'as' => 'admin.api.docs',
        'except' => ['show']
    ]);


    Route::post('api/docs/categories/datatable', 'Api\Docs\CategoriesController@datatable')
        ->name('admin.api.docs.categories.datatable');

    Route::post('api/docs/categories/selected/destroy', 'Api\Docs\CategoriesController@massDestroy')
        ->name('admin.api.docs.categories.selected.destroy');

    Route::get('api/docs/categories/sort', 'Api\Docs\CategoriesController@sortEdit')
        ->name('admin.api.docs.categories.sort');

    Route::post('api/docs/categories/sort', 'Api\Docs\CategoriesController@sortUpdate')
        ->name('admin.api.docs.categories.sort.update');

    Route::resource('api/docs/categories', 'Api\Docs\CategoriesController', [
        'as' => 'admin.api.docs',
        'except' => ['show']
    ]);


    Route::post('api/docs/sections/datatable', 'Api\Docs\SectionsController@datatable')
        ->name('admin.api.docs.sections.datatable');

    Route::post('api/docs/sections/selected/destroy', 'Api\Docs\SectionsController@massDestroy')
        ->name('admin.api.docs.sections.selected.destroy');

    Route::get('api/docs/sections/sort', 'Api\Docs\SectionsController@sortEdit')
        ->name('admin.api.docs.sections.sort');

    Route::post('api/docs/sections/sort', 'Api\Docs\SectionsController@sortUpdate')
        ->name('admin.api.docs.sections.sort.update');

    Route::resource('api/docs/sections', 'Api\Docs\SectionsController', [
        'as' => 'admin.api.docs',
        'except' => ['show']
    ]);


    /*=================================================================================================================
     * CPANEL E-MAIL ACCOUNTS
     =================================================================================================================*/
    Route::post('server/emails/datatable', 'Cpanel\EmailsController@datatable')
        ->name('admin.cpanel.emails.datatable');

    Route::get('server/emails/configs/{type?}', 'Cpanel\EmailsController@configs')
        ->name('admin.cpanel.emails.configs');

    Route::get('server/emails/install', 'Cpanel\EmailsController@installEdit')
        ->name('admin.cpanel.emails.install');

    Route::get('server/emails/{id}/login', 'Cpanel\EmailsController@remoteLogin')
        ->name('admin.cpanel.emails.login');

    Route::post('server/emails/install', 'Cpanel\EmailsController@install')
        ->name('admin.cpanel.emails.install.store');

    Route::get('server/emails/{id}/forwarders', 'Cpanel\EmailsController@forwardersEdit')
        ->name('admin.cpanel.emails.forwarders.edit');

    Route::post('server/emails/{id}/forwarders', 'Cpanel\EmailsController@forwardersStore')
        ->name('admin.cpanel.emails.forwarders.store');

    Route::get('server/emails/{id}/autoresponders', 'Cpanel\EmailsController@autorespondersEdit')
        ->name('admin.cpanel.emails.autoresponders.edit');

    Route::post('server/emails/{id}/autoresponders', 'Cpanel\EmailsController@autorespondersStore')
        ->name('admin.cpanel.emails.autoresponders.store');

    Route::resource('server/emails', 'Cpanel\EmailsController', [
        'as' => 'admin.cpanel',
        'except' => ['show']
    ]);

    /*=================================================================================================================
     * SUPPORT CENTER
     =================================================================================================================*/
    Route::get('helpcenter/{target}', 'KnowledgeController@helpcenter')
        ->name('admin.helpcenter.index');

    /*=================================================================================================================
     * MANAGE TRANSLATIONS
     =================================================================================================================*/
    Route::get('translations', 'TranslationManagerController@getIndex', ['as' => 'admin'])
        ->name('admin.translations.index');

    /*=================================================================================================================
     * TEST CONTROLLER
     =================================================================================================================*/
    Route::get('test', 'TestController@index')
        ->name('admin.test.index');
});
