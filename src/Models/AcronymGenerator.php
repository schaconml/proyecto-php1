<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Generador de acronimos.
 *
 * Responsabilidad unica: convertir una frase en su acronimo. No depende de HTTP,
 * de la base de datos ni de la vista, por lo que puede probarse de forma aislada.
 *
 * Reglas de conversion:
 *  1. Los guiones (y los guiones largos) separan palabras, igual que los espacios.
 *  2. El resto de signos de puntuacion se eliminan, pero NO separan palabras.
 *  3. De cada palabra se toma solamente la primera letra, en mayuscula.
 *
 * Ejemplos: "As Soon As Possible" => ASAP, "Liquid-crystal display" => LCD,
 * "Thank George It's Friday!" => TGIF.
 */
final class AcronymGenerator
{
    /**
     * Guiones y subrayados: actuan como separadores de palabras.
     */
    private const SEPARATORS_PATTERN = '/[\x{2010}-\x{2015}\x{2212}_-]+/u';

    /**
     * Cualquier caracter que no sea letra, digito ni espacio se elimina.
     */
    private const PUNCTUATION_PATTERN = '/[^\p{L}\p{N}\s]/u';

    /**
     * Convierte la frase en su acronimo.
     *
     * @return string Acronimo generado o cadena vacia si la frase no aporta palabras.
     */
    public function generate(string $phrase): string
    {
        $letters = [];

        foreach ($this->splitWords($phrase) as $word) {
            $letters[] = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
        }

        return implode('', $letters);
    }

    /**
     * Devuelve las palabras que componen la frase ya normalizada.
     *
     * @return list<string>
     */
    public function words(string $phrase): array
    {
        return $this->splitWords($phrase);
    }

    /**
     * Normaliza la frase y la divide en palabras.
     *
     * @return list<string>
     */
    private function splitWords(string $phrase): array
    {
        $text = trim($phrase);
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // 1. Los guiones separan palabras.
        $text = preg_replace(self::SEPARATORS_PATTERN, ' ', $text) ?? $text;

        // 2. El resto de la puntuacion se elimina sin separar palabras.
        $text = preg_replace(self::PUNCTUATION_PATTERN, '', $text) ?? $text;

        // 3. Se colapsan los espacios sobrantes.
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return $words === false ? [] : $words;
    }
}
