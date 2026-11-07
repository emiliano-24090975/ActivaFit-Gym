<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RecepcionistaController;

// Autenticación
Route::get('/', [AuthController::class, 'mostrarLogin']);
Route::post('/login', [AuthController::class, 'verificarAcceso'])->name('login.verificar');

// Módulo Cliente
Route::get('/catalogo', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
Route::post('/reservar', [ClienteController::class, 'reservarClase'])->name('cliente.reservar');
Route::post('/cancelar-reserva', [ClienteController::class, 'cancelarReserva'])->name('cliente.cancelar');

// Módulo Recepcionista
Route::get('/recepcion', [RecepcionistaController::class, 'dashboard'])->name('recepcionista.dashboard');
Route::post('/registrar-cliente', [RecepcionistaController::class, 'registrarCliente'])->name('recepcionista.registrar_cliente');

require __DIR__.'/settings.php';



// Ruta principal que muestra tu formulario de inicio de sesión personalizado
Route::get('/', [AuthController::class, 'mostrarLogin']);
// Ruta que procesa los datos enviados por el formulario
Route::post('/login', [AuthController::class, 'verificarAcceso'])->name('login.verificar');

// Ruta del catálogo/panel de clientes (CU-01)
Route::get('/catalogo', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
// Ruta para registrar la inscripción a una clase (POST)
Route::post('/reservar', [ClienteController::class, 'reservarClase'])->name('cliente.reservar');
Route::post('/cancelar-reserva', [ClienteController::class, 'cancelarReserva'])->name('cliente.cancelar');

// Módulo de Recepcionista
Route::get('/recepcion', [RecepcionistaController::class, 'dashboard'])->name('recepcionista.dashboard');
Route::post('/registrar-cliente', [RecepcionistaController::class, 'registrarCliente'])->name('recepcionista.registrar_cliente');
Route::post('/actualizar-cliente', [RecepcionistaController::class, 'actualizarCliente'])->name('recepcionista.actualizar_cliente');
Route::post('/eliminar-cliente', [RecepcionistaController::class, 'eliminarCliente'])->name('recepcionista.eliminar_cliente');

// Archivo opcional de configuraciones de Jetstream/Laravel si lo requiere
require __DIR__.'/settings.php';