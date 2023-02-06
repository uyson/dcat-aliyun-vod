<?php

use Uyson\DcatAdmin\AliyunVod\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('dcat-aliyun-vod', Controllers\DcatAliyunVodController::class.'@index');


Route::prefix('dcat-aliyun-vod')
    ->group(function(){
        Route::resource('catgories', Controllers\CategoryController::class);
        Route::resource('videos', Controllers\VideoController::class);
        Route::post('videos/create-upload-video-request', [
            Controllers\VideoController::class,
            'createUploadVideoRequest'
        ]);
        Route::post('videos/refresh-upload-video-request', [
            Controllers\VideoController::class,
            'refreshUploadVideoRequest'
        ]);
    });
