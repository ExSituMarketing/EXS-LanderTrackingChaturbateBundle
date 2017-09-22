<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingChaturbateBundle\Service\TrackingParameterManager\ChaturbateTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class ChaturbateTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('track')->willReturn('123~UUID987654321~5', '123~UUID987654321', '123')->shouldBeCalledTimes(3);
        $revealedQuery = $query->reveal();

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->extractFromQuery($revealedQuery);

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->extractFromQuery($revealedQuery);

        $this->assertCount(2, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->extractFromQuery($revealedQuery);

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertNull($result['track']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'u' => 'UUID987654321',
            'visit' => 5,
        ]);

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertEquals('123~UUID987654321~5', $result['track']);
    }

    public function testFormatWithCmpAndExid()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'u' => 'UUID987654321',
        ]);

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertEquals('123~UUID987654321', $result['track']);
    }

    public function testFormatWithCmp()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
        ]);

        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('track', $result);
        $this->assertEquals('123', $result['track']);
    }

    public function testInitialize()
    {
        $manager = new ChaturbateTrackingParameterManager(1);

        $result = $manager->initialize();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(1, $result['cmp']);
    }
}
