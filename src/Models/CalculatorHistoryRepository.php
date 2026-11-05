<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Repositorio del historial de la calculadora.
 *
 * Responsabilidad unica: leer y escribir los registros de la tabla
 * historial_calculadora. Es la unica clase del proyecto que escribe SQL.
 *
 * La conexion se recibe por constructor (principio de inversion de
 * dependencias), de modo que la clase no abre conexiones por su cuenta y
 * puede probarse con una conexion alternativa.
 */
final class CalculatorHistoryRepository
{
    private const TABLE = 'historial_calculadora';

    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Registra una operacion y devuelve el identificador generado.
     */
    public function save(float $number1, string $operation, ?float $number2, float $result): int
    {
        $statement = $this->pdo->prepare(sprintf(
            'INSERT INTO %s (numero_1, operacion, numero_2, resultado) VALUES (:numero_1, :operacion, :numero_2, :resultado)',
            self::TABLE
        ));

        $statement->execute([
            'numero_1'  => $this->decimal($number1),
            'operacion' => $operation,
            'numero_2'  => $number2 === null ? null : $this->decimal($number2),
            'resultado' => $this->decimal($result),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Devuelve las operaciones mas recientes, de la mas nueva a la mas antigua.
     *
     * @return list<CalculatorHistoryEntry>
     */
    public function findAll(int $limit = 50): array
    {
        $statement = $this->pdo->prepare(sprintf(
            'SELECT id, numero_1, operacion, numero_2, resultado, fecha_registro
             FROM %s
             ORDER BY fecha_registro DESC, id DESC
             LIMIT :limite',
            self::TABLE
        ));

        $statement->bindValue('limite', $limit, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn (array $row): CalculatorHistoryEntry => new CalculatorHistoryEntry(
                (int) $row['id'],
                (string) $row['numero_1'],
                (string) $row['operacion'],
                $row['numero_2'] === null ? null : (string) $row['numero_2'],
                (string) $row['resultado'],
                (string) $row['fecha_registro']
            ),
            $rows
        );
    }

    /**
     * Elimina una operacion del historial.
     */
    public function deleteById(int $id): void
    {
        $statement = $this->pdo->prepare(sprintf('DELETE FROM %s WHERE id = :id', self::TABLE));
        $statement->execute(['id' => $id]);
    }

    /**
     * Elimina todas las operaciones del historial.
     */
    public function deleteAll(): void
    {
        $statement = $this->pdo->prepare(sprintf('DELETE FROM %s', self::TABLE));
        $statement->execute();
    }

    /**
     * Formatea un numero con los cuatro decimales de la columna DECIMAL(10,4).
     */
    private function decimal(float $value): string
    {
        return number_format($value, 4, '.', '');
    }
}
