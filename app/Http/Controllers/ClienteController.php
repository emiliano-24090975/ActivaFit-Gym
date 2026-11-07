<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteController extends Controller
{
    public function dashboard(Request $request)
    {
        $cliente = session('usuario_activo');
        if (!$cliente) {
            return redirect('/')->withErrors(['codigo' => 'Por favor, inicie sesión primero.']);
        }

        $cliente = (object) $cliente;
        $idCliente = $cliente->id_Cliente ?? $cliente->id_cliente ?? $cliente->id ?? null;

        $disciplinaFiltro = $request->input('disciplina', 'Todas');
        $fechaFiltro = $request->input('fecha', date('Y-m-d'));

        // 1. Traducir la fecha seleccionada al día de la semana en español usando Carbon
        Carbon::setLocale('es');
        $carbonDate = Carbon::parse($fechaFiltro);
        $diaSemanaSeleccionado = ucfirst($carbonDate->translatedFormat('l')); // Ej: Lunes, Martes, Miércoles, Jueves, Viernes, Sábado, Domingo

        // Consultamos la tabla 'clase' con el instructor
        $query = DB::connection('mysql')->table('clase')
            ->join('instructor', 'clase.id_Instructor', '=', 'instructor.id_Instructor')
            ->select('clase.*', 'instructor.nombre as nombre_instructor', 'instructor.especialidad');

        if ($disciplinaFiltro && $disciplinaFiltro != 'Todas') {
            $query->where('instructor.especialidad', $disciplinaFiltro);
        }
        
        $clasesCrudas = $query->get();

        // Mapeo de los días de la semana para evaluar rangos lógicos
        $diasOrden = ['Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 7];
        $numDiaSeleccionado = $diasOrden[$diaSemanaSeleccionado] ?? 0;

        // 2. Filtrar dinámicamente según el rango de 'dias_semana' de la base de datos
        $clasesFiltradasPorFecha = [];
        foreach ($clasesCrudas as $c) {
            $diasClase = trim($c->dias_semana); // Ej: "Lunes a Viernes", "Martes a Sábado", "Sábado a Domingo"
            $coincideFecha = false;

            if (str_contains($diasClase, ' a ')) {
                $partes = explode(' a ', $diasClase);
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
                    // Rango que cruza la semana (por si aplica)
                    if ($numDiaSeleccionado >= $numInicio || $numDiaSeleccionado <= $numFin) {
                        $coincideFecha = true;
                    }
                }
            } else {
                // Si es un día exacto o único
                if (strcasecmp($diasClase, $diaSemanaSeleccionado) == 0) {
                    $coincideFecha = true;
                }
            }

            if ($coincideFecha) {
                $clasesFiltradasPorFecha[] = $c;
            }
        }

        // 3. Procesar cupos e inscripciones para las clases que pasaron el filtro de fecha
        $clases = [];
        foreach ($clasesFiltradasPorFecha as $c) {
            $inscritosCount = DB::connection('mysql')
                ->table('Inscripcion_Clase')
                ->where('id_Clase', $c->id_Clase)
                ->where('estado', '!=', 'Cancelada')
                ->count();

            $c->inscritos_actuales = $inscritosCount;
            $c->esta_llena = $inscritosCount >= $c->cupo_maximo;
            
            $yaInscrito = DB::connection('mysql')
                ->table('Inscripcion_Clase')
                ->where('id_Clase', $c->id_Clase)
                ->where('id_Cliente', $idCliente)
                ->where('estado', '!=', 'Cancelada')
                ->exists();

            $c->usuario_inscrito = $yaInscrito;
            $clases[] = $c;
        }

        // Consultar las clases activas a las que este cliente ya está inscrito
        $misInscripciones = DB::connection('mysql')
            ->table('Inscripcion_Clase')
            ->join('clase', 'Inscripcion_Clase.id_Clase', '=', 'clase.id_Clase')
            ->join('instructor', 'clase.id_Instructor', '=', 'instructor.id_Instructor')
            ->where('Inscripcion_Clase.id_Cliente', $idCliente)
            ->where('Inscripcion_Clase.estado', '!=', 'Cancelada')
            ->select('clase.*', 'instructor.nombre as nombre_instructor', 'Inscripcion_Clase.fecha_Reserva', 'Inscripcion_Clase.estado', 'Inscripcion_Clase.id_Reserva')
            ->get();

        return view('cliente.dashboard', compact('cliente', 'clases', 'misInscripciones', 'disciplinaFiltro', 'fechaFiltro'));
    }

    public function reservarClase(Request $request)
    {
        $cliente = session('usuario_activo');
        if (!$cliente) {
            return redirect('/');
        }
        $cliente = (object) $cliente;
        $idCliente = $cliente->id_Cliente ?? $cliente->id_cliente ?? $cliente->id ?? null;

        $idClase = $request->input('id_clase');
        $fechaReserva = $request->input('fecha', date('Y-m-d'));

        $inscripcionExistente = DB::connection('mysql')
            ->table('Inscripcion_Clase')
            ->where('id_Cliente', $idCliente)
            ->where('id_Clase', $idClase)
            ->where('estado', '!=', 'Cancelada')
            ->exists();

        if ($inscripcionExistente) {
            return back()->withErrors(['duplicado' => 'Ya te encuentras inscrito en esta clase.']);
        }

        $clase = DB::connection('mysql')->table('clase')->where('id_Clase', $idClase)->first();
        $inscritosCount = DB::connection('mysql')
            ->table('Inscripcion_Clase')
            ->where('id_Clase', $idClase)
            ->where('estado', '!=', 'Cancelada')
            ->count();

        if ($inscritosCount >= $clase->cupo_maximo) {
            return back()->withErrors(['cupo' => 'Lo sentimos, esta clase ya está llena.']);
        }

        DB::connection('mysql')->table('Inscripcion_Clase')->insert([
            'id_Reserva' => 'RSV-' . rand(1000, 9999),
            'fecha_Reserva' => $fechaReserva . ' ' . date('H:i:s'),
            'estado' => 'Pendiente',
            'id_Cliente' => $idCliente,
            'id_Clase' => $idClase,
        ]);

        return back()->with('success', '¡Clase reservada con éxito!');
    }

    public function cancelarReserva(Request $request)
    {
        $idReserva = $request->input('id_reserva');

        DB::connection('mysql')
            ->table('Inscripcion_Clase')
            ->where('id_Reserva', $idReserva)
            ->update(['estado' => 'Cancelada']);

        return back()->with('success', 'Te has dado de baja de la clase exitosamente.');
    }
}