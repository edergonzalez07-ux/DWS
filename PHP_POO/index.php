<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/api.php';

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$f3 = new F3('Alicia García', 'Española', 7, 'Apex Racing', 'Academia Talavera');
$f3->otorgarPuntos(2, false);

$f1 = new F1('Luis Sanz', 'Española', 44, 'Fórmula One Club', 'Ferrari');
$f1->otorgarPuntos(5, true);

$f2 = new F2('Nuria López', 'Española', 12, 'Racing GP', true);
$f2->otorgarPuntos(9, true);

$fa = new FAcademy('Dani Pérez', 'Española', 18, 'Driver Academy', 620);
$fa->otorgarPuntos(3, true);

$monoplazas = [$f3, $f1, $f2, $fa];

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de monoplazas</title>
</head>
<body>
    <h1>Puntos de los pilotos</h1>
    <ul>
        <?php foreach ($monoplazas as $monoplaza): ?>
            <li><?= escapar($monoplaza->getNombrePiloto()) ?>: <?= $monoplaza->getPuntos() ?> puntos</li>
        <?php endforeach; ?>
    </ul>

    <h2>Pilotos actuales de Fórmula 1</h2>
    <?php try { ?>
        <?php $pilotos = obtenerPilotosF1([16, 44]); ?>
        <ul>
            <?php foreach ($pilotos as $numero => $piloto): ?>
                <li>
                    <?= (int) $numero ?> —
                    <?= escapar($piloto['full_name'] ?? 'Nombre no disponible') ?>
                    (<?= escapar($piloto['team_name'] ?? 'Escudería no disponible') ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php } catch (Throwable $error) { ?>
        <p>No se pudieron cargar los datos de OpenF1: <?= escapar($error->getMessage()) ?></p>
    <?php } ?>
</body>
</html>
