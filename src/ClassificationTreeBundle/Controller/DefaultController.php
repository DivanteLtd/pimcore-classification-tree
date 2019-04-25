<?php
/**
 * @date        10/01/2018
 * @author      Michał Bolka <mbolka@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */
namespace Divante\ClassificationTreeBundle\Controller;

use Divante\ClassificationTreeBundle\Service\ClassificationTreeBuilder;
use Pimcore\Bundle\AdminBundle\Controller\Admin\DataObject\DataObjectController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController
 * @package Divante\ClassificationTreeBundle\Controller
 * @Route("/admin/classification-tree")
 */
class DefaultController extends DataObjectController
{
    /** @var  ClassificationTreeBuilder */
    protected $treeBuilderService;

    /**
     * DefaultController constructor.
     * @param ClassificationTreeBuilder $treeBuilder
     */
    public function __construct(ClassificationTreeBuilder $treeBuilder)
    {
        $this->treeBuilderService = $treeBuilder;
    }

    /**
     * @Route("/tree-get-childs-by-id")
     *
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return JsonResponse
     */
    public function treeGetChildsByIdAction(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        $limit = $request->get('limit');
        $start = $request->get('start');
        $nodeName = $request->get('nodeName');
        $nodeId = $request->get('node');
        $classificationName = $request->get('classificationName');

        if ($nodeName == 'Home' || !$nodeName) {
            $nodes = $this->treeBuilderService->getRootNodes();
            $total = count($nodes);
        } else {
            $results =
                $this->treeBuilderService->getChildNodes($nodeId, $nodeName, $classificationName, $limit, $start);
            $nodes = $results['results'];
            $total = $results['totalCount'];
        }

        $response = [];
        $response['offset'] = (int)$start;
        $response['limit'] = (int)$limit;
        $response['total'] = (int)$total;
        $response['nodes'] = $nodes;

        return $this->adminJson($response);
    }
}
