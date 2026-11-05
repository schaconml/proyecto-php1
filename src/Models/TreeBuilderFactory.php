<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Construye el arbol binario a partir de los recorridos indicados.
 *
 * Responsabilidad unica: decidir que par de recorridos se utiliza, delegar la
 * reconstruccion en la implementacion correspondiente y comprobar que los
 * datos realmente describen un mismo arbol.
 *
 * Cuando el usuario informa los tres recorridos se usa el par preorden e
 * inorden, que es el unico que determina un arbol de forma univoca.
 */
final class TreeBuilderFactory
{
    public const PREORDER = 'preorder';

    public const INORDER = 'inorder';

    public const POSTORDER = 'postorder';

    public function __construct(
        private readonly PreInorderTreeBuilder $preInorderBuilder,
        private readonly PrePostorderTreeBuilder $prePostorderBuilder,
        private readonly InPostorderTreeBuilder $inPostorderBuilder,
        private readonly TreeTraverser $traverser
    ) {
    }

    /**
     * Construye el arbol a partir de los recorridos proporcionados.
     *
     * @param array<string, list<string>> $traversals Recorridos no vacios,
     *                                                 indexados por tipo.
     *
     * @throws InvalidArgumentException Si faltan recorridos o son inconsistentes.
     */
    public function build(array $traversals): BinaryTree
    {
        $provided = array_filter(
            $traversals,
            static fn (mixed $values): bool => is_array($values) && $values !== []
        );

        if (count($provided) < 2) {
            throw new InvalidArgumentException(
                'Debe ingresar al menos dos recorridos (preorden, inorden o postorden) para construir el arbol.'
            );
        }

        [$builder, $first, $second] = $this->resolve($provided);

        $tree = $builder->build($first, $second);

        $this->assertConsistent($tree, $provided);

        return $tree;
    }

    /**
     * Elige el par de recorridos y la implementacion que los reconstruye.
     *
     * @param array<string, list<string>> $provided
     *
     * @return array{0: TreeBuilder, 1: list<string>, 2: list<string>}
     */
    private function resolve(array $provided): array
    {
        $hasPreorder = isset($provided[self::PREORDER]);
        $hasInorder = isset($provided[self::INORDER]);
        $hasPostorder = isset($provided[self::POSTORDER]);

        if ($hasPreorder && $hasInorder) {
            return [
                $this->preInorderBuilder,
                $provided[self::PREORDER],
                $provided[self::INORDER],
            ];
        }

        if ($hasPreorder && $hasPostorder) {
            return [
                $this->prePostorderBuilder,
                $provided[self::PREORDER],
                $provided[self::POSTORDER],
            ];
        }

        if ($hasInorder && $hasPostorder) {
            return [
                $this->inPostorderBuilder,
                $provided[self::INORDER],
                $provided[self::POSTORDER],
            ];
        }

        throw new InvalidArgumentException(
            'Las combinaciones posibles son preorden e inorden, preorden y postorden, o inorden y postorden.'
        );
    }

    /**
     * Comprueba que los recorridos calculados sobre el arbol coinciden con los
     * datos ingresados: de lo contrario los datos son contradictorios.
     *
     * @param array<string, list<string>> $provided
     */
    private function assertConsistent(BinaryTree $tree, array $provided): void
    {
        $computed = [
            self::PREORDER   => $this->traverser->preorder($tree),
            self::INORDER    => $this->traverser->inorder($tree),
            self::POSTORDER  => $this->traverser->postorder($tree),
        ];

        foreach ($provided as $type => $values) {
            if ($computed[$type] !== $values) {
                throw new InvalidArgumentException(sprintf(
                    'El recorrido en %s no corresponde al arbol construido: los datos son inconsistentes.',
                    $this->label($type)
                ));
            }
        }
    }

    private function label(string $type): string
    {
        return match ($type) {
            self::PREORDER  => 'preorden',
            self::INORDER   => 'inorden',
            self::POSTORDER => 'postorden',
            default         => $type,
        };
    }
}
