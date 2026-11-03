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
        string $patrocinadorPrincipal,
        int $puntos = 0
    ) {
        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntos);
        $this->patrocinadorPrincipal = $patrocinadorPrincipal;
    }

    public function getPatrocinadorPrincipal(): string
    {
        return $this->patrocinadorPrincipal;
    }

    public function subirCategoria(string|bool|null $datoAdicional = null): Monoplaza
    {
        throw new BadMethodCallException('La Fórmula 1 es la máxima categoría del ejercicio.');
    }
}
