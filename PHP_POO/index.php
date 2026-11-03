<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/api.php';

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function detalleCategoria(Monoplaza $monoplaza): string
{
    if ($monoplaza instanceof F1) {
        return 'Patrocinador: ' . $monoplaza->getPatrocinadorPrincipal();
    }
    if ($monoplaza instanceof F2) {
        return 'Superlicencia: ' . ($monoplaza->tieneSuperlicencia() ? 'Sí' : 'No');
    }
    if ($monoplaza instanceof F3) {
        return 'Academia: ' . $monoplaza->getAcademia();
    }
    if ($monoplaza instanceof F4) {
        return 'País: ' . $monoplaza->getPaisCategoria();
    }
    if ($monoplaza instanceof FAcademy) {
        return 'Potencia máxima: ' . $monoplaza->getPotenciaMaximaCV() . ' CV';
    }

    return '';
}

$fAcademy = new FAcademy('Dani Pérez', 'Española', 18, 'Driver Academy', 620);
$fAcademy->otorgarPuntos(3, true);

$f4 = new F4('Marco Rossi', 'Italiana', 21, 'Italian F4 Team', 'Italia');
$f4->otorgarPuntos(5, true);

$f3 = new F3('Alicia García', 'Española', 7, 'Apex Racing', 'Academia Talavera');
$f3->otorgarPuntos(2, false);

$f2 = new F2('Nuria López', 'Española', 12, 'Racing GP', true);
$f2->otorgarPuntos(9, true);

$f1 = new F1('Luis Sanz', 'Española', 44, 'Fórmula One Club', 'Ferrari');
$f1->otorgarPuntos(5, true);

$resultados = [
    ['monoplaza' => $fAcademy, 'posicion' => 3, 'vueltaRapida' => true],
    ['monoplaza' => $f4, 'posicion' => 5, 'vueltaRapida' => true],
    ['monoplaza' => $f3, 'posicion' => 2, 'vueltaRapida' => false],
    ['monoplaza' => $f2, 'posicion' => 9, 'vueltaRapida' => true],
    ['monoplaza' => $f1, 'posicion' => 5, 'vueltaRapida' => true],
];

$promociones = [
    ['origen' => $fAcademy, 'destino' => $fAcademy->subirCategoria('España')],
    ['origen' => $f4, 'destino' => $f4->subirCategoria('Academia Talavera')],
    ['origen' => $f3, 'destino' => $f3->subirCategoria(true)],
    ['origen' => $f2, 'destino' => $f2->subirCategoria('Ferrari')],
];

$mensajeSinSuperlicencia = '';
$pilotoSinSuperlicencia = new F2('Eva Martín', 'Española', 31, 'Racing Junior', false, 35);
try {
    $pilotoSinSuperlicencia->subirCategoria('Ferrari');
} catch (DomainException $error) {
    $mensajeSinSuperlicencia = $error->getMessage();
}

$posicionValida = $f1->posicionValida(5);
$posicionInvalida = $f1->posicionValida(23);

$anio = (int) date('Y');
$sesion = null;
$pilotosApi = [];
$errorApi = null;

try {
    $sesion = obtenerSesionMasReciente($anio);
    $pilotosApi = obtenerPilotosF1([16, 44], (int) $sesion['session_key']);
} catch (RuntimeException | JsonException $error) {
    $errorApi = $error->getMessage();
}

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de monoplazas — Fórmula</title>
    <style>
        body { font: 16px/1.5 system-ui, sans-serif; margin: 2rem auto; max-width: 1050px; padding: 0 1rem; color: #20242a; }
        h1, h2 { line-height: 1.2; }
        section { margin: 2.5rem 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #d5d9de; padding: .65rem; text-align: left; }
        th { background: #f0f2f5; }
        .aviso { background: #fff4e5; border-left: 4px solid #d98200; padding: .8rem 1rem; }
        .ok { color: #146c43; }
        .error { color: #a12622; }
        .nota { color: #555; }
    </style>
</head>
<body>
    <h1>Gestión de monoplazas</h1>

    <section>
        <h2>Puntos de una carrera de ejemplo</h2>
        <p class="nota">Resultados calculados por <code>otorgarPuntos()</code>. F3 y F4 no reciben el punto extra por vuelta rápida.</p>
        <table>
            <thead>
                <tr><th>Categoría</th><th>Piloto</th><th>Posición</th><th>Vuelta rápida</th><th>Posición válida</th><th>Puntos acumulados</th></tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $resultado): ?>
                    <?php $monoplaza = $resultado['monoplaza']; ?>
                    <tr>
                        <td><?= escapar(get_class($monoplaza)) ?></td>
                        <td><?= escapar($monoplaza->getNombrePiloto()) ?></td>
                        <td><?= $resultado['posicion'] ?></td>
                        <td><?= $resultado['vueltaRapida'] ? 'Sí' : 'No' ?></td>
                        <td><?= $monoplaza->posicionValida($resultado['posicion']) ? 'Sí' : 'No' ?></td>
                        <td><?= $monoplaza->getPuntos() ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>Comprobación de posiciones para F1: la posición 5 es <?= $posicionValida ? 'válida' : 'inválida' ?>; la posición 23 es <?= $posicionInvalida ? 'válida' : 'inválida' ?>.</p>
    </section>

    <section>
        <h2>Ejemplos de subida de categoría</h2>
        <p class="nota">Cada llamada crea un nuevo objeto, conserva los datos comunes y los puntos, y añade el dato específico de la categoría de destino.</p>
        <table>
            <thead>
                <tr><th>Piloto</th><th>Sube de</th><th>A</th><th>Dato de la nueva categoría</th><th>Puntos conservados</th></tr>
            </thead>
            <tbody>
                <?php foreach ($promociones as $promocion): ?>
                    <?php $destino = $promocion['destino']; ?>
                    <tr>
                        <td><?= escapar($promocion['origen']->getNombrePiloto()) ?></td>
                        <td><?= escapar(get_class($promocion['origen'])) ?></td>
                        <td><?= escapar(get_class($destino)) ?></td>
                        <td><?= escapar(detalleCategoria($destino)) ?></td>
                        <td><?= $destino->getPuntos() ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="aviso"><strong>Comprobación de superlicencia:</strong> <?= escapar($mensajeSinSuperlicencia) ?></p>
    </section>

    <section>
        <h2>Pilotos actuales de F1 desde OpenF1</h2>
        <?php if ($errorApi !== null): ?>
            <p class="error">No se pudieron cargar los datos de OpenF1: <?= escapar($errorApi) ?></p>
        <?php else: ?>
            <p>
                Temporada <?= $anio ?> ·
                última sesión disponible: <?= escapar($sesion['session_name'] ?? 'Sesión') ?>,
                <?= escapar($sesion['country_name'] ?? 'Lugar no disponible') ?>,
                <?= escapar(date('d/m/Y H:i', strtotime($sesion['date_start']))) ?>.
                Fuente: <a href="https://openf1.org/">OpenF1</a>.
            </p>
            <table>
                <thead>
                    <tr><th>Número</th><th>Piloto</th><th>Escudería</th><th>Siglas</th><th>Nacionalidad</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pilotosApi as $numero => $piloto): ?>
                        <tr>
                            <td><?= (int) $numero ?></td>
                            <td><?= escapar($piloto['full_name'] ?? 'Nombre no disponible') ?></td>
                            <td><?= escapar($piloto['team_name'] ?? 'Escudería no disponible') ?></td>
                            <td><?= escapar($piloto['name_acronym'] ?? '—') ?></td>
                            <td><?= escapar($piloto['country_code'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</body>
</html>
