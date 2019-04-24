<?php

namespace Tests\Unit\Divante\ClassificationTreeBundle\Service;

use AdvancedObjectSearchBundle\Service;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;

class ClassificationTreeBuilderTest extends TestCase
{
    /** @var ClassificationTreeBuilder $classificationTreeBuilder */
    private $classificationTreeBuilder;

    public function setUp(): void
    {
        /** @var Service|MockObject $searchService */
        $searchService = $this->createMock(Service::class);

        /** @var StoreConfig\Listing|MockObject $listing */
        $storeConfigListing = $this->getMockBuilder(StoreConfig\Listing::class)
            ->setMethods(['load'])
            ->getMock();
        $storeConfigListing->method('load')
            ->withAnyParameters()
            ->willReturn([new StoreConfig()]);

        $this->classificationTreeBuilder = new ClassificationTreeBuilder(
            $searchService, $storeConfigListing
        );
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
