<?php


Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ], 'namespace' => 'DefaultSite'], function() {
        
    Route::get('/', 'HomeController@index')
        ->name('home.index');

    Route::get('tracking', 'TrackingController@index')
        ->name('tracking.index');

    Route::post('tracking/sync/{id}', 'TrackingController@syncTracking')
        ->name('tracking.sync');

    Route::post('tracking/location/{trackingCode}', 'TrackingController@getOperatorLocation')
        ->name('tracking.location');

    Route::get('trk/{id?}', 'TrackingController@index')
        ->name('trk.index');

    Route::get('auction/{id}', 'AuctionController@show')
        ->name('auction.show');
});

Route::get('payment/callback/{gateway?}/{paymentType?}', 'CallbackController@index')
    ->name('payment.callback');

//legal notes
Route::get('avisos-legais/{slug?}', 'DefaultSite\LegalController@index')
    ->name('legal.show');

    
//Reschedule Shipment
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ], 'namespace' => 'DefaultSite'], function() {

    Route::resource('reschedule', 'TrackingController', [
        'as' => 'tracking'
    ]);
});