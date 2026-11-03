<?php

declare(strict_types=1);

class F2 extends Monoplaza
{
    protected bool $superlicencia;
    protected int $maximoPilotos = 24;
    protected array $tablaPuntos = [
        1 => 10,
        2 => 8,
        3 => 7,
        4 => 6,
        5 => 5,
        6 => 4,
        7 => 3,
        8 => 2,
        9 => 1,
    ];
    protected bool $otorgaPuntosVueltaRapida = true;

    public function __construct(
        string $nombrePiloto,
        string $nacionalidad,
        int $numeroMonoplaza,
        string $escuderia,
        int|bool $quintoArg = 0,
        mixed $sextoArg = null,
        ?int $puntos = null
    ) {
        if ($puntos !== null) {
            $puntosFinal = $puntos;
            $licencia = is_bool($quintoArg) ? $quintoArg : (bool) ($sextoArg ?? false);
        } elseif (is_bool($quintoArg) || $sextoArg !== null) {
            $puntosFinal = is_bool($quintoArg) ? 0 : (int) $quintoArg;
            $licencia = is_bool($quintoArg) ? $quintoArg : (bool) ($sextoArg ?? false);
        } else {
            $puntosFinal = 0;
            $licencia = (bool) $quintoArg;
        }

        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntosFinal);
        $this->superlicencia = $licencia;
    }

    public function tieneSuperlicencia(): bool
    {
        return $this->superlicencia;
    }

    public function subirCategoria(...$args): Monoplaza
    {
        $patrocinador = $args[0] ?? 'Patrocinador principal';

        return new F1(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $this->puntos,
            $patrocinador
        );
    }
}
