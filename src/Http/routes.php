<?php

use Uyson\DcatAdmin\AliyunVod\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('dcat-aliyun-vod', Controllers\DcatAliyunVodController::class.'@index');


Route::prefix('dcat-aliyun-vod')
    ->group(function(){
        Route::resource('catgories', Controllers\CategoryController::class);
        Route::resource('videos', Controllers\VideoController::class);
    });
