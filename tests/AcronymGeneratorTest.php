<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Acronym\AcronymGenerator;

/**
 * Pruebas del generador de acronimos (casos del enunciado y casos limite).
 *
 * @return callable(Assert): void
 */
return static function (Assert $assert): void {
    $generator = new AcronymGenerator();

    // Casos del enunciado.
    $assert->equals('ASAP', $generator->generate('As Soon As Possible'), 'As Soon As Possible');
    $assert->equals('LCD', $generator->generate('Liquid-crystal display'), 'Liquid-crystal display');
    $assert->equals('TGIF', $generator->generate("Thank George It's Friday!"), "Thank George It's Friday!");

    // Casos habituales.
    $assert->equals('PNG', $generator->generate('Portable Network Graphics'), 'Portable Network Graphics');
    $assert->equals('HW', $generator->generate('Hello World'), 'Hello World');
    $assert->equals('EM', $generator->generate('   espacios   multiples   '), 'Espacios multiples');
    $assert->equals('A', $generator->generate('a'), 'Una sola letra');

    // La puntuacion se elimina pero NO separa palabras.
    $assert->equals('N', $generator->generate('Node.js'), 'Node.js');
    $assert->equals('N', $generator->generate('Node.js'), 'La puntuacion no divide palabras');
    $assert->equals('HW', $generator->generate('Hello, world!'), 'Coma y exclamacion');
    $assert->equals('ENYLN', $generator->generate('El niño y la niña'), 'Palabras con tilde');

    // Los guiones si separan palabras.
    $assert->equals('GMT', $generator->generate('Guion-medio test'), 'Guion como separador');
    $assert->equals('ABC', $generator->generate('a-b-c'), 'Varios guiones');

    // Casos limite: no hay palabras que convertir.
    $assert->equals('', $generator->generate(''), 'Cadena vacia');
    $assert->equals('', $generator->generate('   '), 'Solo espacios');
    $assert->equals('', $generator->generate('---'), 'Solo guiones');
    $assert->equals('', $generator->generate('!!!'), 'Solo puntuacion');

    // Digitos (decision D-01): se toma un caracter por palabra.
    $assert->equals('C1', $generator->generate('COVID-19'), 'COVID-19');
    $assert->equals('42', $generator->generate('4 2'), 'Dos numeros');

    // Metodo auxiliar de tokenizacion.
    $assert->equals(
        ['Liquid', 'crystal', 'display'],
        $generator->words('Liquid-crystal display'),
        'Palabras normalizadas'
    );
    $assert->equals(
        ['Thank', 'George', 'Its', 'Friday'],
        $generator->words("Thank George It's Friday!"),
        'La apostrofe se elimina sin separar'
    );
};
