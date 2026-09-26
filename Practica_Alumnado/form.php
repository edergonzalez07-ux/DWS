<?php
$mensaje = '';
$tipo_mensaje = '';

// Datos para conectarse a MySQL
$servidor = 'localhost';
$base_datos = 'Alumnado';
$usuario = 'root';
$contrasena_bd = '';

// Valores iniciales del formulario
$nombre = '';
$apellidos = '';
$fecha_nacimiento = '';
$curso = 0;
$email = '';
$contrasena = '';
$alumnos = [];
$error_listado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Recogemos los datos enviados por el usuario
	if (isset($_POST['nombre'])) {
		$nombre = trim($_POST['nombre']);
	}
	if (isset($_POST['apellidos'])) {
		$apellidos = trim($_POST['apellidos']);
	}
	if (isset($_POST['fecha_nacimiento'])) {
		$fecha_nacimiento = $_POST['fecha_nacimiento'];
	}
	if (isset($_POST['curso'])) {
		$curso = (int) $_POST['curso'];
	}
	if (isset($_POST['email'])) {
		$email = trim($_POST['email']);
	}
	if (isset($_POST['contrasena'])) {
		$contrasena = $_POST['contrasena'];
	}

	// Comprobamos que los campos tienen valores válidos
	$partes_fecha = explode('-', $fecha_nacimiento);
	$fecha_correcta = count($partes_fecha) === 3
		&& checkdate($partes_fecha[1], $partes_fecha[2], $partes_fecha[0]);
	$datos_validos = $nombre !== ''
		&& $apellidos !== ''
		&& $fecha_correcta
		&& $curso >= 1 && $curso <= 4
		&& filter_var($email, FILTER_VALIDATE_EMAIL)
		&& strlen($contrasena) >= 8;

	if (!$datos_validos) {
		$mensaje = 'Revisa los datos: todos los campos son obligatorios y la contraseña debe tener al menos 8 caracteres.';
		$tipo_mensaje = 'error';
	} else {
		try {
			// Abrimos la conexión con la base de datos 
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$conexion = new mysqli($servidor, $usuario, $contrasena_bd, $base_datos);
			$conexion->set_charset('utf8mb4');

			// Contamos los alumnos del curso antes de matricular
			$consulta_cupos = $conexion->query("SELECT COUNT(*) AS total FROM alumnos WHERE curso = $curso");
			$alumnos_matriculados = (int) $consulta_cupos->fetch_assoc()['total'];

			if ($alumnos_matriculados >= 25) {
				$mensaje = "El curso {$curso}º de la ESO ya tiene 25 alumnos matriculados y no admite más matrículas.";
				$tipo_mensaje = 'error';
			} else {
				// Escapamos los textos y los concatenamos en el INSERT
				$nombre = $conexion->real_escape_string($nombre);
				$apellidos = $conexion->real_escape_string($apellidos);
				$fecha_nacimiento = $conexion->real_escape_string($fecha_nacimiento);
				$email = $conexion->real_escape_string($email);
				$contrasena_cifrada = $conexion->real_escape_string(password_hash($contrasena, PASSWORD_DEFAULT));

				$conexion->query("INSERT INTO alumnos
					(nombre, apellidos, fecha_nacimiento, curso, email, contrasena)
					VALUES ('$nombre', '$apellidos', '$fecha_nacimiento', $curso, '$email', '$contrasena_cifrada')");

				$mensaje = 'Alumno registrado correctamente.';
				$tipo_mensaje = 'exito';
				$nombre = '';
				$apellidos = '';
				$fecha_nacimiento = '';
				$curso = 0;
				$email = '';
			}
		} catch (mysqli_sql_exception $error) {
			// Mostramos un mensaje si falla la conexión o el guardado
			$mensaje = 'No se ha podido guardar el registro. Comprueba la conexión y la estructura de la base de datos.';
			$tipo_mensaje = 'error';
		}
	}
}

// Consultamos el alumnado matriculado para mostrarlo debajo del formulario.
try {
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$conexion_listado = new mysqli($servidor, $usuario, $contrasena_bd, $base_datos);
	$conexion_listado->set_charset('utf8mb4');
	$resultado_alumnos = $conexion_listado->query('SELECT nombre, apellidos, curso FROM alumnos ORDER BY apellidos ASC, curso ASC');
	$alumnos = $resultado_alumnos->fetch_all(MYSQLI_ASSOC);
	$conexion_listado->close();
} catch (mysqli_sql_exception $error) {
	$error_listado = true;
}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registro de alumno</title>
	<link rel="stylesheet" href="form.css">
</head>
<body>
	<main class="contenedor">
		<section class="formulario" aria-labelledby="titulo">
			<p class="etiqueta">Secretaría académica</p>
			<h1 id="titulo">Registro de alumno</h1>
			<p class="introduccion">Completa los datos para registrar la matrícula.</p>

			<?php if ($mensaje !== ''): ?>
				<p class="mensaje <?= $tipo_mensaje ?>" role="alert">
					<?= $mensaje ?>
				</p>
			<?php endif; ?>

			<!-- Este formulario envía los datos a este mismo archivo. -->
			<form method="post">
				<div class="campos">
					<label>
						Nombre
						<input type="text" name="nombre" maxlength="100" required value="<?= $nombre ?>">
					</label>

					<label>
						Apellidos
						<input type="text" name="apellidos" maxlength="200" required value="<?= $apellidos ?>">
					</label>

					<label>
						Fecha de nacimiento
						<input type="date" name="fecha_nacimiento" required value="<?= $fecha_nacimiento ?>">
					</label>

					<label>
						Curso en el que está matriculado
						<select name="curso" required>
							<option value="">Selecciona un curso</option>
							<option value="1">1º de la ESO</option>
							<option value="2">2º de la ESO</option>
							<option value="3">3º de la ESO</option>
							<option value="4">4º de la ESO</option>
						</select>
					</label>

					<label>
						Email de Educamos
						<input type="email" name="email" maxlength="255" required value="<?= $email ?>">
					</label>

					<label>
						Contraseña de Educamos
						<input type="password" name="contrasena" minlength="8" required>
					</label>
				</div>

				<button type="submit">Registrar alumno</button>
			</form>

			<section aria-labelledby="titulo-listado">
				<h2 id="titulo-listado">Alumnado matriculado</h2>
				<?php if ($error_listado): ?>
					<p role="alert">No se ha podido cargar el listado del alumnado.</p>
				<?php elseif (count($alumnos) === 0): ?>
					<p>No hay alumnado matriculado.</p>
				<?php else: ?>
					<table>
						<thead><tr><th>Apellidos</th><th>Nombre</th><th>Curso</th></tr></thead>
						<tbody>
							<?php foreach ($alumnos as $alumno): ?>
								<tr>
									<td><?= htmlspecialchars($alumno['apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
									<td><?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
									<td><?= (int) $alumno['curso'] ?>º de la ESO</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>
		</section>
	</main>
</body>
</html>