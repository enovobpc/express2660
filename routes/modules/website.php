<?php

Route::group(array('prefix' => 'admin/website', 'middleware' => 'auth.admin', 'namespace' => 'Admin\Website'), function() {

    /**
     *
     * Google Analytics
     *
     */
    Route::get('analytics', 'GoogleAnalyticsController@index')
        ->name('admin.website.visits.index');

    Route::post('analytics/get-tops', 'GoogleAnalyticsController@getTops')
        ->name('admin.website.visits.tops');

    Route::post('analytics/get/{slug}', 'GoogleAnalyticsController@get')
        ->name('admin.website.visits.get');

    /**
     *
     * Recruitments
     *
     */

    Route::post('recruitments/datatable', 'RecruitmentsController@datatable')
        ->name('admin.website.recruitments.datatable');

    Route::post('recruitments/selected/destroy', 'RecruitmentsController@massDestroy')
        ->name('admin.website.recruitments.selected.destroy');

    Route::resource('recruitments', 'RecruitmentsController', [
        'as' => 'admin.website',
        'only' => ['index', 'show', 'destroy']]);

    /**
     *
     * Sliders
     *
     */
    Route::post('sliders/sort', 'SlidersController@sort')
        ->name('admin.website.sliders.sort.update');

    Route::resource('sliders', 'SlidersController', [
        'as' => 'admin.website',
        'except' => ['show']]);

    /**
     *
     * Brands
     *
     */

    Route::post('brands/datatable', 'BrandsController@datatable')
        ->name('admin.website.brands.datatable');

    Route::post('brands/selected/destroy', 'BrandsController@massDestroy')
        ->name('admin.website.brands.selected.destroy');

    Route::get('brands/sort', 'BrandsController@sortEdit')
        ->name('admin.website.brands.sort');

    Route::post('brands/sort', 'BrandsController@sortUpdate')
        ->name('admin.website.brands.sort.update');

    Route::resource('brands', 'BrandsController', [
        'as' => 'admin.website',
        'except' => ['show']]);


    /**
     *
     * Blog > Posts
     *
     */
    Route::post('blog/posts/datatable', 'BlogPostsController@datatable')
        ->name('admin.website.blog.posts.datatable');

    Route::post('blog/posts/selected/destroy', 'BlogPostsController@massDestroy')
        ->name('admin.website.blog.posts.selected.destroy');

    Route::resource('blog/posts', 'BlogPostsController', [
        'as' => 'admin.website.blog'
    ]);

    /**
     *
     * FAQ > Categories
     *
     */
    Route::post('faqs/categories/datatable', 'FaqsCategoriesController@datatable')
        ->name('admin.website.faqs.categories.datatable');

    Route::post('faqs/categories/selected/destroy', 'FaqsCategoriesController@massDestroy')
        ->name('admin.website.faqs.categories.selected.destroy');

    Route::get('faqs/categories/sort', 'FaqsCategoriesController@sortEdit')
        ->name('admin.website.faqs.categories.sort');

    Route::post('faqs/categories/sort', 'FaqsCategoriesController@sortUpdate')
        ->name('admin.website.faqs.categories.sort.update');

    Route::resource('faqs/categories', 'FaqsCategoriesController', [
        'as' => 'admin.website.faqs',
        'except' => ['show']]);

    /**
     *
     * FAQ > Questions
     *
     */

    Route::post('faqs/datatable', 'FaqsController@datatable')
        ->name('admin.website.faqs.datatable');

    Route::post('faqs/selected/destroy', 'FaqsController@massDestroy')
        ->name('admin.website.faqs.selected.destroy');

    Route::get('faqs/sort', 'FaqsController@sortEdit')
        ->name('admin.website.faqs.sort');

    Route::post('faqs/sort', 'FaqsController@sortUpdate')
        ->name('admin.website.faqs.sort.update');

    Route::resource('faqs', 'FaqsController', [
        'as' => 'admin.website',
        'except' => ['show']]);

    /**
     *
     * Pages
     *
     */
    Route::post('pages/datatable', 'PagesController@datatable')
        ->name('admin.website.pages.datatable');

    Route::post('pages/selected/destroy', 'PagesController@massDestroy')
        ->name('admin.website.pages.selected.destroy');

    Route::post('pages/{pageId}/sort', 'PagesSectionsController@sort')
        ->name('admin.website.pages.sections.sort.update');

    Route::post('pages/video/get', 'PagesSectionsContentController@getVideo')
        ->name('admin.website.pages.sections.content.video.get');

    Route::post('pages/multimedia/store', 'PagesMultimediaController@store')
        ->name('admin.website.multimedia.store');

    Route::delete('pages/multimedia/{basename}/destroy', 'PagesMultimediaController@destroy')
        ->name('admin.website.multimedia.destroy');

    Route::post('pages/multimedia/selected/destroy', 'PagesMultimediaController@massDestroy')
        ->name('admin.website.multimedia.selected.destroy');

    Route::resource('pages', 'PagesController', [
        'as' => 'admin.website',
        'except' => ['show']]);

    Route::resource('pages.sections', 'PagesSectionsController', [
        'as' => 'admin.website',
        'except' => ['index', 'show']]);

    Route::resource('pages.sections.content', 'PagesSectionsContentController', [
        'as' => 'admin.website',
        'except' => ['index', 'show']]);

    /**
     *
     * Newsletters
     *
     */
    Route::post('newsletters/subscribers/datatable', 'NewslettersSubscribersController@datatable')
        ->name('admin.website.newsletters.subscribers.datatable');

    Route::post('newsletters/subscribers/selected/destroy', 'NewslettersSubscribersController@massDestroy')
        ->name('admin.website.newsletters.subscribers.selected.destroy');

    Route::get('newsletters/subscribers/mail/list', 'NewslettersSubscribersController@mailList')
        ->name('admin.website.newsletters.subscribers.mail.list');

    Route::get('newsletters/subscribers/mail/csv', 'NewslettersSubscribersController@downloadCSV')
        ->name('admin.website.newsletters.subscribers.mail.csv');

    Route::resource('newsletters/subscribers', 'NewslettersSubscribersController', [
        'as' => 'admin.website.newsletters',
        'except' => ['show']]);

    /**
     *
     * Testimonials
     *
     */
    Route::post('testimonials/datatable', 'TestimonialsController@datatable')
        ->name('admin.website.testimonials.datatable');

    Route::post('testimonials/selected/destroy', 'TestimonialsController@massDestroy')
        ->name('admin.website.testimonials.selected.destroy');

    Route::get('testimonials/sort', 'TestimonialsController@sortEdit')
        ->name('admin.website.testimonials.sort');

    Route::post('testimonials/sort', 'TestimonialsController@sortUpdate')
        ->name('admin.website.testimonials.sort.update');

    Route::resource('testimonials', 'TestimonialsController', [
        'as' => 'admin.website',
        'except' => ['show']]);

    /**
     *
     * Documents
     *
     */

    Route::post('documents/datatable', 'DocumentsController@datatable')
        ->name('admin.website.documents.datatable');

    Route::post('documents/selected/destroy', 'DocumentsController@massDestroy')
        ->name('admin.website.documents.selected.destroy');

    Route::get('documents/sort', 'DocumentsController@sortEdit')
        ->name('admin.website.documents.sort');

    Route::post('documents/sort', 'DocumentsController@sortUpdate')
        ->name('admin.website.documents.sort.update');

    Route::resource('documents', 'DocumentsController', [
        'as' => 'admin.website',
        'except' => ['show']]);

});