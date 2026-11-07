<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivaFit - Módulo de Recepción</title>
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
            min-width: 300px;
            max-width: 300px;
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
            <span style="background: #ff7f00; color: white; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 14px;">⚡ MÓDULO DE RECEPCIÓN</span>
        </div>

        <!-- Cabecera Azul Institucional -->
        <div style="background: #1a2b4c; color: white; padding: 30px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <p style="margin: 0; font-size: 12px; letter-spacing: 1.5px; color: #a0aec0; font-weight: bold;">GIMNASIO ACTIVAFIT • RECEPCIÓN</p>
                <h1 style="margin: 5px 0 0 0; font-size: 28px;">Control Operativo</h1>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 28px;">📋</div>
                <div>
                    <span style="font-size: 11px; color: #cbd5e0; display: block;">RECEPCIONISTA: <strong style="color: white;">ID: {{ $recepcionista->id_Recepcionista ?? 'REP-01' }}</strong></span>
                    <h3 style="margin: 2px 0; font-size: 16px; color: #ffffff;">{{ $recepcionista->nombre ?? 'Usuario Recepción' }}</h3>
                    <span style="background: #ff7f00; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: bold;">TURNO: {{ strtoupper($recepcionista->turno ?? 'MATUTINO') }}</span>
                </div>
            </div>
        </div>

        <!-- Pestañas de Navegación en Memoria (Sin Recargas Inesperadas) -->
        <div style="margin-top: 25px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="tab-btn active" onclick="cambiarPestana('nuevo-cliente', this)">👤 Registrar Cliente</button>
            <button class="tab-btn" onclick="cambiarPestana('gestion-clientes', this)">👥 Gestión de Clientes</button>
            <button class="tab-btn" onclick="cambiarPestana('consulta-clases', this)">📅 Consulta de Clases</button>
        </div>

        <!-- SECCIÓN 1: FORMULARIO REGISTRO CLIENTE -->
        <div id="seccion-nuevo-cliente" class="seccion-panel" style="margin-top: 20px;">
            <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 3px 8px rgba(0,0,0,0.04);">
                <h3 style="margin-top: 0; color: #1a2b4c; border-bottom: 2px solid #ff7f00; padding-bottom: 8px;">Alta de Nuevo Cliente</h3>
                
                <form action="{{ route('recepcionista.registrar_cliente') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px;">
                        <div>
                            <label style="font-size: 13px; font-weight: bold; color: #4a5568;">Nombre Completo</label>
                            <input type="text" name="nombre" required placeholder="Ej. Juan Pérez" class="form-control" style="margin-top: 5px;">
                        </div>
                        <div>
                            <label style="font-size: 13px; font-weight: bold; color: #4a5568;">Edad</label>
                            <input type="number" name="edad" min="15" required placeholder="Ej. 24" class="form-control" style="margin-top: 5px;">
                        </div>
                        <div>
                            <label style="font-size: 13px; font-weight: bold; color: #4a5568;">Teléfono de Contacto</label>
                            <input type="text" name="numero" required placeholder="555-0000" class="form-control" style="margin-top: 5px;">
                        </div>
                        <div>
                            <label style="font-size: 13px; font-weight: bold; color: #4a5568;">Plan de Membresía</label>
                            <select name="id_Membresia" id="select_membresia" onchange="actualizarDetallesMembresia()" required class="form-control" style="margin-top: 5px; background: white;">
                                <option value="" disabled selected>Seleccione una membresía...</option>
                                @foreach($membresias as $m)
                                    <option value="{{ $m->id_Membresia }}" data-costo="{{ $m->costo }}" data-dias="{{ $m->duracion_dias }}">
                                        {{ $m->nombre_plan }} (${{ number_format($m->costo, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #cbd5e0; padding: 15px; border-radius: 6px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <span style="font-size: 12px; color: #718096; font-weight: bold;">COSTO A COBRAR:</span>
                            <h2 style="margin: 0; color: #ff7f00; font-size: 24px;" id="lbl_costo">$0.00 MXN</h2>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #718096; font-weight: bold;">FECHA DE REGISTRO:</span>
                            <p style="margin: 0; font-weight: bold; color: #2d3748;">{{ date('d/m/Y') }} (Hoy)</p>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #718096; font-weight: bold;">VENCIMIENTO ESTIMADO:</span>
                            <p style="margin: 0; font-weight: bold; color: #28a745;" id="lbl_vencimiento">Seleccione un plan</p>
                        </div>
                    </div>

                    <div style="margin-top: 25px; text-align: right;">
                        <button type="submit" style="background: #ff7f00; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">
                            + REGISTRAR E INSCRIBIR CLIENTE
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECCIÓN 2: GESTIÓN DE CLIENTES (MODIFICAR Y ELIMINAR CON SLIDER) -->
        <div id="seccion-gestion-clientes" class="seccion-panel" style="margin-top: 20px; display: none;">
            <h3 style="color: #1a2b4c; font-size: 18px; margin-bottom: 15px;">👥 Listado de Clientes Inscritos (Desliza para ver más)</h3>
            
            <div class="slider-horizontal">
                @forelse($clientes as $cli)
                    <div class="card-item">
                        <div style="background: #1a2b4c; color: white; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="margin: 0; font-size: 15px;">{{ $cli->id_Cliente }}</h3>
                            <span style="font-size: 11px; background: #ff7f00; padding: 2px 6px; border-radius: 4px; font-weight: bold;">ACTIVO</span>
                        </div>
                        <div style="padding: 15px;">
                            <form action="{{ route('recepcionista.actualizar_cliente') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_Cliente" value="{{ $cli->id_Cliente }}">
                                
                                <label style="font-size: 11px; font-weight: bold; color: #718096;">NOMBRE:</label>
                                <input type="text" name="nombre" value="{{ $cli->nombre }}" required class="form-control" style="padding: 6px; font-size: 13px; margin-bottom: 8px;">

                                <div style="display: flex; gap: 10px; margin-bottom: 8px;">
                                    <div style="flex: 1;">
                                        <label style="font-size: 11px; font-weight: bold; color: #718096;">EDAD:</label>
                                        <input type="number" name="edad" value="{{ $cli->edad }}" required class="form-control" style="padding: 6px; font-size: 13px;">
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="font-size: 11px; font-weight: bold; color: #718096;">TELÉFONO:</label>
                                        <input type="text" name="numero" value="{{ $cli->numero }}" required class="form-control" style="padding: 6px; font-size: 13px;">
                                    </div>
                                </div>

                                <label style="font-size: 11px; font-weight: bold; color: #718096;">MEMBRESÍA:</label>
                                <select name="id_Membresia" class="form-control" style="padding: 6px; font-size: 13px; margin-bottom: 8px; background: white;">
                                    @foreach($membresias as $m)
                                        <option value="{{ $m->id_Membresia }}" {{ $cli->id_Membresia == $m->id_Membresia ? 'selected' : '' }}>
                                            {{ $m->nombre_plan }}
                                        </option>
                                    @endforeach
                                </select>

                                <p style="margin: 5px 0 12px 0; font-size: 12px; color: #28a745;">📅 Vence: <strong>{{ $cli->fecha_vencimiento }}</strong></p>

                                <div style="display: flex; gap: 8px;">
                                    <button type="submit" style="flex: 1; background: #1a2b4c; color: white; border: none; padding: 7px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px;">ACTUALIZAR</button>
                            </form>
                                    
                                    <form action="{{ route('recepcionista.eliminar_cliente') }}" method="POST" style="flex: 1;">
                                        @csrf
                                        <input type="hidden" name="id_cliente" value="{{ $cli->id_Cliente }}">
                                        <button type="submit" onclick="return confirm('¿Estás seguro de eliminar al cliente {{ $cli->nombre }}?')" style="width: 100%; background: #dc3545; color: white; border: none; padding: 7px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px;">ELIMINAR</button>
                                    </form>
                                </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #666; padding: 20px;">No hay clientes registrados en la base de datos.</p>
                @endforelse
            </div>
        </div>

        <!-- SECCIÓN 3: CONSULTA DE CLASES POR DISCIPLINA Y DÍAS -->
        <div id="seccion-consulta-clases" class="seccion-panel" style="margin-top: 20px; display: none;">
            <form method="GET" action="{{ route('recepcionista.dashboard') }}" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                <h4 style="margin-top: 0; color: #ff7f00; font-size: 14px;">▼ CONSULTA DE DISPONIBILIDAD</h4>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 240px;">
                        <label style="font-size: 12px; color: #4a5568; font-weight: bold; display: block; margin-bottom: 5px;">ESPECIALIDAD / DISCIPLINA</label>
                        <select name="disciplina" onchange="this.form.submit()" class="form-control" style="background: #fff;">
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
                        <label style="font-size: 12px; color: #4a5568; font-weight: bold; display: block; margin-bottom: 5px;">FECHA DE CONSULTA</label>
                        <input type="date" name="fecha" value="{{ $fechaFiltro ?? date('Y-m-d') }}" onchange="this.form.submit()" class="form-control">
                    </div>
                </div>
            </form>

            <div style="margin-top: 25px;">
                <h3 style="color: #1a2b4c; font-size: 18px; margin-bottom: 15px;">📅 Clases Impartidas (Desliza para ver más)</h3>
                
                <div class="slider-horizontal">
                    @forelse($clases as $clase)
                        <div class="card-item" style="max-width: 320px; min-width: 320px;">
                            <div style="background: #1a2b4c; color: white; padding: 15px 20px;">
                                <h3 style="margin: 0; font-size: 17px;">{{ $clase->nombre }}</h3>
                                <span style="font-size: 11px; color: #cbd5e0; letter-spacing: 1px; font-weight: bold;">{{ strtoupper($clase->especialidad) }}</span>
                            </div>
                            <div style="padding: 18px;">
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">👨‍🏫 Instructor: <strong>{{ $clase->nombre_instructor }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">🕒 Horario: <strong>{{ $clase->horario }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">📅 Días: <strong>{{ $clase->dias_semana }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">🏢 Salón: <strong>{{ $clase->salon }}</strong></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #4a5568;">👥 Ocupación: <strong>{{ $clase->inscritos_actuales }} / {{ $clase->cupo_maximo }}</strong></p>
                                
                                <div style="margin-top: 15px;">
                                    @if($clase->esta_llena)
                                        <span style="display: block; text-align: center; background: #fce8e6; color: #c5221f; padding: 6px; border-radius: 6px; font-weight: bold; font-size: 12px;">⚠️ CLASE LLENA</span>
                                    @else
                                        <span style="display: block; text-align: center; background: #e6f4ea; color: #137333; padding: 6px; border-radius: 6px; font-weight: bold; font-size: 12px;">✅ DISPONIBLE</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #666; padding: 20px;">No hay clases programadas para esta disciplina o fecha.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <script>
        // Función mejorada para alternar entre pestañas en memoria (Evita saltos y recargas de pantalla)
        function cambiarPestana(seccionId, boton) {
            document.querySelectorAll('.seccion-panel').forEach(panel => {
                panel.style.display = 'none';
            });
            document.getElementById('seccion-' + seccionId).style.display = 'block';
            
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            boton.classList.add('active');

            // Guardar la pestaña activa en la sesión del navegador para mantenerla al filtrar clases
            sessionStorage.setItem('pestanaActivaRecepcion', seccionId);
        }

        // Al recargar por filtrado de clases, mantener la pestaña de consulta abierta automáticamente
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('disciplina') || urlParams.has('fecha') || sessionStorage.getItem('pestanaActivaRecepcion') === 'consulta-clases') {
                const btnConsulta = document.querySelectorAll('.tab-btn')[2];
                if(btnConsulta) {
                    cambiarPestana('consulta-clases', btnConsulta);
                }
            }
        });

        function actualizarDetallesMembresia() {
            const select = document.getElementById('select_membresia');
            const selectedOption = select.options[select.selectedIndex];
            
            const costo = parseFloat(selectedOption.getAttribute('data-costo') || 0);
            const dias = parseInt(selectedOption.getAttribute('data-dias') || 0);

            document.getElementById('lbl_costo').innerText = `$${costo.toFixed(2)} MXN`;

            if (dias > 0) {
                const hoy = new Date();
                hoy.setDate(hoy.getDate() + dias);
                const fechaFormateada = hoy.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                document.getElementById('lbl_vencimiento').innerText = `${fechaFormateada} (+${dias} días)`;
            }
        }
    </script>
</body>
</html>