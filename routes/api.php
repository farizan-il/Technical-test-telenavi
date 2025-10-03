<?php

use App\Http\Controllers\Api\TodoController;
use Illuminate\Support\Facades\Route;

Route::prefix('todos')->group(function () {
    Route::post('/', [TodoController::class, 'store']);
    Route::get('/', [TodoController::class, 'index']);
    Route::get('/chart', [TodoController::class, 'chartStatus']);
    Route::get('/chart/priority', [TodoController::class, 'chartPriority']);
    Route::get('/chart/assignee', [TodoController::class, 'chartAssignee']);
});