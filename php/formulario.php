<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud | Centro de Ayuda al Empleo</title>
    <link rel="stylesheet" href="../estilos/estilos.css">
    <link rel="icon" type="image/jpeg" href="../imagenes/favicon.jpeg">
</head>
<body>
    <h1>Centro de Ayuda al Empleo</h1>
    <h2>Formulario de solicitud</h2>

    <!-- Envía todos los datos a tabla.php para validarlos y guardarlos. -->
    <form action="tabla.php" method="post" onsubmit="return validarDni()">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" maxlength="50" autocomplete="given-name" required>

            <label for="apellidos">Apellidos</label>
            <input id="apellidos" name="apellidos" type="text" maxlength="100" autocomplete="family-name" required>

            <label for="dni">DNI</label>
            <input id="dni" name="dni" type="text" maxlength="9" pattern="[0-9]{8}[A-Za-z]" title="Introduce 8 números y una letra" required>

            <label for="f_nac">Fecha de nacimiento</label>
            <input id="f_nac" name="f_nac" type="date" required>

            <label for="tlf">Teléfono</label>
            <input id="tlf" name="tlf" type="tel" maxlength="15" autocomplete="tel">

            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" maxlength="100" autocomplete="email" required>

            <label for="profesion">Profesión solicitada</label>
            <select id="profesion" name="profesion" required>
                <option value="">Selecciona una profesión</option>
                <option value="Soldadura">Soldadura</option>
                <option value="Informática">Informática</option>
                <option value="Asistencia Sociosanitaria">Asistencia Sociosanitaria</option>
            </select>

            <label class="opcion-check">
                <input name="jornada_parcial" type="checkbox" value="1">
                Solicito jornada parcial
            </label>

            <!-- Permite elegir un idioma o indicar que conoce ambos, con su nivel. -->
            <fieldset class="idiomas">
                <legend>Idiomas</legend>
                <label for="idioma">Idiomas que conoces</label>
                <select id="idioma" name="idioma" required>
                    <option value="">Selecciona una opción</option>
                    <option value="euskera">Euskera</option>
                    <option value="ingles">Inglés</option>
                    <option value="ambos">Ambos</option>
                </select>
                <label for="nivel">Nivel de los idiomas seleccionados</label>
                <select id="nivel" name="nivel">
                    <option value="Básico">Básico</option>
                    <option value="Intermedio" selected>Intermedio</option>
                    <option value="Avanzado">Avanzado</option>
                    <option value="Nativo">Nativo</option>
                </select>
            </fieldset>

            <button type="submit">Enviar solicitud</button>
    </form>

    <nav aria-label="Solicitudes por profesión">
        <!-- Enlaces para abrir el listado filtrado por cada profesión. -->
        <h2>Ver solicitudes</h2>
        <button type="button" onclick="location.href='tabla.php?tipo=soldadura'">Soldadura</button>
        <button type="button" onclick="location.href='tabla.php?tipo=informatica'">Informática</button>
        <button type="button" onclick="location.href='tabla.php?tipo=socio'">Asistencia Sociosanitaria</button>
    </nav>

    <!-- Comprueba el formato del DNI y que la letra corresponda a sus números. -->
    <script>
        function validarDni() {
            const dni = document.getElementById('dni').value.toUpperCase();
            const letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
            const valido = /^[0-9]{8}[A-Z]$/.test(dni)
                && letras[dni.substring(0, 8) % 23] === dni[8];

            if (!valido) alert('El DNI no es válido.');
            return valido;
        }
    </script>
</body>
</html>