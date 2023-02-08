<?php

use Uyson\DcatAdmin\AliyunVod\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('dcat-aliyun-vod', Controllers\DcatAliyunVodController::class.'@index');


Route::prefix('dcat-aliyun-vod')->name('uyson.vod.')
    ->group(function(){
        Route::resource('catgories', Controllers\CategoryController::class);
        Route::resource('videos', Controllers\VideoController::class);
        Route::post('videos/create-upload-video', [
            Controllers\VideoController::class,
            'createUploadVideoRequest'
        ])->name('videos.create-upload-video');
        Route::post('videos/refresh-upload-video', [
            Controllers\VideoController::class,
            'refreshUploadVideoRequest'
        ])->name('videos.refresh-upload-video');
    });
