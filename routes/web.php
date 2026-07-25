<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EmailVerificationNoticeController;
use App\Http\Controllers\EmailVerificationNotificationController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingOfferController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotFoundController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationMarkAsReadController;
use App\Http\Controllers\RealtorListingAcceptOfferController;
use App\Http\Controllers\RealtorListingController;
use App\Http\Controllers\RealtorListingImageController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ListingController::class, 'index']);

Route::resource('listing', ListingController::class)->only('index', 'show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('notification', NotificationController::class)->only(['index']);
    Route::put('notification/{notification}/seen', NotificationMarkAsReadController::class)->name('notification.seen');
    Route::resource('listing.offer', ListingOfferController::class)->only(['store']);

    Route::prefix('realtor')->as('realtor.')->group(function () {
        Route::get('listing/{listing}/details', [RealtorListingController::class, 'details'])->name('listing.details')->withTrashed();
        Route::name('listing.restore')->put('listing/{listing}/restore', [RealtorListingController::class, 'restore'])->withTrashed();
        Route::resource('listing', RealtorListingController::class)->withTrashed();
        Route::resource('listing.image', RealtorListingImageController::class)
            ->only(['create', 'store', 'destroy'])
            ->scoped();
        Route::put('offer/{offer}/accept', RealtorListingAcceptOfferController::class)->name('offer.accept');
    });
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
});
Route::delete('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/email/verify', EmailVerificationNoticeController::class)->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', EmailVerificationController::class)->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', EmailVerificationNotificationController::class)->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Fallback
Route::fallback(NotFoundController::class);
