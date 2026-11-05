<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Genera los recorridos de un arbol binario.
 *
 * Responsabilidad unica: recorrer el arbol en los tres ordenes posibles
 * (preorden, inorden y postorden) y devolver la secuencia de valores.
 */
final class TreeTraverser
{
    /**
     * Recorrido en preorden: raiz, subarbol izquierdo, subarbol derecho.
     *
     * @return list<string>
     */
    public function preorder(BinaryTree $tree): array
    {
        $values = [];
        $this->walkPreorder($tree->getRoot(), $values);

        return $values;
    }

    /**
     * Recorrido en inorden: subarbol izquierdo, raiz, subarbol derecho.
     *
     * @return list<string>
     */
    public function inorder(BinaryTree $tree): array
    {
        $values = [];
        $this->walkInorder($tree->getRoot(), $values);

        return $values;
    }

    /**
     * Recorrido en postorden: subarbol izquierdo, subarbol derecho, raiz.
     *
     * @return list<string>
     */
    public function postorder(BinaryTree $tree): array
    {
        $values = [];
        $this->walkPostorder($tree->getRoot(), $values);

        return $values;
    }

    /**
     * @param list<string> $values
     */
    private function walkPreorder(?TreeNode $node, array &$values): void
    {
        if ($node === null) {
            return;
        }

        $values[] = $node->value;
        $this->walkPreorder($node->left, $values);
        $this->walkPreorder($node->right, $values);
    }

    /**
     * @param list<string> $values
     */
    private function walkInorder(?TreeNode $node, array &$values): void
    {
        if ($node === null) {
            return;
        }

        $this->walkInorder($node->left, $values);
        $values[] = $node->value;
        $this->walkInorder($node->right, $values);
    }

    /**
     * @param list<string> $values
     */
    private function walkPostorder(?TreeNode $node, array &$values): void
    {
        if ($node === null) {
            return;
        }

        $this->walkPostorder($node->left, $values);
        $this->walkPostorder($node->right, $values);
        $values[] = $node->value;
    }
}
