<?php

declare(strict_types=1);

function solicitarOpenF1(string $url): array
{
    $contexto = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\n",
        ],
    ]);

    $respuesta = @file_get_contents($url, false, $contexto);
    if ($respuesta === false) {
        throw new RuntimeException('No se pudo conectar con la API de OpenF1.');
    }

    $estadoHttp = $http_response_header[0] ?? '';
    if (preg_match('/\s([45]\d\d)\s/', $estadoHttp, $coincidencias) === 1) {
        throw new RuntimeException('OpenF1 devolvió el error HTTP ' . $coincidencias[1] . '.');
    }

    $datos = json_decode($respuesta, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($datos)) {
        throw new RuntimeException('La API de OpenF1 devolvió una respuesta con formato no válido.');
    }

    return $datos;
}

function obtenerSesionMasReciente(int $anio): array
{
    $sesiones = solicitarOpenF1(
        'https://api.openf1.org/v1/sessions?' . http_build_query(['year' => $anio])
    );
    $ahora = time();
    $sesionReciente = null;
    $fechaReciente = 0;

    foreach ($sesiones as $sesion) {
        if (!is_array($sesion) || ($sesion['is_cancelled'] ?? false)) {
            continue;
        }

        $fechaInicio = strtotime($sesion['date_start'] ?? '');
        if ($fechaInicio !== false && $fechaInicio <= $ahora && $fechaInicio > $fechaReciente) {
            $fechaReciente = $fechaInicio;
            $sesionReciente = $sesion;
        }
    }

    if ($sesionReciente === null) {
        throw new RuntimeException("OpenF1 no tiene sesiones disputadas para la temporada {$anio}.");
    }

    return $sesionReciente;
}

function obtenerPilotosF1(array $numeros, int $sessionKey): array
{
    if ($sessionKey < 1) {
        throw new InvalidArgumentException('La sesión de OpenF1 no es válida.');
    }

    $numerosBuscados = [];
    foreach ($numeros as $numero) {
        if (!is_int($numero) || $numero < 1) {
            throw new InvalidArgumentException('Los números de piloto deben ser enteros positivos.');
        }
        $numerosBuscados[$numero] = true;
    }

    $registros = solicitarOpenF1(
        'https://api.openf1.org/v1/drivers?' . http_build_query(['session_key' => $sessionKey])
    );
    $pilotos = [];

    foreach ($registros as $registro) {
        if (!is_array($registro) || !isset($registro['driver_number'])) {
            continue;
        }

        $numero = (int) $registro['driver_number'];
        if (isset($numerosBuscados[$numero])) {
            $pilotos[$numero] = $registro;
        }
    }

    $faltantes = array_diff(array_keys($numerosBuscados), array_keys($pilotos));
    if ($faltantes !== []) {
        throw new RuntimeException(
            'OpenF1 no devolvió los pilotos ' . implode(', ', $faltantes) . ' para la última sesión.'
        );
    }

    ksort($pilotos);
    return $pilotos;
}
