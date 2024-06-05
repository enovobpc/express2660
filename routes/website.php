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

    Route::get(LaravelLocalization::transRoute('website.routes.about'), 'Website\MainController@about')
        ->name('about.index');

    Route::get(LaravelLocalization::transRoute('website.routes.services'), 'Website\MainController@services')
        ->name('services.index');

    // tipos de servicos
    Route::get(LaravelLocalization::transRoute('website.routes.storage'), 'Website\MainController@storage')
        ->name('storage.index');
        
    Route::get(LaravelLocalization::transRoute('website.routes.packaging'), 'Website\MainController@packaging')
        ->name('packaging.index');

    Route::get(LaravelLocalization::transRoute('website.routes.distribution'), 'Website\MainController@distribution')
        ->name('distribution.index');

    Route::get(LaravelLocalization::transRoute('website.routes.callcenter'), 'Website\MainController@callcenter')
        ->name('callcenter.index');

    Route::get(LaravelLocalization::transRoute('website.routes.ecommerce'), 'Website\MainController@ecommerce')
        ->name('ecommerce.index');

    Route::get(LaravelLocalization::transRoute('website.routes.charge'), 'Website\MainController@charge')
        ->name('charge.index');

    Route::get(LaravelLocalization::transRoute('website.routes.devolutions'), 'Website\MainController@devolutions')
        ->name('devolutions.index');

    Route::get(LaravelLocalization::transRoute('website.routes.api'), 'Website\MainController@api')
        ->name('api.index');

    
        
    // Route::get('recrutamento', 'Website\MainController@recruitment')
    //     ->name('recruitment.index');

    Route::get(LaravelLocalization::transRoute('website.routes.budget'), 'Website\MainController@budget')
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
        
    // Route::get('/', 'ContactsController@index')
    //     ->name('website.contacts.index');

     // contactos
    Route::get('/', 'Website\MainController@contacts')
        ->name('contacts.index');

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

//legal notes
Route::get('avisos-legais/{slug?}', 'LegalController@index')
->name('legal.show');
   


Route::get('/emailTest', 'Website\emailTestController@index')
        ->name('website.emailTest');
