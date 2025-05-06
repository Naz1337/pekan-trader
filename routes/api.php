<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UploadThingController;

Route::get('/uploadthing/presigned-url/logo', [UploadThingController::class, 'getPresignedUrlForLogo'])
    ->name('api.uploadthing.presigned-url.logo');
