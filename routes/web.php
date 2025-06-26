<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
Route::get('contact-export', [ContactController::class, 'export'])->name('contacts.export');