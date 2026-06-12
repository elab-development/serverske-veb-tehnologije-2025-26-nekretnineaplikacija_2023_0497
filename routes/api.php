<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MortgageController;
use App\Http\Controllers\PasswordResetController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);

Route::get('/properties/export', [PropertyController::class, 'export']);

Route::get('/properties',       [PropertyController::class, 'index']);
Route::get('/properties/{id}',  [PropertyController::class, 'show']);


Route::get('/mortgage/calculate', [MortgageController::class, 'calculate']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    
    Route::apiResource('properties', PropertyController::class)->except(['index', 'show']);

    
    Route::get('/my-properties',            [PropertyController::class, 'myProperties']);
    Route::apiResource('inquiries',         InquiryController::class);
    Route::patch('/inquiries/{id}/status',  [InquiryController::class, 'updateStatus']);
});