<?php

declare(strict_types=1);

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', static fn () => view('welcome'))->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
});
