<?php

namespace Divante\ClassificationTreeBundle\Service;

use AdvancedObjectSearchBundle\Service;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClassificationTreeBuilderTest extends TestCase
{
    /** @var ClassificationTreeBuilder $classificationTreeBuilder */
    private $classificationTreeBuilder;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Service|MockObject $searchService */
        $searchService = $this->createMock(Service::class);

        $this->classificationTreeBuilder = new ClassificationTreeBuilder($searchService);
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
