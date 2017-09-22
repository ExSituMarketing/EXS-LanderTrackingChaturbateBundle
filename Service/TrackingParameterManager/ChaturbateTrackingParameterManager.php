<?php

namespace EXS\LanderTrackingChaturbateBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterInitializerInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterQueryExtracterInterface;

/**
 * Class ChaturbateTrackingParameterManager
 *
 * @package EXS\LanderTrackingChaturbateBundle\Service\TrackingParameterManager
 */
class ChaturbateTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface, TrackingParameterInitializerInterface
{
    /**
     * @var int
     */
    private $defaultCmp;

    /**
     * AweTrackingParameterManager constructor.
     *
     * @param $defaultCmp
     */
    public function __construct($defaultCmp)
    {
        $this->defaultCmp = $defaultCmp;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $track = $query->get('track'))
            && (preg_match('`^(?<c>[a-z0-9]+)(?:~(?<u>[a-z0-9]+))?(?:~(?<v>[0-9]+))?$`i', $track, $matches))
        ) {
            /** Get 'c', 'u' and 'v' from 'track' query parameter. */
            $trackingParameters['c'] = $matches['c'];

            if (true === isset($matches['u'])) {
                $trackingParameters['u'] = $matches['u'];
            }

            if (true === isset($matches['v'])) {
                $trackingParameters['v'] = $matches['v'];
            }
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $track = null;

        if (
            $trackingParameters->has('c')
            && $trackingParameters->has('u')
            && $trackingParameters->has('v')
        ) {
            $track = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('c'),
                $trackingParameters->get('u'),
                $trackingParameters->get('v')
            );
        } elseif (
            $trackingParameters->has('c')
            && $trackingParameters->has('u')
        ) {
            $track = sprintf(
                '%s~%s',
                $trackingParameters->get('c'),
                $trackingParameters->get('u')
            );
        } elseif ($trackingParameters->has('c')) {
            $track = $trackingParameters->get('c');
        }

        return [
            'track' => $track,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'c' => $this->defaultCmp,
        ];
    }
}
