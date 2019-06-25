<?php

namespace Avorg;

use Exception;
use function get_option;

if (!\defined('ABSPATH')) exit;

class AvorgApi
{
	private $apiBaseUrl = "https://api2.audioverse.org";
	private $apiUser;
	private $apiPass;
	private $context;
	
	public function __construct()
	{
		$this->apiUser = get_option("avorgApiUser");
		$this->apiPass = get_option("avorgApiPass");
	}

	public function getTopics()
	{
		$url = "$this->apiBaseUrl/topics";

		try {
			$response = $this->getResponse($url);

			return array_map(function($item) {
				return $item->topics;
			}, json_decode($response)->result);
		} catch (Exception $e) {
			throw new Exception("Couldn't retrieve topics", 0, $e);
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function getBook($id)
	{
		if (!is_numeric($id)) return false;
		$url = "$this->apiBaseUrl/audiobooks/$id";

		try {
			$response = $this->getResponse($url);
			$responseObject = json_decode($response);

			return $responseObject->result[0]->audiobooks;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getBooks()
	{
		$url = "$this->apiBaseUrl/audiobooks";

		try {
			$response = $this->getResponse($url);

			return array_map(function($item) {
				return $item->audiobooks;
			}, json_decode($response)->result);
		} catch (Exception $e) {
			throw new Exception("Couldn't retrieve books", 0, $e);
		}
	}

	public function getPlaylist($id)
	{
		if (!is_numeric($id)) return false;
		$url = "$this->apiBaseUrl/playlist/$id";

		try {
			$response = $this->getResponse($url);
			$responseObject = json_decode($response);

			return $responseObject->result;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws Exception
	 */
	public function getPresenter($id)
	{
		if (!is_numeric($id)) return false;

		$url = "$this->apiBaseUrl/presenters/{$id}";

		try {
			$response = $this->getResponse($url);

			return json_decode($response)->result[0]->presenters;
		} catch (Exception $e) {
			throw new Exception("Couldn't retrieve recording with ID $id", 0, $e);
		}
	}

	/**
	 * @param null $search
	 * @return mixed
	 * @throws Exception
	 */
	public function getPresenters($search = null)
	{
		$url = "$this->apiBaseUrl/presenters?search=$search";

		try {
			$response = $this->getResponse($url);

			return array_map(function($item) {
				return $item->presenters;
			}, json_decode($response)->result);
		} catch (Exception $e) {
			throw new Exception("Couldn't retrieve presenters with search `$search`", 0, $e);
		}
	}
	
	/**
	 * @param $id
	 * @return bool
	 * @throws Exception
	 */
	public function getRecording($id)
	{
		if (!is_numeric($id)) return false;
		
		$url = "$this->apiBaseUrl/recordings/{$id}";
		
		try {
			$response = $this->getResponse($url);

			return json_decode($response)->result[0];
		} catch (Exception $e) {
			throw new Exception("Couldn't retrieve recording with ID $id", 0, $e);
		}
	}
	
	/**
	 * @param string $list
	 * @return null
	 * @throws Exception
	 */
	public function getRecordings($list = "")
	{
		$endpoint = trim("recordings/$list", "/");

		return $this->getRecordingsResponse($endpoint);
	}

	/**
	 * @param $topicId
	 * @return null
	 * @throws Exception
	 */
	public function getTopicRecordings($topicId)
	{
		return $this->getRecordingsResponse("recordings/topic/$topicId");
	}

	/**
	 * @param $presenterId
	 * @return bool|null
	 * @throws Exception
	 */
	public function getPresenterRecordings($presenterId)
	{
		if (!is_numeric($presenterId)) return false;

		return $this->getRecordingsResponse("recordings/presenter/$presenterId");
	}

	/**
	 * @param $bookId
	 * @return bool|null
	 * @throws Exception
	 */
	public function getBookRecordings($bookId)
	{
		if (!is_numeric($bookId)) return false;

		return $this->getRecordingsResponse("recordings/audiobook/$bookId");
	}

	/**
	 * @param $endpoint
	 * @return null
	 * @throws Exception
	 */
	private function getRecordingsResponse($endpoint)
	{
		$url = "$this->apiBaseUrl/$endpoint";
		
		try {
			$response = $this->getResponse($url);
			$responseObject = json_decode($response);

			return (isset($responseObject->result)) ? $responseObject->result : null;
		} catch (Exception $e) {
			throw new Exception("Couldn't retrieve list of recordings", 0, $e);
		}
	}
	
	/**
	 * @param $url
	 * @return bool|string
	 * @throws Exception
	 */
	private function getResponse($url)
	{
		if (!$this->context) $this->context = $this->createContext();
		
		if ($result = @file_get_contents($url, false, $this->context)) {
			return $result;
		} else {
			throw new Exception("Failed to get response from network");
		}
	}
	
	private function createContext()
	{
		$opts = array('http' =>
			array(
				'header' => "Content-Type: text/xml\r\n" .
					"Authorization: Basic " . base64_encode("$this->apiUser:$this->apiPass") . "\r\n"
			)
		);
		
		return stream_context_create($opts);
	}
}