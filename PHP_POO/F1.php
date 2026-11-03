<?php

declare(strict_types=1);

class F1 extends Monoplaza
{
    protected string $patrocinadorPrincipal;
    protected int $maximoPilotos = 22;
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
            $patrocinador = is_string($quintoArg) ? $quintoArg : (string) ($sextoArg ?? '');
        } elseif (is_string($quintoArg) || $sextoArg !== null) {
            $puntosFinal = is_string($quintoArg) ? 0 : (int) $quintoArg;
            $patrocinador = is_string($quintoArg) ? $quintoArg : (string) ($sextoArg ?? '');
        } else {
            $puntosFinal = 0;
            $patrocinador = (string) $quintoArg;
        }

        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntosFinal);
        $this->patrocinadorPrincipal = $patrocinador;
    }

    public function getPatrocinadorPrincipal(): string
    {
        return $this->patrocinadorPrincipal;
    }

    public function subirCategoria(...$args): Monoplaza
    {
        throw new BadMethodCallException('La Fórmula 1 es la máxima categoría del ejercicio.');
    }
}
