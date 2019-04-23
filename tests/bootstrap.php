<?php

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/TestAppKernel.php';

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::boostrap();
