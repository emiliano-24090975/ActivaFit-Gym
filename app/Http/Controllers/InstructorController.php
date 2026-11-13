<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstructorController extends Controller
{
    public function dashboard(Request $request)
    {
        $instructor = session('usuario_activo');
        if (!$instructor) {
            return redirect('/')->withErrors(['codigo' => 'Por favor, inicie sesión primero.']);
        }

        $instructor = (object) $instructor;
        $idInstructor = $instructor->id_Instructor ?? null;

        // 0. LIMPIEZA AUTOMÁTICA DE ASESORÍAS ANTIGUAS (+7 DÍAS)
        $fechaLimiteAsesoria = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
        DB::connection('mysql')->table('Asesoria')
            ->where('id_Instructor', $idInstructor)
            ->where('fecha_hora', '<', $fechaLimiteAsesoria)
            ->delete();

        // 1. Obtener las clases que imparte este instructor específico
        $clasesImpartidas = DB::connection('mysql')->table('clase')
            ->where('id_Instructor', $idInstructor)
            ->get();

        foreach ($clasesImpartidas as $c) {
            $inscritosCount = DB::connection('mysql')
                ->table('Inscripcion_Clase')
                ->where('id_Clase', $c->id_Clase)
                ->where('estado', '!=', 'Cancelada')
                ->count();
            $c->inscritos_actuales = $inscritosCount;
        }

        // 2. Obtener solicitudes de cambio
        $solicitudes = DB::connection('mysql')->table('SolicitudCambio')
            ->join('clase', 'SolicitudCambio.id_Clase', '=', 'clase.id_Clase')
            ->where('SolicitudCambio.id_Instructor', $idInstructor)
            ->select('SolicitudCambio.*', 'clase.nombre as nombre_clase')
            ->orderBy('SolicitudCambio.fecha', 'desc')
            ->get();

        // 3. MÓDULO DE ASESORÍAS (INSTRUCTOR) - Con Filtro de Bandeja y Ordenamiento
        $bandejaAsesoria = $request->input('bandeja_asesoria', 'Pendientes'); // 'Pendientes' o 'Historial'
        $ordenAsesoria = $request->input('orden_asesoria', 'fecha_hora'); 
        $dirAsesoria = $request->input('dir_asesoria', 'asc');

        $asesoriasQuery = DB::connection('mysql')->table('Asesoria')
            ->join('Cliente', 'Asesoria.id_Cliente', '=', 'Cliente.id_Cliente')
            ->where('Asesoria.id_Instructor', $idInstructor)
            ->select('Asesoria.*', 'Cliente.nombre as nombre_cliente');

        // Filtrar por bandeja
        if ($bandejaAsesoria === 'Pendientes') {
            $asesoriasQuery->where('Asesoria.estado', 'Pendiente');
        } else {
            $asesoriasQuery->whereIn('Asesoria.estado', ['Realizada', 'Cancelada']);
        }

        // Aplicar ordenamiento dinámico
        if ($ordenAsesoria === 'nombre_cliente') {
            $asesoriasQuery->orderBy('Cliente.nombre', $dirAsesoria);
        } else {
            $asesoriasQuery->orderBy('Asesoria.' . $ordenAsesoria, $dirAsesoria);
        }

        $asesorias = $asesoriasQuery->get();
        $tabActiva = $request->input('tab_activa', 'mis-clases');

        return view('instructor.dashboard', compact(
            'instructor', 
            'clasesImpartidas', 
            'solicitudes', 
            'asesorias', 
            'bandejaAsesoria',
            'ordenAsesoria', 
            'dirAsesoria',
            'tabActiva'
        ));
    }

    public function solicitarCambio(Request $request)
    {
        $instructor = session('usuario_activo');
        if (!$instructor) {
            return redirect('/');
        }
        $instructor = (object) $instructor;
        $idInstructor = $instructor->id_Instructor ?? null;

        $request->validate([
            'id_clase' => 'required|string',
            'motivo' => 'required|string|max:500',
        ]);

        $ultimaSolicitud = DB::connection('mysql')->table('SolicitudCambio')
            ->orderBy('idSolicitudCambio', 'desc')
            ->first();

        if ($ultimaSolicitud) {
            $numActual = (int) substr($ultimaSolicitud->idSolicitudCambio, 4);
            $nuevoId = 'SCM-' . str_pad($numActual + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nuevoId = 'SCM-0001';
        }

        DB::connection('mysql')->table('SolicitudCambio')->insert([
            'idSolicitudCambio' => $nuevoId,
            'motivo' => $request->input('motivo'),
            'fecha' => Carbon::now()->format('Y-m-d H:i:s'),
            'estado' => 'Pendiente',
            'id_Clase' => $request->input('id_clase'),
            'id_Instructor' => $idInstructor,
        ]);

        return redirect()->route('instructor.dashboard', ['tab_activa' => 'solicitudes'])
            ->with('success', "¡Solicitud {$nuevoId} enviada al administrador correctamente!");
    }

    public function actualizarAsesoria(Request $request)
    {
        $instructor = session('usuario_activo');
        if (!$instructor) {
            return redirect('/');
        }

        $idAsesoria = $request->input('id_asesoria');
        $nuevoEstado = $request->input('estado'); // 'Realizada' o 'Cancelada'
        $observaciones = $request->input('observaciones');

        $asesoria = DB::connection('mysql')->table('Asesoria')
            ->where('id_Asesoria', $idAsesoria)
            ->first();

        if (!$asesoria) {
            return back()->withErrors(['msg' => 'Asesoría no encontrada.']);
        }

        // CANDADO DE SEGURIDAD: Si ya no está pendiente, no se puede modificar
        if ($asesoria->estado !== 'Pendiente') {
            return back()->withErrors(['msg' => 'Esta asesoría ya fue procesada y no puede ser modificada.']);
        }

        DB::connection('mysql')->table('Asesoria')
            ->where('id_Asesoria', $idAsesoria)
            ->update([
                'estado' => $nuevoEstado,
                'observaciones' => $observaciones ?: 'Sin observaciones'
            ]);

        return redirect()->route('instructor.dashboard', ['tab_activa' => 'asesorias'])
            ->with('success', "¡Asesoría {$idAsesoria} actualizada con éxito!");
    }
}