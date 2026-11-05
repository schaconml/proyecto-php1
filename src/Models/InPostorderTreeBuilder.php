<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Reconstruye el arbol a partir del inorden y el postorden.
 *
 * La raiz es el ultimo elemento del postorden y su posicion en el inorden
 * separa el subarbol izquierdo del derecho.
 */
final class InPostorderTreeBuilder extends AbstractTreeBuilder
{
    /**
     * @param list<string> $inorder
     * @param list<string> $postorder
     */
    public function build(array $inorder, array $postorder): BinaryTree
    {
        $this->assertCompatible($inorder, $postorder);

        $root = $this->buildSubtree($inorder, 0, count($inorder), $postorder, 0, count($postorder));

        return new BinaryTree($root);
    }

    /**
     * @param list<string> $inorder
     * @param list<string> $postorder
     */
    private function buildSubtree(
        array $inorder,
        int $inorderStart,
        int $inorderEnd,
        array $postorder,
        int $postorderStart,
        int $postorderEnd
    ): ?TreeNode {
        if ($inorderStart >= $inorderEnd) {
            return null;
        }

        $rootValue = $postorder[$postorderEnd - 1];
        $rootIndex = $this->indexOf($inorder, $rootValue, $inorderStart, $inorderEnd);

        if ($rootIndex === -1) {
            throw new InvalidArgumentException(
                sprintf('El nodo "%s" del postorden no aparece en el inorden.', $rootValue)
            );
        }

        $leftSize = $rootIndex - $inorderStart;

        $node = new TreeNode($rootValue);
        $node->left = $this->buildSubtree(
            $inorder,
            $inorderStart,
            $rootIndex,
            $postorder,
            $postorderStart,
            $postorderStart + $leftSize
        );
        $node->right = $this->buildSubtree(
            $inorder,
            $rootIndex + 1,
            $inorderEnd,
            $postorder,
            $postorderStart + $leftSize,
            $postorderEnd - 1
        );

        return $node;
    }
}
