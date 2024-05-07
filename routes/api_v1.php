<?php

use App\Http\Api\V1\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/templates', [TemplateController::class, 'index']);
