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
            && (preg_match('`^(?<cmp>[a-z0-9]+)(?:~(?<u>[a-z0-9]+))?(?:~(?<visit>[0-9]+))?$`i', $track, $matches))
        ) {
            /** Get 'cmp', 'u' and 'visit' from 'track' query parameter. */
            $trackingParameters['cmp'] = $matches['cmp'];

            if (true === isset($matches['u'])) {
                $trackingParameters['u'] = $matches['u'];
            }

            if (true === isset($matches['visit'])) {
                $trackingParameters['visit'] = $matches['visit'];
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
            $trackingParameters->has('cmp')
            && $trackingParameters->has('u')
            && $trackingParameters->has('visit')
        ) {
            $track = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('u'),
                $trackingParameters->get('visit')
            );
        } elseif (
            $trackingParameters->has('cmp')
            && $trackingParameters->has('u')
        ) {
            $track = sprintf(
                '%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('u')
            );
        } elseif ($trackingParameters->has('cmp')) {
            $track = $trackingParameters->get('cmp');
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
            'cmp' => $this->defaultCmp,
        ];
    }
}
