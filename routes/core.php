<?php

    /*=================================================================================================================
     * LICENSE
     =================================================================================================================*/

    /**
     * Switcher Authorization
     */
    Route::get('core/remote/auth/{target?}', 'Core\SourceAuthController@auth')
        ->name('core.remote.auth');

    /**
     * Switcher Login
     */
    Route::get('core/remote/login/{hash}', 'Core\SourceAuthController@login')
        ->name('core.remote.login');

    /**
     * Enable/Disable license
     */
    Route::get('core/license/check/{hash}', 'Core\LicenseController@check')
        ->name('core.license.check');

    /**
     * Notify payments
     */
    Route::post('core/license/payment/notification/{hash}', 'Core\LicenseController@payments')
        ->name('core.license.payment.notification');



    Route::group(array('prefix' => 'core', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function() {

        /**
         * Installer
         */
        Route::get('install', 'Core\InstallerController@index')
            ->name('admin.core.install.index');

        Route::post('install', 'Core\InstallerController@store')
            ->name('admin.core.install.store');

        Route::post('upload-logos', 'Core\InstallerController@uploadLogos')
            ->name('admin.core.install.upload-logos');

        Route::post('test-email', 'Core\InstallerController@testEmail')
            ->name('admin.core.install.test-email');

        /**
         * Provider agencies
         */

        Route::post('core/provider/agencies/datatable', 'Core\ProviderAgenciesController@datatable')
            ->name('core.provider.agencies.datatable');

        Route::post('core/provider/agencies/selected/destroy', 'Core\ProviderAgenciesController@massDestroy')
            ->name('core.provider.agencies.selected.destroy');

        Route::post('core/provider/agencies/selected/update', 'Core\ProviderAgenciesController@massUpdate')
            ->name('core.provider.agencies.selected.update');

        Route::resource('provider/agencies', 'Core\ProviderAgenciesController', [
            'as' => 'core.provider',
            'except' => ['show']]);

        /**
         * Terminal
         */
        Route::get('terminal', 'Core\TerminalController@index')
            ->name('admin.core.terminal.index');

        Route::post('terminal', 'Core\TerminalController@index')
            ->name('admin.core.terminal.auth');

        Route::post('terminal/execute', 'Core\TerminalController@execute')
            ->name('admin.core.terminal.execute');

        /**
         * LICENSE
         */
        Route::resource('license', 'Core\LicenseController', [
            'as' => 'core',
            'only' => ['index','store']]);

        Route::post('license/storage/clean', 'Core\LicenseController@storageClean')
            ->name('admin.core.license.storage.clean');

        Route::get('license/directory/show', 'Core\LicenseController@showDirectory')
            ->name('admin.core.license.directory.show');

        Route::delete('license/directory/clean', 'Core\LicenseController@cleanDirectory')
            ->name('admin.core.license.directory.clean');

        Route::get('license/file/download', 'Core\LicenseController@downloadFile')
            ->name('admin.core.license.file.download');

        Route::post('license/file/destroy', 'Core\LicenseController@destroyFile')
            ->name('admin.core.license.file.destroy');

        Route::post('license/directory/compact', 'Core\LicenseController@compactDirectory')
            ->name('admin.core.license.directory.compact');

        Route::post('license/directory/load', 'Core\LicenseController@loadDirectories')
            ->name('admin.core.license.directory.load');

        /**
         * TRANSLATIONS
         */
        Route::post('translations/datatable', 'Core\TranslationsController@datatable')
            ->name('core.translations.datatable');

        Route::post('translations/find', 'Core\TranslationsController@findTranslations')
            ->name('core.translations.find');

        Route::post('translations/import', 'Core\TranslationsController@importTranslations')
            ->name('core.translations.import');

        Route::post('translations/publish', 'Core\TranslationsController@publishTranslations')
            ->name('core.translations.publish');

        Route::post('core/translations/selected/destroy', 'Core\TranslationsController@massDestroy')
            ->name('core.translations.selected.destroy');

        Route::resource('translations', 'Core\TranslationsController', [
            'as' => 'core',
            'only' => ['index', 'create', 'edit', 'store', 'update', 'destroy']]);


    });