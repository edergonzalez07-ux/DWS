<?php

declare(strict_types=1);

function obtenerPilotosF1(array $numeros): array
{
    $resultado = [];

    foreach ($numeros as $numero) {
        $numero = (int) $numero;
        if ($numero < 1) {
            throw new InvalidArgumentException('El número del piloto debe ser positivo.');
        }

        $url = 'https://api.openf1.org/v1/drivers?driver_number=' . $numero;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => "Accept: application/json\r\n",
            ],
        ]);

        $respuesta = file_get_contents($url, false, $context);
        if ($respuesta === false) {
            throw new RuntimeException("No se pudieron obtener los datos del piloto {$numero} desde OpenF1.");
        }

        $datos = json_decode($respuesta, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($datos)) {
            throw new UnexpectedValueException("La respuesta de OpenF1 para el piloto {$numero} no tiene el formato esperado.");
        }

        $piloto = end($datos);
        if (!is_array($piloto)) {
            throw new RuntimeException("OpenF1 no devolvió datos para el piloto {$numero}.");
        }

        $resultado[$numero] = $piloto;
    }

    return $resultado;
}
