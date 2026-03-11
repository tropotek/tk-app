<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tk\Breadcrumbs\Breadcrumbs;

class BreadcrumbsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test()
    {
        $crumbs = new \Tk\Breadcrumbs\Breadcrumbs('Test', '/');

        $this->assertEquals($crumbs->getHomeTitle(), 'Test',
            "Home Title property invalid");


        $this->assertEquals($crumbs->getHomeUrl(), '/',
            "Home URL property invalid");


        $crumbs->push('Page 1', '/page1');
        $crumbs->push('Page 2', '/page2');
        $crumbs->push('Page 3', '/page3');
        $this->assertEquals($crumbs->__toString(), 'Test > Page 1 > Page 2 > Page 3',
            "Invalid crumb stack values");


        $crumbs->push('Page 2', '/page2');
        $this->assertEquals($crumbs->__toString(), 'Test > Page 1 > Page 2',
            "Crumb stack trimming errors");


        $stack = [
            'Test' => '/',
            'Page 1' => '/page1',
            'Page 2' => '/page2',
        ];
        $this->assertEquals($stack, $crumbs->toArray(),
            "Crumb stack generated array error");


        $url = "/test/page";
        $this->assertEquals($crumbs->getResetUrl($url), url('/test/page?'.Breadcrumbs::CRUMB_RESET.'=1'),
            "Error setting crumb reset query param on url");


    }


}
