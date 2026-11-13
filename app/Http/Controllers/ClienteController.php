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
        $diaSemanaSeleccionado = ucfirst($carbonDate->translatedFormat('l')); 

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
            $diasClase = trim($c->dias_semana); 
            $coincideFecha = false;

            if (str_contains($diasClase, ' a ')) {
                $partes = explode(' a ', $diasClase);
                $diaInicio = trim($partes[0]);
                $diaFin = trim($partes[1]);

                $numInicio = $diasOrden[$diaInicio] ?? 0;
                $numFin = $diasOrden[$diaFin] ?? 0;

                if ($numInicio <= $numFin) {
                    if ($numDiaSeleccionado >= $numInicio && $numDiaSeleccionado <= $numFin) {
                        $coincideFecha = true;
                    }
                } else {
                    if ($numDiaSeleccionado >= $numInicio || $numDiaSeleccionado <= $numFin) {
                        $coincideFecha = true;
                    }
                }
            } else {
                if (strcasecmp($diasClase, $diaSemanaSeleccionado) == 0) {
                    $coincideFecha = true;
                }
            }

            if ($coincideFecha) {
                $clasesFiltradasPorFecha[] = $c;
            }
        }

        // 3. Procesar cupos e inscripciones para las clases filtradas
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

        // 4. MÓDULO DE ASESORÍAS (CLIENTE)
        $asesorias = DB::connection('mysql')->table('Asesoria')
            ->join('instructor', 'Asesoria.id_Instructor', '=', 'instructor.id_Instructor')
            ->where('Asesoria.id_Cliente', $idCliente)
            ->select('Asesoria.*', 'instructor.nombre as nombre_instructor')
            ->orderBy('Asesoria.fecha_hora', 'desc')
            ->get();

        $instructores = DB::connection('mysql')->table('Instructor')->get();
        $tabActiva = $request->input('tab_activa', 'catalogo');

        return view('cliente.dashboard', compact(
            'cliente', 
            'clases', 
            'misInscripciones', 
            'disciplinaFiltro', 
            'fechaFiltro', 
            'asesorias', 
            'instructores', 
            'tabActiva'
        ));
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

    // Registrar una nueva asesoría solicitada por el cliente
    public function registrarAsesoria(Request $request)
    {
        $cliente = session('usuario_activo');
        if (!$cliente) {
            return redirect('/');
        }
        $cliente = (object) $cliente;
        $idCliente = $cliente->id_Cliente ?? $cliente->id_cliente ?? $cliente->id ?? null;

        $request->validate([
            'id_Instructor' => 'required',
            'fecha_hora' => 'required|date',
        ]);

        $fechaSeleccionada = Carbon::parse($request->input('fecha_hora'));
        $idInstructor = $request->input('id_Instructor');

        // 1. Verificación: No permitir asesorías en fechas u horas menores a la actual
        if ($fechaSeleccionada->lt(Carbon::now())) {
            return redirect()->route('cliente.dashboard', ['tab_activa' => 'asesorias'])
                ->withErrors(['fecha' => 'No puedes agendar una asesoría en una fecha u hora pasada.']);
        }

        // 2. Verificación: Comprobar si el instructor está disponible en esa fecha y hora exacta
        $fechaFormateada = $fechaSeleccionada->format('Y-m-d H:i:s');
        
        $instructorOcupado = DB::connection('mysql')->table('Asesoria')
            ->where('id_Instructor', $idInstructor)
            ->where('fecha_hora', $fechaFormateada)
            ->where('estado', '!=', 'Cancelada')
            ->exists();

        if ($instructorOcupado) {
            return redirect()->route('cliente.dashboard', ['tab_activa' => 'asesorias'])
                ->withErrors(['disponibilidad' => 'El instructor ya tiene una asesoría agendada en este horario. Por favor, elige otra fecha u hora.']);
        }

        // Generar un ID único para la asesoría (ASR-XXXX)
        $ultimoId = DB::connection('mysql')->table('Asesoria')->max('id_Asesoria');
        $siguienteNumero = $ultimoId ? intval(substr($ultimoId, 4)) + 1 : 1;
        $nuevoIdAsesoria = 'ASR-' . str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT);

        // Inserción del registro con valor predeterminado para el campo NOT NULL
        DB::connection('mysql')->table('Asesoria')->insert([
            'id_Asesoria' => $nuevoIdAsesoria,
            'fecha_hora' => $fechaFormateada,
            'estado' => 'Pendiente',
            'observaciones' => 'Ingrese observacion', // Texto predeterminado requerido por la restricción NOT NULL
            'id_Cliente' => $idCliente,
            'id_Instructor' => $idInstructor,
        ]);

        return redirect()->route('cliente.dashboard', ['tab_activa' => 'asesorias'])
            ->with('success', "¡Asesoría {$nuevoIdAsesoria} solicitada con éxito!");
    }
}