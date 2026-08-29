<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/quiz/{category?}', function (?Category $category = null) {
    $category = $category ?? Category::firstOrFail();
    return view('quiz', ['category' => $category]);
});
