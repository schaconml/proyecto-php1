<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Calcula la posicion de cada nodo para dibujar el arbol.
 *
 * Responsabilidad unica: obtener las coordenadas de los nodos y de las aristas
 * que los unen, de modo que la vista solo tenga que pintar el resultado.
 *
 * La posicion horizontal se asigna segun el orden inorden (cada nodo ocupa una
 * columna distinta), por lo que ningun nodo se superpone con otro. La
 * posicion vertical corresponde a la profundidad dentro del arbol.
 */
final class TreeLayoutCalculator
{
    private const X_SPACING = 72;

    private const Y_SPACING = 96;

    private const MARGIN = 48;

    /**
     * Calcula la disposicion del arbol.
     *
     * @return array{
     *     nodes: list<array{value: string, x: int, y: int, depth: int}>,
     *     edges: list<array{from: int, to: int}>,
     *     width: int,
     *     height: int
     * }
     */
    public function calculate(BinaryTree $tree): array
    {
        $collected = [];
        $this->collect($tree->getRoot(), 0, $collected);

        $nodes = [];
        $indexes = [];

        foreach ($collected as $index => $entry) {
            $nodes[] = [
                'value' => $entry['node']->value,
                'x'    => self::MARGIN + ($index * self::X_SPACING),
                'y'    => self::MARGIN + ($entry['depth'] * self::Y_SPACING),
                'depth' => $entry['depth'],
            ];

            $indexes[spl_object_id($entry['node'])] = $index;
        }

        $edges = [];
        $this->collectEdges($tree->getRoot(), $indexes, $edges);

        $levels = $tree->height();

        return [
            'nodes'  => $nodes,
            'edges'  => $edges,
            'width'  => (count($nodes) * self::X_SPACING) + (self::MARGIN * 2),
            'height' => ($levels * self::Y_SPACING) + (self::MARGIN * 2),
        ];
    }

    /**
     * Recorre el arbol en orden inorden anotando la profundidad de cada nodo.
     *
     * @param list<array{node: TreeNode, depth: int}> $collected
     */
    private function collect(?TreeNode $node, int $depth, array &$collected): void
    {
        if ($node === null) {
            return;
        }

        $this->collect($node->left, $depth + 1, $collected);
        $collected[] = ['node' => $node, 'depth' => $depth];
        $this->collect($node->right, $depth + 1, $collected);
    }

    /**
     * Registra las aristas que unen cada nodo con sus hijos.
     *
     * @param array<int, int> $indexes
     * @param list<array{from: int, to: int}> $edges
     */
    private function collectEdges(?TreeNode $node, array $indexes, array &$edges): void
    {
        if ($node === null) {
            return;
        }

        $parent = $indexes[spl_object_id($node)];

        foreach ([$node->left, $node->right] as $child) {
            if ($child === null) {
                continue;
            }

            $edges[] = ['from' => $parent, 'to' => $indexes[spl_object_id($child)]];
            $this->collectEdges($child, $indexes, $edges);
        }
    }
}
