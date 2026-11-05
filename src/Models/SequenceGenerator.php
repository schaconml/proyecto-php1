<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Contrato de los generadores de secuencias numericas.
 *
 * Cada implementacion resuelve una operacion distinta (sucesion de Fibonacci,
 * factorial, etc.). Gracias a esta interfaz el controlador no necesita saber
 * que algoritmo se esta ejecutando, y anadir una operacion nueva no obliga a
 * modificar el codigo existente (principios OCP e ISP).
 */
interface SequenceGenerator
{
    /**
     * Genera la secuencia correspondiente al numero indicado.
     *
     * @return list<int|string> Los elementos de la secuencia.
     */
    public function generate(int $n): array;

    /**
     * Valor minimo aceptado por el generador.
     */
    public function minInput(): int;

    /**
     * Valor maximo aceptado por el generador.
     */
    public function maxInput(): int;
}
