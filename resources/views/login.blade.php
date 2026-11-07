<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido! - ActivaFit</title>
    <!-- Ruta protegida para los estilos en la carpeta public/css -->
    <link rel="stylesheet" href="{{ asset('css/estilos1.css') }}">
</head>
<body>
    <section class="cuadInfo">
        <div class="titulo">
            <h1> ¡Bienvenido a ActivaFit! </h1> Por favor, ingrese sus datos para continuar
        </div>
        
        <!-- El formulario apunta a la ruta de validación por POST -->
        <form action="{{ route('login.verificar') }}" method="POST">
            @csrf <!-- Token de seguridad obligatorio en Laravel para formularios POST -->
            
            <div class="cuestionario">
                <div> 
                    <label for="idSelUsuario"> Seleccione el tipo de usuario </label>
                    <select name="tipo_usuario" id="idSelUsuario">
                        <option value="Cliente"> Cliente </option>
                        <option value="Recepcionista"> Recepcionista </option>
                        <option value="Instructor"> Instructor </option>
                        <option value="Admin"> Administrador </option>
                    </select> 
                </div> <br>
                
                <div> 
                    <label for="idCodigo"> Coloque su código: </label>
                    <input type="text" name="codigo" id="idCodigo" required> 
                </div> <br>
            </div>

            <div class="empezar">
                <!-- Cambiamos el input button por un submit para que envíe el formulario -->
                <button type="submit" class="Boton">Iniciar</button>
            </div>
        </form>
    </section>
</body>
</html>