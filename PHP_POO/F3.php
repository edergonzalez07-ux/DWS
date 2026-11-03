<?php

declare(strict_types=1);

class F3 extends Monoplaza
{
    protected string $academia;
    protected int $maximoPilotos = 30;
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
            $academiaFinal = is_string($quintoArg) ? $quintoArg : (string) ($sextoArg ?? '');
        } elseif (is_string($quintoArg) || $sextoArg !== null) {
            $puntosFinal = is_string($quintoArg) ? 0 : (int) $quintoArg;
            $academiaFinal = is_string($quintoArg) ? $quintoArg : (string) ($sextoArg ?? '');
        } else {
            $puntosFinal = 0;
            $academiaFinal = (string) $quintoArg;
        }

        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntosFinal);
        $this->academia = $academiaFinal;
    }

    public function getAcademia(): string
    {
        return $this->academia;
    }

    public function subirCategoria(...$args): Monoplaza
    {
        $superlicencia = $args[0] ?? false;

        return new F2(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $this->puntos,
            $superlicencia
        );
    }
}
