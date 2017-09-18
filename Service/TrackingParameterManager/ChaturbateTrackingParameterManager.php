<?php

namespace EXS\LanderTrackingChaturbateBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterExtracterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;

/**
 * Class ChaturbateTrackingParameterManager
 *
 * @package EXS\LanderTrackingChaturbateBundle\Service\TrackingParameterManager
 */
class ChaturbateTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
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
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (
            (null !== $track = $request->query->get('track'))
            && (preg_match('`^(?<cmp>[a-z0-9]+)(?:~(?<exid>[a-z0-9]+))?(?:~(?<visit>[0-9]+))?$`i', $track, $matches))
        ) {
            /** Get 'cmp', 'exid' and 'visit' from 'track' query parameter. */
            $trackingParameters['cmp'] = $matches['cmp'];
            $trackingParameters['exid'] = isset($matches['exid']) ? $matches['exid'] : null;
            $trackingParameters['visit'] = isset($matches['visit']) ? $matches['visit'] : 1;
        } else {
            $trackingParameters['cmp'] = $request->cookies->get('cmp', $this->defaultCmp);
            $trackingParameters['exid'] = $request->cookies->get('exid');
            $trackingParameters['visit'] = $request->cookies->get('visit');
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
            $trackingParameters->has('exid')
            && $trackingParameters->has('visit')
        ) {
            $track = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('cmp', $this->defaultCmp),
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit')
            );
        } elseif ($trackingParameters->has('exid')) {
            $track = sprintf(
                '%s~%s~1',
                $trackingParameters->get('cmp', $this->defaultCmp),
                $trackingParameters->get('exid')
            );
        } else {
            $track = $trackingParameters->get('cmp', $this->defaultCmp);
        }

        return [
            'track' => $track,
        ];
    }
}
