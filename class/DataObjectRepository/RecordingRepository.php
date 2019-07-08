<?php

namespace Avorg\DataObjectRepository;

use Avorg\DataObject;
use Avorg\DataObjectRepository;
use function defined;
use Exception;
use natlib\Stub;

if (!defined('ABSPATH')) exit;

class RecordingRepository extends DataObjectRepository
{
	protected $dataObjectClass = "Avorg\\DataObject\\Recording";

    /**
     * @param string $list
     * @return array
     * @throws Exception
     */
    public function getRecordings($list = "")
    {
        $apiResponse = $this->api->getRecordings($list);

		return $this->makeDataObjects($apiResponse);
    }

	/**
	 * @param $presenterId
	 * @return array
	 * @throws Exception
	 */
	public function getPresenterRecordings($presenterId)
	{
		$apiResponse = $this->api->getPresenterRecordings($presenterId);

		return $this->makeDataObjects($apiResponse);
	}

	/**
	 * @param $id
	 * @return DataObject|null
	 * @throws Exception
	 */
	public function getRecording($id)
    {
        $apiResponse = $this->api->getRecording($id);

        return $apiResponse ? $this->makeDataObject($apiResponse) : null;
    }

	/**
	 * @param $conferenceId
	 * @return array
	 * @throws Exception
	 */
	public function getConferenceRecordings($conferenceId)
	{
		$rawObjects = $this->api->getConferenceRecordings($conferenceId);

		return $this->makeDataObjects($rawObjects);
	}

	/**
	 * @param $sponsorId
	 * @return array
	 * @throws Exception
	 */
	public function getSponsorRecordings($sponsorId)
	{
		$rawObjects = $this->api->getSponsorRecordings($sponsorId);

		return $this->makeDataObjects($rawObjects);
	}

	/**
	 * @param $topicId
	 * @return array
	 * @throws Exception
	 */
	public function getTopicRecordings($topicId)
	{
		$apiResponse = $this->api->getTopicRecordings($topicId);

		return $this->makeDataObjects($apiResponse);
	}

	/**
	 * @param $playlistId
	 * @return array
	 * @throws Exception
	 */
	public function getPlaylistRecordings($playlistId)
	{
		$apiResponse = $this->api->getPlaylist($playlistId);

		return array_map(function ($recording) {
			return $this->makeDataObject($recording);
		}, $apiResponse->recordings ?: []);
	}

	/**
	 * @param $bookId
	 * @return array
	 * @throws Exception
	 */
	public function getBookRecordings($bookId)
	{
		$apiResponse = $this->api->getBookRecordings($bookId);

		return $this->makeDataObjects($apiResponse);
	}

	public function getSeriesRecordings($seriesId)
	{
		$rawObjects = $this->api->getSeriesRecordings($seriesId);

		return $this->makeDataObjects($rawObjects);
	}

}