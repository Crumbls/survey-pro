<?php
use Illuminate\Support\Facades\Route;

Route::get('responses', \App\Livewire\Response\ListResource::class)
    ->name('responses.index');
