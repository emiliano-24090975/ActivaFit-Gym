<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivaFit - Módulo Administrador</title>
    <link rel="stylesheet" href="{{ asset('css/estilos1.css') }}">
    <style>
        /* Fondo fijo profesional para evitar bugs de repetición o espacios negros */
        body { 
            background-color: #1a1a1a; 
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 100px 20px; /* Margen superior e inferior de 200px como solicitaste */
        }
        
        .main-container { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: rgba(244, 246, 249, 0.98); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.4); 
        }

        .tab-btn { background: #e2e8f0; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        .tab-btn.active { background: #1a2b4c; color: white; }
        .tab-btn.disabled { background: #e2e8f0; color: #a0aec0; cursor: not-allowed; }

        .slider-horizontal {
            display: flex; gap: 20px; overflow-x: auto; padding-bottom: 15px; scroll-behavior: smooth;
        }
        .slider-horizontal::-webkit-scrollbar { height: 8px; }
        .slider-horizontal::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 4px; }
        .slider-horizontal::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }
        
        .card-item {
            min-width: 310px; max-width: 310px; flex: 0 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 3px 8px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;
        }
        .form-control {
            width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-size: 14px;
        }
        .badge-permiso {
            font-size: 11px; padding: 3px 10px; border-radius: 12px; font-weight: bold; text-transform: uppercase;
        }

        /* Contenedor con scroll interno para tablas grandes para evitar que colapse la pantalla */
        .table-scroll-container {
            max-height: 420px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 15px;
        }
        .table-scroll-container table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 13px;
            text-align: left;
        }
        .table-scroll-container thead {
            position: sticky;
            top: 0;
            background: #1a2b4c;
            color: white;
            z-index: 10;
        }
        .table-scroll-container th {
            padding: 12px;
        }
        .table-scroll-container td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="main-container">
        @if(session('success'))
            <div style="background: #e6f4ea; color: #137333; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fce8e6; color: #c5221f; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <!-- Barra Superior -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <a href="/" style="background: white; border: 1px solid #cbd5e0; padding: 10px 18px; text-decoration: none; border-radius: 6px; color: #2d3748; font-weight: bold; font-size: 14px;">← Cerrar Sesión</a>
            <span style="background: #ff7f00; color: white; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 14px;">⚡ PANEL ADMINISTRATIVO</span>
        </div>

        <!-- Cabecera Institucional -->
        <div style="background: #1a2b4c; color: white; padding: 30px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <p style="margin: 0; font-size: 12px; letter-spacing: 1.5px; color: #a0aec0; font-weight: bold;">GIMNASIO ACTIVAFIT • DIRECCIÓN</p>
                <h1 style="margin: 5px 0 0 0; font-size: 28px;">Control General y Configuración</h1>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 28px;">🛡️</div>
                <div>
                    <span style="font-size: 11px; color: #cbd5e0; display: block;">ADMINISTRADOR: <strong style="color: white;">ID: {{ $admin->id_Admin ?? 'ADM-01' }}</strong></span>
                    <h3 style="margin: 2px 0; font-size: 16px; color: #ffffff;">{{ $admin->nombre ?? 'Usuario Admin' }}</h3>
                    <span class="badge-permiso" style="background: {{ $nivelPermiso == 'Alto' ? '#ff7f00' : ($nivelPermiso == 'Medio' ? '#3182ce' : '#718096') }}; color: white;">
                        NIVEL DE ACCESO: {{ $nivelPermiso }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Pestañas según Niveles de Permiso -->
        <div style="margin-top: 25px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="tab-btn active" onclick="cambiarPestana('solicitudes', this)">📝 Solicitudes de Cambio</button>
            
            @if(in_array($nivelPermiso, ['Medio', 'Alto']))
                <button class="tab-btn" onclick="cambiarPestana('personal', this)">👥 Directivo / Personal</button>
            @else
                <button class="tab-btn disabled" title="Requiere Permiso Nivel Medio o Alto">🔒 Directivo / Personal</button>
            @endif

            @if($nivelPermiso == 'Alto')
                <button class="tab-btn" onclick="cambiarPestana('reportes', this)">📊 Reporte de Pagos / Inscripciones</button>
            @else
                <button class="tab-btn disabled" title="Requiere Permiso Nivel Alto">🔒 Reporte de Pagos</button>
            @endif
        </div>

        <!-- SECCIÓN 1: SOLICITUDES DE CAMBIO (Bandeja Pendientes / Procesadas + Purga +30 Días) -->
        <div id="seccion-solicitudes" class="seccion-panel" style="margin-top: 20px;">
            <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 2px solid #ff7f00; padding-bottom: 8px;">
                    <h3 style="margin: 0; color: #1a2b4c;">Solicitudes Enviadas por Instructores</h3>
                    <span style="font-size: 11px; color: #718096; background: #edf2f7; padding: 4px 10px; border-radius: 12px; font-weight: bold;">
                        🧹 Autodepuración: Registros > 30 días se eliminan de la BD
                    </span>
                </div>
                
                <!-- Formulario de Filtros para Solicitudes -->
                <form method="GET" action="{{ route('admin.dashboard') }}" style="margin-top: 15px; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="tab_activa" value="solicitudes">

                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">MOSTRAR BANDEJA</label>
                        <select name="estado_sol" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white; font-weight: bold;">
                            <option value="Pendiente" {{ ($estadoSolFiltro ?? '') == 'Pendiente' ? 'selected' : '' }}>⏳ Solo Pendientes por Revisar</option>
                            <option value="Procesadas" {{ ($estadoSolFiltro ?? '') == 'Procesadas' ? 'selected' : '' }}>✅ Historial de Procesadas (Aprobadas/Rechazadas)</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">ORDENAR POR</label>
                        <select name="orden_sol" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                            <option value="fecha" {{ ($ordenSol ?? '') == 'fecha' ? 'selected' : '' }}>Fecha Solicitada</option>
                            <option value="idSolicitudCambio" {{ ($ordenSol ?? '') == 'idSolicitudCambio' ? 'selected' : '' }}>ID Petición</option>
                            <option value="nombre_instructor" {{ ($ordenSol ?? '') == 'nombre_instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="estado" {{ ($ordenSol ?? '') == 'estado' ? 'selected' : '' }}>Estado</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">DIRECCIÓN</label>
                        <select name="dir_sol" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                            <option value="desc" {{ ($dirSol ?? '') == 'desc' ? 'selected' : '' }}>Descendente (Más reciente ⬇️)</option>
                            <option value="asc" {{ ($dirSol ?? '') == 'asc' ? 'selected' : '' }}>Ascendente (Más antiguo ⬆️)</option>
                        </select>
                    </div>
                </form>

                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Petición</th>
                                <th>Instructor</th>
                                <th>Clase</th>
                                <th>Motivo</th>
                                <th>Fecha Solicitada</th>
                                <th>Estado Actual</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $sol)
                                <tr>
                                    <td style="font-weight: bold;">{{ $sol->idSolicitudCambio }}</td>
                                    <td>{{ $sol->nombre_instructor }}</td>
                                    <td>{{ $sol->nombre_clase }}</td>
                                    <td style="color: #4a5568;">{{ $sol->motivo }}</td>
                                    <td>{{ $sol->fecha }}</td>
                                    <td>
                                        @if($sol->estado == 'Aprobada')
                                            <span style="background: #e6f4ea; color: #137333; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Aprobada</span>
                                        @elseif($sol->estado == 'Rechazada')
                                            <span style="background: #fce8e6; color: #c5221f; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Rechazada</span>
                                        @else
                                            <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Pendiente</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($sol->vigente && $sol->estado == 'Pendiente')
                                            <div style="display: flex; gap: 5px; justify-content: center;">
                                                <form action="{{ route('admin.responder_solicitud') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_solicitud" value="{{ $sol->idSolicitudCambio }}">
                                                    <input type="hidden" name="estado" value="Aprobada">
                                                    <button type="submit" style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 11px;">Aprobar</button>
                                                </form>
                                                <form action="{{ route('admin.responder_solicitud') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_solicitud" value="{{ $sol->idSolicitudCambio }}">
                                                    <input type="hidden" name="estado" value="Rechazada">
                                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 11px;">Rechazar</button>
                                                </form>
                                            </div>
                                        @else
                                            <span style="color: #a0aec0; font-size: 11px; font-style: italic;">Sin acciones pendientes</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 30px; text-align: center; color: #718096; font-weight: bold; font-size: 15px;">
                                        📋 Sin solicitudes por revisar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: DIRECTORIO DE PERSONAL (Medio, Alto) -->
        @if(in_array($nivelPermiso, ['Medio', 'Alto']))
            <div id="seccion-personal" class="seccion-panel" style="margin-top: 20px; display: none;">
                <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff7f00; padding-bottom: 8px;">
                        <h3 style="margin: 0; color: #1a2b4c;">Directorio de Personal del Gimnasio</h3>
                        <div>
                            <button class="tab-btn active" id="btn-rec" onclick="mostrarPersonal('recepcionistas', this)">Recepcionistas</button>
                            <button class="tab-btn" id="btn-ins" onclick="mostrarPersonal('instructores', this)">Instructores</button>
                        </div>
                    </div>

                    <!-- TABLA RECEPCIONISTAS -->
                    <div id="div-recepcionistas" style="margin-top: 20px;">
                        <h4 style="color: #4a5568; margin-bottom: 10px;">📋 Personal de Recepción Registrado</h4>
                        <div class="slider-horizontal">
                            @forelse($recepcionistas as $rec)
                                <div class="card-item">
                                    <div style="background: #1a2b4c; color: white; padding: 12px 15px;">
                                        <h3 style="margin: 0; font-size: 15px;">{{ $rec->id_Recepcionista }}</h3>
                                    </div>
                                    <div style="padding: 15px;">
                                        <p style="margin: 5px 0; font-size: 14px; font-weight: bold; color: #2d3748;">{{ $rec->nombre }}</p>
                                        <p style="margin: 5px 0; font-size: 13px; color: #718096;">Turno: <strong>{{ strtoupper($rec->turno) }}</strong></p>
                                    </div>
                                </div>
                            @empty
                                <p style="color: #666;">No hay recepcionistas registrados.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- TABLA INSTRUCTORES -->
                    <div id="div-instructores" style="margin-top: 20px; display: none;">
                        <h4 style="color: #4a5568; margin-bottom: 10px;">💪 Equipo de Instructores Registrados</h4>
                        <div class="slider-horizontal">
                            @forelse($instructores as $ins)
                                <div class="card-item">
                                    <div style="background: #ff7f00; color: white; padding: 12px 15px;">
                                        <h3 style="margin: 0; font-size: 15px;">{{ $ins->id_Instructor }}</h3>
                                    </div>
                                    <div style="padding: 15px;">
                                        <p style="margin: 5px 0; font-size: 14px; font-weight: bold; color: #2d3748;">{{ $ins->nombre }}</p>
                                        <p style="margin: 5px 0; font-size: 13px; color: #718096;">Especialidad: <strong>{{ $ins->especialidad }}</strong></p>
                                    </div>
                                </div>
                            @empty
                                <p style="color: #666;">No hay instructores registrados.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- SECCIÓN 3: REPORTE DE PAGOS E INSCRIPCIONES (Solo Alto) -->
        @if($nivelPermiso == 'Alto')
            <div id="seccion-reportes" class="seccion-panel" style="margin-top: 20px; display: none;">
                <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 2px solid #ff7f00; padding-bottom: 12px;">
                        <div>
                            <h3 style="margin: 0; color: #1a2b4c;">📊 Reporte Financiero de Pagos e Inscripciones</h3>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #718096;">Consulta de recaudación por periodos semestrales y exportación a Excel.</p>
                        </div>
                        
                        @if(count($pagos) > 0)
                            <a href="{{ route('admin.exportar_excel', ['semestre' => $semestreFiltro, 'anio' => $anioFiltro]) }}" style="background: #107c41; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                                📥 Descargar Excel (.csv)
                            </a>
                        @else
                            <span style="background: #cbd5e0; color: #718096; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 13px; cursor: not-allowed;" title="No hay datos disponibles para exportar">
                                🔒 Descargar Excel (.csv) [Apagado]
                            </span>
                        @endif
                    </div>

                    <!-- Filtro con Persistencia de Pestaña -->
                    <form method="GET" action="{{ route('admin.dashboard') }}" style="margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                        <input type="hidden" name="tab_activa" value="reportes">

                        <div>
                            <label style="font-size: 12px; font-weight: bold; color: #4a5568;">SEMESTRE</label>
                            <select name="semestre" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                                <option value="1" {{ $semestreFiltro == '1' ? 'selected' : '' }}>Semestre 1 (Enero - Junio)</option>
                                <option value="2" {{ $semestreFiltro == '2' ? 'selected' : '' }}>Semestre 2 (Julio - Diciembre)</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: bold; color: #4a5568;">AÑO</label>
                            <select name="anio" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                                @foreach($aniosDisponibles as $anioOpt)
                                    <option value="{{ $anioOpt }}" {{ $anioFiltro == $anioOpt ? 'selected' : '' }}>{{ $anioOpt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: bold; color: #4a5568;">ORDENAR POR</label>
                            <select name="orden_por" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                                <option value="id_Cliente" {{ $ordenPor == 'id_Cliente' ? 'selected' : '' }}>ID Cliente</option>
                                <option value="nombre_cliente" {{ $ordenPor == 'nombre_cliente' ? 'selected' : '' }}>Nombre de Cliente</option>
                                <option value="nombre_plan" {{ $ordenPor == 'nombre_plan' ? 'selected' : '' }}>Membresía</option>
                                <option value="costo" {{ $ordenPor == 'costo' ? 'selected' : '' }}>Monto Abonado</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: bold; color: #4a5568;">DIRECCIÓN</label>
                            <select name="direccion" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                                <option value="asc" {{ $ordenDireccion == 'asc' ? 'selected' : '' }}>Menor a Mayor (Ascendente ⬆️)</option>
                                <option value="desc" {{ $ordenDireccion == 'desc' ? 'selected' : '' }}>Mayor a Menor (Descendente ⬇️)</option>
                            </select>
                        </div>
                    </form>

                    <!-- Lista de Pagos con Scroll Interno Controlado -->
                    <div class="table-scroll-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Membresía</th>
                                    <th>Monto Abonado</th>
                                    <th>Fecha Contratación</th>
                                    <th>Fecha Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagos as $p)
                                    <tr>
                                        <td style="font-weight: bold;">{{ $p->id_Cliente }}</td>
                                        <td>{{ $p->nombre_cliente }}</td>
                                        <td><span style="background: #edf2f7; color: #2d3748; padding: 3px 8px; border-radius: 4px; font-weight: bold;">{{ $p->nombre_plan }}</span></td>
                                        <td style="color: #28a745; font-weight: bold;">${{ number_format($p->costo, 2) }} MXN</td>
                                        <td style="color: #4a5568;">{{ $p->fecha_contratacion }}</td>
                                        <td style="color: #718096;">{{ $p->fecha_vencimiento }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 30px; text-align: center; color: #e53e3e; font-weight: bold; font-size: 15px;">
                                            ⚠️ Sin pagos de clientes en este semestre.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <script>
        function cambiarPestana(seccionId, boton) {
            document.querySelectorAll('.seccion-panel').forEach(panel => {
                panel.style.display = 'none';
            });
            document.getElementById('seccion-' + seccionId).style.display = 'block';
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (!btn.classList.contains('disabled')) {
                    btn.classList.remove('active');
                }
            });
            if (boton) {
                boton.classList.add('active');
            }
        }

        function mostrarPersonal(tipo, btn) {
            if (tipo === 'recepcionistas') {
                document.getElementById('div-recepcionistas').style.display = 'block';
                document.getElementById('div-instructores').style.display = 'none';
                document.getElementById('btn-rec').classList.add('active');
                document.getElementById('btn-ins').classList.remove('active');
            } else {
                document.getElementById('div-recepcionistas').style.display = 'none';
                document.getElementById('div-instructores').style.display = 'block';
                document.getElementById('btn-rec').classList.remove('active');
                document.getElementById('btn-ins').classList.add('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var tabActiva = "{{ $tabActiva ?? 'solicitudes' }}";
            var btnTarget = document.querySelector(".tab-btn[onclick*='" + tabActiva + "']");
            cambiarPestana(tabActiva, btnTarget);
        });
    </script>
</body>
</html>