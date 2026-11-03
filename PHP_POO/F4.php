<?php

declare(strict_types=1);

class F4 extends Monoplaza
{
    protected string $paisCategoria;
    protected int $maximoPilotos = 30;
    protected array $tablaPuntos = [
        1 => 25,
        2 => 18,
        3 => 15,
        4 => 12,
        5 => 10,
        6 => 8,
        7 => 6,
        8 => 4,
        9 => 2,
        10 => 1,
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
            $pais = is_string($quintoArg) ? $quintoArg : (string) ($sextoArg ?? '');
        } elseif (is_string($quintoArg) || $sextoArg !== null) {
            $puntosFinal = is_string($quintoArg) ? 0 : (int) $quintoArg;
            $pais = is_string($quintoArg) ? $quintoArg : (string) ($sextoArg ?? '');
        } else {
            $puntosFinal = 0;
            $pais = (string) $quintoArg;
        }

        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntosFinal);
        $this->paisCategoria = $pais;
    }

    public function getPaisCategoria(): string
    {
        return $this->paisCategoria;
    }

    public function subirCategoria(...$args): Monoplaza
    {
        $academia = $args[0] ?? 'Academia';

        return new F3(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $this->puntos,
            $academia
        );
    }}