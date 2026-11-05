<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Nodo de un arbol binario.
 *
 * Responsabilidad unica: representar un nodo y sus dos enlaces. El valor es
 * inmutable y los enlaces pueden asignarse durante la construccion del arbol.
 */
final class TreeNode
{
    public ?TreeNode $left = null;

    public ?TreeNode $right = null;

    public function __construct(public readonly string $value)
    {
    }

    /**
     * Indica si el nodo no tiene hijos.
     */
    public function isLeaf(): bool
    {
        return $this->left === null && $this->right === null;
    }
}
