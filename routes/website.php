<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ]], function() {
    

    Route::get('/', 'Website\MainController@index')
        ->name('home.index');

    Route::get('sobre-nos', 'Website\MainController@about')
        ->name('about.index');

    Route::get('serviços', 'Website\MainController@services')
        ->name('services.index');

    // tipos de serviços
    Route::get('serviço-armazenagem', 'Website\MainController@storage')
        ->name('storage.index');
        
    Route::get('serviço-embalamentoetiquetagem', 'Website\MainController@packaging')
        ->name('packaging.index');

    Route::get('serviço-distribuição', 'Website\MainController@distribution')
        ->name('distribution.index');

    Route::get('serviço-callcenter', 'Website\MainController@callcenter')
        ->name('callcenter.index');

    Route::get('serviço-ecommerce', 'Website\MainController@ecommerce')
        ->name('ecommerce.index');

        // contactos
    Route::get('contactos', 'Website\MainController@contacts')
        ->name('contacts.index');
        
    Route::get('recrutamento', 'Website\MainController@recruitment')
        ->name('recruitment.index');

    Route::get('pedir-orçamento', 'Website\MainController@budget')
        ->name('budget.index');



        
    Route::get(LaravelLocalization::transRoute('website.routes.tracking').'/{tracking?}', 'DefaultSite\TrackingController@index')
        ->name('tracking.index');

    Route::get('trk/{tracking?}', 'DefaultSite\TrackingController@index')
        ->name('trk.index');
        
    Route::get('seguimiento/{tracking?}', 'DefaultSite\TrackingController@index')
        ->name('seguimiento.index');
        
    Route::get('tracking/{tracking?}', 'DefaultSite\TrackingController@index')
        ->name('trackingen.index');

    Route::get(LaravelLocalization::transRoute('website.routes.tracking'), 'DefaultSite\TrackingController@index')
        ->name('website.tracking.index');
        
     Route::get('trk', 'DefaultSite\TrackingController@index')
        ->name('tracking.index');
        
    Route::post('tracking/sync/{id}', 'DefaultSite\TrackingController@syncTracking')
        ->name('tracking.sync');
});



//contacts
Route::group(['prefix' => LaravelLocalization::setLocale().'/'.LaravelLocalization::transRoute('website.routes.contacts'), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ], 'namespace' => 'Website'], function() {
        
    Route::get('/', 'ContactsController@index')
        ->name('website.contacts.index');

    Route::post('/', 'ContactsController@mail')
        ->name('website.contacts.mail');

    Route::post('/call', 'ContactsController@call')
        ->name('website.contacts.call');
});


//signup
Route::group(['prefix' => LaravelLocalization::setLocale().'/'.LaravelLocalization::transRoute('website.routes.signup'), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ], 'namespace' => 'Website'], function() {

    Route::get('/', 'RegisterController@index')
        ->name('website.signup.index');

    Route::post('/', 'RegisterController@store')
        ->name('website.signup.store');
});



Route::get('/emailTest', 'Website\emailTestController@index')
        ->name('website.emailTest');
