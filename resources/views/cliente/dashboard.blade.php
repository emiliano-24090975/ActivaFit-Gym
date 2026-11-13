<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivaFit - Portal de Clientes</title>
    <link rel="stylesheet" href="{{ asset('css/estilos1.css') }}">
    <style>
        body { 
            background-color: #1a1a1a; 
            background-attachment: fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 50px 20px; 
        }
        .main-container { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: rgba(244, 246, 249, 0.98); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
        }
        .tab-btn { background: #e2e8f0; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        .tab-btn.active { background: #1a2b4c; color: white; }

        .clases-slider {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 15px;
            scroll-behavior: smooth;
        }
        .clases-slider::-webkit-scrollbar { height: 8px; }
        .clases-slider::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 4px; }
        .clases-slider::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }
        .clase-card {
            min-width: 330px;
            max-width: 330px;
            flex: 0 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .form-control {
            width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-size: 14px;
        }

        /* Tabla con scroll interno mantenido */
        .table-scroll-container {
            max-height: 380px;
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
            z-index: 5;
        }
        .table-scroll-container th, .table-scroll-container td {
            padding: 12px;
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
            <span style="background: #ff7f00; color: white; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 14px;">⚡ MÓDULO DE CLIENTES</span>
        </div>

        <!-- Cabecera Azul Institucional -->
        <div style="background: #1a2b4c; color: white; padding: 30px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <p style="margin: 0; font-size: 12px; letter-spacing: 1.5px; color: #a0aec0; font-weight: bold;">GIMNASIO ACTIVAFIT • PORTAL DE CLIENTES</p>
                <h1 style="margin: 5px 0 0 0; font-size: 28px;">Panel de Control</h1>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 28px;">👤</div>
                <div>
                    <span style="font-size: 11px; color: #cbd5e0; display: block;">CLIENTE: <strong style="color: white;">ID: {{ $cliente->id_Cliente ?? 'CLI-0001' }}</strong></span>
                    <h3 style="margin: 2px 0; font-size: 16px; color: #ffffff;">{{ $cliente->nombre ?? 'Usuario' }}</h3>
                    <span style="background: #28a745; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: bold;">● VIGENTE</span>
                </div>
            </div>
        </div>

        <!-- Pestañas de Navegación -->
        <div style="margin-top: 25px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="tab-btn active" onclick="cambiarPestana('catalogo', this)">📚 Catálogo de Clases</button>
            <button class="tab-btn" onclick="cambiarPestana('mis-clases', this)">🎟️ Mis Clases Inscritas</button>
            <button class="tab-btn" onclick="cambiarPestana('asesorias', this)">📋 Mis Asesorías</button>
        </div>

        <!-- SECCIÓN 1: CATÁLOGO Y FILTROS -->
        <div id="seccion-catalogo" class="seccion-panel" style="margin-top: 20px;">
            <form method="GET" action="{{ route('cliente.dashboard') }}" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                <h4 style="margin-top: 0; color: #ff7f00; font-size: 14px;">▼ FILTROS POR DISCIPLINA / APTITUD</h4>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 240px;">
                        <label style="font-size: 12px; color: #4a5568; font-weight: bold; display: block; margin-bottom: 5px;">ESPECIALIDAD</label>
                        <select name="disciplina" onchange="this.form.submit()" style="width: 100%; padding: 11px; border: 1px solid #cbd5e0; border-radius: 6px; background: #fff;">
                            <option value="Todas" {{ ($disciplinaFiltro == 'Todas') ? 'selected' : '' }}>Todas las Disciplinas</option>
                            <option value="Zumba" {{ ($disciplinaFiltro == 'Zumba') ? 'selected' : '' }}>Zumba</option>
                            <option value="Calistenia" {{ ($disciplinaFiltro == 'Calistenia') ? 'selected' : '' }}>Calistenia</option>
                            <option value="Crossfit" {{ ($disciplinaFiltro == 'Crossfit') ? 'selected' : '' }}>Crossfit</option>
                            <option value="Pilates" {{ ($disciplinaFiltro == 'Pilates') ? 'selected' : '' }}>Pilates</option>
                            <option value="Ciclismo indoor" {{ ($disciplinaFiltro == 'Ciclismo indoor') ? 'selected' : '' }}>Ciclismo indoor</option>
                            <option value="Yoga" {{ ($disciplinaFiltro == 'Yoga') ? 'selected' : '' }}>Yoga</option>
                            <option value="Boxeo" {{ ($disciplinaFiltro == 'Boxeo') ? 'selected' : '' }}>Boxeo</option>
                            <option value="Spinning" {{ ($disciplinaFiltro == 'Spinning') ? 'selected' : '' }}>Spinning</option>
                            <option value="TRX" {{ ($disciplinaFiltro == 'TRX') ? 'selected' : '' }}>TRX</option>
                            <option value="Entrenamiento Funcional" {{ ($disciplinaFiltro == 'Entrenamiento Funcional') ? 'selected' : '' }}>Entrenamiento Funcional</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 240px;">
                        <label style="font-size: 12px; color: #4a5568; font-weight: bold; display: block; margin-bottom: 5px;">FECHA DE CLASE</label>
                        <input type="date" name="fecha" value="{{ $fechaFiltro ?? date('Y-m-d') }}" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box;">
                    </div>
                </div>
            </form>

            <div style="margin-top: 25px;">
                <h3 style="color: #1a2b4c; font-size: 18px; margin-bottom: 15px;">📅 Clases Disponibles (Desliza para ver más)</h3>
                
                <div class="clases-slider">
                    @forelse($clases as $clase)
                        <div class="clase-card">
                            <div style="background: #1a2b4c; color: white; padding: 15px 20px;">
                                <h3 style="margin: 0; font-size: 17px;">{{ $clase->nombre }}</h3>
                                <span style="font-size: 11px; color: #cbd5e0; letter-spacing: 1px; font-weight: bold;">{{ strtoupper($clase->especialidad) }}</span>
                            </div>
                            <div style="padding: 18px;">
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">👨‍🏫 Instructor: <strong>{{ $clase->nombre_instructor }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">🕒 Horario: <strong>{{ $clase->horario }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">📅 Días: <strong>{{ $clase->dias_semana }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">🏢 Salón: <strong>{{ $clase->salon }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">👥 Inscritos: <strong>{{ $clase->inscritos_actuales }} / {{ $clase->cupo_maximo }}</strong></p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                                    @if($clase->usuario_inscrito)
                                        <span style="background: #e1effe; color: #1e429f; padding: 6px 10px; border-radius: 6px; font-weight: bold; font-size: 12px;">✔️ INSCRITO</span>
                                        <button type="button" style="background: #cbd5e0; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: not-allowed; font-size: 13px;" disabled>YA APARTADO</button>
                                    @elseif($clase->esta_llena)
                                        <span style="background: #fce8e6; color: #c5221f; padding: 6px 10px; border-radius: 6px; font-weight: bold; font-size: 12px;">⚠️ LLENO</span>
                                        <button type="button" style="background: #cbd5e0; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: not-allowed; font-size: 13px;" disabled>SIN CUPO</button>
                                    @else
                                        <span style="background: #e6f4ea; color: #137333; padding: 6px 10px; border-radius: 6px; font-weight: bold; font-size: 12px;">Disponibles</span>
                                        <form action="{{ route('cliente.reservar') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_clase" value="{{ $clase->id_Clase }}">
                                            <input type="hidden" name="fecha" value="{{ $fechaFiltro ?? date('Y-m-d') }}">
                                            <button type="submit" style="background: #ff7f00; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px;">RESERVAR</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #666; padding: 20px;">No hay clases disponibles para la especialidad seleccionada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: MIS CLASES INSCRITAS -->
        <div id="seccion-mis-clases" class="seccion-panel" style="margin-top: 20px; display: none;">
            <h3 style="color: #1a2b4c; font-size: 18px;">🎟️ Historial de Mis Clases Reservadas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(48%, 1fr)); gap: 20px; margin-top: 15px;">
                @forelse($misInscripciones as $inscripcion)
                    <div style="background: white; border-radius: 8px; padding: 20px; border-left: 5px solid #28a745; box-shadow: 0 3px 8px rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0 0 10px 0; color: #1a2b4c;">{{ $inscripcion->nombre }}</h3>
                            <p style="margin: 4px 0; font-size: 13px; color: #666;">👨‍🏫 Instructor: <strong>{{ $inscripcion->nombre_instructor }}</strong></p>
                            <p style="margin: 4px 0; font-size: 13px; color: #666;">📅 Fecha de reserva: <strong>{{ $inscripcion->fecha_Reserva }}</strong></p>
                            <p style="margin: 4px 0; font-size: 13px; color: #666;">🕒 Horario: <strong>{{ $inscripcion->horario }} ({{ $inscripcion->dias_semana }})</strong></p>
                            <p style="margin: 4px 0; font-size: 13px; color: #666;">🏢 Salón: <strong>{{ $inscripcion->salon }}</strong></p>
                            <span style="display: inline-block; margin-top: 10px; background: #e6f4ea; color: #137333; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;">Estado: {{ $inscripcion->estado }}</span>
                        </div>
                        
                        <div>
                            <form action="{{ route('cliente.cancelar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_reserva" value="{{ $inscripcion->id_Reserva }}">
                                <button type="submit" onclick="return confirm('¿Estás seguro de que deseas darte de baja de esta clase?')" style="background: #dc3545; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 12px;">SALIR</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p style="color: #666;">Aún no tienes clases reservadas. ¡Explora el catálogo y aparta tu lugar!</p>
                @endforelse
            </div>
        </div>

        <!-- SECCIÓN 3: MÓDULO DE ASESORÍAS (CLIENTE) -->
        <div id="seccion-asesorias" class="seccion-panel" style="margin-top: 20px; display: none;">
            <!-- Formulario de Agendar -->
            <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                <h3 style="margin-top: 0; color: #1a2b4c; border-bottom: 2px solid #ff7f00; padding-bottom: 8px;">Solicitar Nueva Asesoría Personalizada</h3>
                
                <form action="{{ route('cliente.registrar_asesoria') }}" method="POST" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; margin-top: 15px;">
                    @csrf
                    <div style="flex: 1; min-width: 250px;">
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">SELECCIONAR INSTRUCTOR</label>
                        <select name="id_Instructor" class="form-control" required style="background: white; margin-top: 4px;">
                            <option value="">-- Elige un Instructor --</option>
                            @foreach($instructores as $ins)
                                <option value="{{ $ins->id_Instructor }}">{{ $ins->nombre }} ({{ $ins->especialidad }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 200px;">
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">FECHA Y HORA DE CITA</label>
                        <input type="datetime-local" name="fecha_hora" class="form-control" required style="background: white; margin-top: 4px;">
                    </div>

                    <div>
                        <button type="submit" style="background: #ff7f00; color: white; border: none; padding: 11px 22px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            📅 Agendar Asesoría
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de Asesorías -->
            <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04); margin-top: 20px;">
                <h3 style="margin-top: 0; color: #1a2b4c; border-bottom: 2px solid #ff7f00; padding-bottom: 8px;">Historial de Mis Asesorías</h3>

                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Asesoría</th>
                                <th>Instructor Asignado</th>
                                <th>Fecha y Hora</th>
                                <th>Estado Actual</th>
                                <th>Observaciones del Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asesorias as $asr)
                                <tr>
                                    <td style="font-weight: bold;">{{ $asr->id_Asesoria }}</td>
                                    <td>{{ $asr->nombre_instructor }}</td>
                                    <td>{{ $asr->fecha_hora }}</td>
                                    <td>
                                        @if($asr->estado == 'Realizada')
                                            <span style="background: #e6f4ea; color: #137333; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Realizada</span>
                                        @elseif($asr->estado == 'Cancelada')
                                            <span style="background: #fce8e6; color: #c5221f; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Cancelada</span>
                                        @else
                                            <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Pendiente</span>
                                        @endif
                                    </td>
                                    <td style="color: #4a5568; font-style: italic;">{{ $asr->observaciones }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 30px; text-align: center; color: #718096; font-weight: bold; font-size: 14px;">
                                        📋 Aún no has agendado asesorías personalizadas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        function cambiarPestana(seccionId, boton) {
            document.querySelectorAll('.seccion-panel').forEach(panel => {
                panel.style.display = 'none';
            });
            document.getElementById('seccion-' + seccionId).style.display = 'block';
            
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            if (boton) {
                boton.classList.add('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var tabActiva = "{{ $tabActiva ?? 'catalogo' }}";
            var btnTarget = document.querySelector(".tab-btn[onclick*='" + tabActiva + "']");
            cambiarPestana(tabActiva, btnTarget);
        });
    </script>
</body>
</html>