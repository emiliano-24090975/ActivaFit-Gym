<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivaFit - Módulo de Instructores</title>
    <link rel="stylesheet" href="{{ asset('css/estilos1.css') }}">
    <style>
        body { background-color: #1a1a1a; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; }
        .main-container { max-width: 1100px; margin: 0 auto; background: rgba(244, 246, 249, 0.98); padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .tab-btn { background: #e2e8f0; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        .tab-btn.active { background: #1a2b4c; color: white; }

        .slider-horizontal {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 15px;
            scroll-behavior: smooth;
        }
        .slider-horizontal::-webkit-scrollbar { height: 8px; }
        .slider-horizontal::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 4px; }
        .slider-horizontal::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }
        
        .card-item {
            min-width: 310px;
            max-width: 310px;
            flex: 0 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .form-control {
            width: 100%; padding: 8px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-size: 13px;
        }

        /* Contenedor con scroll vertical para evitar sobrecarga */
        .table-scroll-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 15px;
        }
        table { width: 100%; border-collapse: collapse; background: white; font-size: 13px; text-align: left; }
        thead { position: sticky; top: 0; background: #1a2b4c; color: white; z-index: 5; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
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
            <span style="background: #ff7f00; color: white; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 14px;">⚡ MÓDULO DE INSTRUCTORES</span>
        </div>

        <!-- Cabecera Azul Institucional -->
        <div style="background: #1a2b4c; color: white; padding: 30px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <p style="margin: 0; font-size: 12px; letter-spacing: 1.5px; color: #a0aec0; font-weight: bold;">GIMNASIO ACTIVAFIT • INSTRUCTOR</p>
                <h1 style="margin: 5px 0 0 0; font-size: 28px;">Panel del Instructor</h1>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 28px;">💪</div>
                <div>
                    <span style="font-size: 11px; color: #cbd5e0; display: block;">INSTRUCTOR: <strong style="color: white;">ID: {{ $instructor->id_Instructor ?? 'INS-0001' }}</strong></span>
                    <h3 style="margin: 2px 0; font-size: 16px; color: #ffffff;">{{ $instructor->nombre ?? 'Profesor' }}</h3>
                    <span style="background: #ff7f00; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: bold;">ESPECIALIDAD: {{ strtoupper($instructor->especialidad ?? 'GENERAL') }}</span>
                </div>
            </div>
        </div>

        <!-- Pestañas de Navegación en Memoria -->
        <div style="margin-top: 25px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="tab-btn active" onclick="cambiarPestana('mis-clases', this)">🏋️ Mis Clases Asignadas</button>
            <button class="tab-btn" onclick="cambiarPestana('solicitudes', this)">📝 Solicitar Cambio / Cancelación</button>
            <button class="tab-btn" onclick="cambiarPestana('asesorias', this)">🤝 Asesorías con Clientes</button>
        </div>

        <!-- SECCIÓN 1: MIS CLASES ASIGNADAS (SLIDER) -->
        <div id="seccion-mis-clases" class="seccion-panel" style="margin-top: 20px;">
            <h3 style="color: #1a2b4c; font-size: 18px; margin-bottom: 15px;">📅 Clases a tu cargo (Desliza para ver más)</h3>
            
            <div class="slider-horizontal">
                @forelse($clasesImpartidas as $clase)
                    <div class="card-item">
                        <div style="background: #1a2b4c; color: white; padding: 15px 20px;">
                            <h3 style="margin: 0; font-size: 17px;">{{ $clase->nombre }}</h3>
                            <span style="font-size: 11px; color: #cbd5e0; letter-spacing: 1px; font-weight: bold;">SALÓN: {{ $clase->salon }}</span>
                        </div>
                        <div style="padding: 18px;">
                            <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">🕒 Horario: <strong>{{ $clase->horario }}</strong></p>
                            <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">📅 Días: <strong>{{ $clase->dias_semana }}</strong></p>
                            <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">👥 Alumnos Inscritos: <strong>{{ $clase->inscritos_actuales }} / {{ $clase->cupo_maximo }}</strong></p>
                            
                            <div style="margin-top: 20px; text-align: center;">
                                <span style="background: #e6f4ea; color: #137333; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 12px; display: inline-block;">Activa en Agenda</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #666; padding: 20px;">Actualmente no tienes clases asignadas a tu cargo en el sistema.</p>
                @endforelse
            </div>
        </div>

        <!-- SECCIÓN 2: SOLICITUD DE CAMBIO / CANCELACIÓN -->
        <div id="seccion-solicitudes" class="seccion-panel" style="margin-top: 20px; display: none;">
            <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                <h3 style="margin-top: 0; color: #1a2b4c; border-bottom: 2px solid #ff7f00; padding-bottom: 8px;">Enviar Solicitud de Cambio o Cancelación</h3>
                
                <form action="{{ route('instructor.solicitar_cambio') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
                        <div>
                            <label style="font-size: 13px; font-weight: bold; color: #4a5568; display: block; margin-bottom: 5px;">SELECCIONAR CLASE</label>
                            <select name="id_clase" required class="form-control" style="background: #fff;">
                                <option value="" disabled selected>Elige una de tus clases...</option>
                                @foreach($clasesImpartidas as $c)
                                    <option value="{{ $c->id_Clase }}">{{ $c->nombre }} ({{ $c->horario }} - {{ $c->dias_semana }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <label style="font-size: 13px; font-weight: bold; color: #4a5568; display: block; margin-bottom: 5px;">MOTIVO DEL CAMBIO O CANCELACIÓN</label>
                        <textarea name="motivo" rows="4" required placeholder="Explica detalladamente el motivo de la modificación de horario o cancelación temporal..." class="form-control"></textarea>
                    </div>

                    <div style="margin-top: 25px; text-align: right;">
                        <button type="submit" style="background: #ff7f00; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">
                            ENVIAR SOLICITUD AL ADMIN
                        </button>
                    </div>
                </form>

                <!-- Historial de Solicitudes Enviadas -->
                <h4 style="margin-top: 35px; color: #1a2b4c; border-bottom: 1px solid #cbd5e0; padding-bottom: 6px;">Historial de Solicitudes Enviadas</h4>
                <div style="margin-top: 15px; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background: white; font-size: 13px; text-align: left;">
                        <thead>
                            <tr style="background: #1a2b4c; color: white;">
                                <th style="padding: 10px;">ID Solicitud</th>
                                <th style="padding: 10px;">Clase</th>
                                <th style="padding: 10px;">Motivo</th>
                                <th style="padding: 10px;">Fecha Envío</th>
                                <th style="padding: 10px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $sol)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 10px; font-weight: bold;">{{ $sol->idSolicitudCambio }}</td>
                                    <td style="padding: 10px;">{{ $sol->nombre_clase }}</td>
                                    <td style="padding: 10px; color: #4a5568;">{{ $sol->motivo }}</td>
                                    <td style="padding: 10px;">{{ $sol->fecha }}</td>
                                    <td style="padding: 10px;">
                                        @if($sol->estado == 'Aprobada')
                                            <span style="background: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Aprobada</span>
                                        @elseif($sol->estado == 'Rechazada')
                                            <span style="background: #fce8e6; color: #c5221f; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Rechazada</span>
                                        @else
                                            <span style="background: #fef3c7; color: #b45309; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 15px; text-align: center; color: #666;">No has enviado ninguna solicitud de cambio.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: ASESORÍAS CON CLIENTES (INSTRUCTOR) -->
        <!-- SECCIÓN 3: ASESORÍAS CON CLIENTES (INSTRUCTOR) -->
        <div id="seccion-asesorias" class="seccion-panel" style="margin-top: 20px; display: none;">
            <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff7f00; padding-bottom: 8px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="margin: 0; color: #1a2b4c;">Gestión de Asesorías de Clientes</h3>
                    <span style="font-size: 11px; color: #718096; background: #edf2f7; padding: 4px 10px; border-radius: 12px; font-weight: bold;">
                        🧹 Autodepuración: Registros > 7 días se eliminan de la BD
                    </span>
                </div>

                <!-- Formulario de Filtro de Bandeja y Ordenamiento -->
                <form method="GET" action="{{ route('instructor.dashboard') }}" style="margin-top: 15px; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="tab_activa" value="asesorias">
                    
                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">BANDEJA</label>
                        <select name="bandeja_asesoria" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                            <option value="Pendientes" {{ ($bandejaAsesoria ?? '') == 'Pendientes' ? 'selected' : '' }}>⏳ Solo Pendientes</option>
                            <option value="Historial" {{ ($bandejaAsesoria ?? '') == 'Historial' ? 'selected' : '' }}>📁 Completadas / Canceladas (Historial)</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">ORDENAR POR</label>
                        <select name="orden_asesoria" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                            <option value="fecha_hora" {{ ($ordenAsesoria ?? '') == 'fecha_hora' ? 'selected' : '' }}>Fecha y Hora</option>
                            <option value="id_Asesoria" {{ ($ordenAsesoria ?? '') == 'id_Asesoria' ? 'selected' : '' }}>ID Asesoría</option>
                            <option value="nombre_cliente" {{ ($ordenAsesoria ?? '') == 'nombre_cliente' ? 'selected' : '' }}>Nombre del Cliente</option>
                            <option value="estado" {{ ($ordenAsesoria ?? '') == 'estado' ? 'selected' : '' }}>Estado</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #4a5568;">DIRECCIÓN</label>
                        <select name="dir_asesoria" onchange="this.form.submit()" class="form-control" style="margin-top: 3px; background: white;">
                            <option value="asc" {{ ($dirAsesoria ?? '') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                            <option value="desc" {{ ($dirAsesoria ?? '') == 'desc' ? 'selected' : '' }}>Descendente</option>
                        </select>
                    </div>
                </form>

                <!-- Tabla con Scroll Vertical -->
                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Asesoría</th>
                                <th>Cliente</th>
                                <th>Fecha y Hora</th>
                                <th>Estado Actual</th>
                                <th>Observaciones</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asesorias as $asr)
                                <tr>
                                    <td style="font-weight: bold;">{{ $asr->id_Asesoria }}</td>
                                    <td>{{ $asr->nombre_cliente }}</td>
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
                                    <td>
                                        @if($asr->estado == 'Pendiente')
                                            <form action="{{ route('instructor.actualizar_asesoria') }}" method="POST" id="form-{{ $asr->id_Asesoria }}">
                                                @csrf
                                                <input type="hidden" name="id_asesoria" value="{{ $asr->id_Asesoria }}">
                                                <input type="hidden" name="tab_activa" value="asesorias">
                                                <input type="hidden" name="bandeja_asesoria" value="{{ $bandejaAsesoria }}">
                                                <input type="text" name="observaciones" value="{{ $asr->observaciones }}" class="form-control" placeholder="Escribe observaciones..." required>
                                        @else
                                            <span style="color: #4a5568; font-style: italic;">{{ $asr->observaciones }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($asr->estado == 'Pendiente')
                                                <div style="display: flex; gap: 5px; justify-content: center;">
                                                    <button type="submit" name="estado" value="Realizada" style="background: #28a745; color: white; border: none; padding: 6px 10px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 11px;">Completar</button>
                                                    <button type="submit" name="estado" value="Cancelada" style="background: #dc3545; color: white; border: none; padding: 6px 10px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 11px;">Cancelar</button>
                                                </div>
                                            </form>
                                        @else
                                            <span style="color: #a0aec0; font-size: 11px; font-style: italic;">🔒 Bloqueado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 30px; text-align: center; color: #718096; font-weight: bold; font-size: 14px;">
                                        📋 No hay asesorías en esta bandeja actualmente.
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
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            if (boton) {
                boton.classList.add('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var tabActiva = "{{ $tabActiva ?? 'mis-clases' }}";
            var btnTarget = document.querySelector(".tab-btn[onclick*='" + tabActiva + "']");
            cambiarPestana(tabActiva, btnTarget);
        });
    </script>
</body>
</html>