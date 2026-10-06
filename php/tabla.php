<?php
$mensaje = '';
$tipoMensaje = '';

// Configuración de acceso a la base de datos
$servidor = 'localhost';
$base_datos = 'Examen';
$usuario = 'root';
$contrasena_bd = '';
$puerto = 8080;

$profesiones = [
    'soldadura' => 'Soldadura',
    'informatica' => 'Informática',
    'socio' => 'Asistencia Sociosanitaria',
];

// Protege la página al mostrar datos que vienen de la base de datos.
function escaparHTML($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesion = $_POST['profesion'] ?? '';
    $tipo = array_search($profesion, $profesiones, true);
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $dni = strtoupper(trim($_POST['dni'] ?? ''));
    $fechaNacimiento = $_POST['f_nac'] ?? '';
    $telefono = trim($_POST['tlf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $jornadaParcial = isset($_POST['jornada_parcial']) ? 1 : 0;
    $idiomaSeleccionado = $_POST['idioma'] ?? '';
    $fechaValida = DateTime::createFromFormat('!Y-m-d', $fechaNacimiento);
    $error = '';

    if (!$tipo || $nombre === '' || strlen($nombre) > 50 || $apellidos === '' || strlen($apellidos) > 100) {
        $error = 'Revisa el nombre, los apellidos y la profesión.';
    } elseif (!preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
        $error = 'El DNI debe contener 8 números y una letra.';
    } elseif (!$fechaValida || $fechaValida->format('Y-m-d') !== $fechaNacimiento) {
        $error = 'La fecha de nacimiento no es válida.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $error = 'Introduce un correo electrónico válido.';
    } elseif (strlen($telefono) > 15) {
        $error = 'El teléfono no puede superar los 15 caracteres.';
    } elseif (!in_array($idiomaSeleccionado, ['ninguno', 'euskera', 'ingles', 'ambos'], true)) {
        $error = 'Selecciona Ninguno, Euskera, Inglés o Ambos.';
    }

    if ($error !== '') {
        http_response_code(400);
    } else {
        $transaccionIniciada = false;
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $conexion = new mysqli($servidor, $usuario, $contrasena_bd, $base_datos, $puerto);
            $conexion->set_charset('utf8mb4');

            if ($idiomaSeleccionado === 'ninguno') {
                $idiomaGuardado = 'Ninguno';
            } elseif ($idiomaSeleccionado === 'euskera') {
                $idiomaGuardado = 'Euskera';
            } elseif ($idiomaSeleccionado === 'ingles') {
                $idiomaGuardado = 'Inglés';
            } else {
                $idiomaGuardado = 'Euskera, Inglés';
            }

            $conexion->begin_transaction();
            $transaccionIniciada = true;

            // Escapamos los textos antes de montar la consulta SQL
            $nombre = $conexion->real_escape_string($nombre);
            $apellidos = $conexion->real_escape_string($apellidos);
            $dni = $conexion->real_escape_string($dni);
            $fechaNacimiento = $conexion->real_escape_string($fechaNacimiento);
            $telefono = $conexion->real_escape_string($telefono);
            $email = $conexion->real_escape_string($email);
            $profesion = $conexion->real_escape_string($profesion);
            $idiomaGuardado = $conexion->real_escape_string($idiomaGuardado);

            $telefonoSQL = $telefono !== '' ? "'$telefono'" : 'NULL';
            $conexion->query("INSERT INTO solicitantes
                (nombre, apellidos, dni, f_nac, tlf, email, profesion, jornada_parcial, idioma)
                VALUES ('$nombre', '$apellidos', '$dni', '$fechaNacimiento', $telefonoSQL,
                '$email', '$profesion', $jornadaParcial, '$idiomaGuardado')");
            $conexion->commit();
            $transaccionIniciada = false;
            $conexion->close();

            header('Location: tabla.php?tipo=' . urlencode($tipo) . '&guardado=1', true, 303);
            exit;
        } catch (InvalidArgumentException $exception) {
            if ($transaccionIniciada && isset($conexion)) {
                $conexion->rollback();
            }
            $error = $exception->getMessage();
            http_response_code(400);
        } catch (Throwable $exception) {
            if ($transaccionIniciada && isset($conexion)) {
                $conexion->rollback();
            }
            error_log('Error al guardar la solicitud: ' . $exception->getMessage());
            $error = 'No se pudo guardar la solicitud: ' . $exception->getMessage();
            http_response_code(500);
        }
    }

    if ($error !== '') {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error en la solicitud</title>
            <link rel="stylesheet" href="../estilos/estilos.css">
        </head>
        <body>
            <h1>No se pudo completar la solicitud</h1>
            <p role="alert"><?= escaparHTML($error) ?></p>
            <a href="formulario.php">Volver al formulario</a>
        </body>
        </html>
        <?php
        exit;
    }
}

$tipo = $_GET['tipo'] ?? '';
if (!isset($profesiones[$tipo])) {
    http_response_code(400);
    $tipo = '';
    $solicitudes = [];
    $errorListado = 'Selecciona una profesión válida para consultar las solicitudes.';
} else {
    $profesion = $profesiones[$tipo];
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conexion = new mysqli($servidor, $usuario, $contrasena_bd, $base_datos, $puerto);
        $conexion->set_charset('utf8mb4');
        $profesionEscapada = $conexion->real_escape_string($profesion);
        $resultadoSolicitudes = $conexion->query(
                "SELECT s.id, s.nombre, s.apellidos, s.dni, s.f_nac, s.tlf, s.email, s.profesion,
                    s.jornada_parcial, s.idioma
             FROM solicitantes s
             WHERE s.profesion = '$profesionEscapada'
             GROUP BY s.id
             ORDER BY s.apellidos, s.nombre"
        );
        $solicitudes = $resultadoSolicitudes->fetch_all(MYSQLI_ASSOC);
        $conexion->close();
        $errorListado = '';
    } catch (Throwable $exception) {
        error_log('Error al consultar las solicitudes: ' . $exception->getMessage());
        $solicitudes = [];
        $errorListado = 'No se pudieron cargar las solicitudes. Comprueba la conexión y que las tablas estén creadas.';
        http_response_code(500);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes | Centro de Ayuda al Empleo</title>
    <link rel="stylesheet" href="../estilos/estilos.css">
    <link rel="icon" type="image/jpeg" href="../imagenes/favicon.jpeg">
</head>
<body>
    <h1>Centro de Ayuda al Empleo</h1>
    <?php if ($tipo !== ''): ?>
        <h2>Solicitudes de <?= escaparHTML($profesion) ?></h2>
    <?php endif; ?>

    <?php if (isset($_GET['guardado']) && $_GET['guardado'] === '1'): ?>
        <p role="status">La solicitud se ha guardado correctamente.</p>
    <?php endif; ?>

    <?php if ($errorListado !== ''): ?>
        <p role="alert"><?= escaparHTML($errorListado) ?></p>
    <?php elseif (!$solicitudes): ?>
        <p>No hay solicitudes registradas para esta profesión.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Fecha de nacimiento</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Profesión</th>
                    <th>Jornada parcial</th>
                    <th>Idioma</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><?= escaparHTML($solicitud['nombre'] . ' ' . $solicitud['apellidos']) ?></td>
                        <td><?= escaparHTML($solicitud['dni']) ?></td>
                        <td><?= escaparHTML($solicitud['f_nac']) ?></td>
                        <td><?= escaparHTML($solicitud['tlf'] ?? '') ?></td>
                        <td><?= escaparHTML($solicitud['email']) ?></td>
                        <td><?= escaparHTML($solicitud['profesion']) ?></td>
                        <td><?= $solicitud['jornada_parcial'] ? 'Sí' : 'No' ?></td>
                        <td><?= escaparHTML($solicitud['idioma'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="formulario.php">Volver al formulario</a></p>
</body>
</html>