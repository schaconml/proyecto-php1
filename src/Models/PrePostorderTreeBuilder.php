<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Reconstruye el arbol a partir del preorden y el postorden.
 *
 * En este par la raiz es el ultimo elemento del postorden (y el primero del
 * preorden). El tamano del subarbol izquierdo se deduce de la posicion que
 * ocupa la raiz del subarbol izquierdo dentro del postorden.
 */
final class PrePostorderTreeBuilder extends AbstractTreeBuilder
{
    /**
     * @param list<string> $preorder
     * @param list<string> $postorder
     */
    public function build(array $preorder, array $postorder): BinaryTree
    {
        $this->assertCompatible($preorder, $postorder);

        $root = $this->buildSubtree($preorder, 0, count($preorder), $postorder, 0, count($postorder));

        return new BinaryTree($root);
    }

    /**
     * @param list<string> $preorder
     * @param list<string> $postorder
     */
    private function buildSubtree(
        array $preorder,
        int $preorderStart,
        int $preorderEnd,
        array $postorder,
        int $postorderStart,
        int $postorderEnd
    ): ?TreeNode {
        if ($preorderStart >= $preorderEnd) {
            return null;
        }

        $rootValue = $preorder[$preorderStart];

        if ($postorder[$postorderEnd - 1] !== $rootValue) {
            throw new InvalidArgumentException(
                sprintf('El nodo "%s" no coincide con la raiz del postorden.', $rootValue)
            );
        }

        $leftSize = $this->leftSubtreeSize(
            $preorder,
            $postorder,
            $preorderStart,
            $preorderEnd,
            $postorderStart,
            $postorderEnd
        );

        $node = new TreeNode($rootValue);
        $node->left = $this->buildSubtree(
            $preorder,
            $preorderStart + 1,
            $preorderStart + 1 + $leftSize,
            $postorder,
            $postorderStart,
            $postorderStart + $leftSize
        );
        $node->right = $this->buildSubtree(
            $preorder,
            $preorderStart + 1 + $leftSize,
            $preorderEnd,
            $postorder,
            $postorderStart + $leftSize,
            $postorderEnd - 1
        );

        return $node;
    }

    /**
     * Calcula cuantos nodos tiene el subarbol izquierdo.
     *
     * La raiz del subarbol izquierdo es el elemento que sigue a la raiz en el
     * preorden; en el postorden ese mismo nodo cierra el subarbol izquierdo,
     * de modo que su posicion determina el tamano.
     *
     * @param list<string> $preorder
     * @param list<string> $postorder
     */
    private function leftSubtreeSize(
        array $preorder,
        array $postorder,
        int $preorderStart,
        int $preorderEnd,
        int $postorderStart,
        int $postorderEnd
    ): int {
        // Si el subarbol actual tiene un solo nodo, no puede tener hijo izquierdo.
        if ($preorderEnd - $preorderStart <= 1) {
            return 0;
        }

        $leftRoot = $preorder[$preorderStart + 1];
        $leftRootIndex = $this->indexOf($postorder, $leftRoot, $postorderStart, $postorderEnd - 1);

        if ($leftRootIndex === -1) {
            throw new InvalidArgumentException(
                sprintf('El nodo "%s" del preorden no aparece en el postorden.', $leftRoot)
            );
        }

        return $leftRootIndex - $postorderStart + 1;
    }
}
