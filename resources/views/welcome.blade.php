<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> ¡Bienvenido! </title>
    <link rel="stylesheet" href="{{ asset('css/estilos1.css') }}">
</head>
<body>
    <section class="cuadInfo">
        <div class="titulo">
            <h1> ¡Bienvenido a ActivaFit! </h1> Porfavor, llene el siguiente formulario para ingresar
        </div>
        <div class="cuestionario">
            <form action="get">
                <div> <label for="idSelUsuario"> Seleccione el tipo de usuario </label>
                <select name="Usuarios" id="idSelUsuario">
                    <option value="Cliente"> Cliente </option>
                    <option value="Recepcionista"> Recepcionista </option>
                    <option value="Instructor"> Instructor </option>
                    <option value="Admin"> Administrador </option>
                </select> </div> <br>
                <div> <label for="idCodigo"> Coloque su codigo: </label>
                <input type="text" name="Codigo" id="idCodigo"> </div> <br>
                <div> <label for="idCodigo"> Coloque su contraseña: </label>
                <input type="password" name="Contraseña" id="idContraseña"> </div>
            
        </div>
        <div class="empezar">
            <input type="button" value="Iniciar" class="Boton">
            </form>
        </div>
    </section>
</body>
</html>