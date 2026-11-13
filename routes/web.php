<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RecepcionistaController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AdminController;


// Autenticación
Route::get('/', [AuthController::class, 'mostrarLogin']);
Route::post('/login', [AuthController::class, 'verificarAcceso'])->name('login.verificar');

// Módulo Cliente
Route::get('/catalogo', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
Route::post('/reservar', [ClienteController::class, 'reservarClase'])->name('cliente.reservar');
Route::post('/cancelar-reserva', [ClienteController::class, 'cancelarReserva'])->name('cliente.cancelar');
Route::get('/cliente/asesorias', [ClienteController::class, 'asesorias'])->name('cliente.asesorias');
Route::post('/cliente/asesorias/registrar', [ClienteController::class, 'registrarAsesoria'])->name('cliente.registrar_asesoria');


// Módulo Recepcionista
Route::get('/recepcion', [RecepcionistaController::class, 'dashboard'])->name('recepcionista.dashboard');
Route::post('/registrar-cliente', [RecepcionistaController::class, 'registrarCliente'])->name('recepcionista.registrar_cliente');

//Modulo Instructor
Route::get('/instructor', [InstructorController::class, 'dashboard'])->name('instructor.dashboard');
Route::post('/instructor/solicitar-cambio', [InstructorController::class, 'solicitarCambio'])->name('instructor.solicitar_cambio');
Route::post('/instructor/asesorias/actualizar', [InstructorController::class, 'actualizarAsesoria'])->name('instructor.actualizar_asesoria');

// Módulo Administrador
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/responder-solicitud', [AdminController::class, 'responderSolicitud'])->name('admin.responder_solicitud');
Route::get('/admin/exportar-excel', [AdminController::class, 'descargarReporteExcel'])->name('admin.exportar_excel');

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