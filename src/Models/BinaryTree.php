<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Arbol binario.
 *
 * Responsabilidad unica: contener la raiz y ofrecer consultas sobre la
 * estructura. No sabe como se construyo el arbol ni que recorrido lo genero.
 */
final class BinaryTree
{
    public function __construct(private readonly ?TreeNode $root = null)
    {
    }

    public function getRoot(): ?TreeNode
    {
        return $this->root;
    }

    public function isEmpty(): bool
    {
        return $this->root === null;
    }

    /**
     * Cantidad de nodos del arbol.
     */
    public function size(): int
    {
        return $this->countNodes($this->root);
    }

    /**
     * Altura del arbol (numero de niveles del nodo mas profundo).
     */
    public function height(): int
    {
        return $this->measureHeight($this->root);
    }

    private function countNodes(?TreeNode $node): int
    {
        if ($node === null) {
            return 0;
        }

        return 1 + $this->countNodes($node->left) + $this->countNodes($node->right);
    }

    private function measureHeight(?TreeNode $node): int
    {
        if ($node === null) {
            return 0;
        }

        return 1 + max($this->measureHeight($node->left), $this->measureHeight($node->right));
    }
}
