<?php

use App\Http\Controllers\API\Authentication\ActiveController;
use App\Http\Controllers\API\Authentication\LoginController;
use App\Http\Controllers\API\Authentication\ResetPasswordController;
use App\Http\Controllers\API\Authentication\SignUpController;
use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/signup', [SignUpController::class, 'signUp']);
    Route::post('/active_account', [ActiveController::class, 'active']);
    Route::post('/send_mail_reset', [ResetPasswordController::class, 'sendMail']);
    Route::post('reset_password', [ResetPasswordController::class, 'resetPassword']);
});

Route::middleware(['authentication:api'])->group(function () {
    Route::get('/', function () {
        return 'Success in authentication';
    });
});
