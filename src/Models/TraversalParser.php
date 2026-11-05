<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Convierte el texto de un recorrido en la secuencia de nodos.
 *
 * Responsable unica del formato de entrada: separa los nodos, comprueba que
 * cada uno sea un unico caracter (como en el enunciado, donde el arbol tiene
 * los nodos A, B, C, D, E) y que no haya nodos repetidos.
 */
final class TraversalParser
{
    /**
     * Analiza el recorrido indicado.
     *
     * @param string $raw   Texto con los nodos separados por espacios o comas.
     * @param string $label Nombre del recorrido, usado en los mensajes de error.
     *
     * @return list<string>
     *
     * @throws InvalidArgumentException Si el formato no es valido.
     */
    public function parse(string $raw, string $label): array
    {
        $tokens = preg_split('/[\s,;]+/u', trim($raw), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($tokens === []) {
            throw new InvalidArgumentException(sprintf('El recorrido en %s esta vacio.', $label));
        }

        $values = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) !== 1) {
                throw new InvalidArgumentException(sprintf(
                    'El recorrido en %s debe tener un solo caracter por nodo: "%s" no es valido.',
                    $label,
                    $token
                ));
            }

            if (!preg_match('/^[\p{L}\p{N}]$/u', $token)) {
                throw new InvalidArgumentException(sprintf(
                    'El recorrido en %s solo admite letras y digitos: "%s" no es valido.',
                    $label,
                    $token
                ));
            }

            $values[] = $token;
        }

        if (count($values) !== count(array_unique($values))) {
            throw new InvalidArgumentException(sprintf(
                'El recorrido en %s no puede repetir nodos.',
                $label
            ));
        }

        return $values;
    }

    /**
     * Analiza a la vez los tres recorridos indicados.
     *
     * Solo se procesan los recorridos con contenido; cada error queda
     * asociado a su propio recorrido para poder mostrarse en su campo.
     *
     * @param array<string, string> $raw
     *
     * @return array{traversals: array<string, list<string>>, errors: array<string, string>}
     */
    public function parseMany(array $raw): array
    {
        $traversals = [];
        $errors = [];

        foreach ($raw as $type => $value) {
            if (trim($value) === '') {
                continue;
            }

            try {
                $traversals[$type] = $this->parse($value, self::labelFor($type));
            } catch (InvalidArgumentException $exception) {
                $errors[$type] = $exception->getMessage();
            }
        }

        return ['traversals' => $traversals, 'errors' => $errors];
    }

    /**
     * Nombre del recorrido tal como aparece en el enunciado.
     */
    private static function labelFor(string $type): string
    {
        return match ($type) {
            'preorder'  => 'preorden',
            'inorder'   => 'inorden',
            'postorder' => 'postorden',
            default     => $type,
        };
    }
}
