<?php
/**
 * @category    wurth.dev
 * @date        10/01/2018
 * @author      Michał Bolka <mbolka@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */
namespace Divante\ClassificationTreeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Divante\ClassificationTreeBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('divante_classification_tree');
        return $treeBuilder;
    }
}
