<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $admin = session('usuario_activo');
        if (!$admin || session('tipo') !== 'Admin') {
            return redirect('/')->withErrors(['codigo' => 'Por favor, inicie sesión como Administrador.']);
        }

        $admin = (object) $admin;
        
        // Obtener el identificador del administrador desde la sesión (id_Admin)
        $idAdmin = $admin->id_Admin ?? $admin->codigo ?? $admin->id ?? null;
        $nivelPermiso = 'Bajo'; // Valor por defecto de seguridad

        if ($idAdmin) {
            try {
                // Consultar directamente la tabla correcta en la BD: `administrador` con la columna `nivelPermisos`
                $adminDb = DB::connection('mysql')->table('administrador')
                    ->where('id_Admin', $idAdmin)
                    ->orWhere('correo', $idAdmin)
                    ->first();

                if ($adminDb) {
                    // Leer la columna exacta `nivelPermisos` de tu base de datos
                    $nivelBruto = $adminDb->nivelPermisos ?? $adminDb->nivel_permiso ?? $adminDb->nivel ?? 'Bajo';
                    $nivelPermiso = ucfirst(strtolower(trim($nivelBruto)));
                }
            } catch (\Exception $e) {
                $nivelPermiso = $admin->nivelPermisos ?? $admin->nivel_permiso ?? $admin->nivel ?? 'Bajo';
            }
        }
        
        // Asegurar que el nivel sea válido
        if (!in_array($nivelPermiso, ['Bajo', 'Medio', 'Alto'])) {
            $nivelPermiso = 'Bajo';
        }

        // 1. LIMPIEZA AUTOMÁTICA DE REGISTROS ANTIGUOS (+30 DÍAS)
        $fechaLimitePurga = Carbon::now()->subDays(30)->format('Y-m-d H:i:s');
        DB::connection('mysql')->table('SolicitudCambio')
            ->where('fecha', '<', $fechaLimitePurga)
            ->delete();

        // 2. MÓDULO SOLICITUDES DE CAMBIO (Accesible para Nivel Bajo, Medio y Alto)
        $estadoSolFiltro = $request->input('estado_sol', 'Pendiente'); 
        $ordenSol = $request->input('orden_sol', 'fecha'); 
        $dirSol = $request->input('dir_sol', 'desc');

        $solicitudesQuery = DB::connection('mysql')->table('SolicitudCambio')
            ->join('clase', 'SolicitudCambio.id_Clase', '=', 'clase.id_Clase')
            ->join('instructor', 'SolicitudCambio.id_Instructor', '=', 'instructor.id_Instructor')
            ->select('SolicitudCambio.*', 'clase.nombre as nombre_clase', 'instructor.nombre as nombre_instructor');

        if ($estadoSolFiltro === 'Pendiente') {
            $solicitudesQuery->where('SolicitudCambio.estado', 'Pendiente');
        } else {
            $solicitudesQuery->whereIn('SolicitudCambio.estado', ['Aprobada', 'Rechazada']);
        }

        if ($ordenSol === 'nombre_instructor') {
            $solicitudesQuery->orderBy('instructor.nombre', $dirSol);
        } else {
            $solicitudesQuery->orderBy('SolicitudCambio.' . $ordenSol, $dirSol);
        }

        $solicitudes = $solicitudesQuery->get();

        foreach ($solicitudes as $sol) {
            $sol->vigente = Carbon::parse($sol->fecha)->greaterThanOrEqualTo(Carbon::today());
        }

        // 3. MÓDULO PERSONAL (Restringido estrictamente a Nivel Medio y Alto)
        $filtroPersonal = $request->input('tipo_personal', 'Recepcionista');
        $recepcionistas = [];
        $instructores = [];

        if (in_array($nivelPermiso, ['Medio', 'Alto'])) {
            $recepcionistas = DB::connection('mysql')->table('Recepcionista')->get();
            $instructores = DB::connection('mysql')->table('Instructor')->get();
        }

        // 4. MÓDULO REPORTE FINANCIERO (Restringido estrictamente a Nivel Alto)
        $pagos = [];
        $totalRecaudado = 0;
        $semestreFiltro = $request->input('semestre', '1'); 
        $anioFiltro = $request->input('anio', date('Y'));
        
        $ordenPor = $request->input('orden_por', 'id_Cliente'); 
        $ordenDireccion = $request->input('direccion', 'asc'); 

        $aniosDisponibles = [date('Y')];

        if ($nivelPermiso === 'Alto') {
            $rawFechas = DB::connection('mysql')->table('Cliente')->pluck('fecha_vencimiento');
            $yearsSet = [(int)date('Y'), 2027];
            foreach ($rawFechas as $fVenc) {
                if ($fVenc) {
                    $y = (int)Carbon::parse($fVenc)->year;
                    $yearsSet[] = $y;
                    $yearsSet[] = $y - 1;
                }
            }
            $aniosDisponibles = array_unique($yearsSet);
            rsort($aniosDisponibles);

            $mesInicio = ($semestreFiltro == '1') ? 1 : 7;
            $mesFin = ($semestreFiltro == '1') ? 6 : 12;

            $fechaInicioSemestre = Carbon::createFromDate($anioFiltro, $mesInicio, 1)->startOfDay();
            $fechaFinSemestre = Carbon::createFromDate($anioFiltro, $mesFin, 1)->endOfMonth()->endOfDay();

            $pagosCrudos = DB::connection('mysql')->table('Cliente')
                ->join('Membresia', 'Cliente.id_Membresia', '=', 'Membresia.id_Membresia')
                ->select(
                    'Cliente.id_Cliente',
                    'Cliente.nombre as nombre_cliente',
                    'Membresia.nombre_plan',
                    'Membresia.duracion_dias',
                    'Membresia.costo',
                    'Cliente.fecha_vencimiento'
                )
                ->get();

            foreach ($pagosCrudos as $p) {
                $duracionDias = $p->duracion_dias ?? 30;
                $fechaContratacion = Carbon::parse($p->fecha_vencimiento)->subDays($duracionDias);

                if ($fechaContratacion->between($fechaInicioSemestre, $fechaFinSemestre)) {
                    $p->fecha_contratacion = $fechaContratacion->format('Y-m-d');
                    $pagos[] = $p;
                    $totalRecaudado += $p->costo;
                }
            }

            $pagosCollection = collect($pagos);
            if ($ordenDireccion === 'desc') {
                $pagos = $pagosCollection->sortByDesc($ordenPor)->values()->all();
            } else {
                $pagos = $pagosCollection->sortBy($ordenPor)->values()->all();
            }
        }

        $tabActiva = $request->input('tab_activa', 'solicitudes');

        return view('admin.dashboard', compact(
            'admin',
            'nivelPermiso',
            'solicitudes',
            'filtroPersonal',
            'recepcionistas',
            'instructores',
            'pagos',
            'totalRecaudado',
            'semestreFiltro',
            'anioFiltro',
            'aniosDisponibles',
            'ordenPor',
            'ordenDireccion',
            'ordenSol',
            'dirSol',
            'estadoSolFiltro',
            'tabActiva'
        ));
    }

    public function responderSolicitud(Request $request)
    {
        $admin = session('usuario_activo');
        if (!$admin) {
            return redirect('/');
        }
        
        $idSolicitud = $request->input('id_solicitud');
        $nuevoEstado = $request->input('estado'); 

        $solicitud = DB::connection('mysql')->table('SolicitudCambio')
            ->where('idSolicitudCambio', $idSolicitud)
            ->first();

        if (!$solicitud) {
            return back()->withErrors(['msg' => 'Solicitud no encontrada.']);
        }

        if (Carbon::parse($solicitud->fecha)->lt(Carbon::today())) {
            return back()->withErrors(['msg' => 'No es posible modificar esta solicitud porque su fecha ya expiró.']);
        }

        DB::connection('mysql')->table('SolicitudCambio')
            ->where('idSolicitudCambio', $idSolicitud)
            ->update(['estado' => $nuevoEstado]);

        return back()->with('success', "¡Solicitud {$idSolicitud} marcada como {$nuevoEstado}!");
    }

    public function descargarReporteExcel(Request $request)
    {
        $admin = session('usuario_activo');
        if (!$admin) {
            return redirect('/');
        }

        $adminObj = (object) $admin;
        $idAdmin = $adminObj->id_Admin ?? null;
        
        $nivel = 'Bajo';
        if ($idAdmin) {
            $dbAdmin = DB::connection('mysql')->table('administrador')->where('id_Admin', $idAdmin)->first();
            $nivel = $dbAdmin->nivelPermisos ?? 'Bajo';
        }

        if ($nivel !== 'Alto') {
            return redirect('/admin')->withErrors(['msg' => 'Acceso denegado. Se requiere Nivel de Permiso Alto para exportar reportes.']);
        }

        $semestre = $request->input('semestre', '1');
        $anio = $request->input('anio', date('Y'));

        $mesInicio = ($semestre == '1') ? 1 : 7;
        $mesFin = ($semestre == '1') ? 6 : 12;
        $fechaInicioSemestre = Carbon::createFromDate($anio, $mesInicio, 1)->startOfDay();
        $fechaFinSemestre = Carbon::createFromDate($anio, $mesFin, 1)->endOfMonth()->endOfDay();

        $pagosCrudos = DB::connection('mysql')->table('Cliente')
            ->join('Membresia', 'Cliente.id_Membresia', '=', 'Membresia.id_Membresia')
            ->select('Cliente.id_Cliente', 'Cliente.nombre', 'Membresia.nombre_plan', 'Membresia.duracion_dias', 'Membresia.costo', 'Cliente.fecha_vencimiento')
            ->get();

        $pagos = [];
        foreach ($pagosCrudos as $p) {
            $duracion = $p->duracion_dias ?? 30;
            $fContratacion = Carbon::parse($p->fecha_vencimiento)->subDays($duracion);
            if ($fContratacion->between($fechaInicioSemestre, $fechaFinSemestre)) {
                $p->fecha_contratacion = $fContratacion->format('Y-m-d');
                $pagos[] = $p;
            }
        }

        $filename = "Reporte_Pagos_ActivaFit_{$anio}_S{$semestre}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['ID Cliente', 'Nombre Cliente', 'Membresia', 'Monto ($ MXN)', 'Fecha Contratacion', 'Fecha Vencimiento']);

        foreach ($pagos as $p) {
            fputcsv($output, [
                $p->id_Cliente,
                $p->nombre,
                $p->nombre_plan,
                '$' . number_format($p->costo, 2),
                $p->fecha_contratacion,
                $p->fecha_vencimiento
            ]);
        }

        fclose($output);
        exit;
    }
}