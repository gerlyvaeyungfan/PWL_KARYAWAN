<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\GajiController;

Route::get('/', [WelcomeController::class, 'index']);

// route CRUD karyawan
Route::group(['prefix' => 'karyawan'], function () {
    Route::get('/', [KaryawanController::class, 'index']);
    Route::post('/list', [KaryawanController::class, 'list']);

    // Create AJAX
    Route::get('/create_ajax', [KaryawanController::class, 'create_ajax']);
    Route::post('/ajax', [KaryawanController::class, 'store_ajax']);

    // Edit AJAX
    Route::get('/{id}/edit_ajax', [KaryawanController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [KaryawanController::class, 'update_ajax']);

    // Delete AJAX
    Route::get('/{id}/delete_ajax', [KaryawanController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [KaryawanController::class, 'delete_ajax']);
});

// route CRUD jabatan
Route::group(['prefix' => 'jabatan'], function () {
    Route::get('/', [JabatanController::class, 'index']);
    Route::post('/list', [JabatanController::class, 'list']);

    // Create AJAX
    Route::get('/create_ajax', [JabatanController::class, 'create_ajax']);
    Route::post('/ajax', [JabatanController::class, 'store_ajax']);

    // Edit AJAX
    Route::get('/{id}/edit_ajax', [JabatanController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [JabatanController::class, 'update_ajax']);

    // Delete AJAX
    Route::get('/{id}/delete_ajax', [JabatanController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [JabatanController::class, 'delete_ajax']);
});

// route CRUD gaji
Route::group(['prefix' => 'gaji'], function () {
    Route::get('/', [GajiController::class, 'index']);
    Route::post('/list', [GajiController::class, 'list']);

    // Create AJAX
    Route::get('/create_ajax', [GajiController::class, 'create_ajax']);
    Route::post('/ajax', [GajiController::class, 'store_ajax']);

    // Edit AJAX
    Route::get('/{id}/edit_ajax', [GajiController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [GajiController::class, 'update_ajax']);

    // Delete AJAX
    Route::get('/{id}/delete_ajax', [GajiController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [GajiController::class, 'delete_ajax']);
});