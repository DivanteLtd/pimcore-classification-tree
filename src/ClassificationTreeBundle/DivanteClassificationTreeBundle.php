<?php
/**
 * @date        10/01/2018
 * @author      Michał Bolka <mbolka@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */
namespace Divante\ClassificationTreeBundle;

use Divante\ClassificationTreeBundle\Migrations\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

/**
 * Class DivanteClassificationTreeBundle
 * @package Divante\ClassificationTreeBundle
 */
class DivanteClassificationTreeBundle extends AbstractPimcoreBundle
{
    /**
     * @return array
     */
    public function getJsPaths()
    {
        return [
            '/bundles/divanteclassificationtree/js/pimcore/startup.js',
            '/bundles/divanteclassificationtree/js/pimcore/customViews/tree.js',
            '/bundles/divanteclassificationtree/js/pimcore/customViews/PagingTreeStore.js'
        ];
    }
}
