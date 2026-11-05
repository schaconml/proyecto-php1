<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TreeBuilderFactory;
use App\Models\TreeLayoutCalculator;
use App\Models\TraversalParser;
use App\Models\TreeTraverser;
use App\Models\Validator;
use App\Views\View;
use InvalidArgumentException;

/**
 * Controlador de la aplicacion de construccion de arboles binarios.
 *
 * Responsabilidad unica: leer los recorridos, delegar el analisis en el parser
 * y la construccion en el factory, y preparar los datos de la vista.
 * No contiene los algoritmos de reconstruccion.
 */
final class TreeController
{
    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly TraversalParser $parser,
        private readonly TreeBuilderFactory $factory,
        private readonly TreeTraverser $traverser,
        private readonly TreeLayoutCalculator $layoutCalculator
    ) {
    }

    /**
     * Muestra el formulario con los tres recorridos.
     */
    public function index(): string
    {
        return $this->view->render('tree', [
            'preorder'   => '',
            'inorder'    => '',
            'postorder'  => '',
            'layout'     => null,
            'traversals' => [],
            'errors'     => [],
        ], 'Arbol binario');
    }

    /**
     * Construye el arbol a partir de los recorridos ingresados.
     */
    public function store(): string
    {
        $raw = [
            TreeBuilderFactory::PREORDER  => (string) $this->request->postParam('preorder', ''),
            TreeBuilderFactory::INORDER   => (string) $this->request->postParam('inorder', ''),
            TreeBuilderFactory::POSTORDER => (string) $this->request->postParam('postorder', ''),
        ];

        $errors = [];
        $traversals = [];

        foreach ($raw as $type => $value) {
            if (trim($value) === '') {
                continue;
            }

            try {
                $traversals[$type] = $this->parser->parse($value, $this->label($type));
            } catch (InvalidArgumentException $exception) {
                $errors[$type] = $exception->getMessage();
            }
        }

        $layout = null;
        $computed = [];

        if ($errors === []) {
            try {
                $tree = $this->factory->build($traversals);
                $layout = $this->layoutCalculator->calculate($tree);
                $computed = [
                    TreeBuilderFactory::PREORDER  => $this->traverser->preorder($tree),
                    TreeBuilderFactory::INORDER   => $this->traverser->inorder($tree),
                    TreeBuilderFactory::POSTORDER => $this->traverser->postorder($tree),
                ];
            } catch (InvalidArgumentException $exception) {
                $errors['tree'] = $exception->getMessage();
            }
        }

        return $this->view->render('tree', [
            'preorder'   => $raw[TreeBuilderFactory::PREORDER],
            'inorder'    => $raw[TreeBuilderFactory::INORDER],
            'postorder'  => $raw[TreeBuilderFactory::POSTORDER],
            'layout'     => $layout,
            'traversals' => $computed,
            'errors'     => $errors,
        ], 'Arbol binario');
    }

    private function label(string $type): string
    {
        return match ($type) {
            TreeBuilderFactory::PREORDER  => 'preorden',
            TreeBuilderFactory::INORDER   => 'inorden',
            TreeBuilderFactory::POSTORDER => 'postorden',
            default                       => $type,
        };
    }
}
