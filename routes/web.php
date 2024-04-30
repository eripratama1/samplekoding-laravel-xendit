<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('order');
});

Route::get('/',[App\Http\Controllers\OrderController::class,'index'])->name('home');
Route::post('/create-invoice',[App\Http\Controllers\OrderController::class,'createInvoice'])->name('createInvoice');
Route::post('/notification',[App\Http\Controllers\OrderController::class,'notificationCallback'])->name('notification');
