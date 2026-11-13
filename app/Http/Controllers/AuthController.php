<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Método que muestra la vista de login (si lo tienes implementado)
    public function mostrarLogin()
    {
        return view('auth.login'); // O la vista personalizada que utilices
    }

    // Método que procesa el inicio de sesión y redirige según el rol
    public function verificarAcceso(Request $request)
    {
        $tipo = $request->input('tipo'); // 'Cliente', 'Recepcionista', 'Instructor', 'Admin'
        $codigo = $request->input('codigo'); // Ej: CLI-0001, REP-01, etc.

        $tabla = '';
        $campoId = '';

        // Definimos la tabla y la columna ID según el rol seleccionado
        switch ($tipo) {
            case 'Cliente':
                $tabla = 'Cliente';
                $campoId = 'id_Cliente';
                break;
            case 'Recepcionista':
                $tabla = 'Recepcionista';
                $campoId = 'id_Recepcionista';
                break;
            case 'Instructor':
                $tabla = 'Instructor';
                $campoId = 'id_Instructor';
                break;
            case 'Admin':
                $tabla = 'Administrador';
                $campoId = 'id_Admin';
                break;
            default:
                return back()->withErrors(['codigo' => 'Tipo de usuario no válido.']);
        }

        // Consultamos directamente en la base de datos MySQL usando el driver forzado
        $usuario = DB::connection('mysql')->table($tabla)->where($campoId, $codigo)->first();

        if ($usuario) {
            // Guardamos la sesión del usuario exitosamente
            session(['usuario_activo' => $usuario, 'tipo' => $tipo]);

            // Redirección dinámica según el rol del usuario
            if ($tipo == 'Cliente') {
                return redirect('/catalogo');
            } elseif ($tipo == 'Recepcionista') {
                return redirect('/recepcion'); // Redirige al nuevo módulo de recepción
            } elseif ($tipo == 'Instructor') {
                return redirect('/instructor');
            } elseif ($tipo == 'Admin') {
                return redirect('/admin');
            }
        } else {
            // Si el código no existe en la base de datos
            return back()->withErrors(['codigo' => 'El código ingresado no existe para este tipo de usuario.']);
        }
    }
}

class ClienteController extends Controller
{
    public function dashboard(Request $request)
    {
        // Obtenemos el usuario activo guardado en la sesión al iniciar sesión
        $cliente = session('usuario_activo');

        if (!$cliente) {
            return redirect('/')->withErrors(['codigo' => 'Por favor, inicie sesión primero.']);
        }

        // Consultamos las clases disponibles desde tu base de datos MySQL
        $clases = DB::table('clases')->get(); // Asegúrate de que tu tabla se llame 'clases'

        // Retornamos la vista pasando los datos del cliente y las clases
        return view('cliente.dashboard', compact('cliente', 'clases'));
    }
}