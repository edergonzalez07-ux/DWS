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
        string $paisCategoria,
        int $puntos = 0
    ) {
        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntos);
        $this->paisCategoria = $paisCategoria;
    }

    public function getPaisCategoria(): string
    {
        return $this->paisCategoria;
    }

    public function subirCategoria(string|bool|null $datoAdicional = null): Monoplaza
    {
        if (!is_string($datoAdicional) || trim($datoAdicional) === '') {
            throw new InvalidArgumentException('Indica la academia a la que pertenece el piloto.');
        }

        return new F3(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $datoAdicional,
            $this->puntos
        );
    }}