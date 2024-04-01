<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\S3Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/uploadtoS3', [S3Controller::class, 'uploadToS3'])->name('s3.upload');
    Route::get('/all/images', [S3Controller::class, 'allImages'])->name('all.images');
    Route::get('/view/image/{id}', [S3Controller::class, 'viewImage'])->name('view.image');
    //Route::get('/view/news/{id}', [S3Controller::class, 'viewNews'])->name('view.news');


});

require __DIR__.'/auth.php';

// view news for public
Route::get('/view/news/{id}', [S3Controller::class, 'viewNews'])->name('view.news');
