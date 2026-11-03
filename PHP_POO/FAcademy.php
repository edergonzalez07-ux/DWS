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
        int $potenciaMaximaCV,
        int $puntos = 0
    ) {
        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntos);
        $this->potenciaMaximaCV = $potenciaMaximaCV;
    }

    public function getPotenciaMaximaCV(): int
    {
        return $this->potenciaMaximaCV;
    }

    public function subirCategoria(string|bool|null $datoAdicional = null): Monoplaza
    {
        if (!is_string($datoAdicional) || trim($datoAdicional) === '') {
            throw new InvalidArgumentException('Indica el país de la categoría F4.');
        }

        return new F4(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $datoAdicional,
            $this->puntos
        );
    }
}
