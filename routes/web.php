<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home'); // 'Home' é o componente Vue dentro de resources/js/Pages/Home.vue
});
