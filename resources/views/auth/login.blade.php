<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivaFit - Iniciar Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/estilos1.css') }}">
    <style>
        body {
            background-color: #1a1a1a;
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: rgba(26, 43, 76, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            background: #fff;
            color: #333;
            font-size: 15px;
            box-sizing: border-box;
        }
        .btn-submit {
            width: 100%;
            background: #ff7f00;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover {
            background: #e06d00;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div style="text-align: center; margin-bottom: 25px;">
            <p style="margin: 0; font-size: 11px; letter-spacing: 2px; color: #a0aec0; font-weight: bold;">GIMNASIO</p>
            <h1 style="margin: 5px 0 0 0; font-size: 26px; color: #ff7f00;">ACTIVAFIT</h1>
        </div>

        @if($errors->any())
            <div style="background: #fce8e6; color: #c5221f; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; font-weight: bold;">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <!-- Formulario apuntando a nuestra ruta de verificación personalizada -->
        <form method="POST" action="{{ route('login.verificar') }}">
            @csrf

            <div class="form-group">
                <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px;">TIPO DE USUARIO</label>
                <select name="tipo" class="form-control" required>
                    <option value="" disabled selected>Seleccione su rol...</option>
                    <option value="Cliente">Cliente</option>
                    <option value="Recepcionista">Recepcionista</option>
                    <option value="Instructor">Instructor</option>
                    <option value="Admin">Administrador</option>
                </select>
            </div>

            <div class="form-group">
                <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px;">CÓDIGO DE ACCESO</label>
                <input type="text" name="codigo" required placeholder="Ej. CLI-0001 o REP-01" class="form-control" autocomplete="off">
            </div>

            <button type="submit" class="btn-submit">INGRESAR AL SISTEMA</button>
        </form>
    </div>

</body>
</html>