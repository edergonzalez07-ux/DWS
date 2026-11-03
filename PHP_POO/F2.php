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
        bool $superlicencia,
        int $puntos = 0
    ) {
        parent::__construct($nombrePiloto, $nacionalidad, $numeroMonoplaza, $escuderia, $puntos);
        $this->superlicencia = $superlicencia;
    }

    public function tieneSuperlicencia(): bool
    {
        return $this->superlicencia;
    }

    public function subirCategoria(string|bool|null $datoAdicional = null): Monoplaza
    {
        if (!$this->superlicencia) {
            throw new DomainException('El piloto no tiene los puntos necesarios de superlicencia para subir a F1.');
        }
        if (!is_string($datoAdicional) || trim($datoAdicional) === '') {
            throw new InvalidArgumentException('Indica el patrocinador principal para crear el monoplaza de F1.');
        }

        return new F1(
            $this->nombrePiloto,
            $this->nacionalidad,
            $this->numeroMonoplaza,
            $this->escuderia,
            $datoAdicional,
            $this->puntos
        );
    }
}
