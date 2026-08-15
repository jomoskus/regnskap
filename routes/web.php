<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('inbox', 'pages::inbox')->name('inbox');
    Route::redirect('dashboard', '/inbox')->name('dashboard');

    Route::livewire('transactions/create', 'pages::transactions.create')->name('transactions.create');
    Route::livewire('transactions/import', 'pages::transactions.import')->name('transactions.import');
});

require __DIR__.'/settings.php';
