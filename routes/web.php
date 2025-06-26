<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
Route::post('contact-export', [ContactController::class, 'export'])->name('contacts.export');
Route::get('check-export-ready', [ContactController::class, 'check_export_ready']);