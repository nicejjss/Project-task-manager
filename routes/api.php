<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\SignUpController;
use App\Http\Controllers\Authentication\ActiveController;
use App\Http\Controllers\Authentication\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'login']);
Route::post('/signup', [SignUpController::class, 'signUp']);
Route::post('/active_account', [ActiveController::class, 'active']);
Route::post('/send_mail_reset', [ResetPasswordController::class, 'sendMail']);
Route::post('reset_password', [ResetPasswordController::class, 'reset_password']);

Route::middleware(['authentication'])->group(function () {
    Route::get('/home', function () {
        return 'Success in authentication';
    });
});
