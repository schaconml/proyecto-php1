<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Reconstruye el arbol a partir del preorden y el inorden.
 *
 * Es el par por defecto: la raiz es el primer elemento del preorden y su
 * posicion dentro del inorden separa el subarbol izquierdo del derecho.
 */
final class PreInorderTreeBuilder extends AbstractTreeBuilder
{
    /**
     * @param list<string> $preorder
     * @param list<string> $inorder
     */
    public function build(array $preorder, array $inorder): BinaryTree
    {
        $this->assertCompatible($preorder, $inorder);

        $root = $this->buildSubtree($preorder, 0, count($preorder), $inorder, 0, count($inorder));

        return new BinaryTree($root);
    }

    /**
     * @param list<string> $preorder
     * @param list<string> $inorder
     */
    private function buildSubtree(
        array $preorder,
        int $preorderStart,
        int $preorderEnd,
        array $inorder,
        int $inorderStart,
        int $inorderEnd
    ): ?TreeNode {
        if ($preorderStart >= $preorderEnd) {
            return null;
        }

        $rootValue = $preorder[$preorderStart];
        $rootIndex = $this->indexOf($inorder, $rootValue, $inorderStart, $inorderEnd);

        if ($rootIndex === -1) {
            throw new InvalidArgumentException(
                sprintf('El nodo "%s" del preorden no aparece en el inorden.', $rootValue)
            );
        }

        $leftSize = $rootIndex - $inorderStart;

        $node = new TreeNode($rootValue);
        $node->left = $this->buildSubtree(
            $preorder,
            $preorderStart + 1,
            $preorderStart + 1 + $leftSize,
            $inorder,
            $inorderStart,
            $rootIndex
        );
        $node->right = $this->buildSubtree(
            $preorder,
            $preorderStart + 1 + $leftSize,
            $preorderEnd,
            $inorder,
            $rootIndex + 1,
            $inorderEnd
        );

        return $node;
    }
}
