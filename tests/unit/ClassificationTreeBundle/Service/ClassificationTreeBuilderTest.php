<?php

namespace Tests\Unit\Divante\ClassificationTreeBundle\Service;

use Divante\ClassificationTreeBundle\Service\ClassificationTreeBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;

class ClassificationTreeBuilderTest extends TestCase
{
    /** @var ClassificationTreeBuilder $classificationTreeBuilder */
    private $classificationTreeBuilder;

    public function setUp(): void
    {
        $this->classificationTreeBuilder = new ClassificationTreeBuilder();
    }

    public function testGetRootNodes()
    {
        $result = $this->classificationTreeBuilder->getRootNodes();

        $this->assertArrayHasKey('allowChildren', current($result));
        $this->assertArrayHasKey('allowDrop', current($result));
        $this->assertArrayHasKey('basePath', current($result));
        $this->assertArrayHasKey('elementType', current($result));
        $this->assertArrayHasKey('expanded', current($result));
        $this->assertArrayHasKey('iconCls', current($result));
        $this->assertArrayHasKey('id', current($result));
        $this->assertArrayHasKey('isTarget', current($result));
        $this->assertArrayHasKey('leaf', current($result));
        $this->assertArrayHasKey('locked', current($result));
        $this->assertArrayHasKey('path', current($result));
        $this->assertArrayHasKey('permissions', current($result));
        $this->assertArrayHasKey('qtipCfg', current($result));
        $this->assertArrayHasKey('text', current($result));
        $this->assertArrayHasKey('text', current($result));
    }
}
