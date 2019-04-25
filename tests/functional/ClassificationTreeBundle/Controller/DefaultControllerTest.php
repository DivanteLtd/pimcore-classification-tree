<?php

namespace Tests\Functional\Divante\ClassificationTreeBundle\Controller;

use Tests\FunctionalTester;

class DefaultControllerTest
{
    public function testSomething(FunctionalTester $I)
    {
        die("IM HERE");
        $I->amOnPage('/');
        $I->canSeeResponseCodeIs(200);
    }
}
