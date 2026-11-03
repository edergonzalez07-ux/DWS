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
        string $academia,
        int $puntos = 0
    ) {
        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntos);
        $this->academia = $academia;
    }

    public function getAcademia(): string
    {
        return $this->academia;
    }

    public function subirCategoria(string|bool|null $datoAdicional = null): Monoplaza
    {
        if (!is_bool($datoAdicional)) {
            throw new InvalidArgumentException('Indica si el piloto tiene los puntos necesarios de superlicencia.');
        }

        return new F2(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $datoAdicional,
            $this->puntos
        );
    }
}
