<?php

namespace JeremyWorboys\SonarrPutIO\Events;

abstract class Parameters
{
    /** @var string */
    private $eventType;

    /**
     * Parameters constructor.
     */
    private function __construct() { }

    /**
     * @return \JeremyWorboys\SonarrPutIO\Events\Parameters
     */
    final public static function createFromServer(): Parameters
    {
        return self::create($_SERVER['sonarr_eventtype'], $_SERVER);
    }

    /**
     * @param array $parameters
     * @return \JeremyWorboys\SonarrPutIO\Events\Parameters
     */
    final public static function createFromArray(array $parameters): Parameters
    {
        return self::create($parameters['sonarr_eventtype'], $parameters);
    }

    /**
     * @param string $eventType
     * @param array  $parameters
     * @return \JeremyWorboys\SonarrPutIO\Events\Parameters
     */
    private static function create(string $eventType, array $parameters): Parameters
    {
        switch ($eventType) {
            case 'Grab':
                $params = new GrabParameters();
                $params->eventType = $eventType;
                $params->fill($parameters);
                return $params;

            case 'Download':
                $params = new DownloadParameters();
                $params->eventType = $eventType;
                $params->fill($parameters);
                return $params;

            case 'Rename':
                $params = new RenameParameters();
                $params->eventType = $eventType;
                $params->fill($parameters);
                return $params;

            default:
                throw new \LogicException('Unrecognised event type "' . $eventType . '".');
        }
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @param array $params
     */
    abstract protected function fill(array $params): void;
}
