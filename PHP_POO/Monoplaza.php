<?php

declare(strict_types=1);

abstract class Monoplaza
{
    protected string $nombrePiloto;
    protected string $nacionalidad;
    protected int $numeroMonoplaza;
    protected string $escuderia;
    protected int $puntos = 0;
    protected int $maximoPilotos = 0;
    protected array $tablaPuntos = [];
    protected bool $otorgaPuntosVueltaRapida = false;

    public function __construct(string $nombrePiloto, string $nacionalidad, int $numeroMonoplaza, string $escuderia, int $puntos = 0)
    {
        $this->nombrePiloto = $nombrePiloto;
        $this->nacionalidad = $nacionalidad;
        $this->numeroMonoplaza = $numeroMonoplaza;
        $this->escuderia = $escuderia;
        $this->puntos = $puntos;
    }

    public function getNombrePiloto(): string
    {
        return $this->nombrePiloto;
    }

    public function getNacionalidad(): string
    {
        return $this->nacionalidad;
    }

    public function getNumeroMonoplaza(): int
    {
        return $this->numeroMonoplaza;
    }

    public function getEscuderia(): string
    {
        return $this->escuderia;
    }

    public function getPuntos(): int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): void
    {
        $this->puntos = $puntos;
    }

    public function getMaximoPilotos(): int
    {
        return $this->maximoPilotos;
    }

    public function getPuntosPorPosicion(int $posicion): int
    {
        if (!$this->posicionValida($posicion)) {
            return 0;
        }

        return $this->tablaPuntos[$posicion] ?? 0;
    }

    public function posicionValida(int $posicion): bool
    {
        return $posicion >= 1 && $posicion <= $this->maximoPilotos;
    }

    public function otorgarPuntos(int $posicion, bool $vueltaRapida = false): void
    {
        if (!$this->posicionValida($posicion)) {
            return;
        }

        $total = $this->getPuntosPorPosicion($posicion);

        if ($this->otorgaPuntosVueltaRapida && $vueltaRapida && $posicion <= 10) {
            $total++;
        }

        $this->puntos += $total;
    }

    public function subirCategoria(string|bool|null $datoAdicional = null): Monoplaza
    {
        throw new BadMethodCallException('La categoría actual no tiene una categoría superior definida.');
    }
}
