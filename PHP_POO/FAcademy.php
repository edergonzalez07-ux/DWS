<?php

declare(strict_types=1);

class FAcademy extends Monoplaza
{
    protected int $potenciaMaximaCV;
    protected int $maximoPilotos = 18;
    protected array $tablaPuntos = [
        1 => 18,
        2 => 15,
        3 => 12,
        4 => 10,
        5 => 8,
        6 => 6,
        7 => 4,
        8 => 2,
        9 => 1,
    ];
    protected bool $otorgaPuntosVueltaRapida = true;

    public function __construct(
        string $nombrePiloto,
        string $nacionalidad,
        int $numeroMonoplaza,
        string $escuderia,
        int|string $quintoArg = 0,
        mixed $sextoArg = null,
        ?int $puntos = null
    ) {
        if ($puntos !== null) {
            $puntosFinal = $puntos;
            $potencia = is_int($quintoArg) ? $quintoArg : (int) ($sextoArg ?? 0);
        } elseif ($sextoArg !== null) {
            $puntosFinal = (int) $quintoArg;
            $potencia = (int) $sextoArg;
        } else {
            $puntosFinal = 0;
            $potencia = (int) $quintoArg;
        }

        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntosFinal);
        $this->potenciaMaximaCV = $potencia;
    }

    public function getPotenciaMaximaCV(): int
    {
        return $this->potenciaMaximaCV;
    }

    public function subirCategoria(...$args): Monoplaza
    {
        $paisCategoria = $args[0] ?? 'España';

        return new F4(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $this->puntos,
            $paisCategoria
        );
    }
}
