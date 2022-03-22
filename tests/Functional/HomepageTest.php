<?php

namespace Tests\Functional;

class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetHomepageWithoutName()
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase('PHP Tools', (string)$response->getBody());
    }

    // /**
    //  * Test that the index route won't accept a post request
    //  */
    // public function testPostHomepageNotAllowed()
    // {
    //     $app = $this->getAppInstance();

    //     $request = $this->createRequest('POST', '/')
    //         ->withParsedBody(['test']);
    //     $response = $app->handle($request);

    //     // $this->assertEquals(405, $response->getStatusCode());
    //     // $this->assertStringContainsStringIgnoringCase('Method not allowed', (string)$response->getBody());
    // }
}