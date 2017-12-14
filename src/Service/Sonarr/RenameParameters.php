<?php

namespace JeremyWorboys\SonarrPutIO\Service\Sonarr;

class RenameParameters extends Parameters
{
    /** @var string|null */
    private $seriesId;

    /** @var string|null */
    private $seriesTitle;

    /** @var string|null */
    private $seriesPath;

    /** @var string|null */
    private $seriesTvdbId;

    /**
     * Internal ID of the series
     *
     * @return string|null
     */
    public function getSeriesId()
    {
        return $this->seriesId;
    }

    /**
     * Title of the series
     *
     * @return string|null
     */
    public function getSeriesTitle()
    {
        return $this->seriesTitle;
    }

    /**
     * Full path to the series
     *
     * @return string|null
     */
    public function getSeriesPath()
    {
        return $this->seriesPath;
    }

    /**
     * TVDB ID for the series
     *
     * @return string|null
     */
    public function getSeriesTvdbId()
    {
        return $this->seriesTvdbId;
    }

    /**
     * @param array $params
     */
    protected function fill(array $params): void
    {
        $this->seriesId = $params['sonarr_series_id'] ?? null;
        $this->seriesTitle = $params['sonarr_series_title'] ?? null;
        $this->seriesPath = $params['sonarr_series_path'] ?? null;
        $this->seriesTvdbId = $params['sonarr_series_tvdbid'] ?? null;
    }
}
