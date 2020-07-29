<?php

Route::namespace('\Done\LaravelAPM')->group(function () {
    Route::middleware(config('apm.middlewares'))->group(function () {
        Route::get(config('apm.route.uri', '/apm'), 'ApmController@index')->name(config('apm.route.name', '/apm'));
    });
});
