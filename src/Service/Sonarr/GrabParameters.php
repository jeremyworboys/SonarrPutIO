<?php

namespace JeremyWorboys\SonarrPutIO\Service\Sonarr;

class GrabParameters extends Parameters
{
    /** @var string|null */
    private $seriesId;

    /** @var string|null */
    private $seriesTitle;

    /** @var string|null */
    private $seriesTvdbId;

    /** @var string|null */
    private $seriesTvmazeId;

    /** @var string|null */
    private $seriesImdb;

    /** @var string|null */
    private $seriesType;

    /** @var string|null */
    private $releaseEpisodeCount;

    /** @var string|null */
    private $releaseSeasonNumber;

    /** @var string|null */
    private $releaseEpisodeNumbers;

    /** @var string|null */
    private $releaseEpisodeAirDates;

    /** @var string|null */
    private $releaseEpisodeAirDatesUTC;

    /** @var string|null */
    private $releaseEpisodeTitles;

    /** @var string|null */
    private $releaseTitle;

    /** @var string|null */
    private $releaseIndexer;

    /** @var string|null */
    private $releaseSize;

    /** @var string|null */
    private $releaseQuality;

    /** @var string|null */
    private $releaseQualityVersion;

    /** @var string|null */
    private $releaseReleaseGroup;

    /** @var string|null */
    private $downloadId;

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
     * TVDB ID for the series
     *
     * @return string|null
     */
    public function getSeriesTvdbId()
    {
        return $this->seriesTvdbId;
    }

    /**
     * TVMaze ID for the series
     *
     * @return string|null
     */
    public function getSeriesTvmazeId()
    {
        return $this->seriesTvmazeId;
    }

    /**
     * IMDB ID for the series
     *
     * @return string|null
     */
    public function getSeriesImdb()
    {
        return $this->seriesImdb;
    }

    /**
     * Type of the series, Anime, Daily or Standard
     *
     * @return string|null
     */
    public function getSeriesType()
    {
        return $this->seriesType;
    }

    /**
     * Number of episodes in the release
     *
     * @return string|null
     */
    public function getReleaseEpisodeCount()
    {
        return $this->releaseEpisodeCount;
    }

    /**
     * Season number from release
     *
     * @return string|null
     */
    public function getReleaseSeasonNumber()
    {
        return $this->releaseSeasonNumber;
    }

    /**
     * Comma separated list of episode numbers
     *
     * @return string|null
     */
    public function getReleaseEpisodeNumbers()
    {
        return $this->releaseEpisodeNumbers;
    }

    /**
     * Air date from original network
     *
     * @return string|null
     */
    public function getReleaseEpisodeAirDates()
    {
        return $this->releaseEpisodeAirDates;
    }

    /**
     * Air Date with Time in UTC
     *
     * @return string|null
     */
    public function getReleaseEpisodeAirDatesUTC()
    {
        return $this->releaseEpisodeAirDatesUTC;
    }

    /**
     * Pipe (|) separated list of episode titles
     *
     * @return string|null
     */
    public function getReleaseEpisodeTitles()
    {
        return $this->releaseEpisodeTitles;
    }

    /**
     * NZB/Torrent title
     *
     * @return string|null
     */
    public function getReleaseTitle()
    {
        return $this->releaseTitle;
    }

    /**
     * Indexer where the release was grabbed
     *
     * @return string|null
     */
    public function getReleaseIndexer()
    {
        return $this->releaseIndexer;
    }

    /**
     * Size of the release reported by the indexer
     *
     * @return string|null
     */
    public function getReleaseSize()
    {
        return $this->releaseSize;
    }

    /**
     * Quality name from Sonarr
     *
     * @return string|null
     */
    public function getReleaseQuality()
    {
        return $this->releaseQuality;
    }

    /**
     * 1 is the default, 2 for proper, 3+ could be used for anime versions
     *
     * @return string|null
     */
    public function getReleaseQualityVersion()
    {
        return $this->releaseQualityVersion;
    }

    /**
     * Release Group, will not be set if it is unknown
     *
     * @return string|null
     */
    public function getReleaseReleaseGroup()
    {
        return $this->releaseReleaseGroup;
    }

    /**
     * The hash of the torrent/NZB file downloaded (used to uniquely identify the download in the download client)
     *
     * @return string|null
     */
    public function getDownloadId()
    {
        return $this->downloadId;
    }

    /**
     * @param array $params
     */
    protected function fill(array $params): void
    {
        $this->seriesId = $params['sonarr_series_id'] ?? null;
        $this->seriesTitle = $params['sonarr_series_title'] ?? null;
        $this->seriesTvdbId = $params['sonarr_series_tvdbid'] ?? null;
        $this->seriesTvmazeId = $params['sonarr_series_tvmazeid'] ?? null;
        $this->seriesImdb = $params['sonarr_series_imdb'] ?? null;
        $this->seriesType = $params['sonarr_series_type'] ?? null;
        $this->releaseEpisodeCount = $params['sonarr_release_episodecount'] ?? null;
        $this->releaseSeasonNumber = $params['sonarr_release_seasonnumber'] ?? null;
        $this->releaseEpisodeNumbers = $params['sonarr_release_episodenumbers'] ?? null;
        $this->releaseEpisodeAirDates = $params['sonarr_release_episodeairdates'] ?? null;
        $this->releaseEpisodeAirDatesUTC = $params['sonarr_release_episodeairdatesutc'] ?? null;
        $this->releaseEpisodeTitles = $params['sonarr_release_episodetitles'] ?? null;
        $this->releaseTitle = $params['sonarr_release_title'] ?? null;
        $this->releaseIndexer = $params['sonarr_release_indexer'] ?? null;
        $this->releaseSize = $params['sonarr_release_size'] ?? null;
        $this->releaseQuality = $params['sonarr_release_quality'] ?? null;
        $this->releaseQualityVersion = $params['sonarr_release_qualityversion'] ?? null;
        $this->releaseReleaseGroup = $params['sonarr_release_releasegroup'] ?? null;
        $this->downloadId = $params['sonarr_download_id'] ?? null;
    }
}
