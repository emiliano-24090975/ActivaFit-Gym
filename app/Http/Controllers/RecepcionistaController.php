<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecepcionistaController extends Controller
{
    public function dashboard(Request $request)
    {
        $recepcionista = session('usuario_activo');
        if (!$recepcionista) {
            return redirect('/')->withErrors(['codigo' => 'Por favor, inicie sesión primero.']);
        }

        $recepcionista = (object)$recepcionista;

        // 1. Obtener membresías y la lista completa de clientes registrados para el nuevo módulo
        $membresias = DB::connection('mysql')->table('Membresia')->get();$clientes = DB::connection('mysql')->table('Cliente')
            ->join('Membresia', 'Cliente.id_Membresia', '=', 'Membresia.id_Membresia')
            ->select('Cliente.*', 'Membresia.nombre_plan')
            ->orderBy('Cliente.id_Cliente', 'asc')
            ->get();

        // 2. Filtros para la visualización de clases
        $disciplinaFiltro =$request->input('disciplina', 'Todas');
        $fechaFiltro =$request->input('fecha', date('Y-m-d'));

        Carbon::setLocale('es');
        $carbonDate = Carbon::parse($fechaFiltro);
        $diaSemanaSeleccionado = ucfirst($carbonDate->translatedFormat('l'));

        // Consultar clases con datos de instructor
        $query = DB::connection('mysql')->table('clase')
            ->join('instructor', 'clase.id_Instructor', '=', 'instructor.id_Instructor')
            ->select('clase.*', 'instructor.nombre as nombre_instructor', 'instructor.especialidad');

        if ($disciplinaFiltro &&$disciplinaFiltro != 'Todas') {
            $query->where('instructor.especialidad',$disciplinaFiltro);
        }

        $clasesCrudas =$query->get();

        // Filtrado por día de la semana
        $diasOrden = ['Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 7];
        $numDiaSeleccionado = $diasOrden[$diaSemanaSeleccionado] ?? 0;

        $clases = [];
        foreach ($clasesCrudas as $c) {$diasClase = trim($c->dias_semana);$coincideFecha = false;

            if (str_contains($diasClase, ' a ')) {
                $partes = explode(' a ',$diasClase);
                $diaInicio = trim($partes[0]);
                $diaFin = trim($partes[1]);

                $numInicio = $diasOrden[$diaInicio] ?? 0;
                $numFin = $diasOrden[$diaFin] ?? 0;

                if ($numInicio <= $numFin) {
                    // Rango normal (ej. Lunes a Viernes -> 1 al 5)
                    if ($numDiaSeleccionado >= $numInicio && $numDiaSeleccionado <= $numFin) {
                        $coincideFecha = true;
                    }
                } else {
                    // Rango alternativo con operador OR (||) corregido
                    if ($numDiaSeleccionado >= $numInicio || $numDiaSeleccionado <= $numFin) {
                        $coincideFecha = true;
                    }
                }
            } else {
                if (strcasecmp($diasClase, $diaSemanaSeleccionado) == 0) {$coincideFecha = true;
                }
            }

            if ($coincideFecha) {$inscritosCount = DB::connection('mysql')
                    ->table('Inscripcion_Clase')
                    ->where('id_Clase', $c->id_Clase)
                    ->where('estado', '!=', 'Cancelada')
                    ->count();

                $c->inscritos_actuales =$inscritosCount;
                $c->esta_llena = $inscritosCount >=$c->cupo_maximo;
                $clases[] =$c;
            }
        }

        return view('recepcionista.dashboard', compact('recepcionista', 'membresias', 'clientes', 'clases', 'disciplinaFiltro', 'fechaFiltro'));
    }

    public function registrarCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'edad' => 'required|integer|min:15',
            'numero' => 'required|string|max:15',
            'id_Membresia' => 'required|string',
        ]);

        $ultimoCliente = DB::connection('mysql')->table('Cliente')
            ->orderBy('id_Cliente', 'desc')
            ->first();

        if ($ultimoCliente) {
            $numActual = (int) substr($ultimoCliente->id_Cliente, 4);
            $nuevoId = 'CLI-' . str_pad($numActual + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nuevoId = 'CLI-0001';
        }

        $membresia = DB::connection('mysql')->table('Membresia')
            ->where('id_Membresia', $request->input('id_Membresia'))
            ->first();

        $duracionDias = $membresia ? $membresia->duracion_dias : 30;
        $fechaVencimiento = Carbon::now()->addDays($duracionDias)->format('Y-m-d');

        DB::connection('mysql')->table('Cliente')->insert([
            'id_Cliente' => $nuevoId,
            'nombre' => $request->input('nombre'),
            'edad' => $request->input('edad'),
            'numero' => $request->input('numero'),
            'id_Membresia' => $request->input('id_Membresia'),
            'fecha_vencimiento' => $fechaVencimiento
        ]);

        return back()->with('success', "¡Cliente registrado con éxito! ID: {$nuevoId}");
    }

    // Método para actualizar datos del cliente existente
    public function actualizarCliente(Request $request)
    {
        $idCliente =$request->input('id_Cliente');

        DB::connection('mysql')->table('Cliente')
            ->where('id_Cliente', $idCliente)
            ->update([
                'nombre' => $request->input('nombre'),
                'edad' => $request->input('edad'),
                'numero' => $request->input('numero'),
                'id_Membresia' => $request->input('id_Membresia'),
            ]);

        return back()->with('success', "Datos del cliente {$idCliente} actualizados correctamente.");
    }

    // Método para eliminar un cliente de la base de datos
    public function eliminarCliente(Request $request)
    {
        $idCliente =$request->input('id_cliente');

        // Eliminar reservas asociadas primero por integridad referencial si es necesario
        DB::connection('mysql')->table('Inscripcion_Clase')->where('id_Cliente', $idCliente)->delete();
        
        // Eliminar cliente
        DB::connection('mysql')->table('Cliente')->where('id_Cliente', $idCliente)->delete();

        return back()->with('success', "Cliente {$idCliente} eliminado del sistema.");
    }
}