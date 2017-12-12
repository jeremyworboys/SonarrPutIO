<?php

namespace JeremyWorboys\SonarrPutIO\Events;

class DownloadParameters extends Parameters
{
    /** @var string|null */
    private $seriesId;

    /** @var string|null */
    private $seriesPath;

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
    private $episodeFileEpisodeAirDates;

    /** @var string|null */
    private $episodeFileEpisodeAirDatesUTC;

    /** @var string|null */
    private $episodeFileEpisodeCount;

    /** @var string|null */
    private $episodeFileEpisodeNumbers;

    /** @var string|null */
    private $episodeFileEpisodeTitles;

    /** @var string|null */
    private $episodeFileId;

    /** @var string|null */
    private $episodeFilePath;

    /** @var string|null */
    private $episodeFileQuality;

    /** @var string|null */
    private $episodeFileQualityVersion;

    /** @var string|null */
    private $episodeFileRelativePath;

    /** @var string|null */
    private $episodeFileReleaseGroup;

    /** @var string|null */
    private $episodeFileSceneName;

    /** @var string|null */
    private $episodeFileSeasonNumber;

    /** @var string|null */
    private $episodeFileSourceFolder;

    /** @var string|null */
    private $episodeFileSourcePath;

    /** @var string|null */
    private $downloadId;

    /** @var string|null */
    private $downloadClient;

    /** @var string|null */
    private $isUpgrade;

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
     * @return string|null
     */
    public function getSeriesPath()
    {
        return $this->seriesPath;
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
     * Full path to the series
     *
     * @return string|null
     */
    public function getReleaseEpisodeCount()
    {
        return $this->releaseEpisodeCount;
    }

    /**
     * True when an an existing file is upgraded, otherwise False
     *
     * @return string|null
     */
    public function getReleaseSeasonNumber()
    {
        return $this->releaseSeasonNumber;
    }

    /**
     * Internal ID of the episode file
     *
     * @return string|null
     */
    public function getReleaseEpisodeNumbers()
    {
        return $this->releaseEpisodeNumbers;
    }

    /**
     * Path to the episode file relative to the series' path
     *
     * @return string|null
     */
    public function getReleaseEpisodeAirDates()
    {
        return $this->releaseEpisodeAirDates;
    }

    /**
     * Full path to the episode file
     *
     * @return string|null
     */
    public function getReleaseEpisodeAirDatesUTC()
    {
        return $this->releaseEpisodeAirDatesUTC;
    }

    /**
     * Number of episodes in the episode file
     *
     * @return string|null
     */
    public function getReleaseEpisodeTitles()
    {
        return $this->releaseEpisodeTitles;
    }

    /**
     * Season number of episode file
     *
     * @return string|null
     */
    public function getReleaseTitle()
    {
        return $this->releaseTitle;
    }

    /**
     * Comma separated list of episode numbers
     *
     * @return string|null
     */
    public function getReleaseIndexer()
    {
        return $this->releaseIndexer;
    }

    /**
     * Air date from original network
     *
     * @return string|null
     */
    public function getReleaseSize()
    {
        return $this->releaseSize;
    }

    /**
     * Air Date with Time in UTC
     *
     * @return string|null
     */
    public function getReleaseQuality()
    {
        return $this->releaseQuality;
    }

    /**
     * Pipe separated list of episode titles
     *
     * @return string|null
     */
    public function getReleaseQualityVersion()
    {
        return $this->releaseQualityVersion;
    }

    /**
     * Quality name from Sonarr
     *
     * @return string|null
     */
    public function getReleaseReleaseGroup()
    {
        return $this->releaseReleaseGroup;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileEpisodeAirDates()
    {
        return $this->episodeFileEpisodeAirDates;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileEpisodeAirDatesUTC()
    {
        return $this->episodeFileEpisodeAirDatesUTC;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileEpisodeCount()
    {
        return $this->episodeFileEpisodeCount;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileEpisodeNumbers()
    {
        return $this->episodeFileEpisodeNumbers;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileEpisodeTitles()
    {
        return $this->episodeFileEpisodeTitles;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileId()
    {
        return $this->episodeFileId;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFilePath()
    {
        return $this->episodeFilePath;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileQuality()
    {
        return $this->episodeFileQuality;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileQualityVersion()
    {
        return $this->episodeFileQualityVersion;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileRelativePath()
    {
        return $this->episodeFileRelativePath;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileReleaseGroup()
    {
        return $this->episodeFileReleaseGroup;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileSceneName()
    {
        return $this->episodeFileSceneName;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileSeasonNumber()
    {
        return $this->episodeFileSeasonNumber;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileSourceFolder()
    {
        return $this->episodeFileSourceFolder;
    }

    /**
     * @return string|null
     */
    public function getEpisodeFileSourcePath()
    {
        return $this->episodeFileSourcePath;
    }

    /**
     * 1 is the default, 2 for proper, 3+ could be used for anime versions
     *
     * @return string|null
     */
    public function getDownloadId()
    {
        return $this->downloadId;
    }

    /**
     * @return string|null
     */
    public function getDownloadClient()
    {
        return $this->downloadClient;
    }

    /**
     * @return string|null
     */
    public function isUpgrade()
    {
        return $this->isUpgrade;
    }



    /**
     * @param array $params
     */
    protected function fill(array $params): void
    {
        $this->seriesId = $params['sonarr_series_id'] ?? null;
        $this->seriesPath = $params['sonarr_series_path'] ?? null;
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
        $this->episodeFileEpisodeAirDates = $params['sonarr_episodefile_episodeairdates'] ?? null;
        $this->episodeFileEpisodeAirDatesUTC = $params['sonarr_episodefile_episodeairdatesutc'] ?? null;
        $this->episodeFileEpisodeCount = $params['sonarr_episodefile_episodecount'] ?? null;
        $this->episodeFileEpisodeNumbers = $params['sonarr_episodefile_episodenumbers'] ?? null;
        $this->episodeFileEpisodeTitles = $params['sonarr_episodefile_episodetitles'] ?? null;
        $this->episodeFileId = $params['sonarr_episodefile_id'] ?? null;
        $this->episodeFilePath = $params['sonarr_episodefile_path'] ?? null;
        $this->episodeFileQuality = $params['sonarr_episodefile_quality'] ?? null;
        $this->episodeFileQualityVersion = $params['sonarr_episodefile_qualityversion'] ?? null;
        $this->episodeFileRelativePath = $params['sonarr_episodefile_relativepath'] ?? null;
        $this->episodeFileReleaseGroup = $params['sonarr_episodefile_releasegroup'] ?? null;
        $this->episodeFileSceneName = $params['sonarr_episodefile_scenename'] ?? null;
        $this->episodeFileSeasonNumber = $params['sonarr_episodefile_seasonnumber'] ?? null;
        $this->episodeFileSourceFolder = $params['sonarr_episodefile_sourcefolder'] ?? null;
        $this->episodeFileSourcePath = $params['sonarr_episodefile_sourcepath'] ?? null;
        $this->downloadId = $params['sonarr_download_id'] ?? null;
        $this->downloadClient = $params['sonarr_download_client'] ?? null;
        $this->isUpgrade = $params['sonarr_isupgrade'] ?? null;
    }
}
