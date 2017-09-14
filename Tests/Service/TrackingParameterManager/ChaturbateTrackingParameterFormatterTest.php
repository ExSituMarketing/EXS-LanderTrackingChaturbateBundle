<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingChaturbateBundle\Service\TrackingParameterManager\ChaturbateTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ChaturbateTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('track')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new ChaturbateTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertEmpty($result);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('track')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('cmp')->willReturn(123)->shouldBeCalledTimes(1);
        $cookies->get('exid', null)->willReturn('UUID987654321')->shouldBeCalledTimes(1);
        $cookies->get('visit', 1)->willReturn('5')->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new ChaturbateTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals('5', $result['visit']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('track')->willReturn('123~UUID987654321~5', '123~UUID987654321', '123')->shouldBeCalledTimes(3);

        $request->query = $query;

        $revealedRequest = $request->reveal();

        $manager = new ChaturbateTrackingParameterManager();

        $result = $manager->extract($revealedRequest);

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);

        $result = $manager->extract($revealedRequest);

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(1, $result['visit']);

        $result = $manager->extract($revealedRequest);

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertNull($result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(1, $result['visit']);
    }


    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new ChaturbateTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertNull($result['track']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'exid' => 'UUID987654321',
            'visit' => 5,
        ]);

        $formatter = new ChaturbateTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertEquals('123~UUID987654321~5', $result['track']);
    }

    public function testFormatWithCmpAndExid()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'exid' => 'UUID987654321',
        ]);

        $formatter = new ChaturbateTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertEquals('123~UUID987654321~1', $result['track']);
    }

    public function testFormatWithCmp()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
        ]);

        $formatter = new ChaturbateTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertEquals('123', $result['track']);
    }
}
