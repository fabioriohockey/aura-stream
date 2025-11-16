<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return File::get(public_path('index.html'));
});

// Catch-all route to serve React app for SPA routing (excluding API)
Route::get('/{any}', function () {
    $index = public_path('index.html');
    if (File::exists($index)) {
        return File::get($index);
    }
    return view('welcome'); // ou outra view fallback do Laravel
})->where('any', '.*');

// Garante que route('login') exista para middleware que chama redirecionamento
if (!Route::has('login')) {
    Route::get('/login', function () {
        $index = public_path('index.html');
        if (File::exists($index)) {
            return File::get($index);
        }
        return redirect('/'); // fallback
    })->name('login');
}

// Temporarily serve images through Laravel
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!File::exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');
