<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

// Auth rute (javne)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Javno pregledanje nekretnina (bez logina)
Route::get('/properties',       [PropertyController::class, 'index']);
Route::get('/properties/{id}',  [PropertyController::class, 'show']);

// Zaštićene rute
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Resource ruta (zahtev 7 — ovo je tvoja 1 resource ruta)
    Route::apiResource('properties', PropertyController::class)->except(['index', 'show']);

    // Custom rute (zahtev 7 — 3 različita tipa)
    Route::get('/my-properties',            [PropertyController::class, 'myProperties']);
    Route::apiResource('inquiries',         InquiryController::class);
    Route::patch('/inquiries/{id}/status',  [InquiryController::class, 'updateStatus']);
});