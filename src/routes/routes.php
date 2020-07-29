<?php

Route::namespace('\Done\LaravelAPM')->group(function () {
    Route::middleware(config('apm.middlewares'))->group(function () {
        Route::get('/apm', 'ApmController@index')->name('apm');
    });
});
