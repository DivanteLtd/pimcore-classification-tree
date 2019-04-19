<?php
/**
 * @category    Wurth
 * @date        10/01/2018
 * @author      Michał Bolka <mbolka@divante.pl>
 * @copyright   Copyright (c) 2017 DIVANTE (http://divante.pl)
 */

return [
    'treetype' => 'object',
    'condition' => null,
    'icon' => '/bundles/pimcoreadmin/img/flat-color-icons/reading.svg',
    'showroot' => false,
    'position' => 'right',
    'expanded' => false,
    'sort' => '0',
    'having' => '',
    'joins' => [],
    'where' => '',
    'treeContextMenu' => [
        'object' => [
            'items' => [
                'add' => 0,
                'addFolder' => 0,
                'copy' => 0,
                'importCsv' => 0,
                'paste' => 0,
                'cut' => 0,
                'publish' => 1,
                'unpublish' => 1,
                'delete' => 0,
                'rename' => 0,
                'searchAndMove' => 1,
                'lock' => 0,
                'unlock' => 0,
                'lockAndPropagate' => 0,
                'unlockAndPropagete' => 0,
                'reload' => 1
            ],
        ],
    ],
];
